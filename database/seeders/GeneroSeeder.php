<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genero;

class GeneroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Genero::create([
            'nomGenero'     => 'Masculino',
            'desGenero'     => 'Género masculino',
            'nomeGenero'    => 'M',
            'estadoGenero'  => true,
        ]);

        Genero::create([
            'nomGenero'     => 'Femenino',
            'desGenero'     => 'Género femenino',
            'nomeGenero'    => 'F',
            'estadoGenero'  => true,
        ]);

        Genero::create([
            'nomGenero'     => 'Otro',
            'desGenero'     => 'Otro género',
            'nomeGenero'    => 'O',
            'estadoGenero'  => true,
        ]);
    }
}
