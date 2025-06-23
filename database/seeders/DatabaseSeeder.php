<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PaisSeeder::class,
            DepartamentoSeeder::class,
            MunicipioSeeder::class,
            GeneroSeeder::class,
            TipoDocumentoSeeder::class,
            CategoriaSeeder::class,
            CargoSeeder::class,
            UsuarioSeeder::class,
            EmpleadoSeeder::class,
            ProductoSeeder::class,
            FormaPagoSeeder::class,
            EstadoPedidoSeeder::class,
            EstadoSolDevReemSeeder::class
        ]);
    }
}
