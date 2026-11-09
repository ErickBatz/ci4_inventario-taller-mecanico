<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class EntradaRepuestosController extends ResourceController
{
    protected $modelName = 'App\Models\EntradaRepuestosModel';
    protected $format = 'json';

    // GET /api/entradas -> listar todas
    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    // GET /api/entradas/5 -> ver una
    public function show($id = null)
    {
        $entrada = $this->model->find($id);

        if (!$entrada) {
            return $this->respond([
                'status'  => 404,
                'message' => 'No existe la entrada con ID: ' . $id
            ], 404);
        }

        return $this->respond($entrada);
    }

    // POST /api/entradas -> crear  
    public function create()
    {
        $datos = $this->request->getJSON(true);

    // 1. Validar ANTES de tocar la base de datos
    if (empty($datos['detalles']) || !is_array($datos['detalles'])) {
        return $this->respond([
            'status'  => 400,
            'message' => 'La entrada debe traer al menos un repuesto en "detalles"'
        ], 400);
    }

    $repuestosModel   = new \App\Models\RepuestosModel();
    $detalleModel     = new \App\Models\DetalleEntradaModel();
    $movimientoModel  = new \App\Models\MovimientoInventarioModel();

    foreach ($datos['detalles'] as $d) {
        if (empty($d['id_repuesto']) || empty($d['cantidad']) || (int)$d['cantidad'] <= 0) {
            return $this->respond([
                'status'  => 400,
                'message' => 'Cada detalle necesita id_repuesto y una cantidad mayor a 0'
            ], 400);
        }
        if (!$repuestosModel->find($d['id_repuesto'])) {
            return $this->respond([
                'status'  => 404,
                'message' => 'No existe el repuesto con ID: ' . $d['id_repuesto']
            ], 404);
        }
    }

    // 2. Abrir la "caja" (transacción)
    $db = \Config\Database::connect();
    $db->transBegin();

    try {
        // 2.1 Guardar la cabecera
        $idEntrada = $this->model->insert([
            'id_proveedor'     => $datos['id_proveedor'],
            'id_usuario'       => $datos['id_usuario'],
            'fecha_entrada'    => $datos['fecha_entrada'],
            'numero_documento' => $datos['numero_documento'] ?? null,
        ]);

        if ($idEntrada === false) {
            throw new \Exception(json_encode($this->model->errors()));
        }

        // 2.2 Por cada repuesto: detalle + stock + movimiento
        foreach ($datos['detalles'] as $d) {
            $cantidad = (int) $d['cantidad'];

            $okDetalle = $detalleModel->insert([
                'id_entrada'     => $idEntrada,
                'id_repuesto'    => $d['id_repuesto'],
                'cantidad'       => $cantidad,
                'costo_unitario' => $d['costo_unitario'],
            ]);
            if ($okDetalle === false) {
                throw new \Exception(json_encode($detalleModel->errors()));
            }

            // Sumar al stock directamente en SQL
            $db->table('repuestos')
               ->set('stock', 'stock + ' . $cantidad, false)
               ->where('id_repuesto', $d['id_repuesto'])
               ->update();

            $okMov = $movimientoModel->insert([
                'id_repuesto'     => $d['id_repuesto'],
                'id_usuario'      => $datos['id_usuario'],
                'tipo_movimiento' => 'entrada',
                'cantidad'        => $cantidad,
                'fecha'           => date('Y-m-d H:i:s'),
                'referencia'      => $datos['numero_documento'] ?? null,
            ]);
            if ($okMov === false) {
                throw new \Exception(json_encode($movimientoModel->errors()));
            }
        }

        // 3. Todo salió bien: confirmar
        $db->transCommit();

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Entrada registrada con éxito',
            'id'      => $idEntrada
        ]);

    } catch (\Throwable $e) {
        // 4. Algo falló: deshacer TODO
        $db->transRollback();

        return $this->respond([
            'status'  => 500,
            'message' => 'No se pudo registrar la entrada',
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
