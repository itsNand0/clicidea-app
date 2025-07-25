<div>
    <main class="p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-2">
            <input type="text" placeholder="Buscar"
                class="border p-2 rounded-lg w-full md:w-auto text-sm md:text-base"
                wire:model.live.debounce.300ms="search">
            <div class="flex gap-2 w-full md:w-auto justify-end">
                <a href="#"
                    class="bg-lime-600 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-sm md:text-base flex items-center justify-center">
                    <i class="fa-solid fa-plus"></i>
                </a>
                <a href="#"
                    class="bg-lime-600 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-sm md:text-base flex items-center justify-center">
                    <i class="fa-solid fa-file-excel"></i>
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow-md">
                <thead class="hidden md:table-header-group">
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th wire:click="sortBy('idcliente')" class="cursor-pointer py-3 px-6 text-left border-r border-white">#</th>
                        <th wire:click="sortBy('nombre')" class="cursor-pointer py-3 px-6 text-left border-r border-white">Cliente</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm font-light">
                    @forelse ($datas as $data)
                        <tr class="border-b border-gray-200 hover:bg-gray-100 hidden md:table-row">
                            <td class="py-3 px-6">{{ $data->idcliente }}</td>
                            <td class="py-3 px-6">{{ $data->nombre}}</td>
                            <td class="px-6 py-4">
                                <a href="#"
                                    class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-lime-500 fa-solid fa-eye"></a>
                                <a href="#"
                                    class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-cyan-400 fa-solid fa-pen-to-square"></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-4 text-center text-gray-500">No hay clientes registrados
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