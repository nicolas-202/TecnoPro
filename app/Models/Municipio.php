<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'municipio';
    protected $primaryKey = 'idMunicipio';
    public $fillable = [
        'nomMunicipio',
        'desMunicipio',
        'nomeMunicipio',
        'estadoMunicipio',
        'idDepartamento',
    ];
    public $timestamps = true;

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'idDepartamento', 'idDepartamento');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idMunicipio', 'idMunicipio');
    }
}
