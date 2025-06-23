<?php

namespace App\Http\Controllers\configuracion;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class MunicipioController extends BaseController
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
        $municipios = Municipio::with('departamento')->orderBy('idMunicipio', 'desc')->take(10)->get();
        $departamentos = Departamento::where('estadoDepartamento', true)->get();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'municipios' => $municipios->map(function ($municipio) {
                    return [
                        'idMunicipio' => $municipio->idMunicipio,
                        'nomMunicipio' => htmlspecialchars($municipio->nomMunicipio, ENT_QUOTES, 'UTF-8'),
                        'desMunicipio' => htmlspecialchars($municipio->desMunicipio ?? '', ENT_QUOTES, 'UTF-8'),
                        'nomeMunicipio' => htmlspecialchars($municipio->nomeMunicipio ?? '', ENT_QUOTES, 'UTF-8'),
                        'estadoMunicipio' => $municipio->estadoMunicipio,
                        'idDepartamento' => $municipio->idDepartamento,
                        'nomDepartamento' => htmlspecialchars($municipio->departamento->nomDepartamento ?? '', ENT_QUOTES, 'UTF-8'),
                    ];
                })->toArray(),
            ]);
        }
        return view('configuracion.municipio', compact('municipios', 'departamentos'));
    }

    public function buscarMunicipio(Request $request)
    {
        Log::info('BuscarMunicipio iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        try {
            $termino = trim($request->termino);

            $municipios = Municipio::with('departamento')
                ->where('nomMunicipio', 'LIKE', '%' . $termino . '%')
                ->orWhere('idMunicipio', 'LIKE', '%' . $termino . '%')
                ->orWhere('nomeMunicipio', 'LIKE', '%' . $termino . '%')
                ->select('idMunicipio', 'nomMunicipio', 'desMunicipio', 'nomeMunicipio', 'estadoMunicipio', 'idDepartamento')
                ->take(10)
                ->get();

            if ($municipios->isNotEmpty()) {
                Log::info('Municipios encontrados: ' . $municipios->toJson());
                return response()->json([
                    'success' => true,
                    'municipios' => $municipios->map(function ($municipio) {
                        return [
                            'idMunicipio' => $municipio->idMunicipio,
                            'nomMunicipio' => htmlspecialchars($municipio->nomMunicipio, ENT_QUOTES, 'UTF-8'),
                            'desMunicipio' => htmlspecialchars($municipio->desMunicipio ?? '', ENT_QUOTES, 'UTF-8'),
                            'nomeMunicipio' => htmlspecialchars($municipio->nomeMunicipio ?? '', ENT_QUOTES, 'UTF-8'),
                            'estadoMunicipio' => $municipio->estadoMunicipio,
                            'idDepartamento' => $municipio->idDepartamento,
                            'nomDepartamento' => htmlspecialchars($municipio->departamento->nomDepartamento ?? '', ENT_QUOTES, 'UTF-8'),
                        ];
                    })->toArray(),
                ]);
            } else {
                Log::info('No se encontraron municipios con término: ' . $termino);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron municipios que coincidan con la búsqueda.',
                    'municipios' => [],
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscarMunicipio: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar municipios: ' . $e->getMessage(),
                'municipios' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomMunicipio' => 'required|string|max:255',
            'desMunicipio' => 'nullable|string|max:500',
            'nomeMunicipio' => 'nullable|string|max:255',
            'estadoMunicipio' => 'required|boolean',
            'idDepartamento' => 'required|exists:departamento,idDepartamento',
        ]);

        $municipio = new Municipio();
        $municipio->nomMunicipio = $request->nomMunicipio;
        $municipio->desMunicipio = $request->desMunicipio;
        $municipio->nomeMunicipio = $request->nomeMunicipio;
        $municipio->estadoMunicipio = $request->estadoMunicipio;
        $municipio->idDepartamento = $request->idDepartamento;
        $municipio->save();

        return redirect()->route('municipios.index')->with('success', 'Municipio creado exitosamente.');
    }

    public function update(Request $request, $idMunicipio)
    {
        $municipio = Municipio::findOrFail($idMunicipio);

        $request->validate([
            'nomMunicipio' => 'required|string|max:255',
            'desMunicipio' => 'nullable|string|max:500',
            'nomeMunicipio' => 'nullable|string|max:255',
            'estadoMunicipio' => 'required|boolean',
            'idDepartamento' => 'required|exists:departamento,idDepartamento',
        ]);

        $municipio->nomMunicipio = $request->nomMunicipio;
        $municipio->desMunicipio = $request->desMunicipio;
        $municipio->nomeMunicipio = $request->nomeMunicipio;
        $municipio->estadoMunicipio = $request->estadoMunicipio;
        $municipio->idDepartamento = $request->idDepartamento;
        $municipio->save();

        return redirect()->route('municipios.index')->with('success', 'Municipio actualizado correctamente.');
    }

    public function destroy(Municipio $municipio)
    {
        try {
            $municipio->delete();
            return redirect()->route('municipios.index')->with('success', 'Municipio eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar municipio: ' . $e->getMessage());
            return redirect()->route('municipios.index')->with('error', 'Error al eliminar el municipio.');
        }
    }
}
