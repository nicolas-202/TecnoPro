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
        Schema::create('empleado', function (Blueprint $table) {
            $table->id('idEmpleado');
            $table->date('fecIngreso');
            $table->string('imagen')->nullable();
            $table->boolean('estadoEmpleado')->default(true);
            $table->unsignedBigInteger('idCargo');
            $table->foreign('idCargo')->references('idCargo')->on('cargo')->onDelete('restrict');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('usuario')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleado');
    }
};
