<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genero;
use App\Models\Municipio;
use App\Models\TipoDocumento;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    
        
            Usuario::create([
            'nombre'            => 'Administrador General',
            'email'             => 'admin@tecnopro.com',
            'celular'           => '3001234567',
            'fecha_nacimiento'  => '1990-01-01',
            'numero_documento'  => '10000001',
            'password'          =>  Hash::make('admin123'),
            'direccion'         => 'Calle 1 # 2-3',
            'idGenero'          => 1,
            'idTipoDocumento'   => 1,
            'idMunicipio'       => 1,
            'remember_token'    => null,
        ]);

        Usuario::create([
            'nombre'            => 'Juan Pérez',
            'email'             => 'juan@tecnopro.com',
            'celular'           => '3007654321',
            'fecha_nacimiento'  => '1995-05-10',
            'numero_documento'  => '10000002',
            'password'          =>  Hash::make('usuario123'),
            'direccion'         => 'Carrera 10 # 20-30',
            'idGenero'          => 1,
            'idTipoDocumento'   => 1,
            'idMunicipio'       => 1,
            'remember_token'    => null,
        ]);
        
    }
}
