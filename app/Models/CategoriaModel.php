<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table = 'categoria';
    protected $primaryKey = 'id_categoria';
    protected $returnType = 'array';
    protected $allowedFields = ['nombre'];

    protected $validationRules = [
        'nombre' => 'required|min_length[2]|max_length[60]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre es obligatorio',
            'min_length' => 'El nombre debe tener almenos 2 caracteres',
            'max_length' => 'El nombre no puede pasar de 60 caracteres',
        ],
    ];
}
