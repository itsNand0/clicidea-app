<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class NotificacionesCampana extends Component
{
    public $notificaciones = [];
    public $noLeidas = 0;
    public $mostrarDropdown = false;

    public function mount()
    {
        $this->cargarNotificaciones();
    }

    public function cargarNotificaciones()
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if ($user) {
            $this->notificaciones = $user
                ->notifications()
                ->take(10)
                ->get()
                ->map(function ($notification) {
                    $data = $notification->data;
                    return [
                        'id' => $notification->id,
                        'titulo' => $data['titulo'] ?? 'Notificación',
                        'mensaje' => $data['mensaje'] ?? '',
                        'url' => $data['url'] ?? '#',
                        'icono' => $data['icono'] ?? 'fa-solid fa-bell',
                        'color' => $data['color'] ?? 'blue',
                        'fecha' => $notification->created_at->diffForHumans(),
                        'leida' => !is_null($notification->read_at),
                        'tipo' => $data['tipo'] ?? 'general'
                    ];
                })
                ->toArray();

            $this->noLeidas = $user->unreadNotifications()->count();
        } else {
            $this->notificaciones = [];
            $this->noLeidas = 0;
        }
    }

    public function toggleDropdown()
    {
        $this->mostrarDropdown = !$this->mostrarDropdown;
        
        if ($this->mostrarDropdown) {
            $this->cargarNotificaciones();
        }
    }

    public function marcarComoLeida($notificationId)
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if ($user) {
            $notification = $user->notifications()->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
                $this->cargarNotificaciones();
            }
        }
    }

    public function marcarTodasLeidas()
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if ($user) {
            $user->unreadNotifications->markAsRead();
            $this->cargarNotificaciones();
        }
    }

    public function render()
    {
        return view('livewire.notificaciones-campana');
    }
}
