<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CleanSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Vaciado completo de la base de datos dejando únicamente los roles estructurales
     * y el usuario Administrador del Sistema para comenzar la parametrización desde cero.
     */
    public function run(): void
    {
        $now = Carbon::now();

        Schema::disableForeignKeyConstraints();

        // 1. Limpieza de tablas transaccionales y operativas
        $tablas = [
            'resoluciones',
            'citas',
            'respuesta',
            'observaciones',
            'revisiones',
            'asignacion_revisor',
            'versiones_doc',
            'documento',
            'datos_solicitud',
            'folio',
            'solicitudes',
            'requisitos',
            'version_tramite',
            'tipo_tramite',
            'bitacora',
            'recuperacion_contrasena',
            'usuarios_roles',
            'roles',
            'usuarios',
        ];

        foreach ($tablas as $tabla) {
            if (Schema::hasTable($tabla)) {
                DB::table($tabla)->truncate();
            }
        }

        // Limpieza de tablas de sesión y colas de Laravel si existen
        foreach (['sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'] as $frameworkTable) {
            if (Schema::hasTable($frameworkTable)) {
                DB::table($frameworkTable)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();

        // 2. Creación de Roles Estructurales
        $rolAdmin = DB::table('roles')->insertGetId([
            'Nombre' => 'Administrador',
            'Descripcion' => 'Gestión de rubros, tipos de trámite, versiones, usuarios y administración general',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $rolSupervisor = DB::table('roles')->insertGetId([
            'Nombre' => 'Supervisor / Dictaminador',
            'Descripcion' => 'Asignación de expedientes a revisores, control de matriz de cargas y firma de resoluciones oficiales',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $rolRevisor = DB::table('roles')->insertGetId([
            'Nombre' => 'Revisor Técnico',
            'Descripcion' => 'Evaluación documental, emisión de observaciones reglamentarias y dictámenes técnicos',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $rolCiudadano = DB::table('roles')->insertGetId([
            'Nombre' => 'Solicitante / Ciudadano',
            'Descripcion' => 'Inicia trámites, adjunta recaudos, agenda citas y atiende subsanaciones',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Usuario Administrador Inicial
        $adminId = DB::table('usuarios')->insertGetId([
            'Nombre' => 'Administrador del Sistema',
            'Email' => 'admin@tramites.gob.mx',
            'Contrasena' => Hash::make('admin123'),
            'Fecha_Creacion' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 4. Asignación de Rol Administrador
        DB::table('usuarios_roles')->insert([
            'ID_Usuarios' => $adminId,
            'ID_Rol' => $rolAdmin,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5. Registro inicial en Bitácora
        DB::table('bitacora')->insert([
            'ID_Usuarios' => $adminId,
            'Accion' => 'Inicialización de Base de Datos',
            'Descripcion' => 'Base de datos restablecida a estado limpio inicial. Plataforma lista para creación de trámites, usuarios y expedientes.',
            'Fecha_Creacion' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
