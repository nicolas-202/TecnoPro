<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'idProducto';
    protected $fillable = [
        'nomProducto',
        'desProducto',
        'stockMinimo',
        'stockMaximo',
        'cantidadExistente',
        'precioVenta',
        'imagen',
        'estadoProducto',
        'idCategoria',
    ];

    protected $casts = [
        'estadoProducto' => 'boolean',
        'stockMinimo' => 'integer',
        'stockMaximo' => 'integer',
        'cantidadExistente' => 'integer',
        'precioVenta' => 'float',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idCategoria','idCategoria');
    }

    public function kardex()
    {
        return $this->hasMany(Kardex::class,'idProducto','idProducto');
    }
}