<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pais;

class PaisSeeder extends Seeder
{
    public function run(): void
    {
        Pais::create([
            'nomPais'    => 'Colombia',
            'desPais'    => 'República de Colombia',
            'nomePais'   => 'CO',
            'estadoPais' => true,
        ]);
    }
}
