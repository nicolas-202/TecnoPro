<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'pais';
    protected $primaryKey = 'idPais';
    public $fillable = [
        'nomPais',
        'desPais',
        'nomePais',
        'estadoPais',
    ];
    protected $casts = [
        'estadoPais' => 'boolean',
    ];
    public $timestamps = true;

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'idPais', 'idPais');
    }
}
