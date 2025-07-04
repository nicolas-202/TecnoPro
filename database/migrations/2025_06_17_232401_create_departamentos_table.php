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
        Schema::create('departamento', function (Blueprint $table) {
            $table->id('idDepartamento');
            $table->string('nomDepartamento', 40)->unique();
            $table->string('desDepartamento', 255)->nullable();
            $table->string('nomeDepartamento', 3)->unique();
            $table->boolean('estadoDepartamento')->default(true);
            $table->unsignedBigInteger('idPais');
            $table->foreign('idPais')->references('idPais')->on('pais')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamento');
    }
};
