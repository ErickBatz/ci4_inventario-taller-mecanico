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
        'cantidad' => 'required|integer|greater_than[0]',
        'costo_unitario' => 'required|numeric|greater_than_equal_to[0]|regex_match[/^\d{1,8}(\.\d{1,2})?$/]',
    ];

    protected $validationMessages = [
        'cantidad' => [
            'required' => 'La cantidad es obligatoria',
            'integer' => 'La cantidad debe ser un numero entero',
            'greater_than' => 'La cantidad debe ser mayor que 0',
        ],
        'costo_unitario' => [
            'required' => 'El costo unitario es obligatorio',
            'numeric' => 'El costo unitario debe ser un numero',
            'greater_than_equal_to' => 'El costo unitario no puede ser negativo',
            'regex_match' => 'El costo unitario debe tener hasta 8 digitos enteros y 2 decimales',
        ],
    ];
}
