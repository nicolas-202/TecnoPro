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
        Schema::create('productoProveedor', function (Blueprint $table) {
            $table->id('idProductoProveedor');
            $table->date('fechaRegistro');
            $table->decimal('precioUnitario', 10, 2);
            $table->integer('cantidad');
            $table->decimal('precioTotal', 10, 2);
            $table->unsignedBigInteger('idProducto');
            $table->foreign('idProducto')->references('idProducto')->on('producto')->onDelete('restrict');
            $table->unsignedBigInteger('idProveedor');
            $table->foreign('idProveedor')->references('idProveedor')->on('proveedor')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productoProveedor');
    }
};
