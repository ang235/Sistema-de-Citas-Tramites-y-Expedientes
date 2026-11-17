<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bitacora', function (Blueprint $table) {
            $table->unsignedBigInteger('ID_Solicitud')->nullable()->after('ID_Usuarios');
        });
    }

    public function down(): void
    {
        Schema::table('bitacora', function (Blueprint $table) {
            $table->dropColumn('ID_Solicitud');
        });
    }
};
