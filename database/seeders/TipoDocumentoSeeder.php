<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoDocumento;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoDocumento::create([
            'nomTipoDocumento'      => 'Cédula de Ciudadanía',
            'desTipoDocumento'      => 'Documento de identidad para ciudadanos colombianos',
            'nomeTipoDocumento'     => 'CC',
            'estadoTipoDocumento'   => true,
        ]);

        TipoDocumento::create([
            'nomTipoDocumento'      => 'Tarjeta de Identidad',
            'desTipoDocumento'      => 'Documento de identidad para menores de edad',
            'nomeTipoDocumento'     => 'TI',
            'estadoTipoDocumento'   => true,
        ]);
    }
}
