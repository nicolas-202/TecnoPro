<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';
    protected $primaryKey = 'idCategoria';
    public $timestamps = false;

    protected $fillable = [
        'nomCategoria',
        'desCategoria',
        'nomeCategoria',
        'estadoCategoria',
    ];

    protected $casts = [
        'estadoCategoria' => 'boolean',
    ];

    public function productos(){
        return $this->hasMany(Producto::class, 'idCategoria', 'idCategoria');
    }
}