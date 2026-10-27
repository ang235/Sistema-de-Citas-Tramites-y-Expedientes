{{--
    Vista: Dashboard Operativo — Portal Privado (Interno)
    Layout: layouts.portal
    URL: /portal/dashboard
    Roles: Revisor L1/L2, Supervisor, Administrador
--}}
@extends('layouts.portal')

@section('title', 'Dashboard Operativo')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a href="{{ route('portal.dashboard') }}">Operación & Dictamen</a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Dashboard Operativo</span>
@endsection

@section('content')
<div class="space-y-8 max-w-screen-2xl">

    {{-- ══════════════════════════════════════════ PAGE HEADER ══ --}}
    <x-setc-page-header
        title="Dashboard Operativo"
        description="Resumen de carga técnica, SLAs activos y distribución de expedientes del turno en curso."
        icon="dashboard"
        compact>
        <x-setc-button variant="secondary" icon="refresh" size="sm" id="btn-refresh-dashboard">
            Actualizar
        </x-setc-button>
        <x-setc-button variant="secondary" icon="print" size="sm">
            Imprimir Reporte
        </x-setc-button>
    </x-setc-page-header>

    {{-- ══════════════════════════════════════════ KPIs ══ --}}
    <section aria-labelledby="kpis-title">
        <h2 id="kpis-title" class="sr-only">Indicadores operativos</h2>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

            <x-setc-kpi-card
                label="Revisores Activos"
                value="{{ $kpis['revisores_activos'] ?? '8 / 10' }}"
                icon="group"
                icon-color="var(--color-secondary)"
                badge="{{ $kpis['badge_revisores'] ?? '6 Libres • 2 Saturados' }}"
                badge-status="warning" />

            <x-setc-kpi-card
                label="Expedientes en Cola"
                value="{{ $kpis['expedientes_cola'] ?? 14 }}"
                icon="inbox"
                icon-color="var(--color-warning)"
                badge="{{ $kpis['badge_cola'] ?? '4 con SLA < 6 hrs' }}"
                badge-status="danger" />

            <x-setc-kpi-card
                label="Tiempo Promedio / Trámite"
                value="{{ $kpis['tiempo_promedio'] ?? '1h 42m' }}"
                icon="timer"
                icon-color="var(--color-success)"
                trend="{{ $kpis['trend_tiempo'] ?? '-18% vs semana previa' }}"
                :trend-positive="true" />

            <x-setc-kpi-card
                label="Equidad de Carga"
                value="{{ $kpis['equidad'] ?? '94%' }}"
                icon="balance"
                icon-color="var(--color-secondary)"
                trend="{{ $kpis['gini'] ?? 'Gini 0.08' }}"
                :trend-positive="true" />
        </div>
    </section>

    {{-- ══════════════════════════════════════════ TABLA DE EXPEDIENTES ══ --}}
    <section aria-labelledby="expedientes-title">
        <x-setc-card>
            <x-slot:header>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h2 id="expedientes-title" class="text-headline-sm flex items-center gap-2"
                        style="font-family: var(--font-display); color: var(--color-primary);">
                        <span class="material-symbols-outlined" style="color: var(--color-secondary);">folder_open</span>
                        Expedientes Asignados
                    </h2>

                    {{-- Filtros --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-xs pointer-events-none"
                                  style="color: var(--color-on-surface-variant);">search</span>
                            <input type="text" class="input pl-8" style="height:32px; font-size:13px; width:220px;"
                                   placeholder="Filtrar por Folio o Administrado..." aria-label="Filtrar expedientes">
                        </div>
                        <select class="select" style="height:32px; font-size:13px; width:160px;" aria-label="Tipo de trámite">
                            <option>Todos los trámites</option>
                            <option>Licencias Const.</option>
                            <option>ITSE</option>
                            <option>Impacto Ambiental</option>
                        </select>
                        <select class="select" style="height:32px; font-size:13px; width:140px;" aria-label="Estado">
                            <option>Todos los estados</option>
                            <option>En Revisión</option>
                            <option>Observado</option>
                            <option>Aprobado</option>
                        </select>
                    </div>
                </div>
            </x-slot:header>

            {{-- Tabla --}}
            <div style="padding: 0;">
                <div class="table-wrapper" style="border-radius: 0; border: none;">
                    <table class="table" aria-label="Listado de expedientes">
                        <thead>
                            <tr>
                                <th scope="col"><input type="checkbox" class="rounded" aria-label="Seleccionar todos"></th>
                                <th scope="col">Expediente</th>
                                <th scope="col">Administrado</th>
                                <th scope="col">Tipo de Trámite</th>
                                <th scope="col">Prioridad</th>
                                <th scope="col">SLA Restante</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Revisor</th>
                                <th scope="col"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expedientes ?? [] as $exp)
                                <tr>
                                    <td><input type="checkbox" class="rounded" aria-label="Seleccionar expediente {{ $exp->folio }}"></td>
                                    <td>
                                        <span class="text-code-sm font-medium"
                                              style="color: var(--color-secondary);">{{ $exp->folio }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <p class="font-semibold" style="color: var(--color-on-surface);">{{ $exp->administrado }}</p>
                                            <p class="text-body-sm" style="color: var(--color-on-surface-variant);">{{ $exp->ruc }}</p>
                                        </div>
                                    </td>
                                    <td class="text-body-sm">{{ $exp->tipo_tramite }}</td>
                                    <td>
                                        <x-setc-badge :status="$exp->prioridad_status" dot>{{ $exp->prioridad_label }}</x-setc-badge>
                                    </td>
                                    <td>
                                        <span class="text-label-md font-semibold {{ $exp->sla_critico ? 'text-danger' : '' }}"
                                              style="{{ $exp->sla_critico ? 'color: var(--color-danger-text);' : 'color: var(--color-warning-text);' }}">
                                            {{ $exp->sla_restante }}
                                        </span>
                                    </td>
                                    <td><x-setc-badge :status="$exp->estado_status">{{ $exp->estado_label }}</x-setc-badge></td>
                                    <td class="text-body-sm">{{ $exp->revisor }}</td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('portal.expedientes.show', $exp) }}"
                                               class="btn-ghost" style="height:28px; width:28px; padding:0; font-size:13px;"
                                               title="Ver expediente">
                                                <span class="material-symbols-outlined text-base">visibility</span>
                                            </a>
                                            <button class="btn-ghost" style="height:28px; width:28px; padding:0;"
                                                    title="Asignar revisor">
                                                <span class="material-symbols-outlined text-base">person_add</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                {{-- Datos de ejemplo para demo --}}
                                @foreach([
                                    ['folio'=>'SETC-2025-9921','admin'=>'Consorcio Inmobiliario Lima Sur','ruc'=>'RUC: 20491823901','tipo'=>'Habilitación Urbana L3','prio'=>'Alta','prio_s'=>'danger','sla'=>'SLA: 04h 15m','sla_c'=>true,'est'=>'En Revisión','est_s'=>'warning','rev'=>'Ing. Carlos Ruiz'],
                                    ['folio'=>'SETC-2025-9904','admin'=>'Valeria Mendoza Ramos','ruc'=>'DNI: 47291048','tipo'=>'Licencia Comercial A','prio'=>'Media','prio_s'=>'info','sla'=>'SLA: 1d 02h','sla_c'=>false,'est'=>'Pendiente','est_s'=>'neutral','rev'=>'Ing. Carlos Ruiz'],
                                    ['folio'=>'SETC-2025-9892','admin'=>'Operadora LogísticaTransPac S.A.','ruc'=>'RUC: 20559123841','tipo'=>'Edificación Industrial','prio'=>'Normal','prio_s'=>'neutral','sla'=>'SLA: 2d 18h','sla_c'=>false,'est'=>'En Revisión','est_s'=>'warning','rev'=>'Dra. Patricia Alva'],
                                ] as $demo)
                                    <tr>
                                        <td><input type="checkbox" class="rounded" aria-label="Seleccionar"></td>
                                        <td><span class="text-code-sm font-medium" style="color: var(--color-secondary);">{{ $demo['folio'] }}</span></td>
                                        <td>
                                            <p class="font-semibold text-sm" style="color: var(--color-on-surface);">{{ $demo['admin'] }}</p>
                                            <p class="text-body-sm" style="color: var(--color-on-surface-variant);">{{ $demo['ruc'] }}</p>
                                        </td>
                                        <td class="text-body-sm">{{ $demo['tipo'] }}</td>
                                        <td><x-setc-badge :status="$demo['prio_s']" dot>{{ $demo['prio'] }}</x-setc-badge></td>
                                        <td>
                                            <span class="text-label-md font-semibold"
                                                  style="color: {{ $demo['sla_c'] ? 'var(--color-danger-text)' : 'var(--color-warning-text)' }};">
                                                {{ $demo['sla'] }}
                                            </span>
                                        </td>
                                        <td><x-setc-badge :status="$demo['est_s']">{{ $demo['est'] }}</x-setc-badge></td>
                                        <td class="text-body-sm">{{ $demo['rev'] }}</td>
                                        <td>
                                            <div class="flex items-center gap-1">
                                                <button class="btn-ghost" style="height:28px; width:28px; padding:0;" title="Ver expediente">
                                                    <span class="material-symbols-outlined text-base">visibility</span>
                                                </button>
                                                <button class="btn-ghost" style="height:28px; width:28px; padding:0;" title="Asignar">
                                                    <span class="material-symbols-outlined text-base">person_add</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-between gap-4">
                    <p class="text-body-sm" style="color: var(--color-on-surface-variant);">
                        Mostrando 1 – 10 de {{ $total ?? 48 }} expedientes
                    </p>
                    <nav class="pagination" aria-label="Paginación de expedientes">
                        <button class="page-link disabled" aria-label="Página anterior" disabled>
                            <span class="material-symbols-outlined text-sm">chevron_left</span>
                        </button>
                        <button class="page-link active" aria-current="page" aria-label="Página 1">1</button>
                        <button class="page-link" aria-label="Página 2">2</button>
                        <button class="page-link" aria-label="Página 3">3</button>
                        <span class="page-link" aria-hidden="true">…</span>
                        <button class="page-link" aria-label="Última página">41</button>
                        <button class="page-link" aria-label="Página siguiente">
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </button>
                    </nav>
                </div>
            </x-slot:footer>
        </x-setc-card>
    </section>

    {{-- ══════════════════════════════════════════ NORMA SETC ══ --}}
    <div class="alert alert-info" role="note">
        <span class="material-symbols-outlined text-base shrink-0">policy</span>
        <div>
            <p class="font-semibold mb-0.5">Estándar de Interoperabilidad PIDE y Autenticación FIDO2</p>
            <p class="text-body-sm" style="color: var(--color-on-secondary-fixed); opacity: 0.8;">
                Cumplimiento normativo estricto con la R.M. 082-2024-PCM y el marco nacional de Gobernanza y Transformación Digital.
                Todas las modificaciones quedan registradas en el libro mayor de auditoría gubernamental.
            </p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // ── Refresh del dashboard ────────────────────────────────────────────────
    document.getElementById('btn-refresh-dashboard')?.addEventListener('click', () => {
        window.location.reload();
    });
</script>
@endpush
