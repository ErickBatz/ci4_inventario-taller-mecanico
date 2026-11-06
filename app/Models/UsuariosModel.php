<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosModel extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    protected $returnType = 'array';
    protected $allowedFields = [
        'id_rol',
        'usuario',
        'correo_electronico',
        'contrasenia_hash'
    ];

    protected $validationRules = [
        'usuario' => 'required|min_length[2]|max_length[60]',

    ];

    protected $validationMessages = [
        'usuario' => [
            'required' => 'El nombre es obligatorio',
            'min_length' => 'El nombre debe tener almenos 2 caracteres',
            'max_length' => 'El nombre no puede pasar de 60 caracteres',
        ],
    ];
}
