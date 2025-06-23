<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Usuario extends Authenticatable implements AuditableContract
{
    use Notifiable, AuditableTrait;

    protected $table = 'usuario';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'nombre',
        'email',
        'celular',
        'fecha_nacimiento',
        'numero_documento',
        'password',
        'direccion',
        'idGenero',
        'idTipoDocumento',
        'idMunicipio',
        'remember_token',
    ];
    protected $casts = [
        'estadoUsuario' => 'boolean',
        'fecha_nacimiento' => 'date',
        'estado' => 'boolean',
        'password' => 'hashed', // Corrección: 'hash' no es válido, usa 'hashed'
    ];
    public $timestamps = true;

    // Mutator para hashear la contraseña automáticamente
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'idMunicipio', 'idMunicipio');
    }
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'idTipoDocumento', 'idTipoDocumento');
    }
    public function genero()
    {
        return $this->belongsTo(Genero::class, 'idGenero', 'idGenero');
    }
    public function empleado()
    {
        return $this->hasOne(Empleado::class, 'user_id', 'user_id');
    }


    public function pedidos(){
        return $this->hasMany(Pedido::class, 'idUsuario', 'user_id');
    }
}