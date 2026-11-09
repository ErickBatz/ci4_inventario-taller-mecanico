<?php

namespace App\Models;

use CodeIgniter\Model;

class Detalle_VentaModel extends Model
{
    protected $table = 'detalle_venta';
    protected $primaryKey = 'id_detalle_venta';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_venta',
        'id_repuesto',
        'cantidad',
        'precio_unitario',
    ];
}
