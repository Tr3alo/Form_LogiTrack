<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo PagoSuscripcion.
 * Registra cada evento de facturación de la cuenta SaaS (activación de prueba gratis de 14 días,
 * cobros mensuales de $60.00 USD del Plan Pro o cambios de plan).
 */
class PagoSuscripcion extends Model
{
    protected $table = 'pagos_suscripcion';

    protected $fillable = [
        'user_id',
        'referencia',
        'concepto',
        'plan',
        'monto',
        'moneda',
        'metodo_pago',
        'estado',
        'fecha_emision',
    ];

    protected $casts = [
        'monto' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
