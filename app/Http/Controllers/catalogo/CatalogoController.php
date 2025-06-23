<?php

namespace App\Http\Controllers\catalogo;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::all();
        $query = Producto::with('categoria')->where('estadoProducto', 1);
        if ($request->has('idCategoria') && $request->idCategoria != '') {
            $query->where('idCategoria', $request->idCategoria);
        }
        $productos = $query->paginate(12);
        return view('catalogo.catalog', compact('productos', 'categorias'));
    }

    public function show(Producto $producto)
    {
        if (!$producto->estadoProducto) {
            abort(404, 'Producto no disponible.');
        }
        $producto->load('categoria');
        $comentarios = Comentario::where('idProducto', $producto->idProducto)
            ->where('estadoComentario', 1)
            ->with('user')
            ->orderByRaw('idUsuario = ? DESC', [Auth::check() ? Auth::user()->user_id : 0])
            ->orderBy('fechaComentario', 'desc')
            ->take(10)
            ->get();
        return view('catalogo.producto_detalle', compact('producto', 'comentarios'));
    }
}
