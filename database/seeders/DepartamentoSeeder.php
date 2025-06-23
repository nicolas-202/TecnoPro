<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Departamento;
use App\Models\Pais;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
            Departamento::create([
                'nomDepartamento'   => 'Valle del Cauca',
                'desDepartamento'      => 'Departamento del Valle del Cauca, Colombia',
                'nomeDepartamento'     => 'VAC',
                'estadoDepartamento'   => true,
                'idPais'    => 1,
            ]);
        
    }
}
