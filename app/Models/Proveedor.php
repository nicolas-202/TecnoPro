<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    protected $primaryKey = 'idProveedor';
    protected $fillable = [
        'nomProveedor',
        'desProveedor',
        'telProveedor',
        'emailProveedor',
        'nitProveedor',
        'estadoProveedor',
    ];

    protected $casts = [
        'estadoProveedor' => 'boolean',
    ];

    public function ProductoProveedor(){
        return $this->HasMany(ProductoProveedor::class, 'idProveedor', 'idProveedor');
    }
}