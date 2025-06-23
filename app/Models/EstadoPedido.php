<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoPedido extends Model
{
     protected $table = 'estadopedido';
    protected $primaryKey = 'idEstadoPedido';
    public $fillable = [
        'nomEstadoPedido',
        'desEstadoPedido',
        'nomeEstadoPedido',
        'estadoEstadoPedido',
    ];
    protected $casts = [
        'estadoEstadoPedido' => 'boolean',
    ];
    public $timestamps = true;

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'idEstadoPedido', 'idEstadoPedido');
    }
}
