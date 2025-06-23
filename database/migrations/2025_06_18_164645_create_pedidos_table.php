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
        Schema::create('pedido', function (Blueprint $table) {
            $table->id('idPedido');
            $table->date('fechaPedido');
            $table->decimal('totalPedido', 10, 2);
            $table->text('informacionPedido')->nullable();
            $table->unsignedBigInteger('idFormaPago');
            $table->foreign('idFormaPago')->references('idFormaPago')->on('formaPago')->onDelete('restrict');
            $table->unsignedBigInteger('idEstadoPedido');
            $table->foreign('idEstadoPedido')->references('idEstadoPedido')->on('estadoPedido')->onDelete('restrict');
            $table->unsignedBigInteger('idUsuario');
            $table->foreign('idUsuario')->references('user_id')->on('usuario')->onDelete('restrict');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido');
    }
};
