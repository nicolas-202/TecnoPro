<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormaPago extends Model
{
    protected $table = 'formapago';
    protected $primaryKey = 'idFormaPago';
    public $fillable = [
        'nomFormaPago',
        'desFormaPago',
        'nomeFormaPago',
        'estadoFormaPago',
    ];
    protected $casts = [
        'estadoFormaPago' => 'boolean',
    ];
    public $timestamps = true;

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'idFormaPago', 'idFormaPago');
    }
}
