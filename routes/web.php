<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\BitacoraController;
use App\Http\Controllers\Admin\RequisitoController;
use App\Http\Controllers\Admin\TipoTramiteController;
use App\Http\Controllers\Admin\UsuarioAdminController;
use App\Http\Controllers\Admin\VersionTramiteController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Ciudadano\CitaCiudadanoController;
use App\Http\Controllers\Ciudadano\ExpedienteCiudadanoController;
use App\Http\Controllers\Ciudadano\SolicitudCiudadanoController;
use App\Http\Controllers\Ciudadano\TramiteCiudadanoController;
use App\Http\Controllers\Operativo\AsignacionController;
use App\Http\Controllers\Operativo\CitaOperativaController;
use App\Http\Controllers\Operativo\DashboardOperativoController;
use App\Http\Controllers\Operativo\MesaDictamenController;
use App\Http\Controllers\Operativo\ResolucionController;
use Illuminate\Support\Facades\Route;

// Rutas Públicas & Portal Ciudadano
Route::get('/', [TramiteCiudadanoController::class, 'home'])->name('home');
Route::get('/ayuda', fn () => view('public.ayuda.index'))->name('ayuda.index');

// Catálogo de trámites
Route::get('/tramites', [TramiteCiudadanoController::class, 'index'])->name('tramites.index');
Route::get('/tramites/{id}', [TramiteCiudadanoController::class, 'show'])->name('tramites.show');

// Rutas protegidas para Ciudadanos autenticados
Route::middleware('auth')->group(function (): void {
    // Expedientes y Seguimiento (sólo trámites propios)
    Route::get('/expedientes', [ExpedienteCiudadanoController::class, 'index'])->name('expedientes.index');
    Route::get('/expedientes/{codigoFolio}', [ExpedienteCiudadanoController::class, 'show'])->name('expedientes.show');
    Route::post('/expedientes/observacion/{idObservacion}/solventar', [ExpedienteCiudadanoController::class, 'solventarObservacion'])->name('expedientes.solventar');

    // Inicio de solicitud (trámite)
    Route::get('/tramites/{idTipoTramite}/iniciar', [SolicitudCiudadanoController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes', [SolicitudCiudadanoController::class, 'store'])->name('solicitudes.store');

    // Citas Ciudadanas
    Route::get('/citas', [CitaCiudadanoController::class, 'index'])->name('citas.index');
    Route::get('/citas/agendar', [CitaCiudadanoController::class, 'create'])->name('citas.create');
    Route::post('/citas', [CitaCiudadanoController::class, 'store'])->name('citas.store');
    Route::get('/citas/{idCita}/pase', [CitaCiudadanoController::class, 'paseDigital'])->name('citas.pase');
    Route::post('/citas/{idCita}/cancelar', [CitaCiudadanoController::class, 'cancelar'])->name('citas.cancelar');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Autenticación & Cuenta
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Portal Interno — Enterprise / Operativo & Administración
|--------------------------------------------------------------------------
*/
Route::prefix('portal')->name('portal.')->middleware('auth')->group(function (): void {
    // Dashboard y KPIs
    Route::get('/dashboard', [DashboardOperativoController::class, 'index'])->name('dashboard');
    Route::get('/metricas', [DashboardOperativoController::class, 'index'])->name('metricas');

    // Matriz de Asignaciones y Carga de Trabajo
    Route::get('/matriz-cargas', [AsignacionController::class, 'index'])->name('matriz-cargas');
    Route::post('/asignaciones', [AsignacionController::class, 'store'])->name('asignaciones.store');
    Route::post('/asignaciones/auto', [AsignacionController::class, 'autoAsignar'])->name('asignaciones.auto');

    // Mesa de Dictamen & Evaluación de Expedientes
    Route::get('/mesa-dictamen', [MesaDictamenController::class, 'index'])->name('mesa-dictamen');
    Route::get('/expedientes', [MesaDictamenController::class, 'index'])->name('expedientes');
    Route::get('/expedientes/{idSolicitud}', [MesaDictamenController::class, 'show'])->name('expedientes.show');
    Route::get('/expedientes/{idSolicitud}/evaluar', [MesaDictamenController::class, 'show'])->name('expedientes.evaluar');
    Route::post('/documentos/{idDocumento}/evaluar', [MesaDictamenController::class, 'evaluarDocumento'])->name('documentos.evaluar');
    Route::post('/asignaciones/{idAsignacion}/revision', [MesaDictamenController::class, 'registrarRevision'])->name('revisiones.store');

    // Resoluciones
    Route::post('/expedientes/{idSolicitud}/resolucion', [ResolucionController::class, 'store'])->name('resoluciones.store');

    // Citas Operativas
    Route::get('/citas', [CitaOperativaController::class, 'index'])->name('citas');
    Route::patch('/citas/{idCita}/estado', [CitaOperativaController::class, 'actualizarEstado'])->name('citas.estado');

    // Configuración Normativa TUPA & Catálogos
    Route::get('/parametros', [TipoTramiteController::class, 'index'])->name('parametros');
    Route::post('/tipo-tramites', [TipoTramiteController::class, 'store'])->name('tipo-tramites.store');
    Route::put('/tipo-tramites/{id}', [TipoTramiteController::class, 'update'])->name('tipo-tramites.update');
    Route::delete('/tipo-tramites/{id}', [TipoTramiteController::class, 'destroy'])->name('tipo-tramites.destroy');

    Route::post('/version-tramites', [VersionTramiteController::class, 'store'])->name('version-tramites.store');
    Route::patch('/version-tramites/{id}/activar', [VersionTramiteController::class, 'activarVigencia'])->name('version-tramites.activar');
    Route::delete('/version-tramites/{id}', [VersionTramiteController::class, 'destroy'])->name('version-tramites.destroy');

    Route::post('/requisitos', [RequisitoController::class, 'store'])->name('requisitos.store');
    Route::delete('/requisitos/{id}', [RequisitoController::class, 'destroy'])->name('requisitos.destroy');

    // Usuarios y Roles
    Route::get('/usuarios', [UsuarioAdminController::class, 'index'])->name('usuarios');
    Route::post('/usuarios', [UsuarioAdminController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{id}/roles', [UsuarioAdminController::class, 'updateRoles'])->name('usuarios.roles');
    Route::delete('/usuarios/{id}', [UsuarioAdminController::class, 'destroy'])->name('usuarios.destroy');

    // Auditoría y Bitácora
    Route::get('/auditoria', [BitacoraController::class, 'index'])->name('auditoria');
});
