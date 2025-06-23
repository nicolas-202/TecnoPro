<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    protected $table = 'kardex';
    protected $primaryKey = 'idKardex';

    protected $fillable = [
        'tipoMovimiento',
        'cantidadMovimiento',
        'fechaMovimiento',
        'costoUnitario',
        'costoTotal',
        'precioVentaActualizado',
        'idProducto',
    ];
    protected $casts = [
        'tipoMovimiento' => 'string',
        'cantidadMovimiento' => 'integer',
        'fechaMovimiento' => 'date',
        'costoUnitario' => 'float',
        'costoTotal' => 'float',
        'precioVentaActualizado' => 'float',
        'idProducto' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto', 'idProducto');
    }
}