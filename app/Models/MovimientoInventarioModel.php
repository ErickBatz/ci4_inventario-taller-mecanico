<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientoInventarioModel extends Model
{
    protected $table = 'movimiento_inventario';
    protected $primaryKey = 'id_movimiento';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_repuesto',
        'id_usuario',
        'tipo_movimiento',
        'cantidad',
        'fecha',
        'referencia'

    ];

    protected $validationRules = [
        'tipo_movimiento' => 'required|in_list[entrada,salida, ajuste]',
        'cantidad' => 'required|integer|greater_than[0]',
        'fecha' => 'required|valid_date[Y-m-d H:i:s]',
        'referencia' => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'tipo_movimiento' => [
            'required' => 'El tipo de movimiento es obligatorio',
            'in_list' => 'El tipo de movimiento debe ser entrada,salida o ajuste',
        ],
        'cantidad' => [
            'required' => 'La cantidad es obligatoria',
            'integer' => 'La cantidad debe ser un número entero',
            'greater_than' => 'La cantidad debe ser mayor que 0',
        ],
        'fecha' => [
            'required' => 'La fecha es obligatoria',
            'valid_date' => 'La fecha debe tener el formato año-mes-día hora:minuto:segundo',
        ],
        'referencia' => [
            'max_length' => 'La referencia no puede pasar de 255 caracteres',
        ],
    ];
}
