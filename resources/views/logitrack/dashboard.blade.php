@extends('layouts.logitrack')

@section('content')
    @php
        $isFreeAccount = (bool) ($user['is_free'] ?? false);
    @endphp

    <!-- TOP KPI CARDS ROW -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        <!-- Card 1: Equipos Asignados -->
        <a href="{{ route('inventario') }}"
           class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 border-l-4 border-l-[#2563eb] hover:shadow-md transition-all block">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[14px] font-medium text-slate-500">Equipos Asignados</p>
                    <h2 class="text-[34px] font-extrabold text-slate-900 mt-1 leading-none">124</h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#eff6ff] flex items-center justify-center text-[#2563eb]">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 4a2 2 0 00-2 2v1H4a2 2 0 00-2 2v3h20V9a2 2 0 00-2-2h-3V6a2 2 0 00-2-2H9zm0 2h6v1H9V6zm-7 8v5a2 2 0 002 2h16a2 2 0 002-2v-5h-7v1a1 1 0 01-1 1h-4a1 1 0 01-1-1v-1H2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-5 flex items-center gap-1.5 text-[12.5px] font-semibold text-[#16a34a]">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M23 6l-9.5 9.5-5-5L1 18" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 6h6v6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>+12% esta semana</span>
            </div>
        </a>

        <!-- Card 2: Envíos en Ruta -->
        <a href="{{ route('rutas') }}"
           class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 border-l-4 border-l-[#f59e0b] hover:shadow-md transition-all block">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[14px] font-medium text-slate-500">Envíos en Ruta</p>
                    <h2 class="text-[34px] font-extrabold text-slate-900 mt-1 leading-none">8</h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#fffbeb] flex items-center justify-center text-[#d97706]">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 4a2 2 0 00-2 2v10a1 1 0 001 1h1.05a2.5 2.5 0 004.9 0h6.1a2.5 2.5 0 004.9 0H21a1 1 0 001-1v-4.586a1 1 0 00-.293-.707l-3.414-3.414A1 1 0 0017.586 7H15V6a2 2 0 00-2-2H3zm12 5h2.586L20 11.414V13h-5V9zm-9.5 9a1 1 0 110-2 1 1 0 010 2zm11 0a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-5 text-[12.5px] font-medium text-slate-500">
                3 con llegada próxima {{ $isFreeAccount ? '(🔒 Requiere Premium)' : '' }}
            </div>
        </a>

        <!-- Card 3: Alertas / Extravíos -->
        <a href="{{ route('inventario', ['cuadrilla' => 'Cuadrilla B']) }}"
           class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 border-l-4 border-l-[#ef4444] hover:shadow-md transition-all block">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[14px] font-medium text-slate-500">Alertas / Extravíos</p>
                    <h2 class="text-[34px] font-extrabold text-slate-900 mt-1 leading-none">1</h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#fef2f2] flex items-center justify-center text-[#dc2626]">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <div class="mt-5 text-[12.5px] font-medium text-[#ef4444]">
                Revisar taladro asignado a Cuadrilla B
            </div>
        </a>

        <!-- Card 4: Eficiencia Operativa -->
        <a href="{{ route('rutas') }}"
           class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 border-l-4 border-l-[#22c55e] hover:shadow-md transition-all block">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[14px] font-medium text-slate-500">Eficiencia Operativa</p>
                    <h2 class="text-[34px] font-extrabold text-slate-900 mt-1 leading-none">94%</h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#f0fdf4] flex items-center justify-center text-[#16a34a]">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 2a1 1 0 000 2h2v1.07A8.001 8.001 0 004 13a8 8 0 1016 0 7.98 7.98 0 00-2.05-5.34l1.42-1.42a1 1 0 10-1.41-1.41l-1.46 1.46A7.96 7.96 0 0013 5.07V4h2a1 1 0 100-2H9zm3 7a1 1 0 011 1v3.59l2.3 2.3a1 1 0 01-1.42 1.42l-2.59-2.6A1 1 0 0111 14v-4a1 1 0 011-1z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-5 text-[12.5px] font-medium text-[#16a34a]">
                Tiempos de entrega óptimos
            </div>
        </a>

    </div>

    <!-- BOTTOM ROW: ULTIMOS MOVIMIENTOS + RASTREO GPS ACTIVO -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        <!-- Left Card: Últimos Movimientos de Bodega (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 flex flex-col overflow-hidden">
            <div class="px-7 py-5 flex items-center justify-between border-b border-slate-100">
                <h3 class="text-[17px] font-bold text-slate-800">Últimos Movimientos de Bodega</h3>
                <a href="{{ route('inventario') }}" class="text-[13.5px] font-semibold text-[#2563eb] hover:text-blue-700 transition-colors">
                    Ver todo
                </a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#f8fafc] border-b border-slate-100 text-[13px] font-semibold text-slate-400">
                            <th class="py-3.5 px-7">Herramienta/Equipo</th>
                            <th class="py-3.5 px-5">Asignado a</th>
                            <th class="py-3.5 px-5">Destino</th>
                            <th class="py-3.5 px-7">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-[14px]">
                        @foreach($ultimosMovimientos as $mov)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-4 px-7 font-bold text-slate-800">
                                    {{ $mov['herramienta'] }}
                                </td>
                                <td class="py-4 px-5 text-slate-500">
                                    <a href="{{ route('inventario', ['cuadrilla' => $mov['cuadrilla']]) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $mov['asignado_a'] }}
                                    </a>
                                </td>
                                <td class="py-4 px-5 text-slate-500">
                                    {{ $mov['destino'] }}
                                </td>
                                <td class="py-4 px-7">
                                    @if($mov['estado_color'] === 'green')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold bg-[#dcfce7] text-[#16a34a]">
                                            {{ $mov['estado'] }}
                                        </span>
                                    @elseif($mov['estado_color'] === 'yellow')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold bg-[#fef3c7] text-[#d97706]">
                                            {{ $mov['estado'] }}
                                        </span>
                                    @elseif($mov['estado_color'] === 'red')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold bg-[#fee2e2] text-[#dc2626]">
                                            {{ $mov['estado'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $mov['estado'] }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Card: Rastreo GPS Activo (4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 flex flex-col overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-[17px] font-bold text-slate-800">Rastreo GPS Activo</h3>
                @if($isFreeAccount)
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800">
                        🔒 Exclusivo PRO
                    </span>
                @endif
            </div>

            <div class="relative flex-1 min-h-[260px] m-3 mt-0 rounded-xl overflow-hidden flex flex-col items-center justify-center text-center p-6 topo-pattern">
                <div id="mini-leon-map" class="absolute inset-0 opacity-35 pointer-events-none z-0"></div>

                <svg class="absolute inset-0 w-full h-full object-cover opacity-40 pointer-events-none z-0" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M-20 40 C60 10, 120 90, 200 50 C280 10, 340 80, 420 30" stroke="#94a3b8" stroke-width="1.5" fill="none"/>
                    <path d="M-20 80 C80 40, 140 130, 220 85 C300 40, 350 110, 420 70" stroke="#94a3b8" stroke-width="1.5" fill="none"/>
                    <path d="M-20 130 C70 100, 150 180, 240 130 C320 80, 360 160, 420 120" stroke="#94a3b8" stroke-width="1.5" fill="none"/>
                    <path d="M-20 180 C90 150, 160 220, 260 175 C330 140, 370 210, 420 180" stroke="#94a3b8" stroke-width="1.5" fill="none"/>
                    <path d="M-20 230 C110 200, 180 260, 280 220 C340 190, 380 260, 420 240" stroke="#94a3b8" stroke-width="1.5" fill="none"/>
                    <circle cx="80" cy="210" r="45" stroke="#94a3b8" stroke-width="1.2" fill="none"/>
                    <circle cx="80" cy="210" r="70" stroke="#94a3b8" stroke-width="1.2" fill="none"/>
                    <circle cx="330" cy="80" r="40" stroke="#94a3b8" stroke-width="1.2" fill="none"/>
                    <circle cx="330" cy="80" r="65" stroke="#94a3b8" stroke-width="1.2" fill="none"/>
                </svg>

                @if($isFreeAccount)
                    <!-- Vista bloqueada para Cuenta Free -->
                    <div class="relative z-10 flex flex-col items-center bg-slate-900/90 text-white p-5 rounded-2xl border border-slate-700 shadow-lg max-w-xs">
                        <span class="text-2xl mb-1">🔒</span>
                        <h4 class="text-sm font-extrabold text-white">Rastreo GPS Bloqueado (Cuenta Free)</h4>
                        <p class="text-[11.5px] text-slate-300 mt-1 mb-3 leading-relaxed">
                            Las cuentas Free solo tienen vista a Dashboard e Inventario. Renueva tu suscripción para localizar tus cuadrillas en León.
                        </p>
                        <a href="{{ route('suscripcion') }}"
                           class="inline-flex items-center justify-center px-4 py-2 rounded-full bg-[#2563eb] hover:bg-blue-500 text-white text-xs font-bold shadow-md transition-all">
                            Renovar a Premium ($60/mes)
                        </a>
                    </div>
                @else
                    <!-- Vista desbloqueada para Cuenta Premium -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-[#3b82f6] mb-2">
                            <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <circle cx="12" cy="12" r="7" />
                                <circle cx="12" cy="12" r="2.5" fill="currentColor" />
                                <path d="M12 2v3M12 19v3M2 12h3M19 12h3" stroke-linecap="round"/>
                            </svg>
                        </div>

                        <h4 class="text-[15px] font-bold text-[#1e3a8a]">Cuadrilla C - León Centro</h4>
                        <p class="text-[12.5px] font-medium text-[#2563eb] mt-0.5 mb-4">A 2.5 km de distancia</p>

                        <a href="{{ route('rastreo', ['focus' => 'cuadrilla-c']) }}"
                           class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-[#2563eb] hover:bg-blue-700 text-white text-[13px] font-semibold shadow-md shadow-blue-600/25 transition-all">
                            Ver en Mapa Completo
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapEl = document.getElementById('mini-leon-map');
        if (mapEl && typeof L !== 'undefined') {
            const miniMap = L.map('mini-leon-map', {
                zoomControl: false,
                attributionControl: false,
                dragging: false,
                scrollWheelZoom: false,
                doubleClickZoom: false,
                boxZoom: false,
                keyboard: false
            }).setView([12.4395, -86.8792], 14);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(miniMap);
        }
    });
</script>
@endpush
