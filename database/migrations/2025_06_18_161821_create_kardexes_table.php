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
        Schema::create('kardex', function (Blueprint $table) {
            $table->id('idKardex');
            $table->enum('tipoMovimiento', ['entrada', 'salida']);
            $table->integer('cantidadMovimiento');
            $table->date('fechaMovimiento');
            $table->decimal('costoUnitario', 10, 2);
            $table->decimal('costoTotal', 10, 2);
            $table->decimal('precioVentaActualizado', 10, 2);
            $table->unsignedBigInteger('idProducto');
            $table->foreign('idProducto')->references('idProducto')->on('producto')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardex');
    }
};
