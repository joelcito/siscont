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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_id')->nullable()->after('id');
            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->unsignedBigInteger('punto_venta_id')->nullable()->after('empresa_id');
            $table->foreign('punto_venta_id')->references('id')->on('punto_ventas');

            $table->string('nombres')->nullable()->after('password');
            $table->string('ap_paterno')->nullable()->after('nombres');
            $table->string('ap_materno')->nullable()->after('ap_paterno');
            $table->string('correo')->nullable()->after('ap_materno');
            $table->string('numero_celular')->nullable()->after('correo');
            $table->string('estado')->nullable()->after('password');

            $table->datetime('deleted_at')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropColumn('empresa_id');
            $table->dropForeign(['punto_venta_id']);
            $table->dropColumn('punto_venta_id');
        });
    }
};
