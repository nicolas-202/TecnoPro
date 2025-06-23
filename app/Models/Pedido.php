<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedido';
    protected $primaryKey = 'idPedido';
    public $timestamps = true;

    protected $fillable = [
        'fechaPedido',
        'totalPedido',
        'informacionPedido',
        'idEstadoPedido',
        'idUsuario',
        'idFormaPago'
    ];

    protected $casts = [
    'fechaPedido' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }

    public function estadoPedido()
    {
        return $this->belongsTo(EstadoPedido::class, 'idEstadoPedido');
    }

    public function productos()
    {
        return $this->hasMany(PedidoProducto::class, 'idPedido');
    }

    public function FormaPago(){
        return $this->belongsTo(FormaPago::class, 'idFormaPago');
    }
}