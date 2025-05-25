<div>
    <main class="p-6">
        <div class="flex justify-between items-center mb-4">
            <input type="text" placeholder="Buscar" class="border p-2 rounded-lg mb-4"
                wire:model.live.debounce.300ms="search">
            <a href="{{ route('incidencias.create') }}" class="bg-lime-600 text-white px-4 py-2 rounded-lg">
                <i class="fa-solid fa-plus"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">#</th>
                        <th class="py-3 px-6 text-left">Responsable</th>
                        <th class="py-3 px-6 text-left">Estado</th>
                        <th class="py-3 px-6 text-left">Contrato</th>
                        <th class="py-3 px-6 text-left">Creado por</th>
                        <th class="py-3 px-6 text-left">Asunto</th>
                        <th class="py-3 px-6 text-left">Descripcion</th>
                        <th class="py-3 px-6 text-left">Contacto</th>
                        <th class="py-3 px-6 text-left">Fecha de Creación</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm font-light"> 
                    @forelse ($datas as $data)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6">{{ $data->idIncidencia }}</td>
                        <td class="py-3 px-6">
                                @if (isset($data->usuario->cargo))
                                {{ $data->usuario->cargo->nombre_cargo }}
                                @elseif (isset($data->usuario->area))
                                    {{ $data->usuario->area->area_name }}
                                @else
                                    <span class="text-gray-500 italic">Sin asignar</span>
                                @endif
                            </td>
                            <td class="py-3 px-6">{{ $data->estadoincidencia->descriEstadoIncidencia }}</td>
                            <td class="py-3 px-6">{{ $data->cliente->nombre }}</td>
                            <td class="py-3 px-6">{{ $data->usuarioIncidencia }}</td>
                            <td class="py-3 px-6">{{ $data->asuntoIncidencia }}</td>
                            <td class="py-3 px-6">{{ $data->descriIncidencia }}</td>
                            <td class="py-3 px-6">{{ $data->contactoIncidencia }}</td>
                            <td class="py-3 px-6">{{ $data->fechaIncidencia }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('incidencias.show', $data->idIncidencia) }}"
                                    class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-lime-500 fa-solid fa-eye"></a>
                                <a href="#"
                                    class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-cyan-400 fa-solid fa-pen-to-square"></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-4 text-center text-gray-500">No tiene incidencias pendientes</td>
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
