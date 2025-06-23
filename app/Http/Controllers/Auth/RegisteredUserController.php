<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Autenticacion;
use App\Models\Auditoria;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    public function create()
    {
        $generos = \App\Models\Genero::all();
        $tiposDocumento = \App\Models\TipoDocumento::all();
        $municipios = \App\Models\Municipio::all();
        $paises = \App\Models\Pais::all();
        $departamentos = \App\Models\Departamento::all();
        return view('auth.register', compact('generos', 'tiposDocumento', 'municipios', 'paises', 'departamentos'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            Log::info('Datos recibidos del formulario:', $request->all());

            $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuario,email'],
            'celular' => ['required', 'string', 'max:20'],
            'fecha_nacimiento' => ['required', 'date'],
            'numero_documento' => ['required', 'string', 'max:20', 'unique:usuario,numero_documento'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'direccion' => ['required', 'string', 'max:255'],
            'idGenero' => ['required', 'exists:genero,idGenero'],
            'idTipoDocumento' => ['required', 'exists:tipoDocumento,idTipoDocumento'],
            'idMunicipio' => ['required', 'exists:municipio,idMunicipio'],
            'idPais' => ['required', 'exists:pais,idPais'],
            'idDepartamento' => ['required', 'exists:departamento,idDepartamento'],
            ]);

            Log::info('Validación pasada, creando usuario con datos:', $request->only([
            'nombre', 'email', 'celular', 'fecha_nacimiento', 'numero_documento', 'direccion',
            'idGenero', 'idTipoDocumento', 'idMunicipio', 'idPais', 'idDepartamento'
            ]));

            $user = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'celular' => $request->celular,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'numero_documento' => $request->numero_documento,
            'password' => Hash::make($request->password),
            'direccion' => $request->direccion,
            'idGenero' => $request->idGenero,
            'idTipoDocumento' => $request->idTipoDocumento,
            'idMunicipio' => $request->idMunicipio,
            'idPais' => $request->idPais,
            'idDepartamento' => $request->idDepartamento,
            ]);

            Log::info('Usuario creado con ID:', ['user_id' => $user->user_id]);

            event(new Registered($user));

            Auth::login($user);

            return redirect()->intended(route('home', absolute: false));
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validación falló: ' . $e->getMessage(), $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error al procesar registro: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al registrar. Verifica los datos o contacta al administrador.'])->withInput();
        }
    }
}