<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('municipio', function (Blueprint $table) {
            $table->id('idMunicipio');
            $table->string('nomMunicipio', 40)->unique();
            $table->string('desMunicipio', 255)->nullable();
            $table->string('nomeMunicipio', 3)->unique();
            $table->boolean('estadoMunicipio')->default(true);
            $table->unsignedBigInteger('idDepartamento');
            $table->foreign('idDepartamento')->references('idDepartamento')->on('departamento')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipio');
    }
};
