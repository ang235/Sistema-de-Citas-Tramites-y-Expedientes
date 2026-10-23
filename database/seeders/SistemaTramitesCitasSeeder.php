<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SistemaTramitesCitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Inserta datos de prueba coherentes para las 19 tablas del sistema.
     */
    public function run(): void
    {
        $now = Carbon::now();

        Schema::disableForeignKeyConstraints();
        DB::table('resoluciones')->truncate();
        DB::table('citas')->truncate();
        DB::table('respuesta')->truncate();
        DB::table('observaciones')->truncate();
        DB::table('revisiones')->truncate();
        DB::table('asignacion_revisor')->truncate();
        DB::table('versiones_doc')->truncate();
        DB::table('documento')->truncate();
        DB::table('datos_solicitud')->truncate();
        DB::table('folio')->truncate();
        DB::table('solicitudes')->truncate();
        DB::table('requisitos')->truncate();
        DB::table('version_tramite')->truncate();
        DB::table('tipo_tramite')->truncate();
        DB::table('bitacora')->truncate();
        DB::table('recuperacion_contrasena')->truncate();
        DB::table('usuarios_roles')->truncate();
        DB::table('roles')->truncate();
        DB::table('usuarios')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Usuarios
        $adminId = DB::table('usuarios')->insertGetId([
            'Nombre' => 'Administrador del Sistema',
            'Email' => 'admin@tramites.gob.mx',
            'Contrasena' => Hash::make('admin123'),
            'Fecha_Creacion' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $ciudadano1Id = DB::table('usuarios')->insertGetId([
            'Nombre' => 'Carlos Mendoza Ruiz',
            'Email' => 'carlos.mendoza@email.com',
            'Contrasena' => Hash::make('password123'),
            'Fecha_Creacion' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $ciudadano2Id = DB::table('usuarios')->insertGetId([
            'Nombre' => 'María Elena Torres',
            'Email' => 'maria.torres@email.com',
            'Contrasena' => Hash::make('password123'),
            'Fecha_Creacion' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $revisorId = DB::table('usuarios')->insertGetId([
            'Nombre' => 'Lic. Roberto Gómez',
            'Email' => 'revisor@tramites.gob.mx',
            'Contrasena' => Hash::make('revisor123'),
            'Fecha_Creacion' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $supervisorId = DB::table('usuarios')->insertGetId([
            'Nombre' => 'Dra. Patricia Silva',
            'Email' => 'supervisor@tramites.gob.mx',
            'Contrasena' => Hash::make('supervisor123'),
            'Fecha_Creacion' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Roles
        $rolAdmin = DB::table('roles')->insertGetId([
            'Nombre' => 'Administrador',
            'Descripcion' => 'Gestión de rubros, tipos de trámite, versiones y administración general',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $rolSolicitante = DB::table('roles')->insertGetId([
            'Nombre' => 'Solicitante / Ciudadano',
            'Descripcion' => 'Inicia trámites, adjunta documentos, agenda citas y atiende observaciones',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $rolRevisor = DB::table('roles')->insertGetId([
            'Nombre' => 'Revisor Técnico',
            'Descripcion' => 'Revisión técnica de documentos y expedientes, emisión de observaciones',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $rolSupervisor = DB::table('roles')->insertGetId([
            'Nombre' => 'Supervisor / Dictaminador',
            'Descripcion' => 'Asignación de expedientes a revisores y emisión de resoluciones formales',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Usuarios_Roles
        DB::table('usuarios_roles')->insert([
            ['ID_Usuarios' => $adminId, 'ID_Rol' => $rolAdmin, 'created_at' => $now, 'updated_at' => $now],
            ['ID_Usuarios' => $ciudadano1Id, 'ID_Rol' => $rolSolicitante, 'created_at' => $now, 'updated_at' => $now],
            ['ID_Usuarios' => $ciudadano2Id, 'ID_Rol' => $rolSolicitante, 'created_at' => $now, 'updated_at' => $now],
            ['ID_Usuarios' => $revisorId, 'ID_Rol' => $rolRevisor, 'created_at' => $now, 'updated_at' => $now],
            ['ID_Usuarios' => $supervisorId, 'ID_Rol' => $rolSupervisor, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 4. Recuperacion_Contraseña
        DB::table('recuperacion_contrasena')->insert([
            'ID_Usuarios' => $ciudadano1Id,
            'Token' => 'tok_reset_abc123xyz890',
            'Expiracion' => $now->copy()->addHours(24),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5. Bitacora
        DB::table('bitacora')->insert([
            [
                'ID_Usuarios' => $adminId,
                'Accion' => 'Configuración de Rubro',
                'Descripcion' => 'Activación del trámite Licencia de Funcionamiento v1.0 en la plataforma',
                'Fecha_Creacion' => $now->copy()->subDays(2),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ID_Usuarios' => $revisorId,
                'Accion' => 'Revisión Técnica',
                'Descripcion' => 'Revisión documental iniciada para el trámite de Apertura Comercial',
                'Fecha_Creacion' => $now->copy()->subHours(5),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // 6. Tipo_Tramite (Rubro configurable por el administrador)
        $tipoLicencia = DB::table('tipo_tramite')->insertGetId([
            'Nombre' => 'Licencia de Funcionamiento Comercial',
            'Descripcion' => 'Permiso oficial para inicio de operaciones en locales comerciales y servicios',
            'Estado_Tipo_Tr' => 'Activo',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $tipoConstruccion = DB::table('tipo_tramite')->insertGetId([
            'Nombre' => 'Permiso de Construcción y Remodelación',
            'Descripcion' => 'Licencia para remodelaciones, ampliaciones y obras civiles residenciales',
            'Estado_Tipo_Tr' => 'Activo',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $tipoUsoSuelo = DB::table('tipo_tramite')->insertGetId([
            'Nombre' => 'Certificado de Zonificación y Uso de Suelo',
            'Descripcion' => 'Constancia sobre la compatibilidad urbanística para actividades específicas',
            'Estado_Tipo_Tr' => 'Activo',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 7. Version_Tramite
        $verLicencia1 = DB::table('version_tramite')->insertGetId([
            'ID_Tipo_Tramite' => $tipoLicencia,
            'No_Version' => 'v1.0',
            'Estado_Ver_Tr' => 'Vigente',
            'Fecha_Inicio_Vig' => '2026-01-01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $verLicencia2 = DB::table('version_tramite')->insertGetId([
            'ID_Tipo_Tramite' => $tipoLicencia,
            'No_Version' => 'v2.0 (Digital)',
            'Estado_Ver_Tr' => 'Borrador',
            'Fecha_Inicio_Vig' => '2026-07-01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $verConstruccion1 = DB::table('version_tramite')->insertGetId([
            'ID_Tipo_Tramite' => $tipoConstruccion,
            'No_Version' => 'v1.0',
            'Estado_Ver_Tr' => 'Vigente',
            'Fecha_Inicio_Vig' => '2026-01-01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 8. Requisitos
        $reqIne = DB::table('requisitos')->insertGetId([
            'ID_Version_Tramite' => $verLicencia1,
            'Nombre' => 'Identificación Oficial Vigente del Solicitante',
            'Prioridad' => 'Obligatorio',
            'Formato' => 'PDF',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $reqComprobante = DB::table('requisitos')->insertGetId([
            'ID_Version_Tramite' => $verLicencia1,
            'Nombre' => 'Comprobante de Domicilio del Inmueble (Agua o Luz)',
            'Prioridad' => 'Obligatorio',
            'Formato' => 'PDF',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $reqPlano = DB::table('requisitos')->insertGetId([
            'ID_Version_Tramite' => $verLicencia1,
            'Nombre' => 'Croquis o Plano Arquitectónico con Medidas',
            'Prioridad' => 'Obligatorio',
            'Formato' => 'PDF',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $reqEscritura = DB::table('requisitos')->insertGetId([
            'ID_Version_Tramite' => $verConstruccion1,
            'Nombre' => 'Título de Propiedad o Escritura Pública Notariada',
            'Prioridad' => 'Obligatorio',
            'Formato' => 'PDF',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 9. Solicitudes
        $solicitud1 = DB::table('solicitudes')->insertGetId([
            'ID_Solicitante' => $ciudadano1Id,
            'ID_Version_Tramite' => $verLicencia1,
            'Fecha_Creacion_Solicitud' => $now->copy()->subDays(3),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $solicitud2 = DB::table('solicitudes')->insertGetId([
            'ID_Solicitante' => $ciudadano2Id,
            'ID_Version_Tramite' => $verConstruccion1,
            'Fecha_Creacion_Solicitud' => $now->copy()->subDays(1),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 10. Folio
        DB::table('folio')->insert([
            [
                'ID_Solicitud' => $solicitud1,
                'Codigo_Folio' => 'FOL-2026-0001',
                'Fecha_Emision' => $now->copy()->subDays(3),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ID_Solicitud' => $solicitud2,
                'Codigo_Folio' => 'FOL-2026-0002',
                'Fecha_Emision' => $now->copy()->subDays(1),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // 11. Datos_Solicitud
        DB::table('datos_solicitud')->insert([
            [
                'ID_Solicitud' => $solicitud1,
                'Descripcion_Sol' => 'Apertura de cafetería de especialidad con servicio de barra y mesas al interior.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ID_Solicitud' => $solicitud2,
                'Descripcion_Sol' => 'Construcción de barda perimetral de 25 metros lineales en predio habitacional.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // 12. Documento
        $doc1 = DB::table('documento')->insertGetId([
            'ID_Solicitud' => $solicitud1,
            'ID_Requisitos' => $reqIne,
            'Estado_Doc' => 'Aprobado',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $doc2 = DB::table('documento')->insertGetId([
            'ID_Solicitud' => $solicitud1,
            'ID_Requisitos' => $reqComprobante,
            'Estado_Doc' => 'Aprobado',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $doc3 = DB::table('documento')->insertGetId([
            'ID_Solicitud' => $solicitud1,
            'ID_Requisitos' => $reqPlano,
            'Estado_Doc' => 'Observado',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $doc4 = DB::table('documento')->insertGetId([
            'ID_Solicitud' => $solicitud2,
            'ID_Requisitos' => $reqEscritura,
            'Estado_Doc' => 'Aprobado',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 13. Versiones_Doc
        $verDoc1 = DB::table('versiones_doc')->insertGetId([
            'ID_Documento' => $doc1,
            'Fecha_Carga' => $now->copy()->subDays(3),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $verDoc2 = DB::table('versiones_doc')->insertGetId([
            'ID_Documento' => $doc2,
            'Fecha_Carga' => $now->copy()->subDays(3),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $verDoc3_v1 = DB::table('versiones_doc')->insertGetId([
            'ID_Documento' => $doc3,
            'Fecha_Carga' => $now->copy()->subDays(3),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $verDoc3_v2 = DB::table('versiones_doc')->insertGetId([
            'ID_Documento' => $doc3,
            'Fecha_Carga' => $now->copy()->subHours(12),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 14. Asignacion_Revisor
        $asig1 = DB::table('asignacion_revisor')->insertGetId([
            'ID_Solicitud' => $solicitud1,
            'ID_Revisor' => $revisorId,
            'ID_Asignador' => $supervisorId,
            'Fecha_Asignacion' => $now->copy()->subDays(2),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $asig2 = DB::table('asignacion_revisor')->insertGetId([
            'ID_Solicitud' => $solicitud2,
            'ID_Revisor' => $revisorId,
            'ID_Asignador' => $supervisorId,
            'Fecha_Asignacion' => $now->copy()->subDays(1),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 15. Revisiones
        $rev1 = DB::table('revisiones')->insertGetId([
            'ID_Asignacion' => $asig1,
            'Resultado' => 'Con Observaciones',
            'Fecha_Revision' => $now->copy()->subDays(1),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $rev2 = DB::table('revisiones')->insertGetId([
            'ID_Asignacion' => $asig2,
            'Resultado' => 'Aprobado',
            'Fecha_Revision' => $now->copy()->subHours(6),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 16. Observaciones
        $obs1 = DB::table('observaciones')->insertGetId([
            'ID_Revision' => $rev1,
            'Detalles' => 'El plano arquitectónico debe señalar la ubicación de extintor y ruta de evacuación.',
            'Estado_Obs' => 'Subsanada',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 17. Respuesta
        DB::table('respuesta')->insert([
            'ID_Observacion' => $obs1,
            'ID_Version_Doc' => $verDoc3_v2,
            'Mensaje_Respuesta' => 'Se cargó el plano v2 con las rutas de evacuación y ubicación del extintor debidamente marcadas.',
            'Fecha_Respuesta' => $now->copy()->subHours(10),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 18. Citas
        DB::table('citas')->insert([
            [
                'ID_Usuarios' => $ciudadano1Id,
                'ID_Solicitud' => $solicitud1,
                'Fecha' => $now->copy()->addDays(3)->toDateString(),
                'Hora' => '10:30:00',
                'Estado_Citas' => 'Programada',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ID_Usuarios' => $ciudadano2Id,
                'ID_Solicitud' => $solicitud2,
                'Fecha' => $now->copy()->addDays(4)->toDateString(),
                'Hora' => '11:45:00',
                'Estado_Citas' => 'Confirmada',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // 19. Resoluciones
        DB::table('resoluciones')->insert([
            'ID_Solicitud' => $solicitud2,
            'ID_Supervisor' => $supervisorId,
            'Fecha_Emision_Resolucion' => $now->copy()->subHours(2),
            'Tipo_Resolucion' => 'Aprobada Favorablemente',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
