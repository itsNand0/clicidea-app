<?php

namespace App\Livewire;

use App\Models\Cliente;
use Livewire\Component;

class DropdownClientes extends Component
{
    public $search = '';
    public $clientes = [];

    public function updatedSearch()
    {
        $this->clientes = Cliente::where('nombre', 'like', '%' . $this->search . '%')->get();
    }

    public function render()
    {
        return view('livewire.dropdown-clientes');
    }
}
