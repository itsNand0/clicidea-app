<div>
    <main class="p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-2">
            <input type="text" placeholder="Buscar" class="border p-2 rounded-lg w-full md:w-auto text-sm md:text-base"
                wire:model.live.debounce.300ms="search">
            <div class="flex gap-2 w-full md:w-auto justify-end">
                <a href="{{ route('clientes.create') }}"
                    class="bg-lime-600 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-sm md:text-base flex items-center justify-center">
                    <i class="fa-solid fa-plus"></i>
                </a>
                <a href="{{ route('clientes.exportarExcelCliente') }}"
                    class="bg-lime-600 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-sm md:text-base flex items-center justify-center">
                    <i class="fa-solid fa-file-excel"></i>
                </a>
            </div>
        </div>

        {{-- 
        <form action="{{ route('import.sedes') }}" method="POST" enctype="multipart/form-data" class="mb-4">
            @csrf
            <input type="file" name="file" class="border p-2 rounded-lg">
            <button type="submit" class="bg-lime-600 text-white px-4 py-2 rounded-lg">Importar Sedes</button>
        </form>
        --}}

        @if ($errors->any())
            <div class="text-red-500">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Vista móvil - Tarjetas -->
        <div class="md:hidden space-y-4">
            @forelse ($datas as $data)
                <div class="bg-white rounded-lg shadow-md p-4 border">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg">{{ $data->nombre }}</h3>
                            <p class="text-sm text-gray-600">ID: {{ $data->idcliente }}</p>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <a href="{{ route('clientes.edit', $data->idcliente) }}"
                                class="bg-gray-500 text-white text-sm px-3 py-2 rounded hover:bg-cyan-400 transition-colors">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button wire:click="confirmDelete({{ $data->idcliente }})"
                                class="bg-red-500 text-white text-sm px-3 py-2 rounded hover:bg-red-400 transition-colors">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-2 text-sm">
                        <div class="flex justify-between py-1 border-b border-gray-100">
                            <span class="font-medium text-gray-600">ATM ID:</span>
                            <span class="text-gray-900">{{ $data->atm_id }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="font-medium text-gray-600">Zona:</span>
                            <span class="text-gray-900">{{ $data->zona }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fa-solid fa-users text-4xl"></i>
                    </div>
                    <p class="text-gray-500">No hay clientes registrados</p>
                </div>
            @endforelse
        </div>

        <!-- Vista desktop - Tabla -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th wire:click="sortBy('idcliente')"
                            class="cursor-pointer py-3 px-6 text-left border-r border-white hover:bg-gray-300 transition-colors">#</th>
                        <th wire:click="sortBy('atm_id')"
                            class="cursor-pointer py-3 px-6 text-left border-r border-white hover:bg-gray-300 transition-colors">ATM ID</th>
                        <th wire:click="sortBy('nombre')"
                            class="cursor-pointer py-3 px-6 text-left border-r border-white hover:bg-gray-300 transition-colors">Cliente</th>
                        <th wire:click="sortBy('zona')"
                            class="cursor-pointer py-3 px-6 text-left border-r border-white hover:bg-gray-300 transition-colors">Zona</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm font-light">
                    @forelse ($datas as $data)
                        <tr class="border-b border-gray-200 hover:bg-gray-100 transition-colors">
                            <td class="py-3 px-6">{{ $data->idcliente }}</td>
                            <td class="py-3 px-6">{{ $data->atm_id }}</td>
                            <td class="py-3 px-6">{{ $data->nombre }}</td>
                            <td class="py-3 px-6">{{ $data->zona }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('clientes.edit', $data->idcliente) }}"
                                        class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-cyan-400 transition-colors">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button wire:click="confirmDelete({{ $data->idcliente }})"
                                        class="bg-red-500 text-white text-xs px-2 py-1 rounded hover:bg-red-400 transition-colors">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                <div class="text-gray-400 mb-4">
                                    <i class="fa-solid fa-users text-4xl"></i>
                                </div>
                                <p>No hay clientes registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $datas->links() }}
        </div>
    </main>
</div>
