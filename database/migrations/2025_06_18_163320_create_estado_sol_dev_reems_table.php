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
        Schema::create('estadoSolDevReem', function (Blueprint $table) {
            $table->id('idEstadoSolDevReem');
            $table->string('nomEstadoSolDevReem', 40);
            $table->string('desEstadoSolDevReem',255)->nullable();
            $table->string('nomeEstadoSolDevReem', 3);
            $table->boolean('estadoEstadoSolDevReem')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estadoSolDevReem');
    }
};
