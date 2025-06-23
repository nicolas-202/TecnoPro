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
         Schema::create('formapago', function (Blueprint $table) {
            $table->id('idFormaPago')->autoIncrement();
            $table->string('nomFormaPago', 50)->unique();
            $table->string('desFormaPago', 255)->nullable();
            $table->string('nomeFormaPago', 3)->unique();
            $table->boolean('estadoFormaPago')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formapago');
    }
};
