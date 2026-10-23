<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea todas las tablas definidas en el diagrama de Trámites y Citas.
     */
    public function up(): void
    {
        // 1. Usuarios
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('ID_Usuarios');
            $table->string('Nombre', 150);
            $table->string('Email', 150)->unique();
            $table->string('Contrasena', 255);
            $table->timestamp('Fecha_Creacion')->useCurrent();
            $table->timestamps();
        });

        // 2. Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id('ID_Rol');
            $table->string('Nombre', 100);
            $table->text('Descripcion')->nullable();
            $table->timestamps();
        });

        // 3. Usuarios_Roles (Relación Usuarios - Roles)
        Schema::create('usuarios_roles', function (Blueprint $table) {
            $table->id('ID_Usuario_Roles');
            $table->foreignId('ID_Usuarios')->constrained('usuarios', 'ID_Usuarios')->onDelete('cascade');
            $table->foreignId('ID_Rol')->constrained('roles', 'ID_Rol')->onDelete('cascade');
            $table->timestamps();
        });

        // 4. Recuperacion_Contraseña
        Schema::create('recuperacion_contrasena', function (Blueprint $table) {
            $table->id('ID_Contrasena');
            $table->foreignId('ID_Usuarios')->constrained('usuarios', 'ID_Usuarios')->onDelete('cascade');
            $table->string('Token', 255);
            $table->dateTime('Expiracion');
            $table->timestamps();
        });

        // 5. Bitacora
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id('ID_Bitacora');
            $table->foreignId('ID_Usuarios')->constrained('usuarios', 'ID_Usuarios')->onDelete('cascade');
            $table->string('Accion', 150);
            $table->text('Descripcion')->nullable();
            $table->timestamp('Fecha_Creacion')->useCurrent();
            $table->timestamps();
        });

        // 6. Tipo_Tramite (Rubros / Trámites configurables por el administrador)
        Schema::create('tipo_tramite', function (Blueprint $table) {
            $table->id('ID_Tipo_Tramite');
            $table->string('Nombre', 150);
            $table->text('Descripcion')->nullable();
            $table->string('Estado_Tipo_Tr', 50)->default('Activo');
            $table->timestamps();
        });

        // 7. Version_Tramite
        Schema::create('version_tramite', function (Blueprint $table) {
            $table->id('ID_Version_Tramite');
            $table->foreignId('ID_Tipo_Tramite')->constrained('tipo_tramite', 'ID_Tipo_Tramite')->onDelete('cascade');
            $table->string('No_Version', 50);
            $table->string('Estado_Ver_Tr', 50)->default('Vigente');
            $table->date('Fecha_Inicio_Vig')->nullable();
            $table->timestamps();
        });

        // 8. Requisitos (Requisitos asociados a cada versión del trámite)
        Schema::create('requisitos', function (Blueprint $table) {
            $table->id('ID_Requisitos');
            $table->foreignId('ID_Version_Tramite')->constrained('version_tramite', 'ID_Version_Tramite')->onDelete('cascade');
            $table->string('Nombre', 200);
            $table->string('Prioridad', 50)->default('Obligatorio');
            $table->string('Formato', 50)->default('PDF');
            $table->timestamps();
        });

        // 9. Solicitudes
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id('ID_Solicitud');
            $table->foreignId('ID_Solicitante')->constrained('usuarios', 'ID_Usuarios')->onDelete('cascade');
            $table->foreignId('ID_Version_Tramite')->constrained('version_tramite', 'ID_Version_Tramite')->onDelete('cascade');
            $table->timestamp('Fecha_Creacion_Solicitud')->useCurrent();
            $table->timestamps();
        });

        // 10. Folio
        Schema::create('folio', function (Blueprint $table) {
            $table->id('ID_Folio');
            $table->foreignId('ID_Solicitud')->constrained('solicitudes', 'ID_Solicitud')->onDelete('cascade');
            $table->string('Codigo_Folio', 100)->unique();
            $table->timestamp('Fecha_Emision')->useCurrent();
            $table->timestamps();
        });

        // 11. Datos_Solicitud
        Schema::create('datos_solicitud', function (Blueprint $table) {
            $table->id('ID_Dts_Sol');
            $table->foreignId('ID_Solicitud')->constrained('solicitudes', 'ID_Solicitud')->onDelete('cascade');
            $table->text('Descripcion_Sol')->nullable();
            $table->timestamps();
        });

        // 12. Documento
        Schema::create('documento', function (Blueprint $table) {
            $table->id('ID_Documento');
            $table->foreignId('ID_Solicitud')->constrained('solicitudes', 'ID_Solicitud')->onDelete('cascade');
            $table->foreignId('ID_Requisitos')->constrained('requisitos', 'ID_Requisitos')->onDelete('cascade');
            $table->string('Estado_Doc', 50)->default('Pendiente');
            $table->timestamps();
        });

        // 13. Versiones_Doc
        Schema::create('versiones_doc', function (Blueprint $table) {
            $table->id('ID_Version_Doc');
            $table->foreignId('ID_Documento')->constrained('documento', 'ID_Documento')->onDelete('cascade');
            $table->timestamp('Fecha_Carga')->useCurrent();
            $table->timestamps();
        });

        // 14. Asignacion_Revisor
        Schema::create('asignacion_revisor', function (Blueprint $table) {
            $table->id('ID_Asignacion');
            $table->foreignId('ID_Solicitud')->constrained('solicitudes', 'ID_Solicitud')->onDelete('cascade');
            $table->foreignId('ID_Revisor')->constrained('usuarios', 'ID_Usuarios')->onDelete('cascade');
            $table->foreignId('ID_Asignador')->constrained('usuarios', 'ID_Usuarios')->onDelete('cascade');
            $table->timestamp('Fecha_Asignacion')->useCurrent();
            $table->timestamps();
        });

        // 15. Revisiones
        Schema::create('revisiones', function (Blueprint $table) {
            $table->id('ID_Revision');
            $table->foreignId('ID_Asignacion')->constrained('asignacion_revisor', 'ID_Asignacion')->onDelete('cascade');
            $table->string('Resultado', 100)->default('En Revisión');
            $table->timestamp('Fecha_Revision')->useCurrent();
            $table->timestamps();
        });

        // 16. Observaciones
        Schema::create('observaciones', function (Blueprint $table) {
            $table->id('ID_Observacion');
            $table->foreignId('ID_Revision')->constrained('revisiones', 'ID_Revision')->onDelete('cascade');
            $table->text('Detalles');
            $table->string('Estado_Obs', 50)->default('Abierta');
            $table->timestamps();
        });

        // 17. Respuesta
        Schema::create('respuesta', function (Blueprint $table) {
            $table->id('ID_Respuesta');
            $table->foreignId('ID_Observacion')->constrained('observaciones', 'ID_Observacion')->onDelete('cascade');
            $table->foreignId('ID_Version_Doc')->nullable()->constrained('versiones_doc', 'ID_Version_Doc')->onDelete('set null');
            $table->text('Mensaje_Respuesta');
            $table->timestamp('Fecha_Respuesta')->useCurrent();
            $table->timestamps();
        });

        // 18. Citas
        Schema::create('citas', function (Blueprint $table) {
            $table->id('ID_Citas');
            $table->foreignId('ID_Usuarios')->constrained('usuarios', 'ID_Usuarios')->onDelete('cascade');
            $table->foreignId('ID_Solicitud')->nullable()->constrained('solicitudes', 'ID_Solicitud')->onDelete('set null');
            $table->date('Fecha');
            $table->time('Hora');
            $table->string('Estado_Citas', 50)->default('Programada');
            $table->timestamps();
        });

        // 19. Resoluciones
        Schema::create('resoluciones', function (Blueprint $table) {
            $table->id('ID_Resolucion');
            $table->foreignId('ID_Solicitud')->constrained('solicitudes', 'ID_Solicitud')->onDelete('cascade');
            $table->foreignId('ID_Supervisor')->constrained('usuarios', 'ID_Usuarios')->onDelete('cascade');
            $table->timestamp('Fecha_Emision_Resolucion')->useCurrent();
            $table->string('Tipo_Resolucion', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping in reverse order to respect foreign keys
        Schema::dropIfExists('resoluciones');
        Schema::dropIfExists('citas');
        Schema::dropIfExists('respuesta');
        Schema::dropIfExists('observaciones');
        Schema::dropIfExists('revisiones');
        Schema::dropIfExists('asignacion_revisor');
        Schema::dropIfExists('versiones_doc');
        Schema::dropIfExists('documento');
        Schema::dropIfExists('datos_solicitud');
        Schema::dropIfExists('folio');
        Schema::dropIfExists('solicitudes');
        Schema::dropIfExists('requisitos');
        Schema::dropIfExists('version_tramite');
        Schema::dropIfExists('tipo_tramite');
        Schema::dropIfExists('bitacora');
        Schema::dropIfExists('recuperacion_contrasena');
        Schema::dropIfExists('usuarios_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('usuarios');
    }
};
