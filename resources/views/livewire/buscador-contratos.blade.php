<div class="relative">
    <label class="block text-gray-700 font-semibold mb-1">
        <i class="fa-solid fa-search text-blue-600 mr-1"></i>
        Buscar ATM o PDV
    </label>
    
    <!-- Input de búsqueda -->
    <div class="relative">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search"
            wire:keydown.arrow-up.prevent="navigateDropdown('up')"
            wire:keydown.arrow-down.prevent="navigateDropdown('down')"
            wire:keydown.enter.prevent="selectHighlighted"
            wire:keydown.escape="hideDropdown"
            wire:focus="showDropdown"
            wire:blur="hideDropdown"
            placeholder="Busca por nombre del PDV, ATM o ATM ID"
            class="w-full border border-gray-300 px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 pl-10"
        >
        
        <!-- Ícono de búsqueda -->
        <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
            <i class="fa-solid fa-search text-gray-400"></i>
        </div>
        
        <!-- Spinner de carga -->
        <div wire:loading wire:target="search" class="absolute right-3 top-1/2 transform -translate-y-1/2">
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
        </div>
        
        <!-- Botón para limpiar -->
        @if($search)
            <button 
                type="button"
                wire:click="resetSelection"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
            >
                <i class="fa-solid fa-times"></i>
            </button>
        @endif
    </div>

    <!-- Dropdown con resultados -->
    @if($showDropdown && $contracts->count() > 0)
        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
            @foreach($contracts as $index => $contract)
                <div 
                    wire:click="selectContract({{ $contract->idcliente }}, '{{ $contract->nombre }}', '{{ $contract->atm_id }}')"
                    class="px-4 py-3 cursor-pointer transition-colors duration-150 {{ $index === $highlightedIndex ? 'bg-blue-50 border-l-4 border-blue-500' : 'hover:bg-gray-50' }}"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">
                                @if($contract->atm_id)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mr-2">
                                        {{ $contract->atm_id }}
                                    </span>
                                @endif
                                {{ $contract->nombre }}
                            </div>
                            @if($contract->direccion)
                                <div class="text-sm text-gray-600 mt-1">
                                    <i class="fa-solid fa-map-marker-alt mr-1"></i>
                                    {{ $contract->direccion }}
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500">
                                ID: {{ $contract->idcliente }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Mensaje cuando no hay resultados -->
    @if($showDropdown && $search && $contracts->count() === 0)
        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg p-4">
            <div class="text-center text-gray-500">
                <i class="fa-solid fa-search text-2xl mb-2"></i>
                <p class="text-sm">No se encontraron contratos que coincidan con "{{ $search }}"</p>
                <p class="text-xs mt-1">Intenta con el nombre del cliente, ATM ID o dirección</p>
            </div>
        </div>
    @endif

    <!-- Input hidden para el formulario -->
    @if($selectedContract)
        <input type="hidden" name="contrato" value="{{ $selectedContract }}">
        
        <!-- Mostrar selección actual -->
        <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fa-solid fa-check-circle text-green-600 mr-2"></i>
                    <span class="text-sm font-medium text-green-800">Contrato seleccionado:</span>
                </div>
                <button 
                    type="button"
                    wire:click="resetSelection"
                    class="text-green-600 hover:text-green-800 transition-colors"
                    title="Cambiar selección"
                >
                    <i class="fa-solid fa-edit"></i>
                </button>
            </div>
            <div class="mt-1 text-sm text-green-700">
                {{ $selectedContractName }}
            </div>
        </div>
    @endif

    <!-- Instrucciones de uso -->
    @if(!$search && !$selectedContract)
        <div class="mt-2 text-xs text-gray-500">
            <i class="fa-solid fa-info-circle mr-1"></i>
            Usa las flechas ↑↓ para navegar y Enter para seleccionar
        </div>
    @endif
</div>
