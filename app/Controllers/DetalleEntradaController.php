<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class DetalleEntradaController extends ResourceController
{
    protected $modelName = 'App\\Models\\DetalleEntradaModel';
    protected $format = 'json';

    // GET /api/detalles-entrada -> listar todos
    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    // GET /api/detalles-entrada/5 -> ver uno
    public function show($id = null)
    {
        $detalle = $this->model->find($id);

        if (!$detalle) {
            return $this->respond([
                'status'  => 404,
                'message' => 'No existe el detalle de entrada con ID: ' . $id
            ], 404);
        }

        return $this->respond($detalle);
    }

    // POST /api/detalles-entrada -> crear
    public function create()
    {
        $datos = $this->request->getJSON(true);

        if (!is_array($datos) || empty($datos)) {
            return $this->respond([
                'status'  => 400,
                'message' => 'Debes enviar los datos en formato JSON'
            ], 400);
        }

        $id = $this->model->insert($datos);

        if ($id === false) {
            return $this->respond([
                'status'  => 400,
                'message' => 'Datos invalidos',
                'errores' => $this->model->errors()
            ], 400);
        }

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Detalle de entrada creado con exito',
            'id'      => $id
        ]);
    }

    // PUT /api/detalles-entrada/5 -> actualizar
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
                'message' => 'No existe el detalle de entrada con ID: ' . $id
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
            'message' => 'Detalle de entrada actualizado con exito'
        ]);
    }

    // DELETE /api/detalles-entrada/5 -> eliminar
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->respond([
                'status'  => 404,
                'message' => 'No existe el detalle de entrada con ID: ' . $id
            ], 404);
        }

        $this->model->delete($id);

        return $this->respond([
            'status'  => 200,
            'message' => 'Detalle de entrada eliminado con exito'
        ]);
    }
}
