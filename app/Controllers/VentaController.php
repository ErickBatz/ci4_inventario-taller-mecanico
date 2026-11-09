<?php

namespace App\Controllers;

use App\Models\RepuestosModel;
use CodeIgniter\RESTful\ResourceController;

class VentaController extends ResourceController
{
    protected $modelName = 'App\Models\VentaModel';
    protected $format = 'json';

    // GET /api/entradas -> listar todas
    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    // GET /api/entradas/5 -> ver una
    public function show($id = null)
    {
        $venta = $this->model->find($id);

        if (!$venta) {
            return $this->respond([
                'status'  => 404,
                'message' => 'No existe la entrada con ID: ' . $id
            ], 404);
        }

        return $this->respond($venta);
    }

    // POST /api/entradas -> crear  
    // POST /api/ventas -> crear
public function create()
{
    $datos = $this->request->getJSON(true);

    // 1. Validar la estructura ANTES de tocar la base de datos
    if (empty($datos['id_usuario'])) {
        return $this->respond([
            'status'  => 400,
            'message' => 'Falta el id_usuario'
        ], 400);
    }

    if (empty($datos['detalles']) || !is_array($datos['detalles'])) {
        return $this->respond([
            'status'  => 400,
            'message' => 'La venta debe traer al menos un repuesto en "detalles"'
        ], 400);
    }

    $repuestosModel  = new \App\Models\RepuestosModel();
    $detalleModel    = new \App\Models\Detalle_VentaModel();
    $movimientoModel = new \App\Models\MovimientoInventarioModel();
    $facturaModel    = new \App\Models\FacturasModel();

    // 2. Validar cada repuesto, revisar stock y calcular el total
    $total = 0;

    $lineas = []; // aquí guardamos lo ya validado, con el precio real de la BD

    foreach ($datos['detalles'] as $d) {
        if (empty($d['id_repuesto']) || empty($d['cantidad']) || (int)$d['cantidad'] <= 0) {
            return $this->respond([
                'status'  => 400,
                'message' => 'Cada detalle necesita id_repuesto y una cantidad mayor a 0'
            ], 400);
        }

        $repuesto = $repuestosModel->find($d['id_repuesto']);

        if (!$repuesto) {
            return $this->respond([
                'status'  => 404,
                'message' => 'No existe el repuesto con ID: ' . $d['id_repuesto']
            ], 404);
        }

        $cantidad = (int) $d['cantidad'];

        if ($repuesto['stock'] < $cantidad) {
            return $this->respond([
                'status'  => 409,
                'message' => 'Stock insuficiente para "' . $repuesto['nombre'] .
                             '". Disponible: ' . $repuesto['stock']
            ], 409);
        }

        // El precio sale de la BD, NO del frontend
        $precio = (float) $repuesto['precio_venta'];
        $total += $cantidad * $precio;

        $lineas[] = [
            'id_repuesto'     => $repuesto['id_repuesto'],
            'cantidad'        => $cantidad,
            'precio_unitario' => $precio,
        ];
    }

    // 3. Abrir la transacción
    $db = \Config\Database::connect();
    $db->transBegin();

    try {
        // 3.1 Cabecera de la venta
        $idVenta = $this->model->insert([
            'id_usuario'  => $datos['id_usuario'],
            'fecha_venta' => $datos['fecha_venta'] ?? date('Y-m-d'),
            'total'       => $total,
        ]);

        if ($idVenta === false) {
            throw new \Exception(json_encode($this->model->errors()));
        }

        // 3.2 Por cada repuesto: detalle + restar stock + movimiento
        foreach ($lineas as $l) {

            $okDetalle = $detalleModel->insert([
                'id_venta'        => $idVenta,
                'id_repuesto'     => $l['id_repuesto'],
                'cantidad'        => $l['cantidad'],
                'precio_unitario' => $l['precio_unitario'],
            ]);
            if ($okDetalle === false) {
                throw new \Exception(json_encode($detalleModel->errors()));
            }

            // Restar stock SOLO si todavía alcanza (protege contra ventas simultáneas)
            $db->table('repuestos')
               ->set('stock', 'stock - ' . $l['cantidad'], false)
               ->where('id_repuesto', $l['id_repuesto'])
               ->where('stock >=', $l['cantidad'])
               ->update();

            if ($db->affectedRows() === 0) {
                throw new \Exception('Stock insuficiente para el repuesto ID ' . $l['id_repuesto']);
            }

            $okMov = $movimientoModel->insert([
                'id_repuesto'     => $l['id_repuesto'],
                'id_usuario'      => $datos['id_usuario'],
                'tipo_movimiento' => 'salida',
                'cantidad'        => $l['cantidad'],
                'fecha'           => date('Y-m-d H:i:s'),
                'referencia'      => 'VENTA-' . $idVenta,
            ]);
            if ($okMov === false) {
                throw new \Exception(json_encode($movimientoModel->errors()));
            }
        }

        // 3.3 Factura (opcional): solo si mandaron datos del cliente
        if (!empty($datos['factura'])) {
            $f = $datos['factura'];
            $okFactura = $facturaModel->insert([
                'id_venta'       => $idVenta,
                'nit'            => $f['nit'] ?? 'CF',
                'correo'         => $f['correo'] ?? null,
                'nombre_cliente' => $f['nombre_cliente'] ?? 'Consumidor Final',
            ]);
            if ($okFactura === false) {
                throw new \Exception(json_encode($facturaModel->errors()));
            }
        }

        // 4. Todo salió bien: confirmar
        $db->transCommit();

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Venta registrada con éxito',
            'id'      => $idVenta,
            'total'   => $total
        ]);

    } catch (\Throwable $e) {
        // 5. Algo falló: deshacer TODO
        $db->transRollback();

        return $this->respond([
            'status'  => 500,
            'message' => 'No se pudo registrar la venta',
            'detalle' => $e->getMessage()
        ], 500);
    }
}

    // PUT /api/entradas/5 -> actualizar
    
    public function update($id = null)
    {
        $datos = $this->request->getJSON(true);

        if (!is_array($datos) || empty($datos)) {
            return $this->respond([
                'status'  => 400,
                'message' => 'Debes enviar los datos en formato JSON'
            ], 400);
        }

        if (!$this->model->find($id)) {
            return $this->respond([
                'status'  => 404,
                'message' => 'No existe la entrada con ID: ' . $id
            ], 404);
        }

        if (!$this->model->update($id, $datos)) {
            return $this->respond([
                'status'  => 400,
                'message' => 'Datos invalidos',
                'errores' => $this->model->errors()
            ], 400);
        }

        return $this->respond([
            'status'  => 200,
            'message' => 'Entrada actualizada con exito'
        ]);
    }

    // DELETE /api/entradas/5 -> eliminar
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->respond([
                'status'  => 404,
                'message' => 'No existe la entrada con ID: ' . $id
            ], 404);
        }

        $this->model->delete($id);

        return $this->respond([
            'status'  => 200,
            'message' => 'Entrada eliminada con exito'
        ]);
    }
}
