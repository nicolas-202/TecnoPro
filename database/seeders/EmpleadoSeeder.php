<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Empleado;
use App\Models\Usuario;
use App\Models\Cargo;

class EmpleadoSeeder extends Seeder
{
  
    public function run(): void
    {
   

        
            Empleado::create([
                'fecIngreso'      => now(),
                'imagen'          => null,
                'idCargo'         =>1,
                'user_id'         => 1,
                'estadoEmpleado'  => true,
            ]);
        
    }
}
