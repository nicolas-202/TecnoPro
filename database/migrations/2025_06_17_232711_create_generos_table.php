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
        Schema::create('genero', function (Blueprint $table) {
            $table->id('idGenero');
            $table->string('nomGenero', 40)->unique();
            $table->string('desGenero', 255)->nullable();
            $table->string('nomeGenero', 3)->unique();
            $table->boolean('estadoGenero')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genero');
    }
};
