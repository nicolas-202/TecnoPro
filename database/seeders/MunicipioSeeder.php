<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Municipio;
use App\Models\Departamento;

class MunicipioSeeder extends Seeder
{
    public function run(): void
    {
     

     
            Municipio::create([
                'nomMunicipio'      => 'El Dovio',
                'desMunicipio'      => 'Municipio de El Dovio, Valle del Cauca',
                'nomeMunicipio'     => 'EDV',
                'estadoMunicipio'   => true,
                'idDepartamento'    => 1,
            ]);
        
    }
}
