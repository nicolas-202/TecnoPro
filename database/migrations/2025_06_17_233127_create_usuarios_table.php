<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('nombre',80);
            $table->string('email',60)->unique();
            $table->string('celular',10);
            $table->date('fecha_nacimiento');
            $table->string('numero_documento',20)->unique();
            $table->string('password');
            $table->string('direccion',60);
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('idGenero');
            $table->unsignedBigInteger('idTipoDocumento');
            $table->unsignedBigInteger('idMunicipio');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('idMunicipio')->references('idMunicipio')->on('Municipio')->onDelete('restrict');
            $table->foreign('idTipoDocumento')->references('idTipoDocumento')->on('tipoDocumento')->onDelete('restrict');
            $table->foreign('idGenero')->references('idGenero')->on('Genero')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};