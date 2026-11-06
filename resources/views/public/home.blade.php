@extends('layouts.public')

@section('title', 'Ventanilla Única Digital')
@section('meta_description', 'Portal ciudadano de SETC: inicia solicitudes telemáticas, consulta tus expedientes y agenda citas presenciales o virtuales.')

@section('breadcrumb')
    <span class="hover:text-on-surface cursor-pointer">SETC</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="hover:text-on-surface cursor-pointer">Servicios al Ciudadano</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="text-on-surface font-semibold">Ventanilla Digital</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-8 w-full space-y-12">

    {{-- ══════════════════════════════════════════ HERO & CONTEXTUAL WELCOME ══ --}}
    <section class="relative overflow-hidden rounded-xl bg-surface-container-low shadow-sm p-8 lg:p-10">
        <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-secondary-container/10 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-4xl space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container-lowest shadow-sm">
                <span class="inline-block w-2 h-2 rounded-full bg-tertiary-fixed-variant animate-pulse"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Ventanilla Única Digital Gov-ID</span>
            </div>

            <div class="space-y-2">
                <h1 class="font-headline-lg text-headline-lg lg:font-headline-xl lg:text-headline-xl text-primary font-bold tracking-tight">
                    @auth
                        Hola de nuevo, {{ auth()->user()->Nombre ?? 'Mariana' }}.
                    @else
                        Bienvenido a SETC.
                    @endauth
                    <span class="text-secondary font-semibold">¿Qué gestión deseas realizar hoy?</span>
                </h1>
                <p class="font-body-lg text-body-lg text-on-surface-variant">
                    Acompañamos cada etapa de tus trámites institucionales con transparencia, sin intermediarios y con resolución ágil.
                </p>
            </div>

            {{-- Buscador Principal --}}
            <div class="relative max-w-2xl pt-2">
                <form action="{{ route('tramites.index') }}" method="GET" class="relative flex items-center shadow-sm rounded-lg bg-surface-container-lowest overflow-hidden transition-all focus-within:shadow-md">
                    <span class="material-symbols-outlined text-secondary ml-4 mr-2 text-xl select-none">search</span>
                    <input type="text"
                           name="buscar"
                           value="{{ request('buscar') }}"
                           class="w-full py-3.5 pr-4 bg-transparent font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant focus:outline-none"
                           placeholder="Buscar por trámite, norma legal o palabra clave (ej. Inspección ITSE, Licencia)..." />
                    <button type="submit" class="m-1.5 px-4 py-2 bg-primary text-on-primary rounded-lg font-label-md text-label-md hover:bg-primary-container transition-colors shrink-0">
                        Buscar
                    </button>
                </form>

                {{-- Chips / Quick Tags --}}
                <div class="flex items-center gap-2 mt-3 flex-wrap font-label-sm text-label-sm">
                    <span class="text-on-surface-variant font-medium">Búsquedas habituales:</span>
                    <a href="{{ route('tramites.index', ['buscar' => 'Licencia']) }}" class="px-2.5 py-1 rounded-full bg-surface-container-lowest text-on-surface hover:bg-surface-container transition-colors shadow-sm">
                        Licencia de Funcionamiento
                    </a>
                    <a href="{{ route('tramites.index', ['buscar' => 'ITSE']) }}" class="px-2.5 py-1 rounded-full bg-surface-container-lowest text-on-surface hover:bg-surface-container transition-colors shadow-sm">
                        Inspección Técnica ITSE
                    </a>
                    <a href="{{ route('tramites.index', ['buscar' => 'Ambiental']) }}" class="px-2.5 py-1 rounded-full bg-surface-container-lowest text-on-surface hover:bg-surface-container transition-colors shadow-sm">
                        Permiso Ambiental
                    </a>
                    <a href="{{ route('tramites.index', ['buscar' => 'Copia']) }}" class="px-2.5 py-1 rounded-full bg-surface-container-lowest text-on-surface hover:bg-surface-container transition-colors shadow-sm">
                        Copia Certificada
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════ QUICK ACCESS HUB CARDS ══ --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-headline-sm text-headline-sm text-primary font-bold">Trámites destacados y recomendados</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Inicia solicitudes de alta demanda con validación paramétrica inmediata.</p>
            </div>
            <a class="hidden sm:inline-flex items-center gap-1 font-label-md text-label-md text-secondary hover:underline font-semibold" href="{{ route('tramites.index') }}">
                Ver catálogo completo
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1 -->
            <a href="{{ route('tramites.index', ['buscar' => 'Comercial']) }}" class="group bg-surface-container-lowest p-5 rounded-xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-primary group-hover:bg-secondary group-hover:text-on-secondary transition-colors">
                            <span class="material-symbols-outlined">store</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-surface-container-low text-secondary font-label-sm text-label-sm font-semibold">
                            100% digital
                        </span>
                    </div>
                    <div>
                        <h3 class="font-headline-sm text-body-lg font-bold text-on-surface group-hover:text-secondary transition-colors">
                            Licencia Comercial
                        </h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2 mt-1">
                            Autorización municipal definitiva para establecimientos comerciales y profesionales.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-2 flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">schedule</span>
                        Respuesta en 48h
                    </span>
                    <span class="material-symbols-outlined text-lg text-primary opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all">chevron_right</span>
                </div>
            </a>

            <!-- Card 2 -->
            <a href="{{ route('tramites.index', ['buscar' => 'ITSE']) }}" class="group bg-surface-container-lowest p-5 rounded-xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-primary group-hover:bg-secondary group-hover:text-on-secondary transition-colors">
                            <span class="material-symbols-outlined">verified_user</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-surface-container-low text-tertiary-fixed-variant font-label-sm text-label-sm font-semibold">
                            Pre-calificado
                        </span>
                    </div>
                    <div>
                        <h3 class="font-headline-sm text-body-lg font-bold text-on-surface group-hover:text-secondary transition-colors">
                            Inspección ITSE
                        </h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2 mt-1">
                            Certificado de seguridad en edificaciones para riesgo bajo y medio con visita programada.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-2 flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">event_available</span>
                        Cita en 72h
                    </span>
                    <span class="material-symbols-outlined text-lg text-primary opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all">chevron_right</span>
                </div>
            </a>

            <!-- Card 3 -->
            <a href="{{ route('tramites.index', ['buscar' => 'Ambiental']) }}" class="group bg-surface-container-lowest p-5 rounded-xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-primary group-hover:bg-secondary group-hover:text-on-secondary transition-colors">
                            <span class="material-symbols-outlined">nature</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-surface-container-low text-secondary font-label-sm text-label-sm font-semibold">
                            Normativa 2025
                        </span>
                    </div>
                    <div>
                        <h3 class="font-headline-sm text-body-lg font-bold text-on-surface group-hover:text-secondary transition-colors">
                            Permiso Ambiental
                        </h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2 mt-1">
                            Evaluación técnica simplificada y registro de manejo de residuos no peligrosos.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-2 flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">schedule</span>
                        Respuesta en 5 días
                    </span>
                    <span class="material-symbols-outlined text-lg text-primary opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all">chevron_right</span>
                </div>
            </a>

            <!-- Card 4 -->
            <a href="{{ route('tramites.index', ['buscar' => 'Certificada']) }}" class="group bg-surface-container-lowest p-5 rounded-xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-primary group-hover:bg-secondary group-hover:text-on-secondary transition-colors">
                            <span class="material-symbols-outlined">assignment</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-surface-container-low text-secondary font-label-sm text-label-sm font-semibold">
                            Inmediato
                        </span>
                    </div>
                    <div>
                        <h3 class="font-headline-sm text-body-lg font-bold text-on-surface group-hover:text-secondary transition-colors">
                            Copias Certificadas
                        </h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2 mt-1">
                            Expedición con firma electrónica y código QR de validez nacional instantánea.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-2 flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">bolt</span>
                        Descarga al instante
                    </span>
                    <span class="material-symbols-outlined text-lg text-primary opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all">chevron_right</span>
                </div>
            </a>
        </div>
    </section>

    {{-- ══════════════════════════════════════════ ACTIVE DOSSIER & FILE INTAKE WORKSPACE ══ --}}
    <section class="bg-surface-container-lowest rounded-xl shadow-sm p-6 lg:p-10 space-y-8">
        <!-- Transaction Header & Meta Banner -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-surface-container">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm font-semibold">
                        Expediente en curso
                    </span>
                    <span class="font-code-sm text-code-sm text-on-surface-variant">Folio: EXP-2025-08492-CD</span>
                </div>
                <h2 class="font-headline-md text-headline-md text-primary font-bold">
                    Solicitud de Licencia de Funcionamiento Comercial (Tipo A)
                </h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Giro comercial: Consultoría y Servicios Digitales • Superficie declarada: 85.00 m²
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('ayuda.index') }}" class="px-3.5 py-2 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">help</span>
                    Guía de requisitos
                </a>
                <button onclick="window.print();" class="px-3.5 py-2 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">print</span>
                    Resumen
                </button>
            </div>
        </div>

        <!-- Progressive Stepper -->
        <div class="bg-surface-container-low p-5 rounded-xl space-y-3">
            <div class="flex items-center justify-between text-on-surface font-label-md text-label-md">
                <span class="font-bold flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-secondary text-on-secondary flex items-center justify-center text-xs">2</span>
                    Paso 2 de 4: Carga de Documentación Obligatoria
                </span>
                <span class="text-on-surface-variant font-medium">Progreso general: 50%</span>
            </div>
            <div class="w-full bg-surface-container-highest rounded-full h-2 overflow-hidden">
                <div class="bg-secondary h-2 rounded-full transition-all duration-500" style="width: 50%;"></div>
            </div>
            <!-- Step Markers -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 pt-2 text-left font-label-sm text-label-sm">
                <div class="flex items-center gap-1.5 text-on-tertiary-container font-semibold">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    <span>1. Datos Generales</span>
                </div>
                <div class="flex items-center gap-1.5 text-secondary font-bold">
                    <span class="w-4 h-4 rounded-full bg-secondary text-on-secondary flex items-center justify-center text-[10px]">2</span>
                    <span>2. Documentación</span>
                </div>
                <div class="flex items-center gap-1.5 text-on-surface-variant font-medium">
                    <span class="w-4 h-4 rounded-full bg-surface-container-high flex items-center justify-center text-[10px]">3</span>
                    <span>3. Declaración y Firma</span>
                </div>
                <div class="flex items-center gap-1.5 text-on-surface-variant font-medium">
                    <span class="w-4 h-4 rounded-full bg-surface-container-high flex items-center justify-center text-[10px]">4</span>
                    <span>4. Pago y Constancia</span>
                </div>
            </div>
        </div>

        <!-- Humanized Drag & Drop Area -->
        <div class="group relative rounded-xl p-8 lg:p-10 text-center transition-all bg-surface-container-low hover:bg-surface-container cursor-pointer flex flex-col items-center justify-center border-2 border-dashed border-outline-variant hover:border-secondary">
            <div class="w-16 h-16 rounded-full bg-surface-container-lowest shadow-sm flex items-center justify-center text-secondary group-hover:scale-105 transition-transform mb-4">
                <span class="material-symbols-outlined text-3xl">cloud_upload</span>
            </div>
            <h3 class="font-headline-sm text-headline-sm font-bold text-primary mb-1">
                Arrastra tus documentos aquí o presiona para examinar tus archivos
            </h3>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-lg mb-3">
                Detectamos automáticamente a qué requisito corresponde cada archivo gracias a nuestra verificación inteligente.
            </p>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container-lowest text-on-surface-variant font-label-sm text-label-sm shadow-sm">
                <span class="material-symbols-outlined text-sm text-secondary">info</span>
                Formatos PDF, JPG o PNG • Hasta 15 MB por archivo • Optimización automática incluida
            </div>
            <input aria-label="Cargar archivos de expediente" class="absolute inset-0 opacity-0 cursor-pointer" multiple="" type="file"/>
        </div>

        <!-- Interactive Required Documents Checklist -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="font-headline-sm text-body-lg font-bold text-primary flex items-center gap-2">
                    <span>Documentos obligatorios para este trámite</span>
                    <span class="px-2 py-0.5 rounded-full bg-surface-container font-label-sm text-label-sm font-medium text-on-surface-variant">3 de 4 completados o en proceso</span>
                </h4>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Revisión preliminar automatizada</span>
            </div>
            <div class="space-y-3">
                <!-- Doc 1: DNI (Cargado con éxito) -->
                <div class="p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="relative w-12 h-14 rounded-lg bg-surface-container-lowest shadow-sm flex items-center justify-center text-secondary shrink-0 overflow-hidden">
                            <span class="material-symbols-outlined text-2xl">picture_as_pdf</span>
                            <span class="absolute bottom-0 inset-x-0 bg-primary text-[9px] text-on-primary text-center font-bold">PDF</span>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-label-md text-label-md font-bold text-on-surface">1. Documento de Identidad del Titular (DNI / Cédula)</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed-variant font-label-sm text-label-sm font-semibold">
                                    <span class="material-symbols-outlined text-xs">check</span>
                                    Cargado con éxito
                                </span>
                            </div>
                            <div class="flex items-center gap-3 font-code-sm text-code-sm text-on-surface-variant">
                                <span>dni_representante_2025.pdf</span>
                                <span>•</span>
                                <span>1.8 MB</span>
                                <span>•</span>
                                <span class="text-on-tertiary-container font-medium">Validado contra Registro Nacional</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                        <button class="px-3 py-1.5 rounded-lg bg-surface-container-lowest hover:bg-surface-container-high text-on-surface font-label-md text-label-md shadow-sm transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">visibility</span>
                            Ver
                        </button>
                        <button class="px-3 py-1.5 rounded-lg bg-surface-container-lowest hover:bg-surface-container-high text-on-surface font-label-md text-label-md shadow-sm transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">sync</span>
                            Reemplazar
                        </button>
                    </div>
                </div>

                <!-- Doc 2: Comprobante de Domicilio Fiscal (Pendiente de adjuntar) -->
                <div class="p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-14 rounded-lg bg-surface-container-high flex items-center justify-center text-on-surface-variant shrink-0">
                            <span class="material-symbols-outlined text-2xl">receipt_long</span>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-label-md text-label-md font-bold text-on-surface">2. Comprobante de Domicilio Fiscal</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm font-semibold">
                                    <span class="material-symbols-outlined text-xs">pending</span>
                                    Pendiente de adjuntar
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 font-body-sm text-body-sm text-on-surface-variant">
                                <span class="material-symbols-outlined text-sm text-secondary">info</span>
                                <span>Recibo de luz, agua o contrato de arrendamiento (no mayor a 3 meses de antigüedad).</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                        <label class="cursor-pointer px-4 py-2 rounded-lg bg-secondary text-on-secondary font-label-md text-label-md hover:bg-secondary-container transition-colors flex items-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-base">upload_file</span>
                            Adjuntar archivo
                            <input accept=".pdf,.png,.jpg,.jpeg" class="hidden" type="file"/>
                        </label>
                    </div>
                </div>

                <!-- Doc 3: Plano de Distribución Visado (En validación OCR 85%) -->
                <div class="p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="relative w-12 h-14 rounded-lg bg-surface-container-lowest shadow-sm flex items-center justify-center text-secondary shrink-0 overflow-hidden">
                            <span class="material-symbols-outlined text-2xl">architecture</span>
                            <span class="absolute bottom-0 inset-x-0 bg-secondary text-[9px] text-on-secondary text-center font-bold">DWG-PDF</span>
                        </div>
                        <div class="space-y-2 w-full max-w-xl">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-label-md text-label-md font-bold text-on-surface">3. Plano de Distribución y Evacuación</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm font-semibold">
                                    <span class="material-symbols-outlined text-xs animate-spin">refresh</span>
                                    En validación automática OCR (85%)
                                </span>
                            </div>
                            <!-- Tiny Progress Bar -->
                            <div class="space-y-1">
                                <div class="w-full bg-surface-container-highest rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-secondary h-1.5 rounded-full transition-all duration-300" style="width: 85%;"></div>
                                </div>
                                <p class="font-code-sm text-code-sm text-on-surface-variant">plano_arquitectura_local_v2.pdf (12.4 MB) • Comprobando sellos colegiados...</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                        <button class="px-3 py-1.5 rounded-lg bg-surface-container-lowest hover:bg-surface-container-high text-on-surface-variant font-label-md text-label-md transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">pause</span>
                            Pausar
                        </button>
                    </div>
                </div>

                <!-- Doc 4: Declaración Jurada de Cumplimiento -->
                <div class="p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-14 rounded-lg bg-surface-container-high flex items-center justify-center text-on-surface-variant shrink-0">
                            <span class="material-symbols-outlined text-2xl">draw</span>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-label-md text-label-md font-bold text-on-surface">4. Declaración Jurada de Cumplimiento de Condiciones</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm font-semibold">
                                    Listo para firma
                                </span>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">
                                Aceptación de estándares de zonificación, compatibilidad de uso y seguridad no estructural.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                        <a class="px-3 py-1.5 rounded-lg bg-surface-container-lowest hover:bg-surface-container-high text-on-surface font-label-md text-label-md shadow-sm transition-colors flex items-center gap-1" href="#">
                            <span class="material-symbols-outlined text-base">download</span>
                            Descargar plantilla
                        </a>
                        <button class="px-3 py-1.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors flex items-center gap-1 shadow-sm">
                            <span class="material-symbols-outlined text-base">fingerprint</span>
                            Firmar en línea
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empathetic Citizen Guidance Note / Tooltip Alert -->
        <div class="p-4 rounded-xl bg-surface-container-low flex items-start gap-3.5">
            <span class="material-symbols-outlined text-secondary text-2xl shrink-0 mt-0.5">lightbulb</span>
            <div class="space-y-1">
                <span class="font-label-md text-label-md font-bold text-primary">¿No cuentas con certificado o firma digital instalada?</span>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    No te preocupes. Puedes validar este documento y la declaración jurada utilizando únicamente tu <strong>Clave Digital Ciudadana (Gov-ID)</strong> mediante un código de seguridad enviado a tu móvil en el siguiente paso.
                </p>
            </div>
        </div>

        <!-- Bottom Action Bar & Institutional Guarantee -->
        <div class="pt-6 space-y-6">
            <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
                <button class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-base">save</span>
                    Guardar borrador para continuar después
                </button>
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <div class="hidden md:flex flex-col text-right font-label-sm text-label-sm">
                        <span class="text-on-surface font-bold">3 de 4 documentos listos</span>
                        <span class="text-on-surface-variant">Falta subir recibo fiscal</span>
                    </div>
                    <a href="{{ route('citas.create') }}" class="w-full sm:w-auto px-6 py-3 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md font-semibold transition-all flex items-center justify-center gap-2 shadow-sm">
                        <span>Continuar a Revisión y Firma</span>
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </a>
                </div>
            </div>

            <!-- Security & Legal Footer -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-surface-container font-body-sm text-body-sm text-on-surface-variant">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-tertiary-fixed-variant">lock</span>
                    <span>Tus datos y expedientes están protegidos bajo cifrado de grado bancario (TLS 1.3 y AES-256).</span>
                </div>
                <div class="flex items-center gap-4 font-label-sm text-label-sm">
                    <a href="{{ route('ayuda.index') }}" class="hover:underline cursor-pointer">Ley de Protección de Datos</a>
                    <span>•</span>
                    <a href="{{ route('ayuda.index') }}" class="hover:underline cursor-pointer">Soporte SETC 24/7</a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
