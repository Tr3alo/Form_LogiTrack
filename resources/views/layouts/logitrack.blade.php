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
        /* Patrón topográfico para la tarjeta de vista previa GPS en el Dashboard */
        .topo-pattern {
            background-color: #e2e8f0;
            background-image: 
                radial-gradient(circle at 20% 35%, transparent 25%, rgba(148, 163, 184, 0.22) 26%, rgba(148, 163, 184, 0.22) 27%, transparent 28%),
                radial-gradient(circle at 20% 35%, transparent 45%, rgba(148, 163, 184, 0.18) 46%, rgba(148, 163, 184, 0.18) 47%, transparent 48%),
                radial-gradient(circle at 80% 70%, transparent 20%, rgba(148, 163, 184, 0.22) 21%, rgba(148, 163, 184, 0.22) 22%, transparent 23%),
                radial-gradient(circle at 80% 70%, transparent 40%, rgba(148, 163, 184, 0.18) 41%, rgba(148, 163, 184, 0.18) 42%, transparent 43%),
                radial-gradient(circle at 50% 50%, transparent 60%, rgba(148, 163, 184, 0.15) 61%, rgba(148, 163, 184, 0.15) 62%, transparent 63%);
        }
        /* Estilos personalizados para los popups de Leaflet */
        .leaflet-popup-content-wrapper {
            border-radius: 12px !important;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.18) !important;
            padding: 2px !important;
        }
        .leaflet-popup-content {
            margin: 12px 14px !important;
            font-family: 'Inter', sans-serif !important;
        }
        @keyframes bell-shake {
            0%, 100% { transform: rotate(0deg); }
            20%, 60% { transform: rotate(-12deg); }
            40%, 80% { transform: rotate(12deg); }
        }
        .animate-bell {
            animation: bell-shake 0.8s ease-in-out 2;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex bg-[#f4f6f9] antialiased selection:bg-blue-600 selection:text-white">

    @php
        $isFreeAccount = (bool) ($user['is_free'] ?? false);
    @endphp

    {{-- =========================================================================
         SIDEBAR PRINCIPAL DE NAVEGACIÓN (LogiTrack.)
         Aplica restricciones visuales y funcionales según el tipo de cuenta:
         - Cuenta FREE: Solo tiene acceso a Dashboard e Inventario.
           Los módulos Rastreo GPS, Rutas y Envíos, y Personal muestran candado PRO.
         - Cuenta PREMIUM: Acceso desbloqueado a los 5 módulos operativos.
       ========================================================================= --}}
    <aside class="w-64 bg-[#111827] text-slate-300 flex flex-col shrink-0 min-h-screen select-none border-r border-slate-800/80">
        <!-- Brand Logo -->
        <div class="h-20 px-6 flex items-center justify-between border-b border-slate-800/90">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group" title="Ir al Resumen Operativo">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center text-[#2563eb] group-hover:scale-105 transition-transform">
                    <svg class="w-8 h-8" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="25" cy="9" r="3.5" stroke="#2563eb" stroke-width="2.5" fill="#111827"/>
                        <circle cx="10" cy="24" r="3.5" stroke="#2563eb" stroke-width="2.5" fill="#111827"/>
                        <path d="M13.5 24H21C24.5 24 26.5 22 26.5 19.5C26.5 17 24.5 15.5 21 15.5H15C11.5 15.5 9.5 13.5 9.5 11C9.5 8.5 11.5 7 15 7H21" stroke="#2563eb" stroke-width="2.6" stroke-linecap="round"/>
                        <path d="M6 30H28" stroke="#2563eb" stroke-width="2.6" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="text-[22px] font-extrabold tracking-tight text-white">
                    LogiTrack<span class="text-[#2563eb]">.</span>
                </span>
            </a>
        </div>

        <!-- Enlaces de Navegación Operativa -->
        <nav class="flex-1 px-4 py-6 space-y-1.5">
            <div class="px-3 pb-1.5 flex items-center justify-between text-[10.5px] font-bold uppercase tracking-wider text-slate-500">
                <span>Módulos Operativos</span>
                @if($isFreeAccount)
                    <span class="text-amber-400">Modo Free</span>
                @else
                    <span class="text-emerald-400">Premium</span>
                @endif
            </div>

            <!-- 1. Dashboard (Permitido en Free y Premium) -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-[15px] font-medium transition-all duration-150 {{ ($activePage ?? '') === 'dashboard' ? 'bg-[#2563eb] text-white shadow-lg shadow-blue-600/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0 {{ ($activePage ?? '') === 'dashboard' ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="3" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 15l3.5-3.5 2.5 2.5L17 9" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- 2. Inventario (Permitido en Free y Premium) -->
            <a href="{{ route('inventario') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-[15px] font-medium transition-all duration-150 {{ ($activePage ?? '') === 'inventario' ? 'bg-[#2563eb] text-white shadow-lg shadow-blue-600/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0 {{ ($activePage ?? '') === 'inventario' ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Inventario</span>
            </a>

            <!-- 3. Rastreo GPS (RESTRINGIDO EN CUENTA FREE) -->
            <a href="{{ route('rastreo') }}"
               class="flex items-center justify-between px-4 py-3 rounded-lg text-[15px] font-medium transition-all duration-150 {{ ($activePage ?? '') === 'rastreo' ? 'bg-[#2563eb] text-white shadow-lg shadow-blue-600/25' : ($isFreeAccount ? 'text-slate-500 bg-slate-900/40 hover:bg-slate-800/40 border border-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/60') }}"
               title="{{ $isFreeAccount ? 'Bloqueado en Cuenta Free — Renueva a Premium ($60/mes)' : 'Rastreo GPS en Tiempo Real' }}">
                <div class="flex items-center gap-3.5">
                    <svg class="w-5 h-5 shrink-0 {{ ($activePage ?? '') === 'rastreo' ? 'text-white' : ($isFreeAccount ? 'text-slate-600' : 'text-slate-400') }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Rastreo GPS</span>
                </div>
                @if($isFreeAccount)
                    <span class="px-1.5 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-500/15 text-amber-400 border border-amber-500/30">
                        🔒 PRO
                    </span>
                @endif
            </a>

            <!-- 4. Rutas y Envíos (RESTRINGIDO EN CUENTA FREE) -->
            <a href="{{ route('rutas') }}"
               class="flex items-center justify-between px-4 py-3 rounded-lg text-[15px] font-medium transition-all duration-150 {{ ($activePage ?? '') === 'rutas' ? 'bg-[#2563eb] text-white shadow-lg shadow-blue-600/25' : ($isFreeAccount ? 'text-slate-500 bg-slate-900/40 hover:bg-slate-800/40 border border-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/60') }}"
               title="{{ $isFreeAccount ? 'Bloqueado en Cuenta Free — Renueva a Premium ($60/mes)' : 'Control de Rutas y Envíos' }}">
                <div class="flex items-center gap-3.5">
                    <svg class="w-5 h-5 shrink-0 {{ ($activePage ?? '') === 'rutas' ? 'text-white' : ($isFreeAccount ? 'text-slate-600' : 'text-slate-400') }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Rutas y Envíos</span>
                </div>
                @if($isFreeAccount)
                    <span class="px-1.5 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-500/15 text-amber-400 border border-amber-500/30">
                        🔒 PRO
                    </span>
                @endif
            </a>

            <!-- 5. Personal (RESTRINGIDO EN CUENTA FREE) -->
            <a href="{{ route('personal') }}"
               class="flex items-center justify-between px-4 py-3 rounded-lg text-[15px] font-medium transition-all duration-150 {{ ($activePage ?? '') === 'personal' ? 'bg-[#2563eb] text-white shadow-lg shadow-blue-600/25' : ($isFreeAccount ? 'text-slate-500 bg-slate-900/40 hover:bg-slate-800/40 border border-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/60') }}"
               title="{{ $isFreeAccount ? 'Bloqueado en Cuenta Free — Renueva a Premium ($60/mes)' : 'Directorio de Personal' }}">
                <div class="flex items-center gap-3.5">
                    <svg class="w-5 h-5 shrink-0 {{ ($activePage ?? '') === 'personal' ? 'text-white' : ($isFreeAccount ? 'text-slate-600' : 'text-slate-400') }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Personal</span>
                </div>
                @if($isFreeAccount)
                    <span class="px-1.5 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-500/15 text-amber-400 border border-amber-500/30">
                        🔒 PRO
                    </span>
                @endif
            </a>

            <!-- Separador Cuenta SaaS -->
            <div class="pt-4 pb-1.5 px-3 text-[10.5px] font-bold uppercase tracking-wider text-slate-500">
                Cuenta Empresarial SaaS
            </div>

            <!-- Mi Suscripción -->
            <a href="{{ route('suscripcion') }}"
               class="flex items-center justify-between px-4 py-3 rounded-lg text-[15px] font-medium transition-all duration-150 {{ ($activePage ?? '') === 'suscripcion' ? 'bg-[#2563eb] text-white shadow-lg shadow-blue-600/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <div class="flex items-center gap-3.5">
                    <svg class="w-5 h-5 shrink-0 {{ ($activePage ?? '') === 'suscripcion' ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="5" width="20" height="14" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 10h20M6 15h4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Suscripción</span>
                </div>
                @if(($user['subscription_plan'] ?? 'trial') === 'pro' && ! $isFreeAccount)
                    <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        PRO
                    </span>
                @elseif($isFreeAccount)
                    <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-md bg-red-500/20 text-red-300 border border-red-500/30">
                        FREE
                    </span>
                @else
                    <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-md bg-blue-500/20 text-blue-300 border border-blue-500/30">
                        {{ $user['trial_days_remaining'] ?? 11 }}d
                    </span>
                @endif
            </a>

            @if($isFreeAccount)
                <!-- Tarjeta lateral recordatoria cuando está en Cuenta Free -->
                <div class="mx-2 mt-4 p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-xs">
                    <p class="font-bold text-amber-300">⚠️ Cuenta Free Activa</p>
                    <p class="text-[11px] text-slate-300 mt-1 leading-relaxed">
                        Solo tienes vista a <strong>Dashboard</strong> e <strong>Inventario</strong>.
                    </p>
                    <a href="{{ route('suscripcion') }}"
                       class="mt-2.5 block text-center py-1.5 px-3 rounded-lg bg-[#2563eb] hover:bg-blue-500 text-white font-bold text-[11px] transition-colors">
                        Renovar a Premium ($60/mes)
                    </a>
                </div>
            @endif
        </nav>

        <!-- Enlace a Landing Page y Cierre de Sesión -->
        <div class="p-4 border-t border-slate-800/90 space-y-1.5">
            <a href="{{ route('landing') }}"
               class="w-full flex items-center gap-3 px-4 py-2 rounded-lg text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-800/60 transition-colors">
                <svg class="w-4 h-4 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Ver Landing Page SaaS</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800/60 transition-colors cursor-pointer">
                    <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
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

            <div class="flex items-center gap-4">

                <!-- Botón Rápido para alternar entre Cuenta Free y Cuenta Premium durante la Demo -->
                <div class="hidden xl:flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <form method="POST" action="{{ route('suscripcion.update') }}">
                        @csrf
                        <input type="hidden" name="action" value="switch_freemium">
                        <button type="submit"
                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer {{ $isFreeAccount ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                                title="Probar restricciones de Cuenta Free (Solo Dashboard e Inventario)">
                            Cuenta Free
                        </button>
                    </form>
                    <form method="POST" action="{{ route('suscripcion.update') }}">
                        @csrf
                        <input type="hidden" name="action" value="upgrade_pro">
                        <button type="submit"
                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer {{ ! $isFreeAccount ? 'bg-[#2563eb] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                                title="Activar Cuenta Premium ($60/mes) con todo desbloqueado">
                            Cuenta Premium
                        </button>
                    </form>
                </div>

                <!-- Píldora de estado de Suscripción SaaS -->
                <a href="{{ route('suscripcion') }}"
                   class="hidden md:inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition-all {{ $isFreeAccount ? 'bg-amber-50 text-amber-800 border-amber-300 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}"
                   title="Administrar suscripción y facturación">
                    <span class="w-2 h-2 rounded-full {{ $isFreeAccount ? 'bg-amber-500 animate-ping' : 'bg-emerald-500' }}"></span>
                    <span>{{ $user['plan_badge'] ?? 'Cuenta Premium' }}</span>
                </a>

                {{-- =========================================================================
                     CAMPANITA DE NOTIFICACIONES
                     Si el usuario tiene una Cuenta FREE, cada vez que cambia entre
                     Dashboard e Inventario se despliega automáticamente la notificación
                     indicándole que debe renovar su suscripción ($60/mes).
                   ========================================================================= --}}
                <div class="relative">
                    <button type="button"
                            id="notif-bell-btn"
                            onclick="document.getElementById('notif-dropdown').classList.toggle('hidden')"
                            class="relative p-2.5 rounded-full transition-colors cursor-pointer {{ $isFreeAccount ? 'bg-amber-50 text-amber-600 ring-2 ring-amber-300 animate-bell' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}"
                            title="Notificaciones operativas y de suscripción">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        @if($isFreeAccount)
                            <span class="absolute -top-1 -right-1 px-1.5 py-0.5 bg-red-600 text-white text-[9px] font-extrabold rounded-full ring-2 ring-white">
                                !
                            </span>
                        @else
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                        @endif
                    </button>

                    <!-- Dropdown Panel de la Campanita (Se abre automáticamente al cambiar de vista si es Cuenta Free) -->
                    <div id="notif-dropdown"
                         class="{{ ($triggerBellRenewal ?? false) ? '' : 'hidden' }} absolute right-0 mt-3 w-96 bg-white rounded-2xl shadow-2xl border {{ $isFreeAccount ? 'border-amber-300 ring-4 ring-amber-500/10' : 'border-slate-200' }} py-3 z-50">
                        <div class="px-4 pb-2.5 border-b border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-700">
                                Centro de Notificaciones
                            </span>
                            <button type="button"
                                    onclick="document.getElementById('notif-dropdown').classList.add('hidden')"
                                    class="text-xs font-bold text-slate-400 hover:text-slate-700 cursor-pointer">
                                Cerrar ✕
                            </button>
                        </div>

                        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                            @if($isFreeAccount)
                                <!-- ALERTA OBLIGATORIA DE RENOVACIÓN PARA CUENTA FREE -->
                                <div class="p-4 bg-amber-50/90 border-l-4 border-l-amber-500">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-xs font-extrabold text-amber-900">
                                            🔔 Suscripción Pendiente de Renovación
                                        </p>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-200 text-amber-900">
                                            Cuenta Free
                                        </span>
                                    </div>
                                    <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                        Debes <strong>renovar tu suscripción a Premium ($60/mes)</strong>. En modo Free solo tienes acceso a <em>Dashboard</em> e <em>Inventario</em> (Rastreo GPS, Rutas y Personal están bloqueados).
                                    </p>
                                    <div class="mt-3 flex items-center gap-2">
                                        <a href="{{ route('suscripcion') }}"
                                           class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-lg bg-[#2563eb] hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition-all">
                                            Renovar Suscripción Ahora →
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <a href="{{ route('inventario', ['cuadrilla' => 'Cuadrilla B']) }}" class="block px-4 py-3 hover:bg-slate-50 transition-colors">
                                <p class="text-xs font-semibold text-red-600">Reporte de Fallo • Cuadrilla B</p>
                                <p class="text-xs text-slate-600 mt-0.5">Taladro Percutor DeWalt reportado por Téc. Luis Silva en retorno a Bodega Central.</p>
                            </a>

                            @if(! $isFreeAccount)
                                <a href="{{ route('rastreo', ['focus' => 'cuadrilla-c']) }}" class="block px-4 py-3 hover:bg-slate-50 transition-colors">
                                    <p class="text-xs font-semibold text-blue-600">GPS Activo • Cuadrilla C</p>
                                    <p class="text-xs text-slate-600 mt-0.5">A 2.5 km de Residencial Las Mercedes (León Centro).</p>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="h-8 w-px bg-slate-200"></div>

                <!-- User Profile -->
                <a href="{{ route('suscripcion') }}" class="flex items-center gap-3 group" title="Ver perfil y suscripción de {{ $user['name'] ?? 'Carlos Martínez' }}">
                    <div class="w-10 h-10 rounded-full bg-[#0284c7] text-white font-bold text-sm flex items-center justify-center shadow-sm group-hover:ring-2 group-hover:ring-blue-400 transition-all">
                        {{ $user['initials'] ?? 'CM' }}
                    </div>
                    <div class="leading-tight">
                        <div class="text-[14px] font-bold text-slate-800 group-hover:text-blue-600 transition-colors">{{ $user['name'] ?? 'Carlos Martínez' }}</div>
                        <div class="text-[12px] text-slate-400">{{ $user['role'] ?? 'Gerente de Operaciones' }}</div>
                    </div>
                </a>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="flex-1 p-8">
            {{-- Mensaje cuando un usuario Free intenta entrar a Rastreo GPS, Rutas o Personal --}}
            @if(session('restricted_error'))
                <div class="mb-6 p-4 rounded-2xl bg-amber-50 border-2 border-amber-300 text-amber-900 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-start sm:items-center gap-3">
                        <span class="px-2.5 py-1 rounded-lg bg-amber-200 text-amber-900 font-extrabold uppercase">🔒 Restricción Free</span>
                        <span class="font-semibold">{{ session('restricted_error') }}</span>
                    </div>
                    <form method="POST" action="{{ route('suscripcion.update') }}" class="shrink-0">
                        @csrf
                        <input type="hidden" name="action" value="upgrade_pro">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#2563eb] hover:bg-blue-700 text-white font-bold text-xs cursor-pointer">
                            Activar Premium ($60/mes)
                        </button>
                    </form>
                </div>
            @endif

            @if(session('status') && ($activePage ?? '') !== 'inventario')
                <div class="mb-6 px-5 py-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>{{ session('status') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold cursor-pointer">✕</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @stack('scripts')
</body>
</html>
