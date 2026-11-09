<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class CategoriaController extends ResourceController
{
    protected $modelName = 'App\Models\CategoriaModel';
    protected $format = 'json';

    //GET/api/categoria ->listar todas
    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    //GET/api/categoria/5 -> VER UNA 
    public function show($id = null)
    {
        $categoria = $this->model->find($id);
        if (!$categoria) {
            return $this->respond([
                'status' => 404,
                'message' => 'no existe la categoria con ID: ' . $id
            ], 404);
        }
        return $this->respond($categoria);
    }

    // POST /api/categoria  →  crear
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
                'message' => 'Datos inválidos',
                'errores' => $this->model->errors()
            ], 400);
        }

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Categoría creada con éxito',
            'id'      => $id
        ]);
    }

    // PUT /api/categoria/5  →  actualizar
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
                'message' => 'No existe la categoría con ID: ' . $id
            ], 404);
        }

        if (!$this->model->update($id, $datos)) {
            return $this->respond([
                'status'  => 400,
                'message' => 'Datos inválidos',
                'errores' => $this->model->errors()
            ], 400);
        }

        return $this->respond([
            'status'  => 200,
            'message' => 'Categoría actualizada con éxito'
        ]);
    }

    // DELETE /api/categoria/5  →  eliminar
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->respond([
                'status'  => 404,
                'message' => 'No existe la categoría con ID: ' . $id
            ], 404);
        }

        $this->model->delete($id);

        return $this->respond([
            'status'  => 200,
            'message' => 'Categoría eliminada con éxito'
        ]);
    }
}
