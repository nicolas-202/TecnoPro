<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cargo;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Cargo::create([
            'nomCargo'     => 'Administrador',
            'desCargo'     => 'Administrador general del sistema',
            'nomeCargo'    => 'ADM',
            'estadoCargo'  => true,
        ]);
    }
}
