<?php

namespace App\Http\Controllers\configuracion;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class DepartamentoController extends BaseController
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
        $departamentos = Departamento::with('pais')->orderBy('idDepartamento', 'desc')->take(10)->get();
        $paises = Pais::where('estadoPais', true)->get();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'departamentos' => $departamentos->map(function ($departamento) {
                    return [
                        'idDepartamento' => $departamento->idDepartamento,
                        'nomDepartamento' => htmlspecialchars($departamento->nomDepartamento, ENT_QUOTES, 'UTF-8'),
                        'desDepartamento' => htmlspecialchars($departamento->desDepartamento ?? '', ENT_QUOTES, 'UTF-8'),
                        'nomeDepartamento' => htmlspecialchars($departamento->nomeDepartamento ?? '', ENT_QUOTES, 'UTF-8'),
                        'estadoDepartamento' => $departamento->estadoDepartamento,
                        'idPais' => $departamento->idPais,
                        'nomPais' => htmlspecialchars($departamento->pais->nomPais ?? '', ENT_QUOTES, 'UTF-8'),
                    ];
                })->toArray(),
            ]);
        }
        return view('configuracion.departamento', compact('departamentos', 'paises'));
    }

    public function buscarDepartamento(Request $request)
    {
        Log::info('BuscarDepartamento iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        try {
            $termino = trim($request->termino);

            $departamentos = Departamento::with('pais')
                ->where('nomDepartamento', 'LIKE', '%' . $termino . '%')
                ->orWhere('idDepartamento', 'LIKE', '%' . $termino . '%')
                ->orWhere('nomeDepartamento', 'LIKE', '%' . $termino . '%')
                ->select('idDepartamento', 'nomDepartamento', 'desDepartamento', 'nomeDepartamento', 'estadoDepartamento', 'idPais')
                ->take(10)
                ->get();

            if ($departamentos->isNotEmpty()) {
                Log::info('Departamentos encontrados: ' . $departamentos->toJson());
                return response()->json([
                    'success' => true,
                    'departamentos' => $departamentos->map(function ($departamento) {
                        return [
                            'idDepartamento' => $departamento->idDepartamento,
                            'nomDepartamento' => htmlspecialchars($departamento->nomDepartamento, ENT_QUOTES, 'UTF-8'),
                            'desDepartamento' => htmlspecialchars($departamento->desDepartamento ?? '', ENT_QUOTES, 'UTF-8'),
                            'nomeDepartamento' => htmlspecialchars($departamento->nomeDepartamento ?? '', ENT_QUOTES, 'UTF-8'),
                            'estadoDepartamento' => $departamento->estadoDepartamento,
                            'idPais' => $departamento->idPais,
                            'nomPais' => htmlspecialchars($departamento->pais->nomPais ?? '', ENT_QUOTES, 'UTF-8'),
                        ];
                    })->toArray(),
                ]);
            } else {
                Log::info('No se encontraron departamentos con término: ' . $termino);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron departamentos que coincidan con la búsqueda.',
                    'departamentos' => [],
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscarDepartamento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar departamentos: ' . $e->getMessage(),
                'departamentos' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomDepartamento' => 'required|string|max:255',
            'desDepartamento' => 'nullable|string|max:500',
            'nomeDepartamento' => 'nullable|string|max:255',
            'estadoDepartamento' => 'required|boolean',
            'idPais' => 'required|exists:pais,idPais',
        ]);

        $departamento = new Departamento();
        $departamento->nomDepartamento = $request->nomDepartamento;
        $departamento->desDepartamento = $request->desDepartamento;
        $departamento->nomeDepartamento = $request->nomeDepartamento;
        $departamento->estadoDepartamento = $request->estadoDepartamento;
        $departamento->idPais = $request->idPais;
        $departamento->save();

        return redirect()->route('departamentos.index')->with('success', 'Departamento creado exitosamente.');
    }

    public function update(Request $request, $idDepartamento)
    {
        $departamento = Departamento::findOrFail($idDepartamento);

        $request->validate([
            'nomDepartamento' => 'required|string|max:255',
            'desDepartamento' => 'nullable|string|max:500',
            'nomeDepartamento' => 'nullable|string|max:255',
            'estadoDepartamento' => 'required|boolean',
            'idPais' => 'required|exists:pais,idPais',
        ]);

        $departamento->nomDepartamento = $request->nomDepartamento;
        $departamento->desDepartamento = $request->desDepartamento;
        $departamento->nomeDepartamento = $request->nomeDepartamento;
        $departamento->estadoDepartamento = $request->estadoDepartamento;
        $departamento->idPais = $request->idPais;
        $departamento->save();

        return redirect()->route('departamentos.index')->with('success', 'Departamento actualizado correctamente.');
    }

    public function destroy(Departamento $departamento)
    {
        try {
            $departamento->delete();
            return redirect()->route('departamentos.index')->with('success', 'Departamento eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar departamento: ' . $e->getMessage());
            return redirect()->route('departamentos.index')->with('error', 'Error al eliminar el departamento.');
        }
    }
}