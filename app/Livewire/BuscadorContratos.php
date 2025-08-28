<?php

namespace App\Livewire;

use App\Models\Cliente;
use Livewire\Component;

class BuscadorContratos extends Component
{
    public $search = '';
    public $selectedContract = null;
    public $selectedContractName = '';
    public $showDropdown = false;
    public $highlightedIndex = 0;

    // Eventos que puede emitir este componente
    protected $listeners = ['resetContract' => 'resetSelection'];

    public function updatedSearch()
    {
        $this->showDropdown = !empty($this->search);
        $this->highlightedIndex = 0;
        
        // Si se borra la búsqueda, resetear selección
        if (empty($this->search)) {
            $this->resetSelection();
        }
    }

    public function selectContract($clienteId, $nombre, $atmId = null)
    {
        $this->selectedContract = $clienteId;
        $this->selectedContractName = $nombre;
        $this->search = $atmId ? "$atmId - $nombre" : $nombre;
        $this->showDropdown = false;
        
        // Emitir evento para el formulario padre
        $this->dispatch('contractSelected', [
            'id' => $clienteId,
            'name' => $nombre,
            'atmId' => $atmId
        ]);
    }

    public function resetSelection()
    {
        $this->selectedContract = null;
        $this->selectedContractName = '';
        $this->search = '';
        $this->showDropdown = false;
        $this->highlightedIndex = 0;
    }

    public function hideDropdown()
    {
        // Delay para permitir clicks en resultados
        $this->showDropdown = false;
    }

    public function showDropdown()
    {
        if (!empty($this->search)) {
            $this->showDropdown = true;
        }
    }

    public function navigateDropdown($direction)
    {
        $contracts = $this->getFilteredContracts();
        $maxIndex = count($contracts) - 1;

        if ($direction === 'up') {
            $this->highlightedIndex = $this->highlightedIndex > 0 ? $this->highlightedIndex - 1 : $maxIndex;
        } else {
            $this->highlightedIndex = $this->highlightedIndex < $maxIndex ? $this->highlightedIndex + 1 : 0;
        }
    }

    public function selectHighlighted()
    {
        $contracts = $this->getFilteredContracts();
        if (isset($contracts[$this->highlightedIndex])) {
            $contract = $contracts[$this->highlightedIndex];
            $this->selectContract($contract->idcliente, $contract->nombre, $contract->atm_id);
        }
    }

    private function getFilteredContracts()
    {
        if (empty($this->search)) {
            return collect();
        }

        return Cliente::where(function($query) {
            $searchTerm = '%' . $this->search . '%';
            $query->where('nombre', 'ILIKE', $searchTerm)
                  ->orWhere('atm_id', 'ILIKE', $searchTerm);
        })
        ->orderBy('nombre')
        ->limit(10)
        ->get();
    }

    public function render()
    {
        $contracts = $this->getFilteredContracts();

        return view('livewire.buscador-contratos', [
            'contracts' => $contracts
        ]);
    }
}
