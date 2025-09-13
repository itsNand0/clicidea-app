<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebPushSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_token',
        'content_encoding',
        'subscription_data',
        'user_agent',
        'is_active',
        'last_used_at'
    ];

    protected $casts = [
        'subscription_data' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime'
    ];

    /**
     * Relación con el modelo User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para suscripciones activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para un usuario específico
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Marcar la suscripción como usada
     */
    public function markAsUsed()
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Desactivar la suscripción
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Obtener datos para envío de push
     */
    public function getPushData(): array
    {
        return [
            'endpoint' => $this->endpoint,
            'keys' => [
                'p256dh' => $this->public_key,
                'auth' => $this->auth_token
            ],
            'contentEncoding' => $this->content_encoding
        ];
    }
}
