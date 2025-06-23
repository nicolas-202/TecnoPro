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
        Schema::create('solDevReem', function (Blueprint $table) {
            $table->id('idSolDevReem');
            $table->date('fechaSolDevReem');
            $table->text('comentarioSolDevReem')->nullable();
            $table->text('respuestaSolDevReem')->nullable();
            $table->unsignedBigInteger('idEstadoSolDevReem');
            $table->foreign('idEstadoSolDevReem')->references('idEstadoSolDevReem')->on('estadoSolDevReem')->onDelete('restrict');
            $table->unsignedBigInteger('idPedidoProducto');
            $table->foreign('idPedidoProducto')->references('idPedidoProducto')->on('pedidoProducto')->onDelete('restrict'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solDevReem');
    }
};
