<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class RepuestosController extends ResourceController
{
    protected $modelName = 'App\\Models\\RepuestosModel';
    protected $format = 'json';

    // GET /api/repuestos -> listar todos
    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    // GET /api/repuestos/5 -> ver uno
    public function show($id = null)
    {
        $repuesto = $this->model->find($id);

        if (!$repuesto) {
            return $this->respond([
                'status'  => 404,
                'message' => 'No existe el repuesto con ID: ' . $id
            ], 404);
        }

        return $this->respond($repuesto);
    }

    // POST /api/repuestos -> crear
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
            'message' => 'Repuesto creado con exito',
            'id'      => $id
        ]);
    }

    // PUT /api/repuestos/5 -> actualizar
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
                'message' => 'No existe el repuesto con ID: ' . $id
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
            'message' => 'Repuesto actualizado con exito'
        ]);
    }

    // DELETE /api/repuestos/5 -> eliminar
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->respond([
                'status'  => 404,
                'message' => 'No existe el repuesto con ID: ' . $id
            ], 404);
        }

        $this->model->delete($id);

        return $this->respond([
            'status'  => 200,
            'message' => 'Repuesto eliminado con exito'
        ]);
    }
}
