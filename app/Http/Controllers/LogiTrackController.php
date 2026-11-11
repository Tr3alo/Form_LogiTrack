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
 * Implementa:
 * 1. Diferenciación y restricciones estrictas entre Cuenta FREE (`freemium`) y Cuenta PREMIUM (`pro` / `trial`):
 *    - Cuenta FREE: Solo tiene acceso a `Dashboard` e `Inventario` (además de `Suscripción` para renovar).
 *      No puede acceder a `Rastreo GPS`, `Rutas y Envíos` ni `Personal`.
 *    - Cada vez que un usuario FREE navega entre Dashboard e Inventario, se activa un aviso en la
 *      campanita de notificaciones recordándole que debe renovar su suscripción ($60/mes).
 * 2. Gestión de Inventario con envío directo a `Bodega Central` en 1 clic (tanto al registrar un
 *    producto nuevo como al retornar un equipo existente a bodega).
 * 3. Validaciones robustas de entradas en todos los formularios (Login, Registro, Inventario y Facturación).
 */
class LogiTrackController extends Controller
{
    /**
     * Resuelve el usuario activo desde la sesión o carga por defecto a Carlos Martínez desde MySQL.
     */
    private function resolveActiveUser(): User
    {
        if (Schema::hasTable('users') && User::count() === 0) {
            (new DatabaseSeeder())->run();
        }

        if (Auth::check()) {
            return Auth::user();
        }

        return User::where('email', 'c.martinez@logitrack.com.ni')->first()
            ?? User::first();
    }

    /**
     * Construye el arreglo de datos comunes para las vistas del panel e inyecta el estado
     * de permisos de la suscripción (`is_free`, `has_premium`) para aplicar restricciones en la interfaz.
     */
    private function getCommonData(): array
    {
        if (Schema::hasTable('equipos') && Equipo::count() === 0) {
            (new DatabaseSeeder())->run();
        }

        $activeUser = $this->resolveActiveUser();
        $isFree = $activeUser ? $activeUser->isFreeAccount() : false;
        $hasPremium = $activeUser ? $activeUser->hasPremiumAccess() : true;

        $trialDays = $activeUser ? $activeUser->trial_days_remaining : 11;
        $planBadge = match ($activeUser->subscription_plan ?? 'trial') {
            'pro' => 'Cuenta Premium • $60/mes',
            'freemium' => 'Cuenta Free (Restringida)',
            default => "Premium (Prueba: {$trialDays}d restantes)",
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
            'is_free' => $isFree,
            'has_premium' => $hasPremium,
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
     * Procesa la autenticación del usuario con validación robusta de credenciales
     * y soporte de acceso rápido demo (en modo Premium o modo Free para demostraciones).
     */
    public function login(Request $request)
    {
        if (Schema::hasTable('users') && User::count() === 0) {
            (new DatabaseSeeder())->run();
        }

        // Acceso rápido de demostración como Carlos Martínez (permite elegir modo Free o Premium)
        if ($request->boolean('demo_login')) {
            $carlos = User::where('email', 'c.martinez@logitrack.com.ni')->first();
            if ($carlos) {
                if ($request->input('demo_mode') === 'free') {
                    $carlos->update([
                        'subscription_plan' => 'freemium',
                        'subscription_status' => 'freemium',
                        'monthly_price' => 0.00,
                        'auto_renew' => false,
                    ]);
                } elseif ($request->input('demo_mode') === 'premium') {
                    $carlos->update([
                        'subscription_plan' => 'pro',
                        'subscription_status' => 'active',
                        'monthly_price' => 60.00,
                        'auto_renew' => true,
                    ]);
                }

                Auth::login($carlos);
                $request->session()->regenerate();

                return redirect()->route('dashboard')->with(
                    'status',
                    'Sesión iniciada como Carlos Martínez (' . ($carlos->isFreeAccount() ? 'Cuenta Free con restricciones' : 'Cuenta Premium Activa') . ').'
                );
            }
        }

        // Validación robusta de entradas en Login
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ], [
            'email.required' => 'Debes ingresar tu correo electrónico corporativo.',
            'email.email' => 'Ingresa un formato de correo electrónico válido (ejemplo@empresa.com).',
            'password.required' => 'Debes ingresar tu contraseña.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales ingresadas no coinciden con nuestros registros en la base de datos.',
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
     * Registra un nuevo usuario/empresa con validación estricta de campos
     * y configura su tipo de cuenta (Free vs Prueba Gratis 14 Días / Premium $60/mes).
     */
    public function register(Request $request)
    {
        if (Schema::hasTable('users') && User::count() === 0) {
            (new DatabaseSeeder())->run();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120', 'regex:/^[\pL\s\.\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'company_name' => ['required', 'string', 'min:3', 'max:150'],
            'role' => ['required', 'string', 'min:3', 'max:100'],
            'phone' => ['required', 'string', 'min:8', 'max:25', 'regex:/^[0-9\+\-\s\(\)]+$/'],
            'plan_choice' => ['required', 'string', 'in:trial,pro,freemium'],
            'password' => ['required', 'string', 'min:6', 'max:100', 'confirmed'],
        ], [
            'name.required' => 'El nombre completo del administrador es obligatorio.',
            'name.min' => 'El nombre debe contener al menos 3 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'email.required' => 'El correo electrónico corporativo es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado en LogiTrack.',
            'company_name.required' => 'El nombre de la empresa es obligatorio.',
            'company_name.min' => 'El nombre de la empresa debe tener al menos 3 caracteres.',
            'role.required' => 'El cargo dentro de la empresa es obligatorio.',
            'phone.required' => 'El teléfono de contacto es obligatorio.',
            'phone.regex' => 'El teléfono solo puede contener números, espacios y el signo +.',
            'password.min' => 'La contraseña debe tener un mínimo de 6 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        $planChoice = $validated['plan_choice'];

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => trim($validated['role']),
            'company_name' => trim($validated['company_name']),
            'phone' => trim($validated['phone']),
            'subscription_plan' => $planChoice,
            'subscription_status' => $planChoice === 'pro' ? 'active' : ($planChoice === 'freemium' ? 'freemium' : 'on_trial'),
            'monthly_price' => $planChoice === 'freemium' ? 0.00 : 60.00,
            'trial_starts_at' => now(),
            'trial_ends_at' => now()->addDays(14),
            'next_billing_at' => now()->addDays(14),
            'payment_method_brand' => 'Visa Corporativa',
            'payment_method_last4' => '4821',
            'billing_ruc' => 'J0310000991204',
            'auto_renew' => $planChoice !== 'freemium',
        ]);

        PagoSuscripcion::create([
            'user_id' => $user->id,
            'referencia' => 'SUB-' . date('Y') . '-' . rand(100, 999),
            'concepto' => $planChoice === 'freemium'
                ? 'Registro en Cuenta Free (Solo Dashboard e Inventario)'
                : 'Activación Prueba Gratis 14 Días (Acceso Premium -> $60.00 USD/mes)',
            'plan' => $planChoice,
            'monto' => $planChoice === 'pro' ? 60.00 : 0.00,
            'moneda' => 'USD',
            'metodo_pago' => 'Registro Inicial Web',
            'estado' => $planChoice === 'pro' ? 'Pagado' : ($planChoice === 'freemium' ? 'Cuenta Free' : 'Prueba 14 Días Activa'),
            'fecha_emision' => now()->format('d/m/Y'),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with(
            'status',
            $planChoice === 'freemium'
                ? 'Cuenta Free creada. Tienes acceso a Dashboard e Inventario. Renueva tu suscripción para desbloquear GPS, Rutas y Personal.'
                : '¡Cuenta creada con éxito! Tu prueba gratuita de 14 días con acceso Premium está activa.'
        );
    }

    /**
     * Cierra la sesión activa del usuario.
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
     * Permitido tanto para cuentas FREE como PREMIUM.
     * Si el usuario es FREE, activa la alerta en la campanita de notificaciones al entrar a la vista.
     */
    public function dashboard()
    {
        $data = $this->getCommonData();
        $data['activePage'] = 'dashboard';
        $data['pageTitle'] = 'Resumen Operativo';
        $data['pageSubtitle'] = 'Vista general de flota y herramientas - 29/10/2026';
        // Dispara el aviso en la campanita cada vez que un usuario Free cambia de vista a Dashboard
        $data['triggerBellRenewal'] = $data['user']['is_free'];

        return view('logitrack.dashboard', $data);
    }

    /**
     * Vista 2: Inventario de Equipos y Recursos.
     * Permitido tanto para cuentas FREE como PREMIUM.
     * Si el usuario es FREE, activa la alerta en la campanita de notificaciones al cambiar a Inventario.
     */
    public function inventario(Request $request)
    {
        $data = $this->getCommonData();
        $data['activePage'] = 'inventario';
        $data['pageTitle'] = 'Inventario de Equipos y Recursos';
        $data['pageSubtitle'] = 'Control de herramientas por cuadrilla, encargos y bodega central - León, Nicaragua';
        $data['filtroCuadrilla'] = $request->query('cuadrilla', 'todas');
        $data['filtroEncargo'] = $request->query('encargo', 'todos');
        // Dispara el aviso en la campanita cada vez que un usuario Free cambia de vista a Inventario
        $data['triggerBellRenewal'] = $data['user']['is_free'];

        return view('logitrack.inventario', $data);
    }

    /**
     * Guarda un nuevo equipo en la tabla `equipos` de MySQL con validación robusta.
     * Soporta dos modos:
     * 1. Envío directo a Bodega Central (`destino_tipo = 'bodega'` o botón directo a bodega).
     * 2. Asignación a una Cuadrilla y Encargo específico (`destino_tipo = 'encargo'`).
     */
    public function storeEquipo(Request $request)
    {
        $esDirectoBodega = $request->input('destino_tipo') === 'bodega'
            || $request->boolean('enviar_a_bodega')
            || $request->input('cuadrilla') === 'Bodega Central';

        $rules = [
            'codigo' => ['required', 'string', 'min:4', 'max:30', 'regex:/^[A-Za-z0-9\-]+$/'],
            'nombre' => ['required', 'string', 'min:3', 'max:180'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'cantidad' => ['required', 'string', 'min:1', 'max:60'],
            'responsable' => ['required', 'string', 'min:3', 'max:120'],
        ];

        if (! $esDirectoBodega) {
            $rules['cuadrilla'] = ['required', 'string', 'max:80'];
            $rules['encargo'] = ['required', 'string', 'max:180'];
        }

        $validated = $request->validate($rules, [
            'codigo.required' => 'El código del equipo es obligatorio.',
            'codigo.min' => 'El código debe tener al menos 4 caracteres.',
            'codigo.regex' => 'El código solo puede contener letras, números y guiones (ej. EQ-BOD-201).',
            'nombre.required' => 'El nombre de la herramienta o producto es obligatorio.',
            'nombre.min' => 'El nombre del equipo debe tener al menos 3 caracteres.',
            'cantidad.required' => 'Debes especificar la cantidad o unidades del producto.',
            'responsable.required' => 'Debes indicar el responsable o encargado que recibe.',
            'responsable.min' => 'El nombre del responsable debe tener al menos 3 caracteres.',
        ]);

        // Si se envía directamente a Bodega Central, se asignan los valores de custodia en almacén
        $cuadrillaFinal = $esDirectoBodega ? 'Bodega Central' : trim($request->input('cuadrilla', 'Cuadrilla C'));
        $encargoFinal = $esDirectoBodega ? 'Stock Disponible en Bodega' : trim($request->input('encargo', 'Stock Disponible en Bodega'));
        $ubicacionFinal = $esDirectoBodega ? 'Bodega Central, León' : 'León, Nicaragua (' . $cuadrillaFinal . ')';
        $estadoFinal = $esDirectoBodega ? 'En Bodega' : 'En Tránsito';
        $colorFinal = $esDirectoBodega ? 'slate' : 'yellow';

        Equipo::updateOrCreate(
            ['codigo' => strtoupper(trim($validated['codigo']))],
            [
                'nombre' => trim($validated['nombre']),
                'nombre_corto' => trim($validated['nombre']),
                'categoria' => trim($validated['categoria'] ?? ($esDirectoBodega ? 'Almacén / Bodega Central' : 'Equipamiento Operativo')),
                'serie' => 'SN-' . strtoupper(substr(md5($validated['codigo']), 0, 6)),
                'cantidad' => trim($validated['cantidad']),
                'cuadrilla' => $cuadrillaFinal,
                'responsable' => trim($validated['responsable']),
                'encargo' => $encargoFinal,
                'ubicacion' => $ubicacionFinal,
                'destino_movimiento' => $esDirectoBodega ? 'Bodega Central' : $encargoFinal,
                'estado' => $estadoFinal,
                'estado_color' => $colorFinal,
                'ultima_actualizacion' => 'Ahora mismo',
                'es_movimiento_bodega' => true,
            ]
        );

        return redirect()
            ->route('inventario', ['cuadrilla' => $cuadrillaFinal])
            ->with(
                'status',
                $esDirectoBodega
                    ? "Producto [{$validated['nombre']}] ingresado directamente a Bodega Central en la base de datos."
                    : "Equipo [{$validated['nombre']}] asignado a {$cuadrillaFinal} ({$encargoFinal}) correctamente."
            );
    }

    /**
     * Acción de 1 clic para enviar o retornar cualquier producto del inventario
     * directamente a la Bodega Central en la base de datos MySQL.
     */
    public function sendToBodega(Request $request)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'exists:equipos,codigo'],
        ], [
            'codigo.required' => 'Código de equipo no especificado.',
            'codigo.exists' => 'El equipo seleccionado no existe en el inventario.',
        ]);

        $equipo = Equipo::where('codigo', $validated['codigo'])->firstOrFail();

        $equipo->update([
            'cuadrilla' => 'Bodega Central',
            'responsable' => 'Ing. Mario Valladares (Bodega)',
            'encargo' => 'Stock Disponible en Bodega',
            'ubicacion' => 'Bodega Central, León',
            'destino_movimiento' => 'Bodega Central',
            'estado' => 'En Bodega',
            'estado_color' => 'slate',
            'ultima_actualizacion' => 'Recibido en Bodega (Ahora)',
            'es_movimiento_bodega' => true,
        ]);

        return redirect()
            ->route('inventario', ['cuadrilla' => 'Bodega Central'])
            ->with('status', "El producto [{$equipo->nombre_corto}] fue enviado directamente a Bodega Central.");
    }

    /**
     * Verifica si el usuario actual tiene permiso Premium para acceder a módulos restringidos
     * (`Rastreo GPS`, `Rutas y Envíos`, `Personal`). Si es cuenta FREE, bloquea el acceso y
     * redirige a la vista de Suscripción con el aviso en la campanita.
     */
    private function checkPremiumOrRedirect(string $moduloNombre)
    {
        $activeUser = $this->resolveActiveUser();

        if ($activeUser && $activeUser->isFreeAccount()) {
            return redirect()
                ->route('suscripcion')
                ->with(
                    'restricted_error',
                    "Acceso restringido: El apartado de «{$moduloNombre}» es exclusivo para cuentas Premium ($60/mes). Tu cuenta actual es Free (solo acceso a Dashboard e Inventario). Renueva tu suscripción para desbloquearlo."
                );
        }

        return null;
    }

    /**
     * Vista 3: Rastreo GPS en Tiempo Real (Exclusivo Cuenta Premium).
     */
    public function rastreo(Request $request)
    {
        if ($redirect = $this->checkPremiumOrRedirect('Rastreo GPS')) {
            return $redirect;
        }

        $data = $this->getCommonData();
        $data['activePage'] = 'rastreo';
        $data['pageTitle'] = 'Rastreo GPS en Tiempo Real';
        $data['pageSubtitle'] = 'Monitoreo satelital de cuadrillas y flota operativa en León, Nicaragua';
        $data['focusCuadrilla'] = $request->query('focus', 'cuadrilla-c');

        return view('logitrack.rastreo', $data);
    }

    /**
     * Vista 4: Rutas y Envíos con Señal de Destino (Exclusivo Cuenta Premium).
     */
    public function rutas()
    {
        if ($redirect = $this->checkPremiumOrRedirect('Rutas y Envíos')) {
            return $redirect;
        }

        $data = $this->getCommonData();
        $data['activePage'] = 'rutas';
        $data['pageTitle'] = 'Rutas y Envíos Activos';
        $data['pageSubtitle'] = 'Seguimiento de despachos y señales de destino por cuadrilla en León';

        return view('logitrack.rutas', $data);
    }

    /**
     * Vista 5: Directorio de Personal (Exclusivo Cuenta Premium).
     */
    public function personal(Request $request)
    {
        if ($redirect = $this->checkPremiumOrRedirect('Personal')) {
            return $redirect;
        }

        $data = $this->getCommonData();
        $data['activePage'] = 'personal';
        $data['pageTitle'] = 'Personal y Cuadrillas';
        $data['pageSubtitle'] = 'Directorio de encargados de bodega, choferes, técnicos y supervisores';
        $data['filtroRol'] = $request->query('rol', 'todos');

        return view('logitrack.personal', $data);
    }

    /**
     * Vista 6: Administración de Suscripción SaaS (Carlos Martínez).
     */
    public function suscripcion()
    {
        $data = $this->getCommonData();
        $activeUser = $this->resolveActiveUser();

        $data['activePage'] = 'suscripcion';
        $data['pageTitle'] = 'Suscripción y Facturación SaaS';
        $data['pageSubtitle'] = 'Administra tu prueba gratuita de 14 días, Plan Empresarial de $60/mes y permisos de cuenta';
        $data['pagos'] = $activeUser
            ? $activeUser->pagosSuscripcion()->get()
            : collect();
        $data['triggerBellRenewal'] = $data['user']['is_free'];

        return view('logitrack.suscripcion', $data);
    }

    /**
     * Procesa los cambios de plan (Free vs Prueba 14d vs Premium $60/mes) y actualiza los
     * datos de facturación con validación estricta en la base de datos MySQL.
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
                'concepto' => 'Suscripción Mensual LogiTrack Premium — Acceso Total Desbloqueado',
                'plan' => 'pro',
                'monto' => 60.00,
                'moneda' => 'USD',
                'metodo_pago' => $activeUser->payment_method_brand . ' •••• ' . $activeUser->payment_method_last4,
                'estado' => 'Pagado',
                'fecha_emision' => now()->format('d/m/Y'),
            ]);

            return redirect()->route('suscripcion')->with(
                'status',
                '¡Cuenta actualizada a Plan Premium ($60.00 USD/mes)! Se han desbloqueado Rastreo GPS, Rutas y Envíos, y Personal.'
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
                'concepto' => 'Inicio de Prueba Gratis de 14 Días (Acceso Premium -> $60.00 USD/mes)',
                'plan' => 'trial',
                'monto' => 0.00,
                'moneda' => 'USD',
                'metodo_pago' => $activeUser->payment_method_brand . ' •••• ' . $activeUser->payment_method_last4,
                'estado' => 'Prueba 14 Días Activa',
                'fecha_emision' => now()->format('d/m/Y'),
            ]);

            return redirect()->route('suscripcion')->with(
                'status',
                'Periodo de Prueba Gratis de 14 días activado con acceso Premium completo.'
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
                'concepto' => 'Cambio a Cuenta Free (Restringida a Dashboard e Inventario)',
                'plan' => 'freemium',
                'monto' => 0.00,
                'moneda' => 'USD',
                'metodo_pago' => 'Sin Cargo',
                'estado' => 'Cuenta Free ($0/mes)',
                'fecha_emision' => now()->format('d/m/Y'),
            ]);

            return redirect()->route('dashboard')->with(
                'status',
                'Has cambiado a Cuenta Free ($0/mes). Ahora solo tienes acceso a Dashboard e Inventario; Rastreo GPS, Rutas y Personal están bloqueados.'
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
                    : 'Suscripción cancelada/pausada. Tu cuenta ha pasado a modo restringido (Free) hasta que renueves.'
            );
        }

        // Validación robusta de los datos de facturación y método de pago corporativo
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'min:3', 'max:150'],
            'billing_ruc' => ['required', 'string', 'min:8', 'max:25', 'regex:/^[A-Za-z0-9\-]+$/'],
            'payment_method_brand' => ['required', 'string', 'in:Visa Corporativa,Mastercard Empresarial,BAC Credomatic León,Banpro Corporativo'],
            'payment_method_last4' => ['required', 'digits:4'],
        ], [
            'company_name.required' => 'La razón social de la empresa es obligatoria.',
            'company_name.min' => 'La razón social debe tener al menos 3 caracteres.',
            'billing_ruc.required' => 'El número RUC fiscal es obligatorio.',
            'billing_ruc.min' => 'El número RUC debe tener al menos 8 caracteres.',
            'billing_ruc.regex' => 'El RUC solo debe contener letras, números o guiones.',
            'payment_method_last4.required' => 'Los últimos 4 dígitos de la tarjeta son obligatorios.',
            'payment_method_last4.digits' => 'Debes ingresar exactamente 4 dígitos numéricos para la tarjeta.',
        ]);

        $activeUser->update([
            'company_name' => trim($validated['company_name']),
            'billing_ruc' => strtoupper(trim($validated['billing_ruc'])),
            'payment_method_brand' => $validated['payment_method_brand'],
            'payment_method_last4' => $validated['payment_method_last4'],
        ]);

        return redirect()->route('suscripcion')->with(
            'status',
            'Datos de facturación y tarjeta corporativa validados y guardados en la base de datos.'
        );
    }
}
