<?php

namespace App\Http\Controllers;

use App\Models\Cuadrilla;
use App\Models\Equipo;
use App\Models\PagoSuscripcion;
use App\Models\Personal;
use App\Models\RutaEnvio;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Controlador Principal LogiTrackController.
 *
 * Centraliza toda la lógica del SaaS LogiTrack:
 * 1. Landing Page comercial con modelo Freemium (14 días de prueba gratis -> $60 USD/mes).
 * 2. Autenticación de usuarios (Login, Registro con activación automática de 14 días de prueba y Logout).
 * 3. Módulos operativos del Dashboard (Resumen Operativo, Inventario, Rastreo GPS en León, Rutas y Envíos, Personal).
 * 4. Módulo de Administración de Suscripción SaaS para Carlos Martínez (cambio de plan, facturación y método de pago).
 */
class LogiTrackController extends Controller
{
    /**
     * Garantiza que la base de datos tenga los registros semilla iniciales
     * y devuelve el usuario activo (el usuario autenticado o Carlos Martínez por defecto en modo demo).
     */
    private function resolveActiveUser(): User
    {
        // Si las tablas están creadas pero aún no tienen registros, ejecutamos el DatabaseSeeder automáticamente
        if (Schema::hasTable('users') && User::count() === 0) {
            (new DatabaseSeeder())->run();
        }

        // Si existe sesión iniciada, retornamos el usuario autenticado
        if (Auth::check()) {
            return Auth::user();
        }

        // Por defecto, obtenemos a Carlos Martínez (Gerente de Operaciones) desde la BD MySQL
        return User::where('email', 'c.martinez@logitrack.com.ni')->first()
            ?? User::first();
    }

    /**
     * Obtiene y estructura los datos comunes desde la base de datos MySQL (`logitrack_demostration`)
     * para alimentar el layout del Dashboard y sus vistas operativas.
     */
    private function getCommonData(): array
    {
        // Asegurar que la base de datos tenga el inventario inicial
        if (Schema::hasTable('equipos') && Equipo::count() === 0) {
            (new DatabaseSeeder())->run();
        }

        $activeUser = $this->resolveActiveUser();

        // Calcular etiqueta de estado de suscripción para mostrar en la barra superior y menú lateral
        $trialDays = $activeUser ? $activeUser->trial_days_remaining : 11;
        $planBadge = match ($activeUser->subscription_plan ?? 'trial') {
            'pro' => 'Plan Pro Activo • $60/mes',
            'freemium' => 'Plan Freemium • Básico',
            default => "Prueba Gratis • {$trialDays} días restantes",
        };

        $user = [
            'id' => $activeUser->id ?? 1,
            'initials' => $activeUser ? $activeUser->initials : 'CM',
            'name' => $activeUser->name ?? 'Carlos Martínez',
            'email' => $activeUser->email ?? 'c.martinez@logitrack.com.ni',
            'role' => $activeUser->role ?? 'Gerente de Operaciones',
            'company_name' => $activeUser->company_name ?? 'Inversiones Eléctricas de León S.A.',
            'phone' => $activeUser->phone ?? '+505 8810-2200',
            'date' => '29/10/2026',
            'branch' => 'Sede Central León, Nicaragua',
            'subscription_plan' => $activeUser->subscription_plan ?? 'trial',
            'subscription_status' => $activeUser->subscription_status ?? 'on_trial',
            'monthly_price' => $activeUser->monthly_price ?? 60.00,
            'trial_days_remaining' => $trialDays,
            'trial_starts_at' => $activeUser?->trial_starts_at?->format('d/m/Y') ?? now()->subDays(3)->format('d/m/Y'),
            'trial_ends_at' => $activeUser?->trial_ends_at?->format('d/m/Y') ?? now()->addDays(11)->format('d/m/Y'),
            'next_billing_at' => $activeUser?->next_billing_at?->format('d/m/Y') ?? now()->addDays(11)->format('d/m/Y'),
            'payment_method_brand' => $activeUser->payment_method_brand ?? 'Visa Corporativa',
            'payment_method_last4' => $activeUser->payment_method_last4 ?? '4821',
            'billing_ruc' => $activeUser->billing_ruc ?? 'J0310000459128',
            'auto_renew' => (bool) ($activeUser->auto_renew ?? true),
            'plan_badge' => $planBadge,
        ];

        $kpis = [
            'equipos_asignados' => [
                'title' => 'Equipos Asignados',
                'value' => '124',
                'subtitle' => '+12% esta semana',
                'trend' => 'up',
            ],
            'envios_ruta' => [
                'title' => 'Envíos en Ruta',
                'value' => '8',
                'subtitle' => '3 con llegada próxima',
                'trend' => 'neutral',
            ],
            'alertas' => [
                'title' => 'Alertas / Extravíos',
                'value' => '1',
                'subtitle' => 'Revisar taladro asignado a Cuadrilla B',
                'trend' => 'danger',
            ],
            'eficiencia' => [
                'title' => 'Eficiencia Operativa',
                'value' => '94%',
                'subtitle' => 'Tiempos de entrega óptimos',
                'trend' => 'success',
            ],
        ];

        // Consultar Movimientos de Bodega desde la tabla `equipos`
        $ultimosMovimientos = Equipo::where('es_movimiento_bodega', true)
            ->orderBy('id')
            ->get()
            ->map(fn (Equipo $eq) => [
                'id' => 'MOV-' . (1041 + $eq->id),
                'herramienta' => $eq->nombre_corto,
                'codigo' => $eq->codigo,
                'categoria' => $eq->categoria,
                'asignado_a' => $eq->responsable,
                'cuadrilla' => $eq->cuadrilla,
                'encargo' => $eq->encargo,
                'destino' => $eq->destino_movimiento ?: $eq->ubicacion,
                'estado' => $eq->estado,
                'estado_color' => $eq->estado_color,
                'hora' => $eq->ultima_actualizacion,
            ])
            ->toArray();

        // Consultar Inventario completo desde la tabla `equipos`
        $inventario = Equipo::orderByDesc('id')
            ->get()
            ->map(fn (Equipo $eq) => [
                'codigo' => $eq->codigo,
                'nombre' => $eq->nombre,
                'nombre_corto' => $eq->nombre_corto,
                'categoria' => $eq->categoria,
                'serie' => $eq->serie,
                'cantidad' => $eq->cantidad,
                'cuadrilla' => $eq->cuadrilla,
                'responsable' => $eq->responsable,
                'encargo' => $eq->encargo,
                'ubicacion' => $eq->ubicacion,
                'estado' => $eq->estado,
                'estado_color' => $eq->estado_color,
                'ultima_actualizacion' => $eq->ultima_actualizacion,
            ])
            ->toArray();

        // Consultar Cuadrillas GPS en León desde la tabla `cuadrillas`
        $cuadrillasGps = Cuadrilla::orderBy('id')
            ->get()
            ->map(fn (Cuadrilla $c) => [
                'id' => $c->slug,
                'nombre' => $c->nombre,
                'titulo_mapa' => $c->titulo_mapa,
                'vehiculo' => $c->vehiculo,
                'chofer' => $c->chofer,
                'jefe' => $c->jefe,
                'telefono' => $c->telefono,
                'lat' => (float) $c->lat,
                'lng' => (float) $c->lng,
                'destino_nombre' => $c->destino_nombre,
                'destino_lat' => (float) $c->destino_lat,
                'destino_lng' => (float) $c->destino_lng,
                'ubicacion_actual' => $c->ubicacion_actual,
                'distancia' => $c->distancia,
                'velocidad' => $c->velocidad,
                'eta' => $c->eta,
                'progreso' => (int) $c->progreso,
                'estado' => $c->estado,
                'color' => $c->color,
                'badge_class' => $c->badge_class,
                'equipos_a_bordo' => $c->equipos_a_bordo ?? [],
                'encargo' => $c->encargo,
            ])
            ->toArray();

        // Consultar Rutas y Envíos desde la tabla `rutas_envios`
        $rutasEnvios = RutaEnvio::orderBy('id')
            ->get()
            ->map(fn (RutaEnvio $r) => [
                'codigo' => $r->codigo,
                'cuadrilla' => $r->cuadrilla,
                'responsable' => $r->responsable,
                'chofer' => $r->chofer,
                'vehiculo' => $r->vehiculo,
                'origen' => $r->origen,
                'destino' => $r->destino,
                'direccion_destino' => $r->direccion_destino,
                'coordenadas_destino' => $r->coordenadas_destino,
                'senal_destino' => $r->senal_destino,
                'prioridad' => $r->prioridad,
                'hora_salida' => $r->hora_salida,
                'hora_estimada' => $r->hora_estimada,
                'progreso' => (int) $r->progreso,
                'estado' => $r->estado,
                'estado_color' => $r->estado_color,
                'items' => $r->items ?? [],
                'focus_id' => $r->focus_id,
            ])
            ->toArray();

        // Consultar Personal desde la tabla `personal`
        $personal = Personal::orderBy('id')
            ->get()
            ->map(fn (Personal $p) => [
                'codigo' => $p->codigo,
                'iniciales' => $p->iniciales,
                'nombre' => $p->nombre,
                'cargo' => $p->cargo,
                'categoria_rol' => $p->categoria_rol,
                'departamento' => $p->departamento,
                'asignacion' => $p->asignacion,
                'telefono' => $p->telefono,
                'email' => $p->email,
                'estado' => $p->estado,
                'estado_color' => $p->estado_color,
                'avatar_bg' => $p->avatar_bg,
                'experiencia' => $p->experiencia,
                'equipos_a_cargo' => $p->equipos_a_cargo,
            ])
            ->toArray();

        return compact(
            'user',
            'kpis',
            'ultimosMovimientos',
            'inventario',
            'cuadrillasGps',
            'rutasEnvios',
            'personal'
        );
    }

    /**
     * Muestra la Landing Page comercial de LogiTrack SaaS.
     * Explica las capacidades del sistema (Inventario + Rastreo GPS + Rutas)
     * y presenta el esquema Freemium con Prueba Gratis de 14 días y plan Pro de $60 USD/mes.
     */
    public function landing()
    {
        return view('logitrack.landing');
    }

    /**
     * Muestra el formulario de Inicio de Sesión (Login).
     */
    public function showLogin()
    {
        return view('logitrack.auth.login');
    }

    /**
     * Procesa la autenticación del usuario contra la base de datos MySQL.
     * Incluye soporte para acceso rápido de demostración con la cuenta de Carlos Martínez.
     */
    public function login(Request $request)
    {
        if (Schema::hasTable('users') && User::count() === 0) {
            (new DatabaseSeeder())->run();
        }

        // Si el usuario pulsó "Acceso Rápido Demo (Carlos Martínez)"
        if ($request->boolean('demo_login')) {
            $carlos = User::where('email', 'c.martinez@logitrack.com.ni')->first();
            if ($carlos) {
                Auth::login($carlos);
                $request->session()->regenerate();

                return redirect()->route('dashboard')->with('status', 'Bienvenido nuevamente, Carlos Martínez.');
            }
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales ingresadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Muestra el formulario de Registro de nueva cuenta empresarial SaaS.
     */
    public function showRegister(Request $request)
    {
        $selectedPlan = $request->query('plan', 'trial');

        return view('logitrack.auth.register', compact('selectedPlan'));
    }

    /**
     * Registra una nueva empresa/usuario en la base de datos e inicia automáticamente
     * su periodo de Prueba Gratis de 14 días (con cobro programado de $60/mes al finalizar)
     * o el plan Freemium seleccionado.
     */
    public function register(Request $request)
    {
        if (Schema::hasTable('users') && User::count() === 0) {
            (new DatabaseSeeder())->run();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:150|unique:users,email',
            'company_name' => 'required|string|max:150',
            'role' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:40',
            'plan_choice' => 'nullable|string|in:trial,pro,freemium',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $planChoice = $validated['plan_choice'] ?? 'trial';

        // Configurar las fechas de la prueba gratuita de 14 días y el precio mensual de $60 USD
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?: 'Gerente de Operaciones',
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'] ?: '+505 8800-0000',
            'subscription_plan' => $planChoice,
            'subscription_status' => $planChoice === 'pro' ? 'active' : ($planChoice === 'freemium' ? 'freemium' : 'on_trial'),
            'monthly_price' => $planChoice === 'freemium' ? 0.00 : 60.00,
            'trial_starts_at' => now(),
            'trial_ends_at' => now()->addDays(14),
            'next_billing_at' => now()->addDays(14),
            'payment_method_brand' => 'Visa Corporativa',
            'payment_method_last4' => '4821',
            'billing_ruc' => 'J0310000991204',
            'auto_renew' => true,
        ]);

        // Crear el registro de activación en el historial de suscripción
        PagoSuscripcion::create([
            'user_id' => $user->id,
            'referencia' => 'SUB-' . date('Y') . '-' . rand(100, 999),
            'concepto' => $planChoice === 'freemium'
                ? 'Activación Plan Freemium Básico ($0/mes)'
                : 'Activación Prueba Gratis 14 Días (Luego $60.00 USD/mes)',
            'plan' => $planChoice,
            'monto' => $planChoice === 'pro' ? 60.00 : 0.00,
            'moneda' => 'USD',
            'metodo_pago' => 'Registro Inicial Web',
            'estado' => $planChoice === 'pro' ? 'Pagado' : 'Prueba 14 Días Activa',
            'fecha_emision' => now()->format('d/m/Y'),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with(
            'status',
            '¡Cuenta creada con éxito! Tu prueba gratuita de 14 días de LogiTrack Pro está activa.'
        );
    }

    /**
     * Cierra la sesión activa del usuario y redirige a la Landing Page.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    /**
     * Vista 1: Resumen Operativo (Dashboard Principal).
     */
    public function dashboard()
    {
        $data = $this->getCommonData();
        $data['activePage'] = 'dashboard';
        $data['pageTitle'] = 'Resumen Operativo';
        $data['pageSubtitle'] = 'Vista general de flota y herramientas - 29/10/2026';

        return view('logitrack.dashboard', $data);
    }

    /**
     * Vista 2: Inventario de Equipos y Recursos (filtrable por cuadrilla y encargo).
     */
    public function inventario(Request $request)
    {
        $data = $this->getCommonData();
        $data['activePage'] = 'inventario';
        $data['pageTitle'] = 'Inventario de Equipos y Recursos';
        $data['pageSubtitle'] = 'Control de herramientas por cuadrilla, encargos y bodega central - León, Nicaragua';
        $data['filtroCuadrilla'] = $request->query('cuadrilla', 'todas');
        $data['filtroEncargo'] = $request->query('encargo', 'todos');

        return view('logitrack.inventario', $data);
    }

    /**
     * Guarda un nuevo equipo o asignación directamente en la tabla `equipos` de MySQL.
     */
    public function storeEquipo(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50',
            'nombre' => 'required|string|max:180',
            'cantidad' => 'required|string|max:60',
            'cuadrilla' => 'required|string|max:80',
            'responsable' => 'required|string|max:120',
            'encargo' => 'required|string|max:180',
        ]);

        Equipo::updateOrCreate(
            ['codigo' => $validated['codigo']],
            [
                'nombre' => $validated['nombre'],
                'nombre_corto' => $validated['nombre'],
                'categoria' => 'Equipamiento Operativo',
                'serie' => 'SN-' . strtoupper(substr(md5($validated['codigo']), 0, 6)),
                'cantidad' => $validated['cantidad'],
                'cuadrilla' => $validated['cuadrilla'],
                'responsable' => $validated['responsable'],
                'encargo' => $validated['encargo'],
                'ubicacion' => 'León, Nicaragua (' . $validated['cuadrilla'] . ')',
                'destino_movimiento' => $validated['encargo'],
                'estado' => 'En Tránsito',
                'estado_color' => 'yellow',
                'ultima_actualizacion' => 'Ahora mismo',
                'es_movimiento_bodega' => true,
            ]
        );

        return redirect()
            ->route('inventario', ['cuadrilla' => $validated['cuadrilla']])
            ->with('status', 'Equipo registrado y asignado en la base de datos MySQL correctamente.');
    }

    /**
     * Vista 3: Rastreo GPS en Tiempo Real (Leaflet centrado en León, Nicaragua).
     */
    public function rastreo(Request $request)
    {
        $data = $this->getCommonData();
        $data['activePage'] = 'rastreo';
        $data['pageTitle'] = 'Rastreo GPS en Tiempo Real';
        $data['pageSubtitle'] = 'Monitoreo satelital de cuadrillas y flota operativa en León, Nicaragua';
        $data['focusCuadrilla'] = $request->query('focus', 'cuadrilla-c');

        return view('logitrack.rastreo', $data);
    }

    /**
     * Vista 4: Rutas y Envíos con Señal de Destino.
     */
    public function rutas()
    {
        $data = $this->getCommonData();
        $data['activePage'] = 'rutas';
        $data['pageTitle'] = 'Rutas y Envíos Activos';
        $data['pageSubtitle'] = 'Seguimiento de despachos y señales de destino por cuadrilla en León';

        return view('logitrack.rutas', $data);
    }

    /**
     * Vista 5: Directorio de Personal (Encargados de Bodega, Choferes, Técnicos).
     */
    public function personal(Request $request)
    {
        $data = $this->getCommonData();
        $data['activePage'] = 'personal';
        $data['pageTitle'] = 'Personal y Cuadrillas';
        $data['pageSubtitle'] = 'Directorio de encargados de bodega, choferes, técnicos y supervisores';
        $data['filtroRol'] = $request->query('rol', 'todos');

        return view('logitrack.personal', $data);
    }

    /**
     * Vista 6: Administración de Suscripción SaaS (Carlos Martínez).
     * Muestra el estado actual del plan (Freemium / Prueba de 14 días / Plan Pro de $60 USD al mes),
     * consumo de recursos de la empresa, método de pago e historial de facturación desde MySQL.
     */
    public function suscripcion()
    {
        $data = $this->getCommonData();
        $activeUser = $this->resolveActiveUser();

        $data['activePage'] = 'suscripcion';
        $data['pageTitle'] = 'Suscripción y Facturación SaaS';
        $data['pageSubtitle'] = 'Administra tu prueba gratuita de 14 días, Plan Empresarial de $60/mes y métodos de pago';
        $data['pagos'] = $activeUser
            ? $activeUser->pagosSuscripcion()->get()
            : collect();

        return view('logitrack.suscripcion', $data);
    }

    /**
     * Procesa los cambios en la suscripción de Carlos Martínez (o usuario activo) y los persiste en MySQL:
     * - `upgrade_pro`: Pasa inmediatamente al Plan Pro de $60.00 USD/mes y genera factura pagada.
     * - `start_trial`: Activa o reinicia la Prueba Gratis de 14 días ($0 hoy -> $60/mes al día 15).
     * - `switch_freemium`: Cambia al Plan Freemium Básico ($0/mes con límites operativos).
     * - `toggle_autorenew`: Activa o pausa la renovación automática mensual.
     * - `update_billing`: Actualiza tarjeta corporativa, empresa y RUC en la base de datos.
     */
    public function updateSuscripcion(Request $request)
    {
        $activeUser = $this->resolveActiveUser();
        $action = $request->input('action', 'update_billing');

        if ($action === 'upgrade_pro') {
            $activeUser->update([
                'subscription_plan' => 'pro',
                'subscription_status' => 'active',
                'monthly_price' => 60.00,
                'next_billing_at' => now()->addMonth(),
                'auto_renew' => true,
            ]);

            PagoSuscripcion::create([
                'user_id' => $activeUser->id,
                'referencia' => 'FAC-' . date('Y') . '-' . rand(1000, 9999),
                'concepto' => 'Suscripción Mensual LogiTrack Pro — Flota e Inventario Ilimitado',
                'plan' => 'pro',
                'monto' => 60.00,
                'moneda' => 'USD',
                'metodo_pago' => $activeUser->payment_method_brand . ' •••• ' . $activeUser->payment_method_last4,
                'estado' => 'Pagado',
                'fecha_emision' => now()->format('d/m/Y'),
            ]);

            return redirect()->route('suscripcion')->with(
                'status',
                '¡Plan Empresarial Pro ($60.00 USD/mes) activado con éxito! Se ha registrado tu recibo en el historial.'
            );
        }

        if ($action === 'start_trial') {
            $activeUser->update([
                'subscription_plan' => 'trial',
                'subscription_status' => 'on_trial',
                'monthly_price' => 60.00,
                'trial_starts_at' => now(),
                'trial_ends_at' => now()->addDays(14),
                'next_billing_at' => now()->addDays(14),
                'auto_renew' => true,
            ]);

            PagoSuscripcion::create([
                'user_id' => $activeUser->id,
                'referencia' => 'TRL-' . date('Y') . '-' . rand(1000, 9999),
                'concepto' => 'Inicio de Prueba Gratis de 14 Días (Seguido de Plan Pro $60.00 USD/mes)',
                'plan' => 'trial',
                'monto' => 0.00,
                'moneda' => 'USD',
                'metodo_pago' => $activeUser->payment_method_brand . ' •••• ' . $activeUser->payment_method_last4,
                'estado' => 'Prueba 14 Días Activa',
                'fecha_emision' => now()->format('d/m/Y'),
            ]);

            return redirect()->route('suscripcion')->with(
                'status',
                'Periodo de Prueba Gratis de 14 días activado. Tendrás acceso completo sin costo antes del cargo mensual de $60.00 USD.'
            );
        }

        if ($action === 'switch_freemium') {
            $activeUser->update([
                'subscription_plan' => 'freemium',
                'subscription_status' => 'freemium',
                'monthly_price' => 0.00,
                'auto_renew' => false,
            ]);

            PagoSuscripcion::create([
                'user_id' => $activeUser->id,
                'referencia' => 'FRM-' . date('Y') . '-' . rand(1000, 9999),
                'concepto' => 'Cambio a Plan Freemium Básico ($0.00 USD/mes)',
                'plan' => 'freemium',
                'monto' => 0.00,
                'moneda' => 'USD',
                'metodo_pago' => 'Sin Cargo',
                'estado' => 'Activo ($0/mes)',
                'fecha_emision' => now()->format('d/m/Y'),
            ]);

            return redirect()->route('suscripcion')->with(
                'status',
                'Tu cuenta ha cambiado al Plan Freemium ($0/mes). Puedes volver al Plan Pro de $60/mes en cualquier momento.'
            );
        }

        if ($action === 'toggle_autorenew') {
            $newAutoRenew = ! $activeUser->auto_renew;
            $activeUser->update([
                'auto_renew' => $newAutoRenew,
                'subscription_status' => $newAutoRenew ? ($activeUser->subscription_plan === 'trial' ? 'on_trial' : 'active') : 'cancelled',
            ]);

            return redirect()->route('suscripcion')->with(
                'status',
                $newAutoRenew
                    ? 'Renovación automática mensual ($60.00 USD/mes) reactivada.'
                    : 'Renovación automática pausada. Conservarás el acceso hasta el final de tu ciclo actual.'
            );
        }

        // Actualización de datos de facturación y método de pago corporativo
        $validated = $request->validate([
            'company_name' => 'required|string|max:150',
            'billing_ruc' => 'required|string|max:50',
            'payment_method_brand' => 'required|string|max:60',
            'payment_method_last4' => 'required|digits:4',
        ]);

        $activeUser->update($validated);

        return redirect()->route('suscripcion')->with(
            'status',
            'Datos de facturación y método de pago corporativo actualizados en la base de datos.'
        );
    }
}
