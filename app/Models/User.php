<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo User de LogiTrack SaaS.
 * Representa al administrador o gerente operativo de la empresa cliente (ej. Carlos Martínez)
 * e incluye la lógica de permisos entre cuentas FREE (Freemium) y cuentas PREMIUM (Trial 14d / Pro $60/mes).
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atributos asignables en masa, incluyendo datos empresariales y de suscripción SaaS.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_name',
        'phone',
        'subscription_plan',
        'subscription_status',
        'monthly_price',
        'trial_starts_at',
        'trial_ends_at',
        'next_billing_at',
        'payment_method_brand',
        'payment_method_last4',
        'billing_ruc',
        'auto_renew',
    ];

    /**
     * Atributos ocultos al serializar.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversión automática de tipos para fechas de prueba, facturación y montos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'monthly_price' => 'decimal:2',
            'trial_starts_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'next_billing_at' => 'datetime',
            'auto_renew' => 'boolean',
        ];
    }

    /**
     * Relación 1:N con el historial de facturas y pagos de la suscripción.
     */
    public function pagosSuscripcion(): HasMany
    {
        return $this->hasMany(PagoSuscripcion::class, 'user_id')->orderByDesc('id');
    }

    /**
     * Obtiene las iniciales del usuario (ej. "CM" para Carlos Martínez) para mostrar en el avatar.
     */
    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * Calcula cuántos días restan de la prueba gratuita de 14 días.
     */
    public function getTrialDaysRemainingAttribute(): int
    {
        if (! $this->trial_ends_at) {
            return 14;
        }

        $diff = now()->diffInDays($this->trial_ends_at, false);

        return max(0, (int) ceil($diff));
    }

    /**
     * Determina si el usuario se encuentra en el plan gratuito restringido (Freemium)
     * o con la suscripción cancelada/vencida.
     * Un usuario Free solo tiene acceso a Dashboard e Inventario.
     */
    public function isFreeAccount(): bool
    {
        return $this->subscription_plan === 'freemium'
            || $this->subscription_status === 'freemium'
            || $this->subscription_status === 'cancelled';
    }

    /**
     * Determina si el usuario tiene acceso Premium completo (Plan Pro $60/mes o Prueba Gratis 14 días activa).
     * Permite acceder a Rastreo GPS, Rutas y Envíos, y Personal.
     */
    public function hasPremiumAccess(): bool
    {
        return ! $this->isFreeAccount();
    }
}
