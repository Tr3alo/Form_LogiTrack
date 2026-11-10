@extends('layouts.logitrack')

@section('content')
    <!-- SUMMARY CARDS FOR RUTAS Y ENVIOS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Envíos en Ruta Hoy</p>
                <p class="text-2xl font-extrabold text-slate-800 mt-1">8 Despachos</p>
                <p class="text-xs text-slate-500 mt-1">3 con llegada próxima en León</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Señales de Destino Activas</p>
                <p class="text-2xl font-extrabold text-blue-600 mt-1">5 Balizas GPS</p>
                <p class="text-xs text-slate-500 mt-1">Puntos de llegada monitoreados</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="7"/><circle cx="12" cy="12" r="2.5" fill="currentColor"/>
                    <path d="M12 2v3M12 19v3M2 12h3M19 12h3" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Destinos Alcanzados</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1">2 Confirmados</p>
                <p class="text-xs text-slate-500 mt-1">Centro Comercial y Subestación Sur</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Retornos por Alerta</p>
                <p class="text-2xl font-extrabold text-red-600 mt-1">1 Unidad</p>
                <p class="text-xs text-red-500 font-medium mt-1">Cuadrilla B -> Bodega Central</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- FILTER BAR FOR ROUTES -->
    <div class="bg-white rounded-2xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="filterRoutes('todos')" id="rt-btn-todos" class="rt-filter-btn px-4 py-2 rounded-xl text-xs font-semibold bg-[#2563eb] text-white cursor-pointer transition-all">
                Todas las Rutas ({{ count($rutasEnvios) }})
            </button>
            <button type="button" onclick="filterRoutes('En Tránsito')" id="rt-btn-transito" class="rt-filter-btn px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer transition-all">
                En Tránsito (2)
            </button>
            <button type="button" onclick="filterRoutes('Entregado')" id="rt-btn-entregado" class="rt-filter-btn px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer transition-all">
                En Destino / Entregados (2)
            </button>
            <button type="button" onclick="filterRoutes('Reporte Fallo')" id="rt-btn-alerta" class="rt-filter-btn px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer transition-all">
                Alerta / Retorno (1)
            </button>
        </div>

        <div class="flex items-center gap-2 text-xs text-slate-500">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-ping"></span>
            <span>Señales de destino sincronizadas con GPS de León</span>
        </div>
    </div>

    <!-- LISTA DE RUTAS Y ENVIOS CON SEÑAL DE DESTINO -->
    <div class="space-y-5" id="routes-container">
        @foreach($rutasEnvios as $ruta)
            <div class="route-card bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 overflow-hidden transition-all hover:shadow-md"
                 data-estado="{{ $ruta['estado'] }}">

                <div class="grid grid-cols-1 lg:grid-cols-12">

                    <!-- Left Column: Info de Envío, Cuadrilla y Carga (5 cols) -->
                    <div class="lg:col-span-5 p-6 border-b lg:border-b-0 lg:border-r border-slate-100 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-900 text-white font-mono text-xs font-bold">
                                        {{ $ruta['codigo'] }}
                                    </span>
                                    <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold text-xs">
                                        {{ $ruta['cuadrilla'] }}
                                    </span>
                                </div>

                                @if($ruta['estado_color'] === 'green')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#16a34a]">
                                        {{ $ruta['estado'] }}
                                    </span>
                                @elseif($ruta['estado_color'] === 'yellow')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#fef3c7] text-[#d97706]">
                                        {{ $ruta['estado'] }}
                                    </span>
                                @elseif($ruta['estado_color'] === 'red')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#fee2e2] text-[#dc2626]">
                                        {{ $ruta['estado'] }}
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-1.5 text-xs text-slate-600 mt-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Encargado de Cuadrilla:</span>
                                    <span class="font-bold text-slate-800">{{ $ruta['responsable'] }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Chofer Asignado:</span>
                                    <span class="font-semibold text-slate-700">{{ $ruta['chofer'] }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Unidad Vehicular:</span>
                                    <span class="font-mono text-slate-700">{{ $ruta['vehiculo'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Equipos en este Envío -->
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">
                                Recursos / Equipos en Envío:
                            </span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($ruta['items'] as $item)
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium">
                                        📦 {{ $item }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: SEÑAL DEL DESTINO A DONDE DEBE LLEGAR LA CUADRILLA (7 cols) -->
                    <div class="lg:col-span-7 p-6 bg-slate-50/50 flex flex-col justify-between">
                        <!-- Visual Route Origin -> Destination Signal Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/70">
                            <!-- Punto de Salida -->
                            <div class="flex items-start gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400 block">Origen de Salida ({{ $ruta['hora_salida'] }})</span>
                                    <span class="text-xs font-bold text-slate-700">{{ $ruta['origen'] }}</span>
                                </div>
                            </div>

                            <!-- Arrow connector -->
                            <div class="hidden sm:flex items-center text-slate-300">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>

                            <!-- SEÑAL DE DESTINO DESTACADA -->
                            <div class="flex items-start gap-3 bg-white p-3.5 rounded-xl border-2 {{ $ruta['estado_color'] === 'red' ? 'border-red-400' : ($ruta['estado_color'] === 'green' ? 'border-emerald-400' : 'border-blue-500') }} shadow-xs">
                                <div class="relative flex items-center justify-center w-9 h-9 rounded-xl {{ $ruta['estado_color'] === 'red' ? 'bg-red-100 text-red-600' : ($ruta['estado_color'] === 'green' ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600') }} shrink-0">
                                    <!-- Beacon Target Icon -->
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full {{ $ruta['estado_color'] === 'red' ? 'bg-red-500 animate-ping' : ($ruta['estado_color'] === 'green' ? 'bg-emerald-500' : 'bg-blue-600 animate-ping') }}"></span>
                                        <span class="text-[10.5px] font-extrabold uppercase tracking-wider {{ $ruta['estado_color'] === 'red' ? 'text-red-600' : ($ruta['estado_color'] === 'green' ? 'text-emerald-600' : 'text-blue-600') }}">
                                            Señal de Destino Asignado
                                        </span>
                                    </div>
                                    <h4 class="text-sm font-extrabold text-slate-900 mt-0.5">{{ $ruta['destino'] }}</h4>
                                    <p class="text-[11.5px] text-slate-500">{{ $ruta['direccion_destino'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de Progreso de Ruta y Baliza de Estado -->
                        <div class="my-4">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-bold text-slate-700">{{ $ruta['senal_destino'] }}</span>
                                <span class="font-mono text-xs font-bold text-slate-500">ETA: {{ $ruta['hora_estimada'] }} ({{ $ruta['progreso'] }}%)</span>
                            </div>
                            <div class="w-full h-3 bg-slate-200 rounded-full overflow-hidden p-0.5">
                                <div class="h-full rounded-full transition-all {{ $ruta['estado_color'] === 'red' ? 'bg-red-500' : ($ruta['estado_color'] === 'green' ? 'bg-emerald-500' : 'bg-[#2563eb]') }}"
                                     style="width: {{ $ruta['progreso'] }}%"></div>
                            </div>
                        </div>

                        <!-- Coordenadas de Destino y Acción de Localización -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <span class="font-mono bg-white px-2.5 py-1 rounded-md border border-slate-200 text-slate-700 font-semibold">
                                    📍 Coordenadas Destino: {{ $ruta['coordenadas_destino'] }}
                                </span>
                            </div>

                            <a href="{{ route('rastreo', ['focus' => $ruta['focus_id']]) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#2563eb] hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition-all">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="7"/><circle cx="12" cy="12" r="2.5" fill="currentColor"/>
                                    <path d="M12 2v3M12 19v3M2 12h3M19 12h3" stroke-linecap="round"/>
                                </svg>
                                <span>Ver Señal de Destino en Mapa GPS</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
<script>
    function filterRoutes(estado) {
        document.querySelectorAll('.rt-filter-btn').forEach(btn => {
            btn.className = 'rt-filter-btn px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer transition-all';
        });
        event.currentTarget.className = 'rt-filter-btn px-4 py-2 rounded-xl text-xs font-semibold bg-[#2563eb] text-white cursor-pointer transition-all';

        document.querySelectorAll('.route-card').forEach(card => {
            const cardEstado = card.getAttribute('data-estado');
            if (estado === 'todos' || cardEstado === estado) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }
</script>
@endpush
