<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('versiones_doc', function (Blueprint $table) {
            $table->string('Ruta_Archivo', 255)->nullable()->after('Fecha_Carga');
            $table->string('Nombre_Original', 255)->nullable()->after('Ruta_Archivo');
        });
    }

    public function down(): void
    {
        Schema::table('versiones_doc', function (Blueprint $table) {
            $table->dropColumn(['Ruta_Archivo', 'Nombre_Original']);
        });
    }
};
