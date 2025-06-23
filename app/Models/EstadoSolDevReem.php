<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoSolDevReem extends Model
{
   
    protected $table = 'estadosoldevreem';
    protected $primaryKey = 'idEstadoSolDevReem';
    protected $fillable = [
        'nomEstadoSolDevReem',
        'desEstadoSolDevReem',
        'nomeEstadoSolDevReem',
        'estadoEstadoSolDevReem',
    ];

    protected $casts = [
        'estadoEstadoSolDevReem' => 'boolean',
    ];

     public function solicitud()
    {
        return $this->hasMany(SolicitudDevolucionReembolso::class, 'idEstadoPedido', 'idEstadoPedido');
    }
}
