<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleado';
    protected $primaryKey = 'idEmpleado';
    public $fillable = [
        'fecIngreso',
        'imagen',
        'idCargo',
        'user_id',
        'estadoEmpleado',
    ];
    public $timestamps = true;

    protected $casts = [
        'estadoEmpleado' => 'boolean',
    ];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'idCargo', 'idCargo');
    }
    public function user()
    {
        return $this->belongsTo(Usuario::class, 'user_id', 'user_id');
    }
}