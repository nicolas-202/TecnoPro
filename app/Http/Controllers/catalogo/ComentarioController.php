<?php

namespace App\Http\Controllers\catalogo;

use App\Models\Comentario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class ComentarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $idProducto)
    {
        $request->validate([
            'contenidoComentario' => 'required|string|max:1000',
        ]);

        $producto = Producto::findOrFail($idProducto);
        if (!$producto->estadoProducto) {
            return redirect()->back()->with('error', 'No se puede comentar en un producto inactivo.');
        }

        $comentario = new Comentario();
        $comentario->contenidoComentario = $request->contenidoComentario;
        $comentario->fechaComentario = now();
        $comentario->estadoComentario = 1;
        $comentario->idProducto = $idProducto;
        $comentario->idUsuario = Auth::user()->user_id;
        $comentario->save();

        return redirect()->route('catalogo.show', $idProducto)->with('success', 'Comentario publicado correctamente.');
    }

    public function update(Request $request, $idProducto, $idComentario)
    {
        $request->validate([
            'contenidoComentario' => 'required|string|max:1000',
        ]);

        $comentario = Comentario::findOrFail($idComentario);
        if ($comentario->idUsuario !== Auth::user()->user_id) {
            return redirect()->back()->with('error', 'No tienes permiso para editar este comentario.');
        }

        $comentario->contenidoComentario = $request->contenidoComentario;
        $comentario->save();

        return redirect()->route('catalogo.show', $idProducto)->with('success', 'Comentario actualizado correctamente.');
    }

    public function destroy($idProducto, $idComentario)
    {
        $comentario = Comentario::findOrFail($idComentario);
        if ($comentario->idUsuario !== Auth::user()->user_id) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar este comentario.');
        }

        $comentario->estadoComentario = 0;
        $comentario->save();

        return redirect()->route('catalogo.show', $idProducto)->with('success', 'Comentario eliminado correctamente.');
    }
}