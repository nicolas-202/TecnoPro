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
        Schema::create('proveedor', function (Blueprint $table) {
            $table->id('idProveedor');
            $table->string('nomProveedor', 40);
            $table->string('desProveedor', 255)->nullable();
            $table->string('telProveedor', 10);
            $table->string('emailProveedor', 50);
            $table->string('nitProveedor', 10);
            $table->boolean('estadoProveedor')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedor');
    }
};
