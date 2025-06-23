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
        Schema::create('pedidoProducto', function (Blueprint $table) {
            $table->id('idPedidoProducto');
            $table->integer('cantidadProducto');
            $table->decimal('precioProducto', 10, 2);
            $table->decimal('totalProducto', 10, 2);
            $table->unsignedBigInteger('idPedido');
            $table->foreign('idPedido')->references('idPedido')->on('pedido')->onDelete('restrict');
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
        Schema::dropIfExists('pedidoProducto');
    }
};
