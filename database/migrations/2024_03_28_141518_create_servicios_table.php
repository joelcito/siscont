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
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();

            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->foreign('siat_depende_actividades_id')->references('id')->on('siat_depende_actividades');
            $table->unsignedBigInteger('siat_depende_actividades_id')->nullable();
            $table->foreign('siat_producto_servicios_id')->references('id')->on('siat_producto_servicios');
            $table->unsignedBigInteger('siat_producto_servicios_id')->nullable();
            $table->foreign('siat_unidad_medidas_id')->references('id')->on('siat_unidad_medidas');
            $table->unsignedBigInteger('siat_unidad_medidas_id')->nullable();

            $table->string('descripcion')->nullable();
            $table->decimal('precio',12,2)->nullable();
            $table->string('numero_serie')->nullable();
            $table->string('codigo_imei')->nullable();

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
        Schema::dropIfExists('servicios');
    }
};
