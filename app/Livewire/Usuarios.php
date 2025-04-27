<?php

namespace App\Livewire;

use App\Http\Controllers\Usercontroller;
use App\Models\User;
use Livewire\Component;

class Usuarios extends Component
{
    public function render()
    {   
        return view('livewire.usuarios', [
            $users = User::all()
        ]  , compact('users'));
    }
}
