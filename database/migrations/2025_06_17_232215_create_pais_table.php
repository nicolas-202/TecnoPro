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
        Schema::create('pais', function (Blueprint $table) {
            $table->id('idPais');
            $table->string('nomPais', 40)->unique();
            $table->string('desPais', 255)->nullable();
            $table->string('nomePais', 3)->unique();
            $table->boolean('estadoPais')->default(true);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pais');
    }
};
