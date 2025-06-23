<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadoPedido;

class EstadoPedidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EstadoPedido::create([
            'nomEstadoPedido'     => 'Procesando',
            'desEstadoPedido'     => 'El pedido está siendo procesado',
            'nomeEstadoPedido'    => 'PRO',
            'estadoEstadoPedido'  => true,
        ]);
    }
}