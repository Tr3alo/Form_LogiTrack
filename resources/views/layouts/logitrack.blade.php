<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogiTrack - {{ $pageTitle ?? 'Resumen Operativo' }}</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet CSS (Geolocalización León, Nicaragua) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <!-- Vite Compiled Tailwind CSS + Fallback CDN -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
            background-color: #f4f6f9;
            color: #1e293b;
        }
        /* Topographic pattern for the mini GPS card */
        .topo-pattern {
            background-color: #e2e8f0;
            background-image: 
                radial-gradient(circle at 20% 35%, transparent 25%, rgba(148, 163, 184, 0.22) 26%, rgba(148, 163, 184, 0.22) 27%, transparent 28%),
                radial-gradient(circle at 20% 35%, transparent 45%, rgba(148, 163, 184, 0.18) 46%, rgba(148, 163, 184, 0.18) 47%, transparent 48%),
                radial-gradient(circle at 80% 70%, transparent 20%, rgba(148, 163, 184, 0.22) 21%, rgba(148, 163, 184, 0.22) 22%, transparent 23%),
                radial-gradient(circle at 80% 70%, transparent 40%, rgba(148, 163, 184, 0.18) 41%, rgba(148, 163, 184, 0.18) 42%, transparent 43%),
                radial-gradient(circle at 50% 50%, transparent 60%, rgba(148, 163, 184, 0.15) 61%, rgba(148, 163, 184, 0.15) 62%, transparent 63%);
        }
        /* Custom pulse animation for GPS pins and destination signals */
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 0.8; }
            80%, 100% { transform: scale(2.2); opacity: 0; }
        }
        .gps-pulse::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 9999px;
            background-color: currentColor;
            animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
            z-index: 0;
        }
        /* Leaflet custom popup styling */
        .leaflet-popup-content-wrapper {
            border-radius: 12px !important;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.18) !important;
            padding: 2px !important;
        }
        .leaflet-popup-content {
            margin: 12px 14px !important;
            font-family: 'Inter', sans-serif !important;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex bg-[#f4f6f9] antialiased selection:bg-blue-600 selection:text-white">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-[#111827] text-slate-300 flex flex-col shrink-0 min-h-screen select-none border-r border-slate-800/80">
        <!-- Brand Logo -->
        <div class="h-20 px-6 flex items-center gap-3 border-b border-slate-800/90">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <!-- Route & Location Pins Icon matching screenshot -->
                <div class="w-9 h-9 rounded-lg flex items-center justify-center text-[#2563eb] group-hover:scale-105 transition-transform">
                    <svg class="w-8 h-8" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Top-right pin -->
                        <circle cx="25" cy="9" r="3.5" stroke="#2563eb" stroke-width="2.5" fill="#111827"/>
                        <!-- Bottom-left pin -->
                        <circle cx="10" cy="24" r="3.5" stroke="#2563eb" stroke-width="2.5" fill="#111827"/>
                        <!-- Connecting S-curve route -->
                        <path d="M13.5 24H21C24.5 24 26.5 22 26.5 19.5C26.5 17 24.5 15.5 21 15.5H15C11.5 15.5 9.5 13.5 9.5 11C9.5 8.5 11.5 7 15 7H21" stroke="#2563eb" stroke-width="2.6" stroke-linecap="round"/>
                        <!-- Bottom base line -->
                        <path d="M6 30H28" stroke="#2563eb" stroke-width="2.6" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="text-[22px] font-extrabold tracking-tight text-white">
                    LogiTrack<span class="text-[#2563eb]">.</span>
                </span>
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-[15px] font-medium transition-all duration-150 {{ ($activePage ?? '') === 'dashboard' ? 'bg-[#2563eb] text-white shadow-lg shadow-blue-600/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0 {{ ($activePage ?? '') === 'dashboard' ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="3" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 15l3.5-3.5 2.5 2.5L17 9" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Inventario -->
            <a href="{{ route('inventario') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-[15px] font-medium transition-all duration-150 {{ ($activePage ?? '') === 'inventario' ? 'bg-[#2563eb] text-white shadow-lg shadow-blue-600/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0 {{ ($activePage ?? '') === 'inventario' ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Inventario</span>
            </a>

            <!-- Rastreo GPS -->
            <a href="{{ route('rastreo') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-[15px] font-medium transition-all duration-150 {{ ($activePage ?? '') === 'rastreo' ? 'bg-[#2563eb] text-white shadow-lg shadow-blue-600/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0 {{ ($activePage ?? '') === 'rastreo' ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Rastreo GPS</span>
            </a>

            <!-- Rutas y Envíos -->
            <a href="{{ route('rutas') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-[15px] font-medium transition-all duration-150 {{ ($activePage ?? '') === 'rutas' ? 'bg-[#2563eb] text-white shadow-lg shadow-blue-600/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0 {{ ($activePage ?? '') === 'rutas' ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Rutas y Envíos</span>
            </a>

            <!-- Personal -->
            <a href="{{ route('personal') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-[15px] font-medium transition-all duration-150 {{ ($activePage ?? '') === 'personal' ? 'bg-[#2563eb] text-white shadow-lg shadow-blue-600/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0 {{ ($activePage ?? '') === 'personal' ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Personal</span>
            </a>
        </nav>

        <!-- Bottom Logout -->
        <div class="p-4 border-t border-slate-800/90">
            <button type="button"
                    onclick="document.getElementById('logout-toast').classList.remove('hidden')"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800/60 transition-colors cursor-pointer">
                <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Cerrar Sesión</span>
            </button>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- TOP HEADER -->
        <header class="h-20 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between sticky top-0 z-30">
            <div>
                <h1 class="text-[20px] font-bold text-slate-800 leading-tight">{{ $pageTitle ?? 'Resumen Operativo' }}</h1>
                <p class="text-[13px] text-slate-400 mt-0.5">{{ $pageSubtitle ?? 'Vista general de flota y herramientas - 29/10/2026' }}</p>
            </div>

            <div class="flex items-center gap-5">
                <!-- Notification Bell Dropdown -->
                <div class="relative">
                    <button type="button"
                            onclick="document.getElementById('notif-dropdown').classList.toggle('hidden')"
                            class="relative p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-full transition-colors cursor-pointer"
                            title="Notificaciones operativas">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                    </button>

                    <!-- Dropdown Panel -->
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-200 py-3 z-50">
                        <div class="px-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Alertas de Operación (León)</span>
                            <span class="text-[11px] bg-red-100 text-red-700 font-semibold px-2 py-0.5 rounded-full">1 Crítica</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto">
                            <a href="{{ route('inventario', ['cuadrilla' => 'Cuadrilla B']) }}" class="block px-4 py-3 hover:bg-slate-50 transition-colors">
                                <p class="text-xs font-semibold text-red-600">Reporte de Fallo • Cuadrilla B</p>
                                <p class="text-xs text-slate-600 mt-0.5">Taladro Percutor DeWalt reportado por Téc. Luis Silva en retorno a Bodega Central.</p>
                            </a>
                            <a href="{{ route('rastreo', ['focus' => 'cuadrilla-c']) }}" class="block px-4 py-3 hover:bg-slate-50 transition-colors">
                                <p class="text-xs font-semibold text-blue-600">GPS Activo • Cuadrilla C</p>
                                <p class="text-xs text-slate-600 mt-0.5">A 2.5 km de Residencial Las Mercedes (León Centro).</p>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="h-8 w-px bg-slate-200"></div>

                <!-- User Profile -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#0284c7] text-white font-bold text-sm flex items-center justify-center shadow-sm">
                        {{ $user['initials'] ?? 'CM' }}
                    </div>
                    <div class=" leading-tight">
                        <div class="text-[14px] font-bold text-slate-800">{{ $user['name'] ?? 'Carlos Martínez' }}</div>
                        <div class="text-[12px] text-slate-400">{{ $user['role'] ?? 'Gerente de Operaciones' }}</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

    <!-- Demo Logout Toast -->
    <div id="logout-toast" class="hidden fixed bottom-5 right-5 bg-slate-900 text-white px-5 py-3.5 rounded-xl shadow-2xl border border-slate-700 flex items-center gap-3 z-50">
        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-ping"></span>
        <div class="text-xs">
            <p class="font-semibold">Modo Demostración LogiTrack</p>
            <p class="text-slate-400">Sesión activa de {{ $user['name'] ?? 'Carlos Martínez' }} (León, Nicaragua)</p>
        </div>
        <button onclick="document.getElementById('logout-toast').classList.add('hidden')" class="ml-2 text-slate-400 hover:text-white text-xs font-bold cursor-pointer">✕</button>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @stack('scripts')
</body>
</html>
