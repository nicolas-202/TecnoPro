<?php
namespace App\Http\Controllers;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Pais;

class UbicacionController
{
    public function getDepartamentos($idPais)
    {
        return response()->json(Departamento::where('idPais', $idPais)->get());
    }

    public function getMunicipios($idDepartamento)
    {
        return response()->json(Municipio::where('idDepartamento', $idDepartamento)->get());
    }

    public function index()
    {
        $paises = Pais::with('departamentos.municipios')->get();

        return view('ubicaciones.index', compact('paises'));
    }
}
