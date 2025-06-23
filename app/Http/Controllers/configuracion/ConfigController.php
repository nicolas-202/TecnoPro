<?php

namespace App\Http\Controllers\configuracion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class ConfigController extends BaseController
{

     private function authorizeEmployee()
    {
        $user = Auth::user();
        if (!$user || ($user->empleado && !$user->empleado->estadoEmpleado) || !$user->empleado) {
            abort(403, 'Acceso no autorizado. Solo empleados pueden acceder a esta página.');
        }
    }

     public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->authorizeEmployee();
            return $next($request);
        });
    }

    public function index()
    {
        return view('configuracion.config');
    }

    public function empleados()
    {
        return route('configuracion.index');
    }

    public function proveedores()
    {
        return view('configuracion.config-proveedores');
    }

    public function categorias()
    {
        return view('configuracion.categoria');
    }

    public function cargo()
    {
        return view('configuracion.cargo');
    }

    public function tiposDocumento()
    {
        return view('configuracion.tipo_documento');
    }

    public function generadorPedidos()
    {
        return view('configuracion.gestionProductosSolicitudes');
    }

    public function estado()
    {
        return view('configuracion.estados');
    }

    public function productos()
    {
        return view('configuracion.config-productos');
    }

    public function departamentos()
    {
        return view('configuracion.departamento');
    }

    public function pais()
    {
        return view('configuracion.pais');
    }

    public function municipio()
    {
        return view('configuracion.config-municipio');
    }

    public function genero()
    {
        return view('configuracion.config-genero');
    }

    /**
     * Método auxiliar para autorizar acceso solo a empleados.
     */
   
} 