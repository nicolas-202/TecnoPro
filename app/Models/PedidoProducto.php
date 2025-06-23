<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoProducto extends Model
{
    protected $table = 'pedidoproducto';
    protected $primaryKey = 'idPedidoProducto';
    public $timestamps = false;

    protected $fillable = [
        'cantidadProducto',
        'precioProducto',
        'totalProducto',
        'idPedido',
        'idProducto',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'idPedido');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto');
    }

    public function solicitudes(){
        return $this->hasMany(SolicitudDevolucionReembolso::class, 'idPedidoProducto');
    }
}