<?php

namespace App\Models;

use CodeIgniter\Model;

class FacturasModel extends Model
{
    protected $table = 'facturas';
    protected $primaryKey = 'id_factura';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_venta',
        'nit',
        'correo',
        'nombre_cliente',
    ];
}
