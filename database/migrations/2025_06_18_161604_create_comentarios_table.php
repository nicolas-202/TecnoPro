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
        Schema::create('comentario', function (Blueprint $table) {
            $table->id('idComentario');
            $table->text('contenidoComentario');
            $table->date('fechaComentario');
            $table->boolean('estadoComentario')->default(true);
            $table->unsignedBigInteger('idProducto');
            $table->foreign('idProducto')->references('idProducto')->on('producto')->onDelete('cascade');
            $table->unsignedBigInteger('idUsuario');
            $table->foreign('idUsuario')->references('user_id')->on('usuario')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentario');
    }
};
