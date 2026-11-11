<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogiTrack. — Software SaaS de Logística, Inventario y Rastreo GPS</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Vite Compiled Tailwind CSS + Fallback CDN -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
            color: #0f172a;
        }
    </style>
</head>
<body class="bg-[#f8fafc] antialiased selection:bg-blue-600 selection:text-white">

    {{-- =========================================================================
         BARRA DE NAVEGACIÓN COMERCIAL (LANDING PAGE SAAS)
         Mantiene la identidad de marca de LogiTrack. y ofrece acceso directo a:
         - Secciones informativas (Módulos, Cómo Funciona, Precios Freemium / $60)
         - Login, Registro de Prueba de 14 días y acceso directo al Dashboard Demo.
       ========================================================================= --}}
    <header class="sticky top-0 z-50 bg-[#111827]/95 backdrop-blur-md border-b border-slate-800 text-white">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center text-[#2563eb]">
                    <svg class="w-8 h-8" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="25" cy="9" r="3.5" stroke="#3b82f6" stroke-width="2.5" fill="#111827"/>
                        <circle cx="10" cy="24" r="3.5" stroke="#3b82f6" stroke-width="2.5" fill="#111827"/>
                        <path d="M13.5 24H21C24.5 24 26.5 22 26.5 19.5C26.5 17 24.5 15.5 21 15.5H15C11.5 15.5 9.5 13.5 9.5 11C9.5 8.5 11.5 7 15 7H21" stroke="#3b82f6" stroke-width="2.6" stroke-linecap="round"/>
                        <path d="M6 30H28" stroke="#3b82f6" stroke-width="2.6" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="text-2xl font-extrabold tracking-tight text-white">
                    LogiTrack<span class="text-[#3b82f6]">.</span>
                </span>
            </a>

            <!-- Menú Central -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                <a href="#problema-solucion" class="hover:text-white transition-colors">Soluciones</a>
                <a href="#modulos" class="hover:text-white transition-colors">Módulos del Panel</a>
                <a href="#precios" class="hover:text-white transition-colors">Precios (14 Días Gratis)</a>
            </nav>

            <!-- Botones de Acción (Login, Demo Dashboard, Prueba Gratis) -->
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-200 bg-slate-800/90 hover:bg-slate-700 border border-slate-700 transition-all">
                    <span>Ver Panel Demo</span>
                </a>
                <a href="{{ route('login') }}"
                   class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-200 hover:text-white transition-colors">
                    Iniciar Sesión
                </a>
                <a href="{{ route('register') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-bold bg-[#2563eb] hover:bg-blue-500 text-white shadow-lg shadow-blue-600/30 transition-all">
                    Prueba Gratis 14 Días
                </a>
            </div>
        </div>
    </header>

    {{-- =========================================================================
         HERO SECTION: PROPUESTA DE VALOR DEL SAAS Y VISTA PREVIA DEL DASHBOARD
       ========================================================================= --}}
    <section class="relative bg-[#111827] text-white pt-16 pb-24 overflow-hidden border-b border-slate-800">
        <!-- Luces decorativas de fondo -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[400px] bg-blue-600/15 blur-[130px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="max-w-3xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-400/25 text-blue-400 text-xs font-semibold mb-6">
                    <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span>
                    <span>Plataforma SaaS de Control Logístico, Inventario y Rastreo GPS</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-[54px] font-extrabold tracking-tight leading-[1.12] text-white">
                    Elimina el extravío de herramientas y <span class="text-[#3b82f6]">rastrea tus cuadrillas</span> en tiempo real.
                </h1>

                <p class="mt-6 text-base sm:text-lg text-slate-300 leading-relaxed max-w-2xl mx-auto">
                    <strong>LogiTrack.</strong> centraliza la bodega, la asignación de equipos por encargo, las señales de destino y la ubicación GPS de tu flota en un solo panel diseñado para empresas operativas en Nicaragua.
                </p>

                <!-- CTAs Principales del Modelo Freemium / 14 Días Gratis -->
                <div class="mt-9 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl bg-[#2563eb] hover:bg-blue-500 text-white font-bold text-base shadow-xl shadow-blue-600/30 transition-all">
                        <span>Empezar Prueba Gratis de 14 Días</span>
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>

                    <a href="{{ route('dashboard') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl bg-slate-800/90 hover:bg-slate-700 text-white font-semibold text-base border border-slate-700 transition-all">
                        <svg class="w-5 h-5 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="3"/><path d="M7 15l3.5-3.5 2.5 2.5L17 9"/>
                        </svg>
                        <span>Explorar Dashboard en Vivo</span>
                    </a>
                </div>

                <p class="mt-4 text-xs text-slate-400">
                    ✓ Modelo Freemium disponible &nbsp;•&nbsp; ✓ <strong>14 días de prueba gratis</strong> en el Plan Pro, luego <strong>$60 USD / mes</strong> &nbsp;•&nbsp; ✓ Sin contratos forzosos
                </p>
            </div>

            <!-- Vista Previa Interactiva del Dashboard dentro del Hero -->
            <div class="mt-14 bg-[#0b1120] p-3 sm:p-5 rounded-3xl border border-slate-800 shadow-2xl">
                <div class="bg-[#f4f6f9] rounded-2xl overflow-hidden border border-slate-200 text-slate-800">
                    <!-- Barra Superior Simulada del Navegador -->
                    <div class="bg-white px-6 py-3.5 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-400"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                            <span class="ml-3 text-xs font-bold text-slate-700">Resumen Operativo — Vista general de flota y herramientas (León, Nicaragua)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold">Prueba 14 Días Activa</span>
                            <span class="w-7 h-7 rounded-full bg-[#0284c7] text-white text-xs font-bold flex items-center justify-center">CM</span>
                        </div>
                    </div>

                    <!-- Mini Preview del Dashboard real -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white rounded-xl p-4 border border-slate-100 border-l-4 border-l-[#2563eb] shadow-xs">
                                <p class="text-xs font-medium text-slate-500">Equipos Asignados</p>
                                <p class="text-2xl font-extrabold text-slate-900 mt-1">124</p>
                                <p class="text-[11px] font-semibold text-emerald-600 mt-2">↗ +12% esta semana</p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-slate-100 border-l-4 border-l-[#f59e0b] shadow-xs">
                                <p class="text-xs font-medium text-slate-500">Envíos en Ruta</p>
                                <p class="text-2xl font-extrabold text-slate-900 mt-1">8</p>
                                <p class="text-[11px] text-slate-500 mt-2">3 con llegada próxima en León</p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-slate-100 border-l-4 border-l-[#ef4444] shadow-xs">
                                <p class="text-xs font-medium text-slate-500">Alertas / Extravíos</p>
                                <p class="text-2xl font-extrabold text-slate-900 mt-1">1</p>
                                <p class="text-[11px] font-medium text-red-600 mt-2">Revisar taladro asignado a Cuadrilla B</p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-slate-100 border-l-4 border-l-[#22c55e] shadow-xs">
                                <p class="text-xs font-medium text-slate-500">Eficiencia Operativa</p>
                                <p class="text-2xl font-extrabold text-slate-900 mt-1">94%</p>
                                <p class="text-[11px] font-medium text-emerald-600 mt-2">Tiempos de entrega óptimos</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                            <div class="lg:col-span-8 bg-white rounded-xl p-5 border border-slate-100">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-bold text-slate-800">Últimos Movimientos de Bodega</span>
                                    <a href="{{ route('inventario') }}" class="text-xs font-semibold text-blue-600 hover:underline">Abrir Inventario →</a>
                                </div>
                                <div class="divide-y divide-slate-100 text-xs">
                                    <div class="py-2.5 flex items-center justify-between">
                                        <span class="font-bold text-slate-800">Multímetro Digital Fluke</span>
                                        <span class="text-slate-500">Téc. Roberto Gómez • Subestación Sur, León</span>
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold">Entregado</span>
                                    </div>
                                    <div class="py-2.5 flex items-center justify-between">
                                        <span class="font-bold text-slate-800">Kit de Pinzas Amperimétricas</span>
                                        <span class="text-slate-500">Cuadrilla C • Residencial Las Mercedes</span>
                                        <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">En Tránsito</span>
                                    </div>
                                    <div class="py-2.5 flex items-center justify-between">
                                        <span class="font-bold text-slate-800">Taladro Percutor DeWalt</span>
                                        <span class="text-slate-500">Téc. Luis Silva • Bodega Central</span>
                                        <span class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold">Reporte Fallo</span>
                                    </div>
                                </div>
                            </div>

                            <div class="lg:col-span-4 bg-slate-200/80 rounded-xl p-5 flex flex-col items-center justify-center text-center border border-slate-200">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-2">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <circle cx="12" cy="12" r="7"/><circle cx="12" cy="12" r="2.5" fill="currentColor"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-blue-950">Cuadrilla C - León Centro</p>
                                <p class="text-xs text-blue-700 mt-0.5 mb-3">A 2.5 km de Residencial Las Mercedes</p>
                                <a href="{{ route('rastreo') }}" class="px-4 py-2 rounded-full bg-[#2563eb] text-white text-xs font-semibold hover:bg-blue-700 transition-colors">
                                    Ver Mapa GPS en Vivo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================================
         SECCIÓN DE PROBLEMA Y SOLUCIÓN LOGÍSTICA
       ========================================================================= --}}
    <section id="problema-solucion" class="py-20 max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-xs font-extrabold uppercase tracking-wider text-blue-600">¿Por qué LogiTrack?</span>
            <h2 class="text-3xl font-extrabold text-slate-900 mt-2">
                Diseñado para empresas que movilizan recursos valiosos todos los días
            </h2>
            <p class="text-sm text-slate-500 mt-3">
                Cuando tus herramientas salen de bodega en distintas cuadrillas, hojas de cálculo y mensajes sueltos no bastan para saber quién tiene cada equipo ni cuándo llegará a su destino.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-7">
            <div class="bg-white rounded-2xl p-7 border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Auditoría Total de Inventario</h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                    Filtra al instante qué herramientas están en <strong>Bodega Central</strong> y cuáles fueron asignadas a la <strong>Cuadrilla A, B, C o D</strong> o a un encargo técnico específico.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-7 border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Rastreo GPS y Señales de Destino</h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                    Visualiza en el mapa de <strong>León, Nicaragua</strong> la ubicación de cada unidad, su velocidad, el avance del trayecto y la señal exacta del destino donde debe llegar.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-7 border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Alertas de Fallos y Retornos</h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                    Recibe notificaciones inmediatas cuando un técnico reporta daño en un equipo (como un taladro o fusionadora) y monitorea su retorno a bodega.
                </p>
            </div>
        </div>
    </section>

    {{-- =========================================================================
         SECCIÓN DE MÓDULOS INTEGRADOS DEL PANEL
       ========================================================================= --}}
    <section id="modulos" class="py-16 bg-white border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-wider text-blue-600">Plataforma Todo en Uno</span>
                    <h2 class="text-3xl font-extrabold text-slate-900 mt-1">5 Módulos Operativos + Gestión SaaS</h2>
                </div>
                <a href="{{ route('dashboard') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">
                    Probar todos los módulos en el Dashboard →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('dashboard') }}" class="p-6 rounded-2xl bg-slate-50 hover:bg-blue-50/50 border border-slate-200/80 hover:border-blue-300 transition-all group">
                    <span class="text-xs font-bold text-blue-600">01 • RESUMEN OPERATIVO</span>
                    <h4 class="text-base font-bold text-slate-900 mt-1 group-hover:text-blue-600">Dashboard en Tiempo Real</h4>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">KPIs de equipos asignados, envíos en ruta, alertas críticas, eficiencia operativa y últimos movimientos de bodega.</p>
                </a>

                <a href="{{ route('inventario') }}" class="p-6 rounded-2xl bg-slate-50 hover:bg-blue-50/50 border border-slate-200/80 hover:border-blue-300 transition-all group">
                    <span class="text-xs font-bold text-blue-600">02 • CONTROL DE BODEGA</span>
                    <h4 class="text-base font-bold text-slate-900 mt-1 group-hover:text-blue-600">Inventario por Cuadrilla y Encargo</h4>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">Filtra herramientas, bobinas y equipos de medición por cuadrilla asignada, encargo activo o disponibilidad en bodega.</p>
                </a>

                <a href="{{ route('rastreo') }}" class="p-6 rounded-2xl bg-slate-50 hover:bg-blue-50/50 border border-slate-200/80 hover:border-blue-300 transition-all group">
                    <span class="text-xs font-bold text-blue-600">03 • FUNCIÓN GPS</span>
                    <h4 class="text-base font-bold text-slate-900 mt-1 group-hover:text-blue-600">Rastreo GPS en León, Nicaragua</h4>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">Mapa callejero y satelital con simulación en vivo de Cuadrillas, velocidad y carga a bordo.</p>
                </a>

                <a href="{{ route('rutas') }}" class="p-6 rounded-2xl bg-slate-50 hover:bg-blue-50/50 border border-slate-200/80 hover:border-blue-300 transition-all group">
                    <span class="text-xs font-bold text-blue-600">04 • DESPACHOS Y BALIZAS</span>
                    <h4 class="text-base font-bold text-slate-900 mt-1 group-hover:text-blue-600">Rutas y Señales de Destino</h4>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">Lista de envíos con señal clara del punto de destino, coordenadas GPS, porcentaje de avance y ETA.</p>
                </a>

                <a href="{{ route('personal') }}" class="p-6 rounded-2xl bg-slate-50 hover:bg-blue-50/50 border border-slate-200/80 hover:border-blue-300 transition-all group">
                    <span class="text-xs font-bold text-blue-600">05 • RECURSOS HUMANOS</span>
                    <h4 class="text-base font-bold text-slate-900 mt-1 group-hover:text-blue-600">Directorio de Personal y Roles</h4>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">Control de Encargados de Bodega, Choferes de flota, Jefes de Cuadrilla y Técnicos Especialistas.</p>
                </a>

                <a href="{{ route('suscripcion') }}" class="p-6 rounded-2xl bg-slate-50 hover:bg-blue-50/50 border border-slate-200/80 hover:border-blue-300 transition-all group">
                    <span class="text-xs font-bold text-blue-600">06 • ADMINISTRACIÓN SAAS</span>
                    <h4 class="text-base font-bold text-slate-900 mt-1 group-hover:text-blue-600">Suscripción y Facturación</h4>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">Panel para administrar los 14 días de prueba gratuita, el plan mensual de $60 USD y recibos corporativos.</p>
                </a>
            </div>
        </div>
    </section>

    {{-- =========================================================================
         SECCIÓN DE PRECIOS: MODELO FREEMIUM + PRUEBA DE 14 DÍAS -> $60 AL MES
       ========================================================================= --}}
    <section id="precios" class="py-20 max-w-6xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="px-3.5 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-extrabold uppercase tracking-wider">
                Modelo Freemium Transparente
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                Prueba todo el poder de LogiTrack gratis por 14 días
            </h2>
            <p class="text-sm text-slate-500 mt-3">
                Comienza sin costo con nuestro modelo Freemium o activa los <strong>14 días de prueba gratuita</strong> del Plan Empresarial Pro. Al finalizar tu prueba, la inversión es de solo <strong>$60 USD al mes</strong>.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">

            <!-- Plan 1: Freemium Básico -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-extrabold text-slate-900">Plan Freemium Inicial</h3>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Gratis Siempre</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">
                        Para talleres o pequeñas cuadrillas que desean digitalizar su inventario básico de bodega.
                    </p>

                    <div class="mt-6 flex items-baseline gap-1">
                        <span class="text-4xl font-extrabold text-slate-900">$0</span>
                        <span class="text-sm font-semibold text-slate-400">USD / mes</span>
                    </div>

                    <ul class="mt-7 space-y-3 text-xs text-slate-600">
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold">✓</span>
                            <span>Hasta <strong>25 herramientas o equipos</strong> registrados en bodega</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold">✓</span>
                            <span><strong>1 Cuadrilla</strong> activa y hasta 2 rutas simultáneas</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold">✓</span>
                            <span>Directorio básico de personal (hasta 3 colaboradores)</span>
                        </li>
                        <li class="flex items-center gap-2.5 text-slate-400">
                            <span class="w-5 h-5 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center font-bold">–</span>
                            <span>Sin telemetría GPS multi-cuadrilla ni señales de destino en vivo</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100">
                    <a href="{{ route('register', ['plan' => 'freemium']) }}"
                       class="w-full inline-flex items-center justify-center px-6 py-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-bold transition-all">
                        Comenzar con Plan Freemium ($0)
                    </a>
                </div>
            </div>

            <!-- Plan 2: Prueba Gratis 14 Días -> $60 USD / mes (Destacado) -->
            <div class="bg-[#111827] text-white rounded-3xl p-8 border-2 border-[#2563eb] shadow-2xl relative flex flex-col justify-between">
                <div class="absolute -top-3.5 right-6 px-3.5 py-1 rounded-full bg-[#2563eb] text-white text-[11px] font-extrabold uppercase tracking-wider shadow-md">
                    Recomendado • 14 Días Gratis
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-extrabold text-white">Plan Empresarial Pro</h3>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-400/30">
                            Acceso Total
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 mt-2">
                        Disfruta de <strong>14 días de prueba gratis</strong> con todas las funciones ilimitadas. Después, solo <strong>$60 USD al mes</strong>.
                    </p>

                    <div class="mt-6 flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-white">$60</span>
                        <span class="text-sm font-semibold text-slate-400">USD / mes tras los 14 días de prueba gratis</span>
                    </div>
                    <p class="text-xs text-emerald-400 font-semibold mt-1">
                        Hoy pagas $0.00 USD — Prueba gratuita completa durante 14 días
                    </p>

                    <ul class="mt-7 space-y-3 text-xs text-slate-200">
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">✓</span>
                            <span><strong>Inventario ilimitado</strong> filtrable por Cuadrilla, Encargo y Serie</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">✓</span>
                            <span><strong>Rastreo GPS en Tiempo Real</strong> para todas tus cuadrillas y vehículos</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">✓</span>
                            <span><strong>Señales de Destino Activas</strong>, cálculo de ETA y alertas de extravío/fallo</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">✓</span>
                            <span><strong>Directorio completo</strong> de Encargados de Bodega, Choferes y Técnicos</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">✓</span>
                            <span>Panel de administración de suscripción y facturación corporativa</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-800">
                    <a href="{{ route('register', ['plan' => 'trial']) }}"
                       class="w-full inline-flex items-center justify-center px-6 py-3.5 rounded-xl bg-[#2563eb] hover:bg-blue-500 text-white text-sm font-bold shadow-lg shadow-blue-600/30 transition-all">
                        Activar Prueba Gratis de 14 Días (Luego $60/mes)
                    </a>
                </div>
            </div>

        </div>
    </section>

    {{-- =========================================================================
         FOOTER DE LA LANDING PAGE
       ========================================================================= --}}
    <footer class="bg-[#0f172a] text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <span class="text-xl font-extrabold text-white">LogiTrack<span class="text-[#3b82f6]">.</span></span>
                <span class="text-xs text-slate-500">© 2026 LogiTrack SaaS • León, Nicaragua</span>
            </div>
            <div class="flex flex-wrap items-center gap-6 text-xs font-medium">
                <a href="{{ route('login') }}" class="hover:text-white transition-colors">Iniciar Sesión</a>
                <a href="{{ route('register') }}" class="hover:text-white transition-colors">Registrarse (14 Días Gratis)</a>
            </div>
        </div>
    </footer>

</body>
</html>
