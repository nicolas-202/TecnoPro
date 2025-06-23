<?php

namespace App\Http\Controllers\configuracion;

use App\Http\Controllers\Controller;
use App\Models\FormaPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class FormaPagoController extends BaseController
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
        $formasPago = FormaPago::orderBy('idFormaPago', 'desc')->take(10)->get();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'formasPago' => $formasPago->map(function ($formaPago) {
                    return [
                        'idFormaPago' => $formaPago->idFormaPago,
                        'nomFormaPago' => htmlspecialchars($formaPago->nomFormaPago, ENT_QUOTES, 'UTF-8'),
                        'desFormaPago' => htmlspecialchars($formaPago->desFormaPago ?? '', ENT_QUOTES, 'UTF-8'),
                        'nomeFormaPago' => htmlspecialchars($formaPago->nomeFormaPago ?? '', ENT_QUOTES, 'UTF-8'),
                        'estadoFormaPago' => $formaPago->estadoFormaPago,
                    ];
                })->toArray(),
            ]);
        }
        return view('configuracion.forma-pago', compact('formasPago'));
    }

    public function buscarFormaPago(Request $request)
    {
        Log::info('BuscarFormaPago iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        try {
            $termino = trim($request->termino);

            $formasPago = FormaPago::where('nomFormaPago', 'LIKE', '%' . $termino . '%')
                ->orWhere('idFormaPago', 'LIKE', '%' . $termino . '%')
                ->orWhere('nomeFormaPago', 'LIKE', '%' . $termino . '%')
                ->select('idFormaPago', 'nomFormaPago', 'desFormaPago', 'nomeFormaPago', 'estadoFormaPago')
                ->take(10)
                ->get();

            if ($formasPago->isNotEmpty()) {
                Log::info('Formas de pago encontradas: ' . $formasPago->toJson());
                return response()->json([
                    'success' => true,
                    'formasPago' => $formasPago->map(function ($formaPago) {
                        return [
                            'idFormaPago' => $formaPago->idFormaPago,
                            'nomFormaPago' => htmlspecialchars($formaPago->nomFormaPago, ENT_QUOTES, 'UTF-8'),
                            'desFormaPago' => htmlspecialchars($formaPago->desFormaPago ?? '', ENT_QUOTES, 'UTF-8'),
                            'nomeFormaPago' => htmlspecialchars($formaPago->nomeFormaPago ?? '', ENT_QUOTES, 'UTF-8'),
                            'estadoFormaPago' => $formaPago->estadoFormaPago,
                        ];
                    })->toArray(),
                ]);
            } else {
                Log::info('No se encontraron formas de pago con término: ' . $termino);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron formas de pago que coincidan con la búsqueda.',
                    'formasPago' => [],
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscarFormaPago: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar formas de pago: ' . $e->getMessage(),
                'formasPago' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomFormaPago' => 'required|string|max:255',
            'desFormaPago' => 'nullable|string|max:500',
            'nomeFormaPago' => 'nullable|string|max:255',
            'estadoFormaPago' => 'required|boolean',
        ]);

        try {
            $formaPago = new FormaPago();
            $formaPago->nomFormaPago = $request->nomFormaPago;
            $formaPago->desFormaPago = $request->desFormaPago;
            $formaPago->nomeFormaPago = $request->nomeFormaPago;
            $formaPago->estadoFormaPago = $request->estadoFormaPago;
            $formaPago->save();

            Log::info('Forma de pago creada:', ['idFormaPago' => $formaPago->idFormaPago]);

            return redirect()->route('formas-pago.index')->with('success', 'Forma de pago creada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear forma de pago: ' . $e->getMessage());
            return redirect()->route('formas-pago.index')->with('error', 'Error al crear la forma de pago.');
        }
    }

    public function update(Request $request, $idFormaPago)
    {
        $formaPago = FormaPago::findOrFail($idFormaPago);

        $request->validate([
            'nomFormaPago' => 'required|string|max:255',
            'desFormaPago' => 'nullable|string|max:500',
            'nomeFormaPago' => 'nullable|string|max:255',
            'estadoFormaPago' => 'required|boolean',
        ]);

        try {
            $formaPago->nomFormaPago = $request->nomFormaPago;
            $formaPago->desFormaPago = $request->desFormaPago;
            $formaPago->nomeFormaPago = $request->nomeFormaPago;
            $formaPago->estadoFormaPago = $request->estadoFormaPago;
            $formaPago->save();

            Log::info('Forma de pago actualizada:', ['idFormaPago' => $idFormaPago]);

            return redirect()->route('formas-pago.index')->with('success', 'Forma de pago actualizada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar forma de pago: ' . $e->getMessage());
            return redirect()->route('formas-pago.index')->with('error', 'Error al actualizar la forma de pago.');
        }
    }

    public function destroy(FormaPago $formaPago)
    {
        try {
            if ($formaPago->pedidos()->exists()) {
                Log::warning('Intento de eliminar forma de pago con pedidos asociados:', ['idFormaPago' => $formaPago->idFormaPago]);
                return redirect()->route('formas-pago.index')->with('error', 'No se puede eliminar la forma de pago porque tiene pedidos asociados.');
            }

            $idFormaPago = $formaPago->idFormaPago;
            $formaPago->delete();

            Log::info('Forma de pago eliminada:', ['idFormaPago' => $idFormaPago]);

            return redirect()->route('formas-pago.index')->with('success', 'Forma de pago eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar forma de pago: ' . $e->getMessage());
            return redirect()->route('formas-pago.index')->with('error', 'Error al eliminar la forma de pago.');
        }
    }
}
