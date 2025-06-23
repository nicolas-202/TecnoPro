<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FormaPago;

class FormaPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FormaPago::create([
            'nomFormaPago'    => 'Tarjeta de Crédito',
            'desFormaPago'    => 'Pago mediante tarjeta de crédito',
            'nomeFormaPago'   => 'TCR',
            'estadoFormaPago' => true,
        ]);

        FormaPago::create([
            'nomFormaPago'    => 'Tarjeta de Débito',
            'desFormaPago'    => 'Pago mediante tarjeta de débito',
            'nomeFormaPago'   => 'TDB',
            'estadoFormaPago' => true,
        ]);
    }
}
