<?php

declare(strict_types=1);

use App\Models\Cita;
use App\Models\Resolucion;
use App\Models\Rol;
use App\Models\Solicitud;
use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Models\VersionTramite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

uses(RefreshDatabase::class);

test('el pase digital web incluye el codigo QR real y el boton para descargar PDF', function () {
    $rolCiudadano = Rol::create(['Nombre' => 'Ciudadano']);
    $ciudadano = Usuario::create([
        'Nombre' => 'Ciudadano Test',
        'Email' => 'ciudadano@test.com',
        'Contrasena' => bcrypt('password'),
    ]);
    $ciudadano->roles()->attach($rolCiudadano->ID_Rol);

    $cita = Cita::create([
        'ID_Usuarios' => $ciudadano->ID_Usuarios,
        'Fecha' => now()->addDays(2)->toDateString(),
        'Hora' => '10:00:00',
        'Estado_Citas' => 'Programada',
    ]);

    $response = $this->actingAs($ciudadano)->get(route('citas.pase', ['idCita' => $cita->ID_Citas]));

    $response->assertOk();
    $response->assertSee('Descargar PDF');
    $response->assertSee(route('citas.pase.pdf', $cita->ID_Citas));
    // Verifica que el QR se renderiza como SVG
    $response->assertSee('<svg', false);
});

test('un ciudadano puede descargar el PDF de su pase de cita con Spatie PDF', function () {
    Pdf::fake();

    $rolCiudadano = Rol::create(['Nombre' => 'Ciudadano']);
    $ciudadano = Usuario::create([
        'Nombre' => 'Carlos Mendoza',
        'Email' => 'carlos@test.com',
        'Contrasena' => bcrypt('password'),
    ]);
    $ciudadano->roles()->attach($rolCiudadano->ID_Rol);

    $cita = Cita::create([
        'ID_Usuarios' => $ciudadano->ID_Usuarios,
        'Fecha' => now()->addDays(2)->toDateString(),
        'Hora' => '09:30:00',
        'Estado_Citas' => 'Programada',
    ]);

    $response = $this->actingAs($ciudadano)->get(route('citas.pase.pdf', ['idCita' => $cita->ID_Citas]));

    $response->assertOk();
    Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) use ($cita) {
        return $pdf->viewName === 'pdf.pase-cita' &&
               $pdf->viewData['cita']->ID_Citas === $cita->ID_Citas;
    });
});

test('un supervisor puede descargar el PDF de resolucion formal con Spatie PDF', function () {
    Pdf::fake();

    $rolSupervisor = Rol::create(['Nombre' => 'Supervisor']);
    $supervisor = Usuario::create([
        'Nombre' => 'Supervisor General',
        'Email' => 'supervisor@test.com',
        'Contrasena' => bcrypt('password'),
    ]);
    $supervisor->roles()->attach($rolSupervisor->ID_Rol);

    $tipoTramite = TipoTramite::create([
        'Nombre' => 'Licencia Comercial',
        'Descripcion' => 'Trámite test',
        'Estado_Tipo_Tr' => 'Activo',
    ]);

    $versionTramite = VersionTramite::create([
        'ID_Tipo_Tramite' => $tipoTramite->ID_Tipo_Tramite,
        'No_Version' => '1.0',
        'Estado_Ver_Tr' => 'Vigente',
        'Fecha_Inicio_Vig' => now(),
    ]);

    $solicitud = Solicitud::create([
        'ID_Solicitante' => $supervisor->ID_Usuarios,
        'ID_Version_Tramite' => $versionTramite->ID_Version_Tramite,
        'Estado_Solicitud' => 'Aprobado',
    ]);

    $resolucion = Resolucion::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Supervisor' => $supervisor->ID_Usuarios,
        'Tipo_Resolucion' => 'Favorable',
        'Fecha_Emision_Resolucion' => now(),
    ]);

    $response = $this->actingAs($supervisor)->get(route('portal.resoluciones.pdf', ['idSolicitud' => $solicitud->ID_Solicitud]));

    $response->assertOk();
    Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) use ($solicitud) {
        return $pdf->viewName === 'pdf.resolucion' &&
               $pdf->viewData['solicitud']->ID_Solicitud === $solicitud->ID_Solicitud;
    });
});

test('un revisor o administrador puede auditar y descargar el PDF de pase de cualquier cita', function () {
    Pdf::fake();

    $rolRevisor = Rol::create(['Nombre' => 'Revisor Técnico']);
    $revisor = Usuario::create([
        'Nombre' => 'Revisor Auditor',
        'Email' => 'revisor@test.com',
        'Contrasena' => bcrypt('password'),
    ]);
    $revisor->roles()->attach($rolRevisor->ID_Rol);

    $ciudadano = Usuario::create([
        'Nombre' => 'Ciudadano Solicitante',
        'Email' => 'ciudadano2@test.com',
        'Contrasena' => bcrypt('password'),
    ]);

    $cita = Cita::create([
        'ID_Usuarios' => $ciudadano->ID_Usuarios,
        'Fecha' => now()->addDays(1)->toDateString(),
        'Hora' => '11:00:00',
        'Estado_Citas' => 'Programada',
    ]);

    $response = $this->actingAs($revisor)->get(route('citas.pase.pdf', ['idCita' => $cita->ID_Citas]));

    $response->assertOk();
    Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) use ($cita) {
        return $pdf->viewName === 'pdf.pase-cita' &&
               $pdf->viewData['cita']->ID_Citas === $cita->ID_Citas;
    });
});
