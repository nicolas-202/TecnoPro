<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categoria::create([
            'nomCategoria'     => 'Perifericos',
            'desCategoria'     => 'Perifericos',
            'nomeCategoria'    => 'PER',
            'estadoCategoria'  => true,
        ]);

        Categoria::create([
            'nomCategoria'     => 'Portatiles',
            'desCategoria'     => 'Portatiles',
            'nomeCategoria'    => 'POR',
            'estadoCategoria'  => true,
        ]);

        Categoria::create([
            'nomCategoria'     => 'Celulares',
            'desCategoria'     => 'Celulares',
            'nomeCategoria'    => 'CEL',
            'estadoCategoria'  => true,
        ]);
    }
}
