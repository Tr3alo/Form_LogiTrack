<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones para la tabla de usuarios y sesiones.
     * Incluye los campos necesarios para gestionar el modelo SaaS Freemium de LogiTrack:
     * - Prueba gratuita de 14 días (`trial_starts_at`, `trial_ends_at`)
     * - Suscripción mensual de $60 USD (`monthly_price`, `subscription_plan`, `subscription_status`)
     * - Datos de facturación y método de pago de la empresa.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Datos operativos y empresariales del usuario (ej. Carlos Martínez)
            $table->string('role')->default('Gerente de Operaciones');
            $table->string('company_name')->default('Inversiones Eléctricas de León S.A.');
            $table->string('phone')->nullable();

            // Lógica de Suscripción SaaS (Freemium / Prueba Gratis 14 días -> $60/mes)
            $table->string('subscription_plan')->default('trial'); // freemium, trial, pro
            $table->string('subscription_status')->default('on_trial'); // on_trial, active, freemium, cancelled
            $table->decimal('monthly_price', 8, 2)->default(60.00);
            $table->timestamp('trial_starts_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();

            // Datos de método de pago y facturación corporativa
            $table->string('payment_method_brand')->default('Visa Corporativa');
            $table->string('payment_method_last4', 4)->default('4821');
            $table->string('billing_ruc')->default('J0310000459128');
            $table->boolean('auto_renew')->default(true);

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
