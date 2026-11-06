<?php

namespace App\Models;

use CodeIgniter\Model;

class ProveedoresModel extends Model
{
    protected $table = 'proveedor';
    protected $primaryKey = 'id_proveedor';
    protected $returnType = 'array';
    protected $allowedFields = [
        'nombre',
        'direccion',
        'nit',
        'telefono'
    ];

    protected $validationRules = [
        'nombre' => 'required|max_length[100]',
        'direccion' => 'required|max_length[255]',
        'nit' => 'permit_empty|max_length[20]',
        'telefono' => 'permit_empty|max_length[20]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre es obligatorio',
            'max_length' => 'El nombre no puede pasar de 100 caracteres',
        ],
        'direccion' => [
            'required' => 'La direccion es obligatoria',
            'max_length' => 'La direccion no puede pasar de 255 caracteres',
        ],
        'nit' => [
            'max_length' => 'El NIT no puede pasar de 20 caracteres',
        ],
        'telefono' => [
            'max_length' => 'El telefono no puede pasar de 20 caracteres',
        ],
    ];
}
