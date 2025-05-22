<?php

namespace App\Livewire;

use Livewire\WithPagination;

use App\Http\Controllers\Usercontroller;
use App\Models\User;
use Livewire\Component;

class Usuarios extends Component
{
    use WithPagination;

    public $search = "";

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search != '', function ($query) {
                $search = '%' . $this->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search);
                });
            })
            ->paginate(10);

        return view('livewire.usuarios', compact('users'));
    }
}
