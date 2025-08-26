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
            <div class="flex items-center gap-2">
                <!-- Filtro para mostrar estados cerrados -->
                <label class="flex items-center text-sm text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors cursor-pointer">
                    <input type="checkbox" wire:model.live="showClosed" class="mr-2 rounded text-blue-600 focus:ring-blue-500 focus:ring-2">
                    <span class="font-medium">Mostrar cerrados</span>
                </label>

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

        <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th wire:click="sortBy('idincidencia')"
                            class="cursor-pointer px-3 py-2 text-left hover:bg-gray-100 transition-colors">
                            <div class="flex items-center space-x-1">
                                <span>#</span>
                                <i class="fas fa-sort text-gray-400"></i>
                            </div>
                        </th>
                        @if ($visibleColumns['usuario'])
                            <th wire:click="sortBy('usuario_idusuario')"
                                class="cursor-pointer px-3 py-2 text-left hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-1">
                                    <span>Responsable</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                        @endif
                        @if ($visibleColumns['estado'])
                            <th wire:click="sortBy('estadoincidencia_idestadoincidencia')"
                                class="cursor-pointer px-3 py-2 text-left hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-1">
                                    <span>Estado</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                        @endif
                        @if ($visibleColumns['contrato'])
                            <th class="px-3 py-2 text-left">Cliente</th>
                        @endif
                        @if ($visibleColumns['usuarioincidencia'])
                            <th wire:click="sortBy('usuarioincidencia')"
                                class="cursor-pointer px-3 py-2 text-left hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-1">
                                    <span>Autor</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                        @endif
                        @if ($visibleColumns['asunto'])
                            <th class="px-3 py-2 text-left">Asunto</th>
                        @endif
                        @if ($visibleColumns['descripcion'])
                            <th class="px-3 py-2 text-left">Descripción</th>
                        @endif
                        @if ($visibleColumns['contacto'])
                            <th class="px-3 py-2 text-left">Contacto</th>
                        @endif
                        @if ($visibleColumns['fecha'])
                            <th wire:click="sortBy('fechaincidencia')"
                                class="cursor-pointer px-3 py-2 text-left hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-1">
                                    <span>Fecha</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                        @endif
                        @if ($visibleColumns['resolucion'])
                            <th wire:click="sortBy('fecharesolucionincidencia')"
                                class="cursor-pointer px-3 py-2 text-left hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-1">
                                    <span>Resolución</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                        @endif
                        <th class="px-3 py-2 text-center sticky right-0 bg-gray-50">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($datas as $data)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 py-2 text-sm font-medium text-gray-900">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                    #{{ $data->idincidencia }}
                                </span>
                            </td>
                            @if ($visibleColumns['usuario'])
                                <td class="px-3 py-2 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                            <i class="fas fa-user text-xs text-gray-600"></i>
                                        </div>
                                        <div class="truncate max-w-[120px]" title="
                                            @if (isset($data->usuario->cargo))
                                                {{ $data->usuario->cargo->nombre_cargo }}
                                            @elseif (isset($data->usuario->area))
                                                {{ $data->usuario->area->area_name }}
                                            @elseif (isset($data->usuario->name))
                                                {{ $data->usuario->name }}
                                            @else
                                                Sin asignar
                                            @endif
                                        ">
                                            @if (isset($data->usuario->cargo))
                                                {{ $data->usuario->cargo->nombre_cargo }}
                                            @elseif (isset($data->usuario->area))
                                                {{ $data->usuario->area->area_name }}
                                            @elseif (isset($data->usuario->name))
                                                {{ $data->usuario->name }}
                                            @else
                                                <span class="text-gray-400 italic">Sin asignar</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            @endif
                            @if ($visibleColumns['estado'])
                                <td class="px-3 py-2 text-sm">
                                    @php
                                        $estadoColors = [
                                            'Abierta' => 'bg-red-100 text-red-800',
                                            'En Proceso' => 'bg-yellow-100 text-yellow-800',
                                            'Cerrada' => 'bg-green-100 text-green-800',
                                            'Pendiente' => 'bg-orange-100 text-orange-800',
                                        ];
                                        $colorClass = $estadoColors[$data->estadoincidencia->descriestadoincidencia] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $colorClass }}">
                                        {{ $data->estadoincidencia->descriestadoincidencia }}
                                    </span>
                                </td>
                            @endif
                            @if ($visibleColumns['contrato'])
                                <td class="px-3 py-2 text-sm text-gray-600">
                                    <div class="truncate max-w-[150px]" title="{{ $data->cliente->nombre }}">
                                        {{ $data->cliente->nombre }}
                                    </div>
                                </td>
                            @endif
                            @if ($visibleColumns['usuarioincidencia'])
                                <td class="px-3 py-2 text-sm text-gray-600">
                                    <div class="truncate max-w-[120px]" title="{{ $data->usuarioincidencia }}">
                                        {{ $data->usuarioincidencia }}
                                    </div>
                                </td>
                            @endif
                            @if ($visibleColumns['asunto'])
                                <td class="px-3 py-2 text-sm text-gray-900">
                                    <div class="truncate max-w-[200px]" title="{{ $data->asuntoincidencia }}">
                                        {{ $data->asuntoincidencia }}
                                    </div>
                                </td>
                            @endif
                            @if ($visibleColumns['descripcion'])
                                <td class="px-3 py-2 text-sm text-gray-600">
                                    <div class="truncate max-w-[250px]" title="{{ $data->descriincidencia }}">
                                        {{ $data->descriincidencia }}
                                    </div>
                                </td>
                            @endif
                            @if ($visibleColumns['contacto'])
                                <td class="px-3 py-2 text-sm text-gray-600">
                                    <div class="truncate max-w-[120px]" title="{{ $data->contactoincidencia }}">
                                        {{ $data->contactoincidencia }}
                                    </div>
                                </td>
                            @endif
                            @if ($visibleColumns['fecha'])
                                <td class="px-3 py-2 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                        <span class="text-xs">{{ \Carbon\Carbon::parse($data->fechaincidencia)->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                            @endif
                            @if ($visibleColumns['resolucion'])
                                <td class="px-3 py-2 text-sm">
                                    @if ($data->fecharesolucionincidencia)
                                        <div class="flex items-center text-green-600">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <span class="text-xs">{{ \Carbon\Carbon::parse($data->fecharesolucionincidencia)->format('d/m/Y') }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-3 py-2 text-sm sticky right-0 bg-white">
                                <div class="flex items-center justify-center space-x-1">
                                    <a href="{{ route('incidencias.justshow', $data->idincidencia) }}"
                                        class="inline-flex items-center justify-center w-7 h-7 text-white bg-gray-500 hover:bg-blue-600 rounded transition-colors"
                                        title="Ver detalles">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('incidencias.show', $data->idincidencia) }}"
                                        class="inline-flex items-center justify-center w-7 h-7 text-white bg-gray-500 hover:bg-green-600 rounded transition-colors"
                                        title="Editar">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p class="text-sm">No hay incidencias registradas</p>
                                </div>
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
    
    <style>
        /* Línea clamp para truncar texto */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Transiciones suaves para hover */
        .transition-colors {
            transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
        }
        
        /* Tabla compacta en desktop */
        @media (min-width: 1024px) {
            .compact-table td {
                padding: 0.5rem 0.75rem;
            }
            
            .compact-table th {
                padding: 0.5rem 0.75rem;
            }
        }
        
        /* Optimizaciones para móvil */
        @media (max-width: 1023px) {
            .mobile-card {
                margin-bottom: 0.75rem;
            }
        }
        
        /* Hover effects mejorados */
        .hover-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
</div>
