<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento';
    protected $primaryKey = 'idDepartamento';
    public $fillable = [
        'nomDepartamento',
        'desDepartamento',
        'nomeDepartamento',
        'estadoDepartamento',
        'idPais',
    ];
    protected $casts = [
        'estadoDepartamento' => 'boolean',
    ];
    public $timestamps = true;

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'idPais', 'idPais');
    }
    public function municipios(){
        return $this->hasMany(Municipio::class, 'idDepartamento', 'idDepartamento');
    }
}
