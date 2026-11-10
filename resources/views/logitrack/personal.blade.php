@extends('layouts.logitrack')

@section('content')
    <!-- SUMMARY STATS FOR PERSONAL -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Personal Total</p>
                <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ count($personal) }} Colaboradores</p>
                <p class="text-xs text-slate-500 mt-1">Plantilla activa en León</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Encargados de Bodega</p>
                <p class="text-2xl font-extrabold text-indigo-600 mt-1">3 Encargados</p>
                <p class="text-xs text-slate-500 mt-1">Custodia, despacho y auditoría</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Choferes Operativos</p>
                <p class="text-2xl font-extrabold text-amber-600 mt-1">4 Choferes</p>
                <p class="text-xs text-slate-500 mt-1">Conductores de Cuadrillas A, B, C, D</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Jefes de Cuadrilla y Técnicos</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1">4 Especialistas</p>
                <p class="text-xs text-slate-500 mt-1">Responsables de obra en campo</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR FOR PERSONAL -->
    <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Role Pills -->
            <div class="flex flex-wrap items-center gap-2">
                @php
                    $roles = [
                        'todos' => 'Todo el Personal (12)',
                        'Encargado de Bodega' => 'Encargados de Bodega (3)',
                        'Chofer' => 'Choferes de Flota (4)',
                        'Técnico / Encargado' => 'Encargados de Cuadrilla y Técnicos (4)',
                        'Administración' => 'Gerencia y Operaciones (1)',
                    ];
                @endphp
                @foreach($roles as $rolKey => $rolLabel)
                    <button type="button"
                            data-rol="{{ $rolKey }}"
                            onclick="filterPersonalByRole('{{ $rolKey }}')"
                            class="rol-pill px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer border {{ ($filtroRol ?? 'todos') === $rolKey ? 'bg-[#2563eb] text-white border-[#2563eb]' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                        {{ $rolLabel }}
                    </button>
                @endforeach
            </div>

            <!-- Search Input -->
            <div class="relative w-full lg:w-72">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
                </svg>
                <input type="text"
                       id="personal-search"
                       oninput="applyPersonalFilter()"
                       placeholder="Buscar chofer, bodega, cuadrilla..."
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 focus:bg-white">
            </div>
        </div>
    </div>

    <!-- PERSONAL CARDS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="personal-grid">
        @foreach($personal as $p)
            <div class="personal-card bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 flex flex-col justify-between hover:shadow-md transition-all"
                 data-rol="{{ $p['categoria_rol'] }}"
                 data-search="{{ strtolower($p['nombre'] . ' ' . $p['cargo'] . ' ' . $p['categoria_rol'] . ' ' . $p['asignacion'] . ' ' . $p['equipos_a_cargo']) }}">

                <div>
                    <!-- Top Header: Avatar + Name + Role Category Badge -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl {{ $p['avatar_bg'] }} text-white font-extrabold text-sm flex items-center justify-center shrink-0 shadow-xs">
                                {{ $p['iniciales'] }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-[15px] font-bold text-slate-800">{{ $p['nombre'] }}</h3>
                                </div>
                                <p class="text-xs font-semibold text-blue-600 mt-0.5">{{ $p['cargo'] }}</p>
                                <span class="text-[11px] font-mono text-slate-400">{{ $p['codigo'] }} • Exp: {{ $p['experiencia'] }}</span>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 shrink-0">
                            {{ $p['categoria_rol'] }}
                        </span>
                    </div>

                    <!-- Details List -->
                    <div class="mt-4 pt-4 border-t border-slate-100 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Departamento:</span>
                            <span class="font-semibold text-slate-700 text-right">{{ $p['departamento'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Unidad / Asignación:</span>
                            <span class="font-bold text-slate-800 text-right">{{ $p['asignacion'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Custodia / Recursos:</span>
                            <span class="font-medium text-slate-600 text-right">{{ $p['equipos_a_cargo'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Teléfono Directo:</span>
                            <span class="font-mono font-semibold text-slate-700">{{ $p['telefono'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer: Status Pill + Quick Actions -->
                <div class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-between">
                    @if($p['estado_color'] === 'green')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#16a34a]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                            {{ $p['estado'] }}
                        </span>
                    @elseif($p['estado_color'] === 'yellow')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#fef3c7] text-[#d97706]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#d97706]"></span>
                            {{ $p['estado'] }}
                        </span>
                    @elseif($p['estado_color'] === 'red')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#fee2e2] text-[#dc2626]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#dc2626]"></span>
                            {{ $p['estado'] }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            {{ $p['estado'] }}
                        </span>
                    @endif

                    <a href="{{ route('inventario') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                        Ver equipos →
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
<script>
    let currentRol = @json($filtroRol ?? 'todos');

    function filterPersonalByRole(rol) {
        currentRol = rol;
        document.querySelectorAll('.rol-pill').forEach(btn => {
            if (btn.getAttribute('data-rol') === rol) {
                btn.className = 'rol-pill px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer border bg-[#2563eb] text-white border-[#2563eb]';
            } else {
                btn.className = 'rol-pill px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer border bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100';
            }
        });
        applyPersonalFilter();
    }

    function applyPersonalFilter() {
        const q = (document.getElementById('personal-search').value || '').toLowerCase().trim();
        document.querySelectorAll('.personal-card').forEach(card => {
            const rol = card.getAttribute('data-rol');
            const search = card.getAttribute('data-search');

            const matchRol = (currentRol === 'todos' || rol === currentRol);
            const matchSearch = (!q || search.includes(q));

            if (matchRol && matchSearch) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }
</script>
@endpush
