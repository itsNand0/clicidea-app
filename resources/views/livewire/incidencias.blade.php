<div>
    <main class="p-6">
        <div class="flex justify-between items-center mb-4">
            <div class="flex gap-2">
                <input type="text" placeholder="Buscar" class="border p-2 rounded-lg"
                    wire:model.live.debounce.300ms="search">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" type="button" class="bg-gray-300 px-3 py-2 rounded-lg text-gray-700">
                        <i class="fa-solid fa-eye-low-vision"></i>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute mt-2 bg-white border rounded shadow-lg p-4 z-10 w-56">
                        <!-- Column visibility toggles -->
                        <label class="block mb-2"><input type="checkbox" wire:click="toggleColumn('usuario')"
                                {{ $visibleColumns['usuario'] ? 'checked' : '' }}> Responsable</label>
                        <label class="block mb-2"><input type="checkbox" wire:click="toggleColumn('estado')"
                                {{ $visibleColumns['estado'] ? 'checked' : '' }}> Estado</label>
                        <label class="block mb-2"><input type="checkbox" wire:click="toggleColumn('contrato')"
                                {{ $visibleColumns['contrato'] ? 'checked' : '' }}> Contrato</label>
                        <label class="block mb-2"><input type="checkbox" wire:click="toggleColumn('usuarioincidencia')"
                                {{ $visibleColumns['usuarioincidencia'] ? 'checked' : '' }}> Autor</label>
                        <label class="block mb-2"><input type="checkbox" wire:click="toggleColumn('asunto')"
                                {{ $visibleColumns['asunto'] ? 'checked' : '' }}> Asunto</label>
                        <label class="block mb-2"><input type="checkbox" wire:click="toggleColumn('descripcion')"
                                {{ $visibleColumns['descripcion'] ? 'checked' : '' }}> Descripcion</label>
                        <label class="block mb-2"><input type="checkbox" wire:click="toggleColumn('contacto')"
                                {{ $visibleColumns['contacto'] ? 'checked' : '' }}> Contacto</label>
                        <label class="block mb-2"><input type="checkbox" wire:click="toggleColumn('fecha')"
                                {{ $visibleColumns['fecha'] ? 'checked' : '' }}> Fecha</label>
                        <label class="block mb-2"><input type="checkbox" wire:click="toggleColumn('resolucion')"
                                {{ $visibleColumns['resolucion'] ? 'checked' : '' }}> Resolucion</label>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">

                @can('incidencias.crear')
                    <a href="{{ route('incidencias.create') }}" class="bg-lime-600 text-white px-4 py-2 rounded-lg">
                        <i class="fa-solid fa-plus"></i>
                    </a>
                @endcan

                @can('incidencias.exportarExcel')
                    <a href="{{ route('incidencias.exportarExcel') }}" class="bg-lime-600 text-white px-4 py-2 rounded-lg">
                        <i class="fa-solid fa-file-excel"></i>
                    </a>
                @endcan

                {{-- <form action="{{ route('import.incidencias') }}" method="POST" enctype="multipart/form-data"
                    class="mb-4">
                    @csrf
                    <input type="file" name="file" class="border p-2 rounded-lg">
                    <button type="submit" class="bg-lime-600 text-white px-4 py-2 rounded-lg">Importar
                        Incidencias</button>
                </form> --}}
            </div>
        </div>

        @if ($errors->any())
                    <div class="text-red-500">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow-md">
                <thead class="hidden md:table-header-group">
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th wire:click="sortBy('idincidencia')"
                            class="cursor-pointer py-3 px-6 text-left border-r border-white">#</th>
                        @if ($visibleColumns['usuario'])
                            <th wire:click="sortBy('usuario_idusuario')"
                                class="cursor-pointer py-3 px-6 text-left border-r border-white">Responsable</th>
                        @endif
                        @if ($visibleColumns['estado'])
                            <th wire:click="sortBy('estadoincidencia_idestadoincidencia')"
                                class="cursor-pointer py-3 px-6 text-left border-r border-white">Estado</th>
                        @endif
                        @if ($visibleColumns['contrato'])
                            <th wire:click="sortBy('cliente_idcliente')"
                                class="py-3 px-6 text-left border-r
                                border-white">
                                Contrato</th>
                        @endif
                        @if ($visibleColumns['usuarioincidencia'])
                            <th wire:click="sortBy('usuarioincidencia')"
                                class="cursor-pointer py-3 px-6 text-left border-r border-white">Autor</th>
                        @endif
                        @if ($visibleColumns['asunto'])
                            <th class="py-3 px-6 text-left border-r border-white">Asunto</th>
                        @endif
                        @if ($visibleColumns['descripcion'])
                            <th class="py-3 px-6 text-left border-r border-white">Descripcion</th>
                        @endif
                        @if ($visibleColumns['contacto'])
                            <th class="py-3 px-6 text-left border-r border-white">Contacto</th>
                        @endif
                        @if ($visibleColumns['fecha'])
                            <th wire:click="sortBy('fechaincidencia')"
                                class="cursor-pointer py-3 px-6 text-left border-r border-white">Fecha</th>
                        @endif
                        @if ($visibleColumns['resolucion'])
                            <th wire:click="sortBy('fecharesolucionincidencia')"
                                class="cursor-pointer py-3 px-6 text-left border-r border-white">Resolución</th>
                        @endif
                        <th class="py-3 px-6 text-center sticky right-0 bg-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm font-light">
                    @forelse ($datas as $data)
                        <tr class="border-b border-gray-200 hover:bg-gray-100 hidden md:table-row">
                            <td class="py-3 px-6">{{ $data->idincidencia }}</td>
                            @if ($visibleColumns['usuario'])
                                <td class="py-3 px-6">
                                    @if (isset($data->usuario->cargo))
                                        {{ $data->usuario->cargo->nombre_cargo }}
                                    @elseif (isset($data->usuario->area))
                                        {{ $data->usuario->area->area_name }}
                                    @elseif (isset($data->usuario->name))
                                        {{ $data->usuario->name }}
                                    @else
                                        <span class="text-gray-500 italic">Sin asignar</span>
                                    @endif
                                </td>
                            @endif
                            @if ($visibleColumns['estado'])
                                <td class="py-3 px-6">{{ $data->estadoincidencia->descriestadoincidencia }}</td>
                            @endif
                            @if ($visibleColumns['contrato'])
                                <td class="py-3 px-6">{{ $data->cliente->nombre }}</td>
                            @endif
                            @if ($visibleColumns['usuarioincidencia'])
                                <td class="py-3 px-6">{{ $data->usuarioincidencia }}</td>
                            @endif
                            @if ($visibleColumns['asunto'])
                                <td class="py-3 px-6">{{ $data->asuntoincidencia }}</td>
                            @endif
                            @if ($visibleColumns['descripcion'])
                                <td class="py-3 px-6">{{ $data->descriincidencia }}</td>
                            @endif
                            @if ($visibleColumns['contacto'])
                                <td class="py-3 px-6">{{ $data->contactoincidencia }}</td>
                            @endif
                            @if ($visibleColumns['fecha'])
                                <td class="py-3 px-6">{{ $data->fechaincidencia }}</td>
                            @endif
                            @if ($visibleColumns['resolucion'])
                                <td class="py-3 px-6">
                                    @if ($data->fecharesolucionincidencia)
                                        {{ $data->fecharesolucionincidencia }}
                                    @else
                                        <span class="text-gray-500 italic">Sin resolver</span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-6 py-4 sticky right-0 bg-white">
                                <a href="{{ route('incidencias.justshow', $data->idincidencia) }}"
                                    class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-lime-500 fa-solid fa-eye"></a>
                                <a href="{{ route('incidencias.show', $data->idincidencia) }} "
                                    class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-cyan-400 fa-solid fa-pen-to-square"></a>
                            </td>
                        </tr>
                        <!-- Responsive row for mobile -->
                        <tr class="md:hidden border-b border-gray-200 hover:bg-gray-100 block w-full">
                            <td colspan="10" class="block p-4">
                                <div class="mb-2"><span class="font-semibold">#:</span> {{ $data->idincidencia }}
                                </div>
                                <div class="mb-2">
                                    <span class="font-semibold">Responsable:</span>
                                    @if (isset($data->usuario->cargo))
                                        {{ $data->usuario->cargo->nombre_cargo }}
                                    @elseif (isset($data->usuario->area))
                                        {{ $data->usuario->area->area_name }}
                                    @else
                                        <span class="text-gray-500 italic">Sin asignar</span>
                                    @endif
                                </div>
                                <div class="mb-2"><span class="font-semibold">Estado:</span>
                                    {{ $data->estadoincidencia->descriestadoincidencia }}</div>
                                <div class="mb-2"><span class="font-semibold">Contrato:</span>
                                    {{ $data->cliente->nombre }}</div>
                                <div class="mb-2"><span class="font-semibold">Creado por:</span>
                                    {{ $data->usuarioincidencia }}</div>
                                <div class="mb-2"><span class="font-semibold">Asunto:</span>
                                    {{ $data->asuntoincidencia }}</div>
                                <div class="mb-2"><span class="font-semibold">Descripcion:</span>
                                    {{ $data->descriincidencia }}</div>
                                <div class="mb-2"><span class="font-semibold">Contacto:</span>
                                    {{ $data->contactoincidencia }}</div>
                                <div class="mb-2"><span class="font-semibold">Fecha de Creación:</span>
                                    {{ $data->fechaincidencia }}</div>
                                <div class="flex gap-2 mt-2">
                                    <a href="{{ route('incidencias.justshow', $data->idincidencia) }}"
                                        class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-lime-500 fa-solid fa-eye"></a>
                                    <a href="{{ route('incidencias.show', $data->idincidencia) }} "
                                        class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-cyan-400 fa-solid fa-pen-to-square"></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-4 text-center text-gray-500">No tiene incidencias pendientes
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
