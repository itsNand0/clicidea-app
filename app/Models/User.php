<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\DatabaseNotification;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, DatabaseNotification> $unreadNotifications  
 * @property-read int|null $notifications_count
 * @property-read int|null $unread_notifications_count
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'usuario',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    /**
     * Relación con suscripciones web push
     */
    public function webPushSubscriptions()
    {
        return $this->hasMany(WebPushSubscription::class);
    }

    /**
     * Obtener suscripciones activas de web push
     */
    public function activeWebPushSubscriptions()
    {
        return $this->webPushSubscriptions()->active();
    }
}
