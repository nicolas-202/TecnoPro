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
        Schema::create('tipoDocumento', function (Blueprint $table) {
            $table->id('idTipoDocumento');
            $table->string('nomTipoDocumento', 40)->unique();
            $table->string('desTipoDocumento', 255)->nullable();
            $table->string('nomeTipoDocumento', 3)->unique();
            $table->boolean('estadoTipoDocumento')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipoDocumento');
    }
};
