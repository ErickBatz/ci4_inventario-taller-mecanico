<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleEntradaModel extends Model
{
    protected $table = 'detalle_entrada';
    protected $primaryKey = 'id_detalle';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_entrada',
        'id_repuesto',
        'cantidad',
        'costo_unitario'
    ];

    protected $validationRules = [
        'cantidad' => 'required|integer|greater_than[0]'
    ];

    protected $validationMessages = [
        'cantidad' => [
            'required' => 'La cantidad es obligatoria',
            'integer' => 'La cantidad debe ser un numero entero',
            'greater_than' => 'La cantidad debe ser mayor que 0',
        ]
    ];
}
