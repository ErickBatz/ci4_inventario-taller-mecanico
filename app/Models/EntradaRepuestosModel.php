<?php

namespace App\Models;

use CodeIgniter\Model;

class EntradaRepuestosModel extends Model
{
    protected $table = 'entrada_repuestos';
    protected $primaryKey = 'id_entrada';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_proveedor',
        'id_usuario',
        'fecha_entrada',
        'numero_documento'
    ];

    protected $validationRules = [
        'fecha_entrada' => 'required|valid_date[Y-m-d]',
        'numero_documento' => 'required|max_length[255]',
    ];

    protected $validationMessages = [
        'fecha_entrada' => [
            'required' => 'La fecha de entrada es obligatoria',
            'valid_date' => 'La fecha de entrada debe tener el formato YYYY-MM-DD',
        ],
        'numero_documento' => [
            'required' => 'El numero de documento es obligatorio',
            'max_length' => 'El numero de documento no puede pasar de 255 caracteres',
        ],
    ];
}
