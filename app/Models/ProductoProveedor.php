<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoProveedor extends Model
{
    protected $table = 'productoProveedor';
    protected $primaryKey = 'idProductoProveedor';
    public $timestamps = false;

    protected $fillable = [
        'fechaRegistro',
        'precioUnitario',
        'cantidad',
        'precioTotal',
        'idProducto',
        'idProveedor',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'idProveedor');
    }
}