@extends('layouts.logitrack')

@section('content')
    {{-- =========================================================================
         MÓDULO DE ADMINISTRACIÓN DE SUSCRIPCIÓN SAAS (CARLOS MARTÍNEZ)
         Permite gestionar en tiempo real y con persistencia en MySQL (`users` y
         `pagos_suscripcion`) el modelo Freemium de LogiTrack:
         - Periodo de Prueba Gratis de 14 días ($0 hoy -> $60.00 USD/mes al día 15)
         - Plan Empresarial Pro ($60.00 USD / mes)
         - Plan Freemium Básico ($0.00 USD / mes)
         - Método de pago corporativo e historial de facturación.
       ========================================================================= --}}

    <!-- TARJETA PRINCIPAL DE ESTADO DE SUSCRIPCIÓN -->
    <div class="bg-[#111827] text-white rounded-3xl p-7 sm:p-8 shadow-xl border border-slate-800 mb-7 relative overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">

            <!-- Información del Plan Actual -->
            <div class="lg:col-span-7">
                <div class="flex flex-wrap items-center gap-2.5 mb-3">
                    @if(($user['subscription_plan'] ?? 'trial') === 'pro')
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            ✓ Plan Empresarial Pro Activo
                        </span>
                    @elseif(($user['subscription_plan'] ?? 'trial') === 'freemium')
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-slate-700 text-slate-200">
                            Plan Freemium Básico ($0/mes)
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-400/30">
                            ⏳ Periodo de Prueba Gratis (14 Días)
                        </span>
                    @endif

                    <span class="text-xs text-slate-400">
                        Titular: <strong class="text-white">{{ $user['name'] }}</strong> ({{ $user['company_name'] }})
                    </span>
                </div>

                @if(($user['subscription_plan'] ?? 'trial') === 'trial')
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white">
                        Prueba Gratis de 14 Días — Quedan {{ $user['trial_days_remaining'] }} días
                    </h2>
                    <p class="text-sm text-slate-300 mt-2 leading-relaxed">
                        Estás disfrutando de todas las funciones del <strong>Plan Empresarial Pro</strong> sin costo durante 14 días (desde el {{ $user['trial_starts_at'] }} hasta el <strong>{{ $user['trial_ends_at'] }}</strong>). Al finalizar la prueba, tu suscripción se renovará automáticamente por <strong>$60.00 USD al mes</strong>.
                    </p>

                    <!-- Barra de progreso de los 14 días de prueba -->
                    @php
                        $daysUsed = max(1, 14 - (int) ($user['trial_days_remaining'] ?? 11));
                        $trialPercent = min(100, round(($daysUsed / 14) * 100));
                    @endphp
                    <div class="mt-5 max-w-xl">
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="text-blue-300 font-semibold">Avance de la prueba gratuita ({{ $daysUsed }} de 14 días utilizados)</span>
                            <span class="text-slate-300 font-mono">Próximo cobro: {{ $user['next_billing_at'] }} ($60.00 USD)</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-[#3b82f6] rounded-full" style="width: {{ $trialPercent }}%"></div>
                        </div>
                    </div>
                @elseif(($user['subscription_plan'] ?? 'trial') === 'pro')
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white">
                        Suscripción LogiTrack Pro — $60.00 USD / mes
                    </h2>
                    <p class="text-sm text-slate-300 mt-2 leading-relaxed">
                        Tu empresa cuenta con acceso ilimitado al control de inventario por cuadrilla, rastreo GPS satelital en León y señales de destino activas. Próxima fecha de facturación: <strong>{{ $user['next_billing_at'] }}</strong>.
                    </p>
                @else
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white">
                        Plan Freemium Básico — $0.00 USD / mes
                    </h2>
                    <p class="text-sm text-slate-300 mt-2 leading-relaxed">
                        Actualmente utilizas la modalidad gratuita con límites operativos. Activa la <strong>Prueba Gratis de 14 días</strong> o pasa al <strong>Plan Pro ($60/mes)</strong> para desbloquear todas las cuadrillas GPS en León.
                    </p>
                @endif
            </div>

            <!-- Resumen de Costo Mensual y Acciones Rápidas -->
            <div class="lg:col-span-5 bg-slate-900/90 rounded-2xl p-6 border border-slate-800 flex flex-col justify-between">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Tarifa Mensual del Plan</span>
                        <div class="flex items-baseline gap-1.5 mt-1">
                            <span class="text-3xl font-extrabold text-white">
                                ${{ number_format((float) ($user['monthly_price'] ?? 60), 2) }}
                            </span>
                            <span class="text-xs text-slate-400">USD / mes</span>
                        </div>
                        @if(($user['subscription_plan'] ?? 'trial') === 'trial')
                            <span class="text-[11px] text-emerald-400 font-semibold">
                                $0.00 cobrados hoy (en prueba de 14 días)
                            </span>
                        @endif
                    </div>

                    <div class="text-right">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Auto-Renovación</span>
                        <span class="inline-flex items-center gap-1.5 mt-1 text-xs font-bold {{ $user['auto_renew'] ? 'text-emerald-400' : 'text-amber-400' }}">
                            <span class="w-2 h-2 rounded-full {{ $user['auto_renew'] ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                            {{ $user['auto_renew'] ? 'Activa' : 'Pausada' }}
                        </span>
                    </div>
                </div>

                <!-- Botones conectados a MySQL para cambiar o administrar el plan -->
                <div class="mt-4 space-y-2.5">
                    @if(($user['subscription_plan'] ?? 'trial') !== 'pro')
                        <form method="POST" action="{{ route('suscripcion.update') }}">
                            @csrf
                            <input type="hidden" name="action" value="upgrade_pro">
                            <button type="submit"
                                    class="w-full py-2.5 px-4 rounded-xl bg-[#2563eb] hover:bg-blue-500 text-white text-xs font-bold shadow-lg shadow-blue-600/25 transition-all cursor-pointer">
                                Confirmar y Activar Plan Pro Ahora ($60.00 USD / mes)
                            </button>
                        </form>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @if(($user['subscription_plan'] ?? 'trial') !== 'trial')
                            <form method="POST" action="{{ route('suscripcion.update') }}">
                                @csrf
                                <input type="hidden" name="action" value="start_trial">
                                <button type="submit"
                                        class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-blue-300 border border-slate-700 text-xs font-semibold transition-all cursor-pointer">
                                    Reiniciar Prueba 14 Días
                                </button>
                            </form>
                        @endif

                        @if(($user['subscription_plan'] ?? 'trial') !== 'freemium')
                            <form method="POST" action="{{ route('suscripcion.update') }}">
                                @csrf
                                <input type="hidden" name="action" value="switch_freemium">
                                <button type="submit"
                                        class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-semibold transition-all cursor-pointer">
                                    Cambiar a Freemium ($0)
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('suscripcion.update') }}">
                            @csrf
                            <input type="hidden" name="action" value="toggle_autorenew">
                            <button type="submit"
                                    class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-semibold transition-all cursor-pointer">
                                {{ $user['auto_renew'] ? 'Pausar Renovación' : 'Reactivar Renovación' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- COMPARATIVA DE PERMISOS Y RESTRICCIONES: CUENTA FREE VS CUENTA PREMIUM -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-xs mb-7">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800">Diferencias de Acceso: Cuenta Free vs Cuenta Premium ($60/mes)</h3>
                <p class="text-xs text-slate-400">Las cuentas Free solo tienen vista a Dashboard e Inventario y reciben aviso de renovación en la campanita al cambiar de módulo</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-extrabold {{ ($user['is_free'] ?? false) ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                Estado Actual: {{ ($user['is_free'] ?? false) ? '🔒 Cuenta Free (Restringida)' : '✓ Cuenta Premium (Todo Desbloqueado)' }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mt-4 text-xs">
            <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200">
                <span class="font-extrabold text-emerald-800 block">✓ Dashboard</span>
                <span class="text-[11px] text-emerald-700 mt-0.5 block">Incluido en Free y Premium</span>
            </div>
            <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200">
                <span class="font-extrabold text-emerald-800 block">✓ Inventario y Bodega</span>
                <span class="text-[11px] text-emerald-700 mt-0.5 block">Incluido en Free y Premium</span>
            </div>
            <div class="p-3.5 rounded-xl {{ ($user['is_free'] ?? false) ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200' }}">
                <span class="font-extrabold {{ ($user['is_free'] ?? false) ? 'text-red-800' : 'text-blue-800' }} block">
                    {{ ($user['is_free'] ?? false) ? '🔒 Rastreo GPS (Bloqueado)' : '✓ Rastreo GPS Activo' }}
                </span>
                <span class="text-[11px] {{ ($user['is_free'] ?? false) ? 'text-red-600' : 'text-blue-700' }} mt-0.5 block">Exclusivo Cuenta Premium</span>
            </div>
            <div class="p-3.5 rounded-xl {{ ($user['is_free'] ?? false) ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200' }}">
                <span class="font-extrabold {{ ($user['is_free'] ?? false) ? 'text-red-800' : 'text-blue-800' }} block">
                    {{ ($user['is_free'] ?? false) ? '🔒 Rutas y Envíos (Bloqueado)' : '✓ Rutas y Envíos Activo' }}
                </span>
                <span class="text-[11px] {{ ($user['is_free'] ?? false) ? 'text-red-600' : 'text-blue-700' }} mt-0.5 block">Exclusivo Cuenta Premium</span>
            </div>
            <div class="p-3.5 rounded-xl {{ ($user['is_free'] ?? false) ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200' }}">
                <span class="font-extrabold {{ ($user['is_free'] ?? false) ? 'text-red-800' : 'text-blue-800' }} block">
                    {{ ($user['is_free'] ?? false) ? '🔒 Personal (Bloqueado)' : '✓ Personal Desbloqueado' }}
                </span>
                <span class="text-[11px] {{ ($user['is_free'] ?? false) ? 'text-red-600' : 'text-blue-700' }} mt-0.5 block">Exclusivo Cuenta Premium</span>
            </div>
        </div>
    </div>

    <!-- USO ACTUAL DE RECURSOS DE LA EMPRESA EN SU SUSCRIPCIÓN -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-7">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase">Equipos en Bodega/Campo</span>
                <span class="text-xs font-bold text-blue-600">{{ ($user['subscription_plan'] ?? 'trial') === 'freemium' ? 'Límite: 25' : 'Ilimitados' }}</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ count($inventario) }} Lotes (124 Uds)</p>
            <p class="text-xs text-slate-500 mt-1">Sincronizados en MySQL</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase">Cuadrillas con GPS</span>
                <span class="text-xs font-bold text-emerald-600">Activas en León</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ count($cuadrillasGps) }} Unidades</p>
            <p class="text-xs text-slate-500 mt-1">Telemetría Leaflet en tiempo real</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase">Rutas y Señales</span>
                <span class="text-xs font-bold text-amber-600">Balizas Destino</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ count($rutasEnvios) }} Envíos</p>
            <p class="text-xs text-slate-500 mt-1">Monitoreo de llegada activo</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase">Personal Registrado</span>
                <span class="text-xs font-bold text-indigo-600">Multi-Rol</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ count($personal) }} Usuarios</p>
            <p class="text-xs text-slate-500 mt-1">Bodega, Choferes y Técnicos</p>
        </div>
    </div>

    <!-- SECCIÓN INFERIOR: MÉTODO DE PAGO + HISTORIAL DE FACTURACIÓN -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Columna Izquierda: Datos de Facturación y Tarjeta Corporativa (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100">
            <div class="pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Método de Pago y Facturación</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Para el cobro mensual de $60.00 USD tras la prueba</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold text-xs">
                    {{ $user['payment_method_brand'] }} •••• {{ $user['payment_method_last4'] }}
                </span>
            </div>

            @if ($errors->any())
                <div class="mt-4 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 font-medium">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('suscripcion.update') }}" class="mt-5 space-y-4">
                @csrf
                <input type="hidden" name="action" value="update_billing">

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Razón Social / Empresa *</label>
                    <input type="text"
                           name="company_name"
                           value="{{ old('company_name', $user['company_name']) }}"
                           minlength="3"
                           maxlength="150"
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Número RUC Fiscal (Nicaragua) *</label>
                    <input type="text"
                           name="billing_ruc"
                           value="{{ old('billing_ruc', $user['billing_ruc']) }}"
                           minlength="8"
                           maxlength="25"
                           pattern="[A-Za-z0-9\-]+"
                           title="Ingresa un RUC válido (letras, números y guiones)"
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-mono text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tipo de Tarjeta / Medio</label>
                        <select name="payment_method_brand" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800">
                            <option value="Visa Corporativa" {{ $user['payment_method_brand'] === 'Visa Corporativa' ? 'selected' : '' }}>Visa Corporativa</option>
                            <option value="Mastercard Empresarial" {{ $user['payment_method_brand'] === 'Mastercard Empresarial' ? 'selected' : '' }}>Mastercard Empresarial</option>
                            <option value="BAC Credomatic León" {{ $user['payment_method_brand'] === 'BAC Credomatic León' ? 'selected' : '' }}>BAC Credomatic León</option>
                            <option value="Banpro Corporativo" {{ $user['payment_method_brand'] === 'Banpro Corporativo' ? 'selected' : '' }}>Banpro Corporativo</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Últimos 4 Dígitos *</label>
                        <input type="text"
                               name="payment_method_last4"
                               minlength="4"
                               maxlength="4"
                               pattern="[0-9]{4}"
                               title="Exactamente 4 dígitos numéricos"
                               value="{{ old('payment_method_last4', $user['payment_method_last4']) }}"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-mono text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white">
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all cursor-pointer">
                    Guardar Datos de Facturación en BD
                </button>
            </form>
        </div>

        <!-- Columna Derecha: Historial de Facturación y Eventos de Suscripción (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Historial de Suscripción y Facturación</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Registros almacenados en la tabla <code class="text-blue-600">pagos_suscripcion</code> de MySQL</p>
                </div>
                <span class="text-xs font-semibold text-slate-500">{{ count($pagos) }} movimientos</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#f8fafc] border-b border-slate-100 text-[12px] font-semibold uppercase tracking-wider text-slate-400">
                            <th class="py-3.5 px-6">Referencia / Fecha</th>
                            <th class="py-3.5 px-5">Concepto del Plan</th>
                            <th class="py-3.5 px-4">Monto</th>
                            <th class="py-3.5 px-6">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($pagos as $pago)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-mono font-bold text-slate-800">{{ $pago->referencia }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $pago->fecha_emision }}</div>
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-800">{{ $pago->concepto }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">Medio: {{ $pago->metodo_pago }}</div>
                                </td>
                                <td class="py-4 px-4 font-extrabold text-slate-900 whitespace-nowrap">
                                    ${{ number_format($pago->monto, 2) }} {{ $pago->moneda }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    @if(str_contains(strtolower($pago->estado), 'pagado'))
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-700">
                                            {{ $pago->estado }}
                                        </span>
                                    @elseif(str_contains(strtolower($pago->estado), 'prueba'))
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-700">
                                            {{ $pago->estado }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700">
                                            {{ $pago->estado }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    No hay registros de facturación disponibles.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
