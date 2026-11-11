<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — LogiTrack SaaS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[#f4f6f9] flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 antialiased">

    {{-- =========================================================================
         VISTA DE INICIO DE SESIÓN (LOGIN) — LOGITRACK SAAS
         Se conecta al controlador LogiTrackController::login() y autentica contra
         la tabla `users` en MySQL (`logitrack_demostration`).
       ========================================================================= --}}
    <div class="max-w-4xl w-full mx-auto bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden grid grid-cols-1 lg:grid-cols-12">

        <!-- Columna Izquierda: Identidad Visual LogiTrack. -->
        <div class="lg:col-span-5 bg-[#111827] text-white p-8 sm:p-10 flex flex-col justify-between">
            <div>
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-[#3b82f6]">
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

                <h2 class="text-2xl font-extrabold text-white mt-8 leading-snug">
                    Bienvenido a tu Centro de Control Logístico
                </h2>
                <p class="text-xs text-slate-400 mt-3 leading-relaxed">
                    Supervisa el inventario de bodega, las herramientas asignadas a cada cuadrilla y la ubicación GPS de tu flota en León, Nicaragua.
                </p>

                <!-- Credenciales de prueba visibles para facilitar la evaluación 
                <div class="mt-8 p-4 rounded-2xl bg-slate-800/80 border border-slate-700 text-xs space-y-1.5">
                    <p class="font-bold text-blue-400 uppercase tracking-wider text-[11px]">Cuenta Gerencial de Prueba:</p>
                    <p class="text-slate-300"><strong class="text-white">Usuario:</strong> Carlos Martínez</p>
                    <p class="text-slate-300"><strong class="text-white">Correo:</strong> c.martinez@logitrack.com.ni</p>
                    <p class="text-slate-300"><strong class="text-white">Contraseña:</strong> logitrack2026</p>
                </div> -->
            </div>
            <!--
            <div class="pt-8 border-t border-slate-800 mt-8 text-xs text-slate-400">
                <a href="{{ route('landing') }}" class="hover:text-white transition-colors">← Volver a la Landing Page</a>
            </div> -->>
        </div>

        <!-- Columna Derecha: Formulario de Acceso -->
        <div class="lg:col-span-7 p-8 sm:p-10 flex flex-col justify-center">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Iniciar Sesión</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Ingresa tus credenciales corporativas para acceder al Dashboard de LogiTrack
                </p>
            </div>

            @if ($errors->any())
                <div class="mt-4 p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Formulario estándar de Login -->
            <form method="POST" action="{{ route('login.submit') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Correo Electrónico Corporativo</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', 'c.martinez@logitrack.com.ni') }}"
                           required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Contraseña</label>
                    <input type="password"
                           name="password"
                           value="logitrack2026"
                           required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition-all">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="inline-flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" checked class="rounded border-slate-300 text-blue-600">
                        <span>Mantener sesión activa</span>
                    </label>
                    <!--
                    <a href="{{ route('suscripcion') }}" class="text-blue-600 font-semibold hover:underline">
                        Administrar suscripción
                    </a> -->
                </div>

                <button type="submit"
                        class="w-full py-3 px-5 rounded-xl bg-[#2563eb] hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-600/20 transition-all cursor-pointer">
                    Entrar al Panel LogiTrack
                </button>
            </form>

            <!-- Botón de Acceso Rápido 1-Click como Carlos Martínez -->
            <form method="POST" action="{{ route('login.submit') }}" class="mt-3">
                @csrf
                <input type="hidden" name="demo_login" value="1">
                <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs border border-slate-200 transition-all cursor-pointer">
                     Acceso Rápido Demo como Carlos Martínez (Gerente de Operaciones)
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
                ¿Tu empresa aún no usa LogiTrack?
                <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:underline ml-1">
                    Inicia tu prueba gratis de 14 días (luego \$60/mes)
                </a>
            </div>
        </div>

    </div>

</body>
</html>
