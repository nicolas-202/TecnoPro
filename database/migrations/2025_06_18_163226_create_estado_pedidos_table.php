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
        Schema::create('estadoPedido', function (Blueprint $table) {
            $table->id('idEstadoPedido');
            $table->string('nomEstadoPedido', 40);
            $table->string('desEstadoPedido', 255)->nullable();
            $table->string('nomeEstadoPedido', 3);
            $table->boolean('estadoEstadoPedido')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estadoPedido');
    }
};
