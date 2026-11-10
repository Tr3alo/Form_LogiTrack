<?php

namespace App\Http\Controllers;

use App\Models\Cuadrilla;
use App\Models\Equipo;
use App\Models\Personal;
use App\Models\RutaEnvio;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LogiTrackController extends Controller
{
    /**
     * Obtiene los datos desde la base de datos MySQL (logitrack_demostration).
     * Si las tablas están vacías, ejecuta el seeder automáticamente.
     */
    private function getCommonData(): array
    {
        $user = [
            'initials' => 'CM',
            'name' => 'Carlos Martínez',
            'role' => 'Gerente de Operaciones',
            'date' => '29/10/2026',
            'branch' => 'Sede Central León, Nicaragua',
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

        // Asegurar que la base de datos tenga los datos iniciales si ya fue migrada
        if (Schema::hasTable('equipos') && Equipo::count() === 0) {
            (new DatabaseSeeder())->run();
        }

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

    public function dashboard()
    {
        $data = $this->getCommonData();
        $data['activePage'] = 'dashboard';
        $data['pageTitle'] = 'Resumen Operativo';
        $data['pageSubtitle'] = 'Vista general de flota y herramientas - 29/10/2026';

        return view('logitrack.dashboard', $data);
    }

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

    public function rastreo(Request $request)
    {
        $data = $this->getCommonData();
        $data['activePage'] = 'rastreo';
        $data['pageTitle'] = 'Rastreo GPS en Tiempo Real';
        $data['pageSubtitle'] = 'Monitoreo satelital de cuadrillas y flota operativa en León, Nicaragua';
        $data['focusCuadrilla'] = $request->query('focus', 'cuadrilla-c');

        return view('logitrack.rastreo', $data);
    }

    public function rutas()
    {
        $data = $this->getCommonData();
        $data['activePage'] = 'rutas';
        $data['pageTitle'] = 'Rutas y Envíos Activos';
        $data['pageSubtitle'] = 'Seguimiento de despachos y señales de destino por cuadrilla en León';

        return view('logitrack.rutas', $data);
    }

    public function personal(Request $request)
    {
        $data = $this->getCommonData();
        $data['activePage'] = 'personal';
        $data['pageTitle'] = 'Personal y Cuadrillas';
        $data['pageSubtitle'] = 'Directorio de encargados de bodega, choferes, técnicos y supervisores';
        $data['filtroRol'] = $request->query('rol', 'todos');

        return view('logitrack.personal', $data);
    }
}
