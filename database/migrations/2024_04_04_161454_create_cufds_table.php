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
        Schema::create('cufds', function (Blueprint $table) {
            $table->id();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();
            
            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->foreign('sucursal_id')->references('id')->on('sucursales');
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->foreign('cuis_id')->references('id')->on('cuis');
            $table->unsignedBigInteger('cuis_id')->nullable();
            $table->foreign('punto_venta_id')->references('id')->on('punto_ventas');
            $table->unsignedBigInteger('punto_venta_id')->nullable();

            $table->text('codigo_ambiente')->nullable();
            $table->text('codigo')->nullable();
            $table->string('codigo_control')->nullable();
            $table->string('direccion')->nullable();
            $table->dateTime('fecha_vigencia')->nullable();

            $table->string('estado')->nullable();
            $table->datetime('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cufds');
    }
};
