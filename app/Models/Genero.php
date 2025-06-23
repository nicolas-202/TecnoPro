<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    protected $table = 'genero';
    protected $primaryKey = 'idGenero';
    public $fillable = [
        'nomGenero',
        'desGenero',
        'nomeGenero',
        'estadoGenero',
    ];
    protected $casts = [
        'estadoGenero' => 'boolean',
    ];
    public $timestamps = true;

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idGenero', 'idGenero');
    }
}
