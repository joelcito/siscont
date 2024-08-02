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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();

            $table->string('nombre')->nullable();
            $table->string('logo')->nullable();
            $table->string('nit')->nullable();
            $table->string('municipio')->nullable();
            $table->string('celular')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('codigo_ambiente')->nullable();
            $table->string('codigo_modalidad')->nullable();
            $table->string('codigo_sistema')->nullable();
            $table->string('codigo_documento_sector')->nullable();
            $table->string('api_token', 1000)->nullable();
            $table->string('url_facturacionCodigos')->nullable();
            $table->string('url_facturacionSincronizacion')->nullable();
            $table->string('url_servicio_facturacion_compra_venta')->nullable();
            $table->string('url_facturacion_operaciones')->nullable();
            $table->string('url_recepcion_compras')->nullable();
            $table->string('url_verifica')->nullable();
            $table->string('cafc')->nullable();
            $table->string('archivop12')->nullable();
            $table->string('contrasenia')->nullable();
            // $table->string('tipo_empresa')->nullable();
            // $table->string('url_facturacionCodigos_pro')->nullable();
            // $table->string('url_facturacionSincronizacion_pro')->nullable();
            // $table->string('url_servicio_facturacion_compra_venta_pro')->nullable();
            // $table->string('url_facturacion_operaciones_pro')->nullable();

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
        Schema::dropIfExists('empresas');
    }
};
