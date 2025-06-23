<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargo';
    protected $primaryKey = 'idCargo';
    public $fillable = [
        'nomCargo',
        'desCargo',
        'nomeCargo',
        'estadoCargo',
    ];
    public $timestamps = false;
    protected $casts = [
        'estadoCargo' => 'boolean',
    ];
    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'idCargo', 'idCargo');
    }
}
