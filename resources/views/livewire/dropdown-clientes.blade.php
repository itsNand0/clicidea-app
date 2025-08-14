<div class="relative w-full">
    <input type="text" wire:model.debounce.300ms="search" placeholder="Buscar cliente"
        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />

    @if (!empty($clientes))
        <ul class="absolute z-10 w-full bg-white border border-gray-300 rounded mt-1 max-h-60 overflow-y-auto shadow-lg">
            @foreach ($clientes as $cliente)
                <li wire:click="$set('search', '{{ $cliente->nombre }}')"
                    class="px-4 py-2 hover:bg-blue-100 cursor-pointer">
                    {{ $cliente->nombre }}
                </li>
            @endforeach
        </ul>
    @endif
</div>
