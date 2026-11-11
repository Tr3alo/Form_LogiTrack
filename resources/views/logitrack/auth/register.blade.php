<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta y Prueba de 14 Días — LogiTrack SaaS</title>

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
         VISTA DE REGISTRO EMPRESARIAL — LOGITRACK SAAS
         Permite registrar una empresa nueva en la tabla `users` de MySQL y activa
         automáticamente los 14 días de prueba gratis (seguido de $60 USD/mes)
         o el plan Freemium seleccionado.
       ========================================================================= --}}
    <div class="max-w-5xl w-full mx-auto bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden grid grid-cols-1 lg:grid-cols-12">

        <!-- Columna Izquierda: Resumen del Plan Freemium / 14 Días Gratis -->
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

                <div class="mt-8 inline-block px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-400/30 text-[11px] font-bold uppercase tracking-wider">
                    Prueba Gratis de 14 Días
                </div>

                <h2 class="text-2xl font-extrabold text-white mt-3 leading-snug">
                    Comienza a rastrear tus recursos hoy con \$0.00 de cargo inicial
                </h2>

                <div class="mt-6 space-y-4 text-xs text-slate-300">
                    <div class="p-4 rounded-2xl bg-slate-800/80 border border-slate-700">
                        <div class="flex items-center justify-between font-bold text-white text-sm">
                            <span>Hoy (Inicio de Prueba 14 Días)</span>
                            <span class="text-emerald-400">\$0.00 USD</span>
                        </div>
                        <p class="text-slate-400 mt-1">
                            Acceso completo a Inventario por cuadrilla, Rastreo GPS en León, Rutas y Personal.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-800/50 border border-slate-800">
                        <div class="flex items-center justify-between font-bold text-white text-sm">
                            <span>Al finalizar los 14 días</span>
                            <span class="text-blue-400">\$60.00 USD / mes</span>
                        </div>
                        <p class="text-slate-400 mt-1">
                            Puedes cancelar o cambiar al plan Freemium en cualquier momento desde tu apartado de Suscripción.
                        </p>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800 mt-8 text-xs text-slate-400 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="hover:text-white transition-colors">← Volver al inicio</a>
                <a href="{{ route('login') }}" class="text-blue-400 font-semibold hover:underline">Ya tengo cuenta →</a>
            </div>
        </div>

        <!-- Columna Derecha: Formulario de Registro -->
        <div class="lg:col-span-7 p-8 sm:p-10">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Registrar Empresa en LogiTrack</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Configura tu cuenta administrativa en menos de un minuto
                </p>
            </div>

            @if ($errors->any())
                <div class="mt-4 p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 font-medium">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}" class="mt-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre Completo del Administrador</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="Ej. Carlos Martínez"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Cargo en la Empresa</label>
                        <input type="text"
                               name="role"
                               value="{{ old('role', 'Gerente de Operaciones') }}"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre de la Empresa</label>
                        <input type="text"
                               name="company_name"
                               value="{{ old('company_name') }}"
                               placeholder="Ej. Inversiones Eléctricas de León S.A."
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono de Contacto</label>
                        <input type="text"
                               name="phone"
                               value="{{ old('phone', '+505 8810-2200') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico Corporativo</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="gerencia@tuempresa.com.ni"
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white">
                </div>

                <!-- Selector del Modelo SaaS -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Modalidad de Suscripción Inicial</label>
                    <select name="plan_choice"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-blue-200 bg-blue-50/50 text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-600">
                        <option value="trial" {{ ($selectedPlan ?? 'trial') === 'trial' ? 'selected' : '' }}>
                            ⭐ Prueba Gratis de 14 Días — Plan Empresarial Pro (Luego $60.00 USD / mes)
                        </option>
                        <option value="freemium" {{ ($selectedPlan ?? '') === 'freemium' ? 'selected' : '' }}>
                            Plan Freemium Básico ($0.00 USD / mes — Limitado a 1 cuadrilla y 25 equipos)
                        </option>
                        <option value="pro" {{ ($selectedPlan ?? '') === 'pro' ? 'selected' : '' }}>
                            Activar Plan Empresarial Pro Directo ($60.00 USD / mes)
                        </option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Contraseña</label>
                        <input type="password"
                               name="password"
                               placeholder="Mínimo 6 caracteres"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Confirmar Contraseña</label>
                        <input type="password"
                               name="password_confirmation"
                               placeholder="Repite tu contraseña"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white">
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-3.5 px-5 rounded-xl bg-[#2563eb] hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-600/20 transition-all cursor-pointer mt-2">
                    Crear Cuenta e Iniciar Prueba Gratis de 14 Días
                </button>
            </form>

            <div class="mt-5 text-center text-xs text-slate-500">
                ¿Ya tienes una cuenta?
                <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:underline ml-1">
                    Inicia sesión aquí
                </a>
            </div>
        </div>

    </div>

</body>
</html>
