<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->group('api', function($routes){
    $routes->resource('categoria',['controller'=>'CategoriaController']);
    $routes->resource('roles',['controller'=>'RolesController']);
    $routes->resource('usuarios',['controller'=>'UsuariosController']);
    $routes->resource('proveedores',['controller'=>'ProveedoresController']);
    $routes->resource('repuestos',['controller'=>'RepuestosController']);
    $routes->resource('entradas',['controller'=>'EntradaRepuestosController']);
    $routes->resource('detalles-entrada',['controller'=>'DetalleEntradaController']);
    $routes->resource('movimientos-inventario',['controller'=>'MovimientoInventarioController']);
});
