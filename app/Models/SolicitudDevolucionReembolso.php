<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudDevolucionReembolso extends Model
{
    protected $table = 'soldevreem';
    protected $primaryKey = 'idSolDevReem';
    public $timestamps = false;

    protected $fillable = [
        'fechaSolDevReem',
        'comentarioSolDevReem',
        'respuestaSolDevReem',
        'idEstadoSolDevReem',
        'idPedidoProducto',
    ];

    public function pedidoProducto()
    {
        return $this->belongsTo(PedidoProducto::class, 'idPedidoProducto', 'idPedidoProducto');
    }

    public function estadoSolicitud()
    {
        return $this->belongsTo(EstadoSolDevReem::class, 'idEstadoSolDevReem', 'idEstadoSolDevReem');
    }
}