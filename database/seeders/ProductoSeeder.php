<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         
       
            Producto::create([
                'nomProducto'         => 'Mouse Inalámbrico',
                'desProducto'         => 'Mouse óptico inalámbrico USB',
                'stockMinimo'         => 5,
                'stockMaximo'         => 50,
                'cantidadExistente'   => 0,
                'precioVenta'         => 0,
                'imagen'              => null,
                'estadoProducto'      => true,
                'idCategoria'         => 1,
            ]);
      

            Producto::create([
                'nomProducto'         => 'Laptop HP 14"',
                'desProducto'         => 'Portátil HP 14 pulgadas, 8GB RAM, 256GB SSD',
                'stockMinimo'         => 2,
                'stockMaximo'         => 15,
                'cantidadExistente'   => 0,
                'precioVenta'         => 0,
                'imagen'              => null,
                'estadoProducto'      => true,
                'idCategoria'         => 2,
            ]);
      

            Producto::create([
                'nomProducto'         => 'Smartphone Samsung Galaxy A34',
                'desProducto'         => 'Celular Samsung Galaxy A34, 128GB, 6GB RAM',
                'stockMinimo'         => 3,
                'stockMaximo'         => 30,
                'cantidadExistente'   => 0,
                'precioVenta'         => 0,
                'imagen'              => null,
                'estadoProducto'      => true,
                'idCategoria'         => 3,
            ]);
        
    }
}
