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
    editUserRoles: []
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
<div class="mb-4 bg-white rounded-xl border border-slate-200/80 shadow-xs p-4 flex flex-wrap items-center gap-3 justify-between">
    <div class="flex items-center gap-3 flex-wrap">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
            <input class="pl-9 pr-4 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-64"
                   placeholder="Buscar por DNI, RUC, nombre o correo institucional..." type="text">
        </div>
        <select class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 cursor-pointer bg-white">
            <option>Todos los roles</option>
            <option>Ciudadano</option>
            <option>Revisor L1</option>
            <option>Revisor L2</option>
            <option>Supervisor</option>
            <option>Administrador</option>
        </select>
        <select class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 cursor-pointer bg-white">
            <option>Todos los estados</option>
            <option>Activo</option>
            <option>Suspendido</option>
            <option>Sin verificar</option>
        </select>
    </div>
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <span>{{ $usuarios->total() ?? 0 }} registros</span>
    </div>
</div>

{{-- ═══════════════════════════════════ TABLA USUARIOS ══ --}}
<div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50 text-slate-400 uppercase tracking-wider font-semibold text-[11px]">
                    <th class="py-3 px-6">Usuario / Identidad</th>
                    <th class="py-3 px-4">Rol Institucional</th>
                    <th class="py-3 px-4">Sede</th>
                    <th class="py-3 px-4">PKI / 2FA</th>
                    <th class="py-3 px-4">Último Acceso</th>
                    <th class="py-3 px-4">Estado</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse ($usuarios ?? [] as $usuario)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3.5 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#0f2744] text-white flex items-center justify-center font-bold text-xs shrink-0">
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
                                $rolColors = [
                                    'Administrador' => 'bg-[#0f2744] text-white',
                                    'Supervisor' => 'bg-blue-100 text-blue-800',
                                    'Revisor L2' => 'bg-indigo-100 text-indigo-800',
                                    'Revisor L1' => 'bg-sky-100 text-sky-800',
                                    'Ciudadano' => 'bg-slate-100 text-slate-700',
                                ];
                                $rol = $usuario->Rol ?? 'Ciudadano';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $rolColors[$rol] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $rol }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">{{ $usuario->Sede ?? 'Central' }}</td>
                        <td class="py-3.5 px-4">
                            @if ($usuario->pki_activo ?? false)
                                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-semibold">
                                    <span class="material-symbols-outlined text-xs">verified</span> PKI
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] text-slate-400">
                                    <span class="material-symbols-outlined text-xs">cancel</span> Sin PKI
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                            {{ $usuario->updated_at?->diffForHumans() ?? '—' }}
                        </td>
                        <td class="py-3.5 px-4">
                            @if ($usuario->Activo ?? true)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Suspendido
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button"
                                        @click="showEditModal = true; editUserId = {{ $usuario->ID_Usuarios }}; editUserName = '{{ addslashes($usuario->Nombre) }}'; editUserRoles = {{ $usuario->roles->pluck('ID_Rol')->toJson() }};"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 text-[11px] font-semibold transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-xs">edit</span> Roles
                                </button>
                                @if(auth()->id() !== $usuario->ID_Usuarios)
                                    <form method="POST" action="{{ route('portal.usuarios.destroy', $usuario->ID_Usuarios) }}" onsubmit="return confirm('¿Está seguro de eliminar o deshabilitar este usuario del sistema?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Eliminar usuario"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-600 text-[11px] font-semibold transition-colors cursor-pointer">
                                            <span class="material-symbols-outlined text-xs">delete</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    {{-- Filas de demostración --}}
                    @foreach([
                        ['AR','Arturo Salas García','arturo@setc.gob.pe','Revisor L2','Central','Activo'],
                        ['CG','Carmen González Ruiz','carmen@setc.gob.pe','Supervisor','Norte','Activo'],
                        ['MH','Miguel Herrera','miguel@setc.gob.pe','Revisor L1','Sur','Activo'],
                        ['LP','Lorena Parra Díaz','lorena@setc.gob.pe','Administrador','Central','Activo'],
                        ['JM','Juan Mendoza','juan@setc.gob.pe','Ciudadano','—','Suspendido'],
                    ] as $u)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#0f2744] text-white flex items-center justify-center font-bold text-xs shrink-0">{{ $u[0] }}</div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-[#0b1c30]">{{ $u[1] }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $u[2] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $rolColors2 = ['Administrador'=>'bg-[#0f2744] text-white','Supervisor'=>'bg-blue-100 text-blue-800','Revisor L2'=>'bg-indigo-100 text-indigo-800','Revisor L1'=>'bg-sky-100 text-sky-800','Ciudadano'=>'bg-slate-100 text-slate-700'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $rolColors2[$u[3]] ?? 'bg-slate-100 text-slate-700' }}">{{ $u[3] }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $u[4] }}</td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-semibold">
                                    <span class="material-symbols-outlined text-xs">verified</span> PKI
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">hace 2h</td>
                            <td class="py-3.5 px-4">
                                @if($u[5] === 'Activo')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Suspendido</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 text-[11px] font-semibold transition-colors">
                                        <span class="material-symbols-outlined text-xs">edit</span> Editar
                                    </button>
                                    <button class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-600 text-[11px] transition-colors">
                                        <span class="material-symbols-outlined text-xs">block</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <span class="text-xs text-slate-500">
            {{ isset($usuarios) && method_exists($usuarios, 'total') ? $usuarios->total() : 5 }} usuarios registrados
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
                <h3 class="font-bold text-base text-[#0b1c30]">Registrar Nuevo Usuario</h3>
            </div>
            <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('portal.usuarios.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="nombre" class="block text-xs font-semibold text-[#0b1c30] mb-1">Nombre Completo</label>
                <input type="text" name="nombre" id="nombre" required placeholder="Ej. Ing. Carlos Pérez"
                       class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-[#0b1c30] mb-1">Correo Electrónico Institucional</label>
                <input type="email" name="email" id="email" required placeholder="carlos.perez@setc.gob.mx"
                       class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-[#0b1c30] mb-1">Contraseña Inicial</label>
                <input type="password" name="password" id="password" required minlength="8" placeholder="••••••••"
                       class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
            </div>

            <div>
                <span class="block text-xs font-semibold text-[#0b1c30] mb-2">Asignar Roles</span>
                <div class="space-y-2 max-h-40 overflow-y-auto p-2 bg-slate-50 rounded-lg border border-slate-200">
                    @foreach($roles ?? [] as $r)
                        <label class="flex items-center gap-2 text-xs text-[#0b1c30] cursor-pointer">
                            <input type="checkbox" name="roles[]" value="{{ $r->ID_Rol }}"
                                   class="rounded text-[#0051d5] focus:ring-[#0051d5]">
                            <span class="font-medium">{{ $r->Nombre }}</span>
                        </label>
                    @endforeach
                </div>
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

{{-- ═══════════════════════════════════ MODAL EDITAR ROLES ══ --}}
<div x-show="showEditModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
     style="display: none;">
    <div @click.outside="showEditModal = false"
         class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0051d5]">manage_accounts</span>
                <h3 class="font-bold text-base text-[#0b1c30]">Modificar Roles de Usuario</h3>
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
                <span class="block text-xs font-semibold text-[#0b1c30] mb-2">Seleccione los roles aplicables</span>
                <div class="space-y-2 p-3 bg-slate-50 rounded-lg border border-slate-200">
                    @foreach($roles ?? [] as $r)
                        <label class="flex items-center gap-2 text-xs text-[#0b1c30] cursor-pointer">
                            <input type="checkbox" name="roles[]" value="{{ $r->ID_Rol }}"
                                   :checked="editUserRoles.includes({{ $r->ID_Rol }})"
                                   class="rounded text-[#0051d5] focus:ring-[#0051d5]">
                            <span class="font-medium">{{ $r->Nombre }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" @click="showEditModal = false"
                        class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-[#0051d5] hover:bg-[#003ea8] text-white text-xs font-semibold transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

</div> {{-- Cierre de x-data --}}
@endsection
