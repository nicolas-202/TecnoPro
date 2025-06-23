<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'comentario';
    protected $primaryKey = 'idComentario';
    protected $fillable = [
        'contenidoComentario',
        'fechaComentario',
        'estadoComentario',
        'idProducto',
        'idUsuario',
    ];

    protected $dates = ['fechaComentario'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto', 'idProducto');
    }

    public function user()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'user_id');
    }
}