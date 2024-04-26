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
        Schema::create('eventos_significativos', function (Blueprint $table) {
            $table->id();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();

            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->unsignedBigInteger('empresa_id')->nullable();

            $table->foreign('siat_evento_significativo_id')->references('id')->on('siat_evento_significativos');
            $table->unsignedBigInteger('siat_evento_significativo_id')->nullable();

            $table->foreign('punto_venta_id')->references('id')->on('punto_ventas');
            $table->unsignedBigInteger('punto_venta_id')->nullable();

            $table->foreign('sucursal_id')->references('id')->on('sucursales');
            $table->unsignedBigInteger('sucursal_id')->nullable();

            $table->foreign('cufd_activo_id')->references('id')->on('cufds');
            $table->unsignedBigInteger('cufd_activo_id')->nullable();

            $table->foreign('cufd_evento_id')->references('id')->on('cufds');
            $table->unsignedBigInteger('cufd_evento_id')->nullable();

            $table->foreign('cuis_id')->references('id')->on('cuis');
            $table->unsignedBigInteger('cuis_id')->nullable();

            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_ini_evento')->nullable();
            $table->dateTime('fecha_fin_evento')->nullable();
            $table->string('codigoRecepcionEventoSignificativo')->nullable();
            
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
        Schema::dropIfExists('eventos_significativos');
    }
};
