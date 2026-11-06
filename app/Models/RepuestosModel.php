<?php

namespace App\Models;

use CodeIgniter\Model;

class RepuestosModel extends Model
{
    protected $table = 'repuestos';
    protected $primaryKey = 'id_repuesto';
    protected $returnType = 'array';
    protected $allowedFields = [
        'id_categoria',
        'nombre',
        'precio_venta',
        'stock',
        'stock_minimo',
        'estado'
    ];

    protected $validationRules = [
        'nombre' => 'required|max_length[100]',
        'precio_venta' => 'required|numeric|greater_than_equal_to[0]|regex_match[/^\d{1,8}(\.\d{1,2})?$/]',
        'stock' => 'required|integer|greater_than_equal_to[0]',
        'stock_minimo' => 'required|integer|greater_than_equal_to[0]',
        'estado' => 'required|max_length[20]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre es obligatorio',
            'max_length' => 'El nombre no puede pasar de 100 caracteres',
        ],
        'precio_venta' => [
            'required' => 'El precio de venta es obligatorio',
            'numeric' => 'El precio de venta debe ser un numero',
            'greater_than_equal_to' => 'El precio de venta no puede ser negativo',
            'regex_match' => 'El precio de venta debe tener hasta 8 digitos enteros y 2 decimales',
        ],
        'stock' => [
            'required' => 'El stock es obligatorio',
            'integer' => 'El stock debe ser un numero entero',
            'greater_than_equal_to' => 'El stock no puede ser negativo',
        ],
        'stock_minimo' => [
            'required' => 'El stock minimo es obligatorio',
            'integer' => 'El stock minimo debe ser un numero entero',
            'greater_than_equal_to' => 'El stock minimo no puede ser negativo',
        ],
        'estado' => [
            'required' => 'El estado es obligatorio',
            'max_length' => 'El estado no puede pasar de 20 caracteres',
        ],
    ];
}
