<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadoSolDevReem;

class EstadoSolDevReemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EstadoSolDevReem::create([
            'nomEstadoSolDevReem'     => 'Procesando',
            'desEstadoSolDevReem'     => 'La solicitud está siendo procesada',
            'nomeEstadoSolDevReem'    => 'PRO',
            'estadoEstadoSolDevReem'  => true,
        ]);

    }
}