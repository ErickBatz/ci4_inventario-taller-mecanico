<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id_rol';
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
