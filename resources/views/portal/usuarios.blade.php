@extends('layouts.portal')

@section('title', 'Gestión de Usuarios y Control de Accesos')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Administración Central</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Gestión de Usuarios</span>
@endsection

@section('content')

<div x-data="{
    showCreateModal: false,
    showEditModal: false,
    editUserId: null,
    editUserName: '',
    editUserRoleId: ''
}">

{{-- ═══════════════════════════════════ HEADER ══ --}}
<div class="mb-6 space-y-2">
    <nav class="flex items-center gap-1.5 text-xs text-slate-400 font-medium">
        <a class="hover:text-slate-700 transition-colors flex items-center gap-1" href="{{ route('portal.dashboard') }}">
            <span class="material-symbols-outlined text-base">home</span>
            <span>Inicio</span>
        </a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="hover:text-slate-700 transition-colors">Administración Central</span>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-slate-800 font-semibold">Gestión de Usuarios y Permisos RBAC</span>
    </nav>

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pt-1">
        <div>
            <h1 class="font-bold text-2xl lg:text-3xl text-[#0f2744] tracking-tight" style="font-family: var(--font-display);">
                Gestión de Usuarios y Control de Accesos
            </h1>
            <p class="text-sm text-slate-500 mt-1 max-w-3xl leading-relaxed">
                Supervise el padrón de identidades institucionales, delegación jerárquica de roles (Ciudadano, Revisor L1/L2, Supervisor y Administrador) y auditoría de firmas digitales con estándar PKI.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <button @click="showCreateModal = true"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#001229] hover:bg-[#0f2744] text-white rounded-lg text-xs font-semibold shadow-xs transition-all cursor-pointer">
                <span class="material-symbols-outlined text-lg">person_add</span>
                <span>Nuevo Usuario</span>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════ KPI CARDS ══ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-500">
            <span class="text-xs font-semibold uppercase tracking-wider">Total Registrados</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">group</span>
            </div>
        </div>
        <div class="mt-3">
            <div class="font-bold text-2xl text-slate-900">{{ $kpis['total_usuarios'] ?? '2,845' }}</div>
            <div class="flex items-center gap-1.5 mt-1 text-xs text-slate-500">
                <span class="inline-flex items-center font-semibold text-emerald-600">
                    <span class="material-symbols-outlined text-sm">arrow_upward</span>+12
                </span>
                <span>nuevos esta semana</span>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-500">
            <span class="text-xs font-semibold uppercase tracking-wider">Revisores Técnicos</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">verified_user</span>
            </div>
        </div>
        <div class="mt-3">
            <div class="font-bold text-2xl text-slate-900">{{ $kpis['revisores'] ?? 48 }}</div>
            <div class="flex items-center gap-1.5 mt-1 text-xs text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="font-medium">100% firma PKI activa</span>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-500">
            <span class="text-xs font-semibold uppercase tracking-wider">Supervisores</span>
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">manage_accounts</span>
            </div>
        </div>
        <div class="mt-3">
            <div class="font-bold text-2xl text-slate-900">{{ $kpis['supervisores'] ?? 14 }}</div>
            <div class="flex items-center gap-1.5 mt-1 text-xs text-slate-500">
                <span class="material-symbols-outlined text-sm text-slate-400">domain</span>
                <span>4 sedes metropolitanas</span>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-500">
            <span class="text-xs font-semibold uppercase tracking-wider">Administradores</span>
            <div class="w-8 h-8 rounded-lg bg-[#0f2744] text-white flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">admin_panel_settings</span>
            </div>
        </div>
        <div class="mt-3">
            <div class="font-bold text-2xl text-slate-900">{{ $kpis['administradores'] ?? 3 }}</div>
            <div class="flex items-center gap-1.5 mt-1 text-xs text-amber-600 font-medium">
                <span class="material-symbols-outlined text-xs">shield</span>
                <span>2FA obligatorio</span>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-500">
            <span class="text-xs font-semibold uppercase tracking-wider">Accesos Suspendidos</span>
            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">block</span>
            </div>
        </div>
        <div class="mt-3">
            <div class="font-bold text-2xl text-rose-700">{{ $kpis['suspendidos'] ?? 7 }}</div>
            <div class="flex items-center gap-1.5 mt-1 text-xs text-slate-500">
                <span class="material-symbols-outlined text-xs text-rose-400">warning</span>
                <span>Requieren revisión</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════ FILTROS Y BÚSQUEDA ══ --}}
<form method="GET" action="{{ route('portal.usuarios') }}" class="mb-4 bg-white rounded-xl border border-slate-200/80 shadow-xs p-4 flex flex-wrap items-center gap-3 justify-between">
    <div class="flex items-center gap-3 flex-wrap">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
            <input name="buscar" value="{{ request('buscar') }}"
                   class="pl-9 pr-4 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-64"
                   placeholder="Buscar por nombre o correo institucional..." type="text">
        </div>
        <select name="rol" onchange="this.form.submit()" class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 cursor-pointer bg-white">
            <option value="">Todos los roles institucionales</option>
            @foreach($roles ?? [] as $r)
                <option value="{{ $r->Nombre }}" {{ request('rol') === $r->Nombre ? 'selected' : '' }}>{{ $r->Nombre }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold transition-colors">
            Filtrar
        </button>
        @if(request('buscar') || request('rol'))
            <a href="{{ route('portal.usuarios') }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition-colors">
                Limpiar
            </a>
        @endif
    </div>
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <span>{{ $usuarios->total() ?? 0 }} usuarios registrados</span>
    </div>
</form>

{{-- ═══════════════════════════════════ TABLA USUARIOS ══ --}}
<div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50 text-slate-400 uppercase tracking-wider font-semibold text-[11px]">
                    <th class="py-3 px-6">Usuario / Identidad</th>
                    <th class="py-3 px-4">Rol Institucional (Único)</th>
                    <th class="py-3 px-4">Sede Operativa</th>
                    <th class="py-3 px-4">PKI / Firma Digital</th>
                    <th class="py-3 px-4">Última Modificación</th>
                    <th class="py-3 px-4">Estado de Cuenta</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse ($usuarios ?? [] as $usuario)
                    <tr class="hover:bg-slate-50 transition-colors {{ !($usuario->Activo) ? 'bg-slate-50/60 opacity-80' : '' }}">
                        <td class="py-3.5 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#001229] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($usuario->Nombre ?? 'U', 0, 2)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-[#0b1c30]">{{ $usuario->Nombre }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $usuario->Email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            @php
                                $rolNombre = $usuario->role_label;
                                $rolColors = [
                                    'Administrador' => 'bg-[#001229] text-white',
                                    'Supervisor' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                    'Supervisor / Dictaminador' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                    'Revisor Técnico' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                    'Revisor L2' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                    'Revisor L1' => 'bg-sky-100 text-sky-800 border border-sky-200',
                                    'Solicitante / Ciudadano' => 'bg-slate-100 text-slate-700',
                                    'Ciudadano' => 'bg-slate-100 text-slate-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $rolColors[$rolNombre] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $rolNombre }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">{{ $usuario->Sede ?? 'Sede Central' }}</td>
                        <td class="py-3.5 px-4">
                            @if ($usuario->esRevisor() || $usuario->esSupervisor() || $usuario->esAdministrador())
                                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-semibold">
                                    <span class="material-symbols-outlined text-xs">verified</span> PKI Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] text-slate-400">
                                    <span class="material-symbols-outlined text-xs">shield</span> Estándar
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                            {{ $usuario->updated_at?->diffForHumans() ?? '—' }}
                        </td>
                        <td class="py-3.5 px-4">
                            @if ($usuario->Activo)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Deshabilitado
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button"
                                        @click="showEditModal = true; editUserId = {{ $usuario->ID_Usuarios }}; editUserName = '{{ addslashes($usuario->Nombre) }}'; editUserRoleId = '{{ $usuario->roles->first()?->ID_Rol ?? '' }}';"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-700 text-[11px] font-semibold transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-xs">manage_accounts</span> Rol
                                </button>
                                @if(auth()->id() !== $usuario->ID_Usuarios)
                                    <form method="POST" action="{{ route('portal.usuarios.destroy', $usuario->ID_Usuarios) }}" onsubmit="return confirm('¿Está seguro de {{ $usuario->Activo ? 'deshabilitar' : 'habilitar' }} este usuario? La información se conserva de forma íntegra en el sistema.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="{{ $usuario->Activo ? 'Deshabilitar cuenta' : 'Habilitar cuenta' }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-colors cursor-pointer {{ $usuario->Activo ? 'bg-slate-100 hover:bg-amber-50 hover:text-amber-800 text-slate-700' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800' }}">
                                            <span class="material-symbols-outlined text-xs">{{ $usuario->Activo ? 'block' : 'check_circle' }}</span>
                                            <span>{{ $usuario->Activo ? 'Deshabilitar' : 'Habilitar' }}</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-4xl text-slate-300">group_off</span>
                                <span class="text-xs font-medium">No se encontraron usuarios registrados con los criterios solicitados.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <span class="text-xs text-slate-500">
            {{ isset($usuarios) && method_exists($usuarios, 'total') ? $usuarios->total() : 0 }} usuarios institucionales registrados
        </span>
        @if(isset($usuarios) && $usuarios instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $usuarios->links() }}
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════ MODAL CREAR USUARIO ══ --}}
<div x-show="showCreateModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
     style="display: none;">
    <div @click.outside="showCreateModal = false"
         class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0051d5]">person_add</span>
                <h3 class="font-bold text-base text-[#0b1c30]">Registrar Nuevo Usuario Institucional</h3>
            </div>
            <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('portal.usuarios.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="nombre" class="block text-xs font-semibold text-[#0b1c30] mb-1">Nombre Completo</label>
                <input type="text" name="nombre" id="nombre" required placeholder="Ej. Carlos Pérez Gómez"
                       class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-[#0b1c30] mb-1">Correo Electrónico Institucional</label>
                <input type="email" name="email" id="email" required placeholder="carlos.perez@setc.gob.mx"
                       class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-[#0b1c30] mb-1">Contraseña Inicial</label>
                <input type="password" name="password" id="password" required minlength="8" placeholder="••••••••"
                       class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
            </div>

            <div>
                <label for="id_rol" class="block text-xs font-semibold text-[#0b1c30] mb-1">Rol Institucional (Único)</label>
                <select name="id_rol" id="id_rol" required class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5] bg-white">
                    <option value="">-- Seleccionar Rol Institucional --</option>
                    @foreach($roles ?? [] as $r)
                        <option value="{{ $r->ID_Rol }}">{{ $r->Nombre }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs text-amber-500">info</span>
                    <span>Directriz: Cada usuario posee estrictamente un único rol operativo institucional.</span>
                </p>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" @click="showCreateModal = false"
                        class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold transition-colors">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════ MODAL EDITAR ROL ══ --}}
<div x-show="showEditModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
     style="display: none;">
    <div @click.outside="showEditModal = false"
         class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0051d5]">manage_accounts</span>
                <h3 class="font-bold text-base text-[#0b1c30]">Modificar Rol Institucional</h3>
            </div>
            <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <p class="text-xs text-slate-500">
            Usuario seleccionado: <strong class="text-[#0b1c30]" x-text="editUserName"></strong>
        </p>

        <form :action="'{{ url('portal/usuarios') }}/' + editUserId + '/roles'" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="edit_id_rol" class="block text-xs font-semibold text-[#0b1c30] mb-2">Seleccione el rol institucional (Único)</label>
                <select name="id_rol" id="edit_id_rol" x-model="editUserRoleId" required class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5] bg-white">
                    <option value="">-- Seleccionar Rol --</option>
                    @foreach($roles ?? [] as $r)
                        <option value="{{ $r->ID_Rol }}">{{ $r->Nombre }}</option>
                    @endforeach
                </select>
                <div class="mt-2.5 p-2.5 rounded-lg bg-amber-50 border border-amber-200/80 text-[11px] text-amber-800 flex items-start gap-1.5">
                    <span class="material-symbols-outlined text-sm text-amber-600 shrink-0 mt-0.5">warning</span>
                    <span>No es posible asignar múltiples roles a un mismo usuario. Esta acción reasignará su rol activo.</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" @click="showEditModal = false"
                        class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold transition-colors">
                    Actualizar Rol
                </button>
            </div>
        </form>
    </div>
</div>

</div> {{-- Cierre de x-data --}}
@endsection
