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
        Schema::create('producto', function (Blueprint $table) {
            $table->id('idProducto');
            $table->string('nomProducto', 80);
            $table->text('desProducto')->nullable();
            $table->integer('stockMinimo');
            $table->integer('stockMaximo');
            $table->integer('cantidadExistente');
            $table->decimal('precioVenta', 10, 2);
            $table->string('imagen')->nullable();
            $table->boolean('estadoProducto')->default(true);
            $table->unsignedBigInteger('idCategoria');
            $table->foreign('idCategoria')->references('idCategoria')->on('categoria')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto');
    }
};
