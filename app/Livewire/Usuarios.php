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
                $search = '%' . strtolower($this->search) . '%';
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', [$search])
                        ->orWhereRaw('LOWER(email) LIKE ?', [$search])
                        ->orWhereHas('cargo', function ($q) use ($search) {
                            $q->whereRaw('LOWER(nombre_cargo) LIKE ?', [$search]);
                        })
                        ->orWhereHas('area', function ($q) use ($search) {
                            $q->whereRaw('LOWER(area_name) LIKE ?', [$search]);
                        });
                });
            })
            ->paginate(10);

        return view('livewire.usuarios', compact('users'));
    }
}
