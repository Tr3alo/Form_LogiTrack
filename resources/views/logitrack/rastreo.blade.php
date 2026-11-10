@extends('layouts.logitrack')

@section('content')
    <!-- TOP BAR: CONTROLES DE GEOLOCALIZACIÓN LEÓN, NICARAGUA -->
    <div class="bg-white rounded-2xl p-4 px-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </span>
            <div>
                <div class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                    Red GPS Activa • Municipio de León, Nicaragua
                </div>
                <div class="text-xs text-slate-400">
                    5 unidades conectadas • Cobertura Urbana y Suburbana (Sutiaba, León Centro, Las Mercedes, Subestación Sur)
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Botón de Simulación en Tiempo Real -->
            <button type="button"
                    id="btn-toggle-sim"
                    onclick="toggleGpsSimulation()"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-all cursor-pointer">
                <span id="sim-dot" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span id="sim-label">Simulación en Vivo: ACTIVA</span>
            </button>

            <!-- Botón Vista General León -->
            <button type="button"
                    onclick="resetMapLeon()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all cursor-pointer">
                <svg class="w-4 h-4 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Ver Todo León</span>
            </button>
        </div>
    </div>

    <!-- MAIN GPS GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- MAPA INTERACTIVO LEAFLET (8 COLUMNAS) -->
        <div class="lg:col-span-8 bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 overflow-hidden relative">
            <!-- Quick Cuadrilla Switcher Header -->
            <div class="px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2 bg-slate-50/60">
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach($cuadrillasGps as $c)
                        <button type="button"
                                onclick="focusCuadrilla('{{ $c['id'] }}')"
                                id="tab-{{ $c['id'] }}"
                                class="map-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer border border-slate-200 bg-white text-slate-700 hover:border-blue-400">
                            <span class="inline-block w-2 h-2 rounded-full mr-1" style="background-color: {{ $c['color'] }}"></span>
                            {{ $c['nombre'] }}
                        </button>
                    @endforeach
                </div>
                <span class="text-[11px] font-mono text-slate-400">Leaflet • OpenStreetMap León</span>
            </div>

            <!-- Contenedor del Mapa Leaflet -->
            <div id="leon-gps-map" class="w-full h-[560px] z-10"></div>

            <!-- HUD Telemetría de la Cuadrilla Seleccionada -->
            <div class="p-5 bg-white border-t border-slate-100 grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block">Unidad Seleccionada</span>
                    <span id="hud-nombre" class="text-sm font-extrabold text-slate-800 mt-0.5 block">Cuadrilla C - León Centro</span>
                    <span id="hud-vehiculo" class="text-xs text-slate-500 block">Camión Isuzu NPR • Placa LE-20341</span>
                </div>
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block">Ubicación Actual (León)</span>
                    <span id="hud-ubicacion" class="text-xs font-bold text-slate-700 mt-0.5 block">León Centro (Av. Central, 2c al Norte)</span>
                    <span id="hud-coords" class="text-[11px] font-mono text-blue-600 block">12.4395° N, -86.8792° W</span>
                </div>
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block">Señal de Destino</span>
                    <span id="hud-destino" class="text-xs font-bold text-emerald-700 mt-0.5 block">Residencial Las Mercedes, León</span>
                    <span id="hud-distancia" class="text-xs text-slate-500 block">A 2.5 km de distancia (42 km/h)</span>
                </div>
                <div class="flex flex-col justify-center">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-semibold text-slate-500">Trayecto</span>
                        <span id="hud-progreso-txt" class="font-bold text-blue-600">68%</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div id="hud-progreso-bar" class="h-full bg-[#2563eb] rounded-full transition-all duration-300" style="width: 68%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LISTA LATERAL DE CUADRILLAS EN LEÓN (4 COLUMNAS) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-2xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Cuadrillas Desplegadas en León</h3>
                <p class="text-xs text-slate-400 mt-0.5 mb-4">Haz clic en cualquier cuadrilla para localizar su posición y destino en el mapa</p>

                <div class="space-y-3">
                    @foreach($cuadrillasGps as $c)
                        <div id="card-{{ $c['id'] }}"
                             onclick="focusCuadrilla('{{ $c['id'] }}')"
                             class="cuadrilla-card p-4 rounded-xl border border-slate-200/90 hover:border-blue-500 hover:shadow-sm transition-all cursor-pointer bg-white">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $c['color'] }}"></span>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800 leading-tight">{{ $c['titulo_mapa'] }}</h4>
                                        <p class="text-[11.5px] text-slate-400 mt-0.5">{{ $c['vehiculo'] }}</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $c['badge_class'] }}">
                                    {{ $c['estado'] }}
                                </span>
                            </div>

                            <div class="mt-3 pt-3 border-t border-slate-100 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="text-slate-400">Encargado / Chofer:</span>
                                    <span class="font-semibold text-slate-700">{{ $c['jefe'] }} • {{ $c['chofer'] }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="text-slate-400">Destino en León:</span>
                                    <span class="font-bold text-blue-600 text-right">{{ $c['destino_nombre'] }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="text-slate-400">Distancia / Vel:</span>
                                    <span class="font-medium">{{ $c['distancia'] }} ({{ $c['velocidad'] }})</span>
                                </div>
                            </div>

                            <!-- Equipos a bordo -->
                            <div class="mt-3 pt-2.5 border-t border-slate-100">
                                <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Recursos en la Unidad:</span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($c['equipos_a_bordo'] as $eq)
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-md text-[11px] font-medium">
                                            {{ $eq }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    const cuadrillasData = @json($cuadrillasGps);
    const initialFocus = @json($focusCuadrilla ?? 'cuadrilla-c');
    let map;
    let markers = {};
    let destMarkers = {};
    let routeLines = {};
    let simInterval = null;
    let simActive = true;

    document.addEventListener('DOMContentLoaded', function () {
        // Centro de León, Nicaragua (Parque Central / Catedral de León)
        map = L.map('leon-gps-map').setView([12.4379, -86.8780], 14);

        // Capa OpenStreetMap estándar libre y sin requerimiento de API Key
        const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> • León, Nicaragua'
        }).addTo(map);

        // Capa Satelital Esri libre y sin API Key
        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: '&copy; Esri &mdash; León, Nicaragua'
        });

        // Selector interactivo de mapas (Calles / Satelital)
        L.control.layers({
            "🗺️ Mapa Calles": osmLayer,
            "🛰️ Vista Satelital": satelliteLayer
        }, null, { position: 'topright' }).addTo(map);

        // Pin de Bodega Central LogiTrack en León
        const bodegaIcon = L.divIcon({
            className: 'custom-bodega-icon',
            html: `
                <div style="background:#0f172a;color:#fff;padding:5px 10px;border-radius:8px;font-weight:700;font-size:11px;box-shadow:0 4px 12px rgba(0,0,0,0.25);border:2px solid #fff;white-space:nowrap;display:flex;align-items:center;gap:5px;">
                    <span style="width:8px;height:8px;border-radius:50%;background:#38bdf8;display:inline-block;"></span>
                    Bodega Central LogiTrack
                </div>
            `,
            iconSize: [170, 30],
            iconAnchor: [85, 15]
        });

        L.marker([12.4438, -86.8885], { icon: bodegaIcon })
            .addTo(map)
            .bindPopup(`
                <div class="text-xs">
                    <div class="font-bold text-slate-900 text-sm">Bodega Central LogiTrack</div>
                    <div class="text-slate-500 mt-0.5">Zona Industrial Salida a Chinandega, León</div>
                    <div class="mt-2 font-semibold text-blue-600">Encargado: Ing. Mario Valladares</div>
                </div>
            `);

        // Agregar cada Cuadrilla, su ruta y su señal de destino en León
        cuadrillasData.forEach(c => {
            // Línea punteada entre posición actual y el destino asignado
            if (c.lat !== c.destino_lat || c.lng !== c.destino_lng) {
                const line = L.polyline([[c.lat, c.lng], [c.destino_lat, c.destino_lng]], {
                    color: c.color,
                    weight: 3.5,
                    opacity: 0.75,
                    dashArray: '8, 8'
                }).addTo(map);
                routeLines[c.id] = line;

                // Pin de Destino
                const destIcon = L.divIcon({
                    className: 'custom-dest-icon',
                    html: `
                        <div style="background:#ffffff;color:#1e293b;padding:3px 8px;border-radius:6px;font-weight:700;font-size:10.5px;box-shadow:0 2px 8px rgba(0,0,0,0.15);border:2px solid ${c.color};white-space:nowrap;">
                            🎯 Destino ${c.nombre}: ${c.destino_nombre.split(',')[0]}
                        </div>
                    `,
                    iconSize: [180, 24],
                    iconAnchor: [90, 12]
                });

                destMarkers[c.id] = L.marker([c.destino_lat, c.destino_lng], { icon: destIcon })
                    .addTo(map)
                    .bindPopup(`
                        <div class="text-xs">
                            <div class="font-bold text-slate-800">Señal de Destino • ${c.nombre}</div>
                            <div class="text-blue-600 font-semibold mt-0.5">${c.destino_nombre}</div>
                            <div class="text-slate-500 mt-1">Encargo: ${c.encargo}</div>
                        </div>
                    `);
            }

            // Pin de la Cuadrilla
            const unitIcon = L.divIcon({
                className: 'custom-unit-icon',
                html: `
                    <div style="position:relative;display:flex;flex-direction:column;align-items:center;">
                        <div style="background:${c.color};color:#fff;padding:4px 9px;border-radius:9999px;font-weight:800;font-size:11px;box-shadow:0 4px 12px rgba(0,0,0,0.25);border:2px solid #fff;white-space:nowrap;">
                            🚚 ${c.nombre}
                        </div>
                        <div style="width:0;height:0;border-left:6px solid transparent;border-right:6px solid transparent;border-top:7px solid ${c.color};margin-top:-1px;"></div>
                    </div>
                `,
                iconSize: [120, 36],
                iconAnchor: [60, 34],
                popupAnchor: [0, -30]
            });

            const marker = L.marker([c.lat, c.lng], { icon: unitIcon })
                .addTo(map)
                .bindPopup(buildPopupHtml(c));

            marker.on('click', () => {
                updateHudAndHighlight(c.id);
            });

            markers[c.id] = marker;
        });

        // Enfocar la cuadrilla solicitada (por defecto Cuadrilla C - León Centro)
        setTimeout(() => {
            if (initialFocus && markers[initialFocus]) {
                focusCuadrilla(initialFocus);
            } else {
                focusCuadrilla('cuadrilla-c');
            }
        }, 250);

        // Iniciar simulación suave de movimiento en León
        startSimulationLoop();
    });

    function buildPopupHtml(c) {
        const itemsHtml = c.equipos_a_bordo.map(i => `<li class="text-[11px] text-slate-600">• ${i}</li>`).join('');
        return `
            <div class="min-w-[230px]">
                <div class="flex items-center justify-between gap-2 pb-1.5 border-b border-slate-100">
                    <span class="font-extrabold text-slate-900 text-sm">${c.titulo_mapa}</span>
                </div>
                <div class="text-xs text-slate-500 mt-1.5 space-y-1">
                    <div><strong class="text-slate-700">Vehículo:</strong> ${c.vehiculo}</div>
                    <div><strong class="text-slate-700">Chofer:</strong> ${c.chofer}</div>
                    <div><strong class="text-slate-700">Encargado:</strong> ${c.jefe}</div>
                    <div><strong class="text-slate-700">Destino:</strong> <span class="text-blue-600 font-semibold">${c.destino_nombre}</span></div>
                    <div><strong class="text-slate-700">Estado:</strong> ${c.distancia} • ${c.velocidad}</div>
                </div>
                <div class="mt-2 pt-1.5 border-t border-slate-100">
                    <span class="text-[10px] font-bold uppercase text-slate-400">Equipos Asignados:</span>
                    <ul class="mt-0.5 space-y-0.5">${itemsHtml}</ul>
                </div>
            </div>
        `;
    }

    function focusCuadrilla(id) {
        const c = cuadrillasData.find(item => item.id === id);
        if (!c || !markers[id]) return;

        map.flyTo([c.lat, c.lng], 15, { duration: 0.9 });
        markers[id].openPopup();
        updateHudAndHighlight(id);
    }

    function updateHudAndHighlight(id) {
        const c = cuadrillasData.find(item => item.id === id);
        if (!c) return;

        document.getElementById('hud-nombre').textContent = c.titulo_mapa;
        document.getElementById('hud-vehiculo').textContent = c.vehiculo + ' • Chofer: ' + c.chofer;
        document.getElementById('hud-ubicacion').textContent = c.ubicacion_actual;
        document.getElementById('hud-coords').textContent = c.lat.toFixed(4) + '° N, ' + c.lng.toFixed(4) + '° W';
        document.getElementById('hud-destino').textContent = c.destino_nombre;
        document.getElementById('hud-distancia').textContent = c.distancia + ' (' + c.velocidad + ')';
        document.getElementById('hud-progreso-txt').textContent = c.progreso + '%';
        document.getElementById('hud-progreso-bar').style.width = c.progreso + '%';

        // Highlight card & tab
        document.querySelectorAll('.cuadrilla-card').forEach(el => {
            el.classList.remove('ring-2', 'ring-blue-600', 'bg-blue-50/20');
        });
        const activeCard = document.getElementById('card-' + id);
        if (activeCard) {
            activeCard.classList.add('ring-2', 'ring-blue-600', 'bg-blue-50/20');
        }
    }

    function resetMapLeon() {
        map.flyTo([12.4379, -86.8780], 13, { duration: 0.9 });
    }

    function startSimulationLoop() {
        simInterval = setInterval(() => {
            if (!simActive) return;

            cuadrillasData.forEach(c => {
                if (c.progreso < 100) {
                    // Mover suavemente 4% hacia el destino en León y regresar en bucle de demostración
                    const dLat = (c.destino_lat - c.lat) * 0.08;
                    const dLng = (c.destino_lng - c.lng) * 0.08;

                    if (Math.abs(dLat) > 0.0001 || Math.abs(dLng) > 0.0001) {
                        c.lat += dLat;
                        c.lng += dLng;
                        markers[c.id].setLatLng([c.lat, c.lng]);
                        if (routeLines[c.id]) {
                            routeLines[c.id].setLatLngs([[c.lat, c.lng], [c.destino_lat, c.destino_lng]]);
                        }
                    }
                }
            });
        }, 2500);
    }

    function toggleGpsSimulation() {
        simActive = !simActive;
        const label = document.getElementById('sim-label');
        const dot = document.getElementById('sim-dot');
        const btn = document.getElementById('btn-toggle-sim');

        if (simActive) {
            label.textContent = 'Simulación en Vivo: ACTIVA';
            dot.className = 'w-2 h-2 rounded-full bg-emerald-500 animate-pulse';
            btn.className = 'inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-all cursor-pointer';
        } else {
            label.textContent = 'Simulación en Vivo: PAUSADA';
            dot.className = 'w-2 h-2 rounded-full bg-slate-400';
            btn.className = 'inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200 transition-all cursor-pointer';
        }
    }
</script>
@endpush
