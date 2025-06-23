<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipodocumento';
    protected $primaryKey = 'idTipoDocumento';
    public $fillable = [
        'nomTipoDocumento',
        'desTipoDocumento',
        'nomeTipoDocumento',
        'estadoTipoDocumento',
    ];
    protected $casts = [
        'estadoTipoDocumento' => 'boolean',
    ];
    public $timestamps = true;

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idTipoDocumento', 'idTipoDocumento');
    }
}
