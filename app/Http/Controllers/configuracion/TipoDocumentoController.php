<?php

namespace App\Http\Controllers\configuracion;

use App\Http\Controllers\Controller;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class TipoDocumentoController extends BaseController
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

    public function index(Request $request)
    {
        $tiposDocumento = TipoDocumento::orderBy('idTipoDocumento', 'desc')->take(10)->get();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'tiposDocumento' => $tiposDocumento->map(function ($tipoDocumento) {
                    return [
                        'idTipoDocumento' => $tipoDocumento->idTipoDocumento,
                        'nomTipoDocumento' => htmlspecialchars($tipoDocumento->nomTipoDocumento, ENT_QUOTES, 'UTF-8'),
                        'desTipoDocumento' => htmlspecialchars($tipoDocumento->desTipoDocumento ?? '', ENT_QUOTES, 'UTF-8'),
                        'nomeTipoDocumento' => htmlspecialchars($tipoDocumento->nomeTipoDocumento ?? '', ENT_QUOTES, 'UTF-8'),
                        'estadoTipoDocumento' => $tipoDocumento->estadoTipoDocumento,
                    ];
                })->toArray(),
            ]);
        }
        return view('configuracion.tipo_documento', compact('tiposDocumento'));
    }

    public function buscarTipoDocumento(Request $request)
    {
        Log::info('BuscarTipoDocumento iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        try {
            $termino = trim($request->termino);

            $tiposDocumento = TipoDocumento::where('nomTipoDocumento', 'LIKE', '%' . $termino . '%')
                ->orWhere('idTipoDocumento', 'LIKE', '%' . $termino . '%')
                ->orWhere('nomeTipoDocumento', 'LIKE', '%' . $termino . '%')
                ->select('idTipoDocumento', 'nomTipoDocumento', 'desTipoDocumento', 'nomeTipoDocumento', 'estadoTipoDocumento')
                ->take(10)
                ->get();

            if ($tiposDocumento->isNotEmpty()) {
                Log::info('Tipos de documento encontrados: ' . $tiposDocumento->toJson());
                return response()->json([
                    'success' => true,
                    'tiposDocumento' => $tiposDocumento->map(function ($tipoDocumento) {
                        return [
                            'idTipoDocumento' => $tipoDocumento->idTipoDocumento,
                            'nomTipoDocumento' => htmlspecialchars($tipoDocumento->nomTipoDocumento, ENT_QUOTES, 'UTF-8'),
                            'desTipoDocumento' => htmlspecialchars($tipoDocumento->desTipoDocumento ?? '', ENT_QUOTES, 'UTF-8'),
                            'nomeTipoDocumento' => htmlspecialchars($tipoDocumento->nomeTipoDocumento ?? '', ENT_QUOTES, 'UTF-8'),
                            'estadoTipoDocumento' => $tipoDocumento->estadoTipoDocumento,
                        ];
                    })->toArray(),
                ]);
            } else {
                Log::info('No se encontraron tipos de documento con término: ' . $termino);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron tipos de documento que coincidan con la búsqueda.',
                    'tiposDocumento' => [],
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscarTipoDocumento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar tipos de documento: ' . $e->getMessage(),
                'tiposDocumento' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomTipoDocumento' => 'required|string|max:255',
            'desTipoDocumento' => 'nullable|string|max:500',
            'nomeTipoDocumento' => 'nullable|string|max:255',
            'estadoTipoDocumento' => 'required|boolean',
        ]);

        $tipoDocumento = new TipoDocumento();
        $tipoDocumento->nomTipoDocumento = $request->nomTipoDocumento;
        $tipoDocumento->desTipoDocumento = $request->desTipoDocumento;
        $tipoDocumento->nomeTipoDocumento = $request->nomeTipoDocumento;
        $tipoDocumento->estadoTipoDocumento = $request->estadoTipoDocumento;
        $tipoDocumento->save();

        return redirect()->route('tipos_documento.index')->with('success', 'Tipo de documento creado exitosamente.');
    }

    public function update(Request $request, $idTipoDocumento)
    {
        $tipoDocumento = TipoDocumento::findOrFail($idTipoDocumento);

        $request->validate([
            'nomTipoDocumento' => 'required|string|max:255',
            'desTipoDocumento' => 'nullable|string|max:500',
            'nomeTipoDocumento' => 'nullable|string|max:255',
            'estadoTipoDocumento' => 'required|boolean',
        ]);

        $tipoDocumento->nomTipoDocumento = $request->nomTipoDocumento;
        $tipoDocumento->desTipoDocumento = $request->desTipoDocumento;
        $tipoDocumento->nomeTipoDocumento = $request->nomeTipoDocumento;
        $tipoDocumento->estadoTipoDocumento = $request->estadoTipoDocumento;
        $tipoDocumento->save();

        return redirect()->route('tipos_documento.index')->with('success', 'Tipo de documento actualizado correctamente.');
    }

    public function destroy(TipoDocumento $tipoDocumento)
    {
        try {
            $tipoDocumento->delete();
            return redirect()->route('tipos_documento.index')->with('success', 'Tipo de documento eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar tipo de documento: ' . $e->getMessage());
            return redirect()->route('tipos_documento.index')->with('error', 'Error al eliminar el tipo de documento.');
        }
    }
}