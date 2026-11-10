@extends('layouts.logitrack')

@section('content')
    @if(session('status'))
        <div class="mb-6 px-5 py-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
            <span>✓ {{ session('status') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold cursor-pointer">✕</button>
        </div>
    @endif

    <!-- SUMMARY STATS FOR INVENTORY -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Inventario</p>
                <p class="text-2xl font-extrabold text-slate-800 mt-1" id="stat-total">{{ count($inventario) }} Lotes (124 Uds)</p>
                <p class="text-xs text-slate-500 mt-1">Registrados en León, Nicaragua</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">En Cuadrillas (A, B, C, D)</p>
                <p class="text-2xl font-extrabold text-blue-600 mt-1">8 Lotes Activos</p>
                <p class="text-xs text-slate-500 mt-1">Desplegados en proyectos</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Stock en Bodega Central</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1">2 Lotes (9 Kits)</p>
                <p class="text-xs text-slate-500 mt-1">Disponibles para despacho</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Equipos con Alerta</p>
                <p class="text-2xl font-extrabold text-red-600 mt-1">1 Equipo</p>
                <p class="text-xs text-red-500 font-medium mt-1">Taladro DeWalt • Cuadrilla B</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- FILTER CONTROLS CARD (CUADRILLAS + ENCARGOS + SEARCH) -->
    <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-800">Filtrar por Cuadrilla / Unidad Asignada</h2>
                <p class="text-xs text-slate-400 mt-0.5">Selecciona una cuadrilla o encargo específico para auditar sus herramientas asignadas</p>
            </div>

            <button type="button"
                    onclick="document.getElementById('modal-nuevo-equipo').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#2563eb] hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-all cursor-pointer self-start lg:self-auto">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Asignar / Registrar Equipo</span>
            </button>
        </div>

        <!-- Cuadrilla Pills -->
        <div class="flex flex-wrap items-center gap-2 my-4" id="cuadrilla-pills">
            @php
                $cuadrillasList = [
                    'todas' => 'Todas las Cuadrillas',
                    'Cuadrilla A' => 'Cuadrilla A',
                    'Cuadrilla B' => 'Cuadrilla B',
                    'Cuadrilla C' => 'Cuadrilla C',
                    'Cuadrilla D' => 'Cuadrilla D',
                    'Encargo Técnico' => 'Encargos Técnicos',
                    'Bodega Central' => 'En Bodega Central',
                ];
            @endphp
            @foreach($cuadrillasList as $key => $label)
                <button type="button"
                        data-cuadrilla="{{ $key }}"
                        onclick="setCuadrillaFilter('{{ $key }}')"
                        class="cuadrilla-pill px-3.5 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer border {{ $filtroCuadrilla === $key ? 'bg-[#2563eb] text-white border-[#2563eb] shadow-xs' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <!-- Secondary Filters: Encargo Selector + Estado + Search -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 pt-2">
            <!-- Search Input -->
            <div class="md:col-span-5 relative">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Buscar Herramienta, Serie o Responsable</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
                    </svg>
                    <input type="text"
                           id="filter-search"
                           oninput="applyInventoryFilters()"
                           placeholder="Ej. Fluke, Taladro DeWalt, THHN, Roberto Gómez..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                </div>
            </div>

            <!-- Filter by Encargo / Proyecto -->
            <div class="md:col-span-4">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Filtrar por Encargo / Proyecto</label>
                <select id="filter-encargo"
                        onchange="applyInventoryFilters()"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    <option value="todos">Todos los Encargos y Proyectos</option>
                    <option value="Encargo #104 - Proyecto Centro Comercial">Encargo #104 - Proyecto Centro Comercial (Cuadrilla A)</option>
                    <option value="Encargo #105 - Red Eléctrica Sutiaba">Encargo #105 - Red Eléctrica Sutiaba (Cuadrilla B)</option>
                    <option value="Encargo #108 - Instalación Residencial Las Mercedes">Encargo #108 - Residencial Las Mercedes (Cuadrilla C)</option>
                    <option value="Encargo #112 - Mantenimiento Subestación Sur">Encargo #112 - Subestación Sur, León (Téc. Roberto)</option>
                    <option value="Encargo #115 - Alumbrado Reparto Rubén Darío">Encargo #115 - Reparto Rubén Darío (Cuadrilla D)</option>
                    <option value="Stock Disponible en Bodega">Stock Disponible en Bodega Central</option>
                </select>
            </div>

            <!-- Filter by Estado -->
            <div class="md:col-span-3">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Estado del Recurso</label>
                <select id="filter-estado"
                        onchange="applyInventoryFilters()"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    <option value="todos">Todos los Estados</option>
                    <option value="Entregado">Entregado</option>
                    <option value="Operativo">Operativo en Campo</option>
                    <option value="En Tránsito">En Tránsito</option>
                    <option value="Reporte Fallo">Reporte Fallo</option>
                    <option value="En Bodega">En Bodega Central</option>
                </select>
            </div>
        </div>
    </div>

    <!-- INVENTORY TABLE CARD -->
    <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-slate-800">Listado General de Inventario</h3>
                <span id="visible-count-badge" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-600">
                    {{ count($inventario) }} registros
                </span>
            </div>
            <button type="button"
                    onclick="resetInventoryFilters()"
                    class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors cursor-pointer">
                Restablecer filtros
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f8fafc] border-b border-slate-100 text-[12px] font-semibold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">Código / Serie</th>
                        <th class="py-3.5 px-5">Herramienta / Equipo</th>
                        <th class="py-3.5 px-4">Cant.</th>
                        <th class="py-3.5 px-5">Cuadrilla / Asignado a</th>
                        <th class="py-3.5 px-5">Encargo y Ubicación (León)</th>
                        <th class="py-3.5 px-5">Estado</th>
                        <th class="py-3.5 px-6 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody id="inventory-tbody" class="divide-y divide-slate-100 text-[13.5px]">
                    @foreach($inventario as $item)
                        <tr class="inventory-row hover:bg-slate-50/80 transition-colors"
                            data-cuadrilla="{{ $item['cuadrilla'] }}"
                            data-encargo="{{ $item['encargo'] }}"
                            data-estado="{{ $item['estado'] }}"
                            data-search="{{ strtolower($item['codigo'] . ' ' . $item['nombre'] . ' ' . $item['serie'] . ' ' . $item['responsable'] . ' ' . $item['cuadrilla'] . ' ' . $item['encargo'] . ' ' . $item['ubicacion']) }}">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-800 text-xs">{{ $item['codigo'] }}</div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $item['serie'] }}</div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-slate-800">{{ $item['nombre'] }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $item['categoria'] }} • Act. {{ $item['ultima_actualizacion'] }}</div>
                            </td>
                            <td class="py-4 px-4 font-semibold text-slate-700 text-xs whitespace-nowrap">
                                {{ $item['cantidad'] }}
                            </td>
                            <td class="py-4 px-5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    {{ $item['cuadrilla'] }}
                                </span>
                                <div class="text-xs text-slate-500 mt-1">{{ $item['responsable'] }}</div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-semibold text-slate-700 text-xs">{{ $item['encargo'] }}</div>
                                <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>{{ $item['ubicacion'] }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                @if($item['estado_color'] === 'green')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#16a34a]">
                                        {{ $item['estado'] }}
                                    </span>
                                @elseif($item['estado_color'] === 'yellow')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#fef3c7] text-[#d97706]">
                                        {{ $item['estado'] }}
                                    </span>
                                @elseif($item['estado_color'] === 'red')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#fee2e2] text-[#dc2626]">
                                        {{ $item['estado'] }}
                                    </span>
                                @elseif($item['estado_color'] === 'blue')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        {{ $item['estado'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                        {{ $item['estado'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <a href="{{ route('rastreo', ['focus' => strtolower(str_replace(' ', '-', $item['cuadrilla']))]) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <span>Rastrear</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Empty state when filter matches 0 rows -->
            <div id="inventory-empty" class="hidden py-12 text-center">
                <p class="text-sm font-semibold text-slate-600">No se encontraron equipos para los filtros seleccionados.</p>
                <button type="button" onclick="resetInventoryFilters()" class="mt-2 text-xs font-semibold text-blue-600 hover:underline cursor-pointer">
                    Mostrar todo el inventario
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: ASIGNAR / REGISTRAR EQUIPO -->
    <div id="modal-nuevo-equipo" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Asignar Equipo a Cuadrilla / Encargo</h3>
                    <p class="text-xs text-slate-400">Registro inmediato en el control de inventario de León</p>
                </div>
                <button type="button" onclick="document.getElementById('modal-nuevo-equipo').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
            </div>

            <form id="form-nuevo-equipo" method="POST" action="{{ route('inventario.store') }}" class="space-y-4 mt-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Código de Equipo</label>
                        <input type="text" name="codigo" id="new-codigo" value="EQ-NEW-{{ rand(130, 999) }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Cantidad</label>
                        <input type="text" name="cantidad" id="new-cantidad" value="2 Unidades" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nombre de Herramienta / Recurso</label>
                    <input type="text" name="nombre" id="new-nombre" placeholder="Ej. Analizador de Redes Trifásico Fluke" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Cuadrilla Asignada</label>
                        <select name="cuadrilla" id="new-cuadrilla" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs">
                            <option value="Cuadrilla A">Cuadrilla A</option>
                            <option value="Cuadrilla B">Cuadrilla B</option>
                            <option value="Cuadrilla C" selected>Cuadrilla C</option>
                            <option value="Cuadrilla D">Cuadrilla D</option>
                            <option value="Encargo Técnico">Encargo Técnico</option>
                            <option value="Bodega Central">Bodega Central</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Responsable</label>
                        <input type="text" name="responsable" id="new-responsable" value="Téc. Óscar Gurdián" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Encargo / Destino en León</label>
                    <select name="encargo" id="new-encargo" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs">
                        <option value="Encargo #108 - Instalación Residencial Las Mercedes">Encargo #108 - Instalación Residencial Las Mercedes</option>
                        <option value="Encargo #104 - Proyecto Centro Comercial">Encargo #104 - Proyecto Centro Comercial</option>
                        <option value="Encargo #105 - Red Eléctrica Sutiaba">Encargo #105 - Red Eléctrica Sutiaba</option>
                        <option value="Encargo #112 - Mantenimiento Subestación Sur">Encargo #112 - Mantenimiento Subestación Sur</option>
                        <option value="Encargo #115 - Alumbrado Reparto Rubén Darío">Encargo #115 - Alumbrado Reparto Rubén Darío</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modal-nuevo-equipo').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-[#2563eb] text-white hover:bg-blue-700 cursor-pointer">
                        Guardar en Base de Datos
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let currentCuadrilla = @json($filtroCuadrilla ?? 'todas');

    function setCuadrillaFilter(cuadrilla) {
        currentCuadrilla = cuadrilla;
        document.querySelectorAll('.cuadrilla-pill').forEach(btn => {
            if (btn.getAttribute('data-cuadrilla') === cuadrilla) {
                btn.className = 'cuadrilla-pill px-3.5 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer border bg-[#2563eb] text-white border-[#2563eb] shadow-xs';
            } else {
                btn.className = 'cuadrilla-pill px-3.5 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer border bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100';
            }
        });
        applyInventoryFilters();
    }

    function applyInventoryFilters() {
        const search = (document.getElementById('filter-search').value || '').toLowerCase().trim();
        const encargo = document.getElementById('filter-encargo').value;
        const estado = document.getElementById('filter-estado').value;

        let visibleCount = 0;
        document.querySelectorAll('.inventory-row').forEach(row => {
            const rowCuadrilla = row.getAttribute('data-cuadrilla');
            const rowEncargo = row.getAttribute('data-encargo');
            const rowEstado = row.getAttribute('data-estado');
            const rowSearch = row.getAttribute('data-search');

            const matchCuadrilla = (currentCuadrilla === 'todas' || rowCuadrilla === currentCuadrilla);
            const matchEncargo = (encargo === 'todos' || rowEncargo === encargo);
            const matchEstado = (estado === 'todos' || rowEstado === estado);
            const matchSearch = (!search || rowSearch.includes(search));

            if (matchCuadrilla && matchEncargo && matchEstado && matchSearch) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        document.getElementById('visible-count-badge').textContent = visibleCount + ' registros';
        document.getElementById('inventory-empty').classList.toggle('hidden', visibleCount > 0);
    }

    function resetInventoryFilters() {
        document.getElementById('filter-search').value = '';
        document.getElementById('filter-encargo').value = 'todos';
        document.getElementById('filter-estado').value = 'todos';
        setCuadrillaFilter('todas');
    }

    function handleNewEquipment(e) {
        e.preventDefault();
        const codigo = document.getElementById('new-codigo').value;
        const nombre = document.getElementById('new-nombre').value;
        const cantidad = document.getElementById('new-cantidad').value;
        const cuadrilla = document.getElementById('new-cuadrilla').value;
        const responsable = document.getElementById('new-responsable').value;
        const encargo = document.getElementById('new-encargo').value;

        const tbody = document.getElementById('inventory-tbody');
        const tr = document.createElement('tr');
        tr.className = 'inventory-row bg-blue-50/40 hover:bg-slate-50/80 transition-colors';
        tr.setAttribute('data-cuadrilla', cuadrilla);
        tr.setAttribute('data-encargo', encargo);
        tr.setAttribute('data-estado', 'En Tránsito');
        tr.setAttribute('data-search', (codigo + ' ' + nombre + ' ' + responsable + ' ' + cuadrilla + ' ' + encargo).toLowerCase());

        tr.innerHTML = `
            <td class="py-4 px-6">
                <div class="font-bold text-slate-800 text-xs">${codigo}</div>
                <div class="text-[11px] text-slate-400 font-mono mt-0.5">SN-NEW-2026</div>
            </td>
            <td class="py-4 px-5">
                <div class="font-bold text-slate-800">${nombre}</div>
                <div class="text-xs text-blue-600 font-medium mt-0.5">Recién asignado • Hace instantes</div>
            </td>
            <td class="py-4 px-4 font-semibold text-slate-700 text-xs whitespace-nowrap">${cantidad}</td>
            <td class="py-4 px-5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    ${cuadrilla}
                </span>
                <div class="text-xs text-slate-500 mt-1">${responsable}</div>
            </td>
            <td class="py-4 px-5">
                <div class="font-semibold text-slate-700 text-xs">${encargo}</div>
                <div class="text-xs text-slate-400 mt-0.5">León, Nicaragua</div>
            </td>
            <td class="py-4 px-5 whitespace-nowrap">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#fef3c7] text-[#d97706]">
                    En Tránsito
                </span>
            </td>
            <td class="py-4 px-6 text-right whitespace-nowrap">
                <a href="{{ route('rastreo') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100">
                    <span>Rastrear</span>
                </a>
            </td>
        `;
        tbody.prepend(tr);
        document.getElementById('modal-nuevo-equipo').classList.add('hidden');
        document.getElementById('form-nuevo-equipo').reset();
        applyInventoryFilters();
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (currentCuadrilla !== 'todas') {
            applyInventoryFilters();
        }
    });
</script>
@endpush
