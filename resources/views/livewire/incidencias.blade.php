<div>
    <main class="p-4 lg:p-6">
        <!-- Header section with responsive layout -->
        <div class="space-y-4 mb-6">
            <!-- Search bar - full width on mobile -->
            <div class="w-full">
                <input type="text" placeholder="Buscar incidencias..." 
                    class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    wire:model.live.debounce.300ms="search">
            </div>
            
            <!-- Controls row - responsive layout -->
            <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center">
                <!-- Left controls -->
                <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                    <!-- Column visibility toggle -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button" 
                            class="bg-gray-200 hover:bg-gray-300 px-3 py-2 rounded-lg text-gray-700 transition-colors flex items-center gap-2 btn-animated">
                            <i class="fa-solid fa-eye-low-vision"></i>
                            <span class="hidden sm:inline">Columnas</span>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute left-0 mt-2 bg-white border rounded-lg shadow-lg p-4 z-20 w-64 max-h-64 overflow-y-auto mobile-dropdown"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95">
                            <h3 class="font-medium text-gray-900 mb-3 pb-2 border-b border-gray-200">Mostrar columnas</h3>
                            <div class="space-y-3">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:click="toggleColumn('usuario')"
                                        {{ $visibleColumns['usuario'] ? 'checked' : '' }} 
                                        class="rounded text-blue-600 focus:ring-blue-500 mr-3 group-hover:scale-110 transition-transform">
                                    <span class="text-sm group-hover:text-blue-600 transition-colors">Responsable</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:click="toggleColumn('estado')"
                                        {{ $visibleColumns['estado'] ? 'checked' : '' }}
                                        class="rounded text-blue-600 focus:ring-blue-500 mr-3 group-hover:scale-110 transition-transform">
                                    <span class="text-sm group-hover:text-blue-600 transition-colors">Estado</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:click="toggleColumn('contrato')"
                                        {{ $visibleColumns['contrato'] ? 'checked' : '' }}
                                        class="rounded text-blue-600 focus:ring-blue-500 mr-3 group-hover:scale-110 transition-transform">
                                    <span class="text-sm group-hover:text-blue-600 transition-colors">Contrato</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:click="toggleColumn('usuarioincidencia')"
                                        {{ $visibleColumns['usuarioincidencia'] ? 'checked' : '' }}
                                        class="rounded text-blue-600 focus:ring-blue-500 mr-3 group-hover:scale-110 transition-transform">
                                    <span class="text-sm group-hover:text-blue-600 transition-colors">Autor</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:click="toggleColumn('asunto')"
                                        {{ $visibleColumns['asunto'] ? 'checked' : '' }}
                                        class="rounded text-blue-600 focus:ring-blue-500 mr-3 group-hover:scale-110 transition-transform">
                                    <span class="text-sm group-hover:text-blue-600 transition-colors">Asunto</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:click="toggleColumn('descripcion')"
                                        {{ $visibleColumns['descripcion'] ? 'checked' : '' }}
                                        class="rounded text-blue-600 focus:ring-blue-500 mr-3 group-hover:scale-110 transition-transform">
                                    <span class="text-sm group-hover:text-blue-600 transition-colors">Descripción</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:click="toggleColumn('contacto')"
                                        {{ $visibleColumns['contacto'] ? 'checked' : '' }}
                                        class="rounded text-blue-600 focus:ring-blue-500 mr-3 group-hover:scale-110 transition-transform">
                                    <span class="text-sm group-hover:text-blue-600 transition-colors">Contacto</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:click="toggleColumn('fecha')"
                                        {{ $visibleColumns['fecha'] ? 'checked' : '' }}
                                        class="rounded text-blue-600 focus:ring-blue-500 mr-3 group-hover:scale-110 transition-transform">
                                    <span class="text-sm group-hover:text-blue-600 transition-colors">Fecha</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:click="toggleColumn('resolucion')"
                                        {{ $visibleColumns['resolucion'] ? 'checked' : '' }}
                                        class="rounded text-blue-600 focus:ring-blue-500 mr-3 group-hover:scale-110 transition-transform">
                                    <span class="text-sm group-hover:text-blue-600 transition-colors">Resolución</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Show closed toggle -->
                    <label class="flex items-center text-sm text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors cursor-pointer">
                        <input type="checkbox" wire:model.live="showClosed" 
                            class="mr-2 rounded text-blue-600 focus:ring-blue-500 focus:ring-2">
                        <span class="font-medium whitespace-nowrap">Cerrados</span>
                    </label>

                    <!-- Show today only toggle -->
                    <label class="flex items-center text-sm text-gray-700 bg-blue-50 px-3 py-2 rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors cursor-pointer">
                        <input type="checkbox" wire:model.live="showTodayOnly" 
                            class="mr-2 rounded text-blue-600 focus:ring-blue-500 focus:ring-2">
                        <span class="font-medium whitespace-nowrap">Hoy</span>
                    </label>

                    <!-- Show active only toggle -->
                    <label class="flex items-center text-sm text-gray-700 bg-yellow-50 px-3 py-2 rounded-lg border border-yellow-200 hover:bg-yellow-100 transition-colors cursor-pointer">
                        <input type="checkbox" wire:model.live="showActiveOnly" 
                            class="mr-2 rounded text-blue-600 focus:ring-blue-500 focus:ring-2">
                        <span class="font-medium whitespace-nowrap">Activos</span>
                    </label>
                </div>

                <!-- Date filters section -->
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <!-- Date from -->
                    <div class="flex flex-col">
                        <input type="date" wire:model.live="dateFrom"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Date to -->
                    <div class="flex flex-col">
                        <input type="date" wire:model.live="dateTo"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Clear filters button -->
                    <div class="flex items-end">
                        <button wire:click="clearFilters" type="button"
                            class="px-3 py-2 text-sm bg-red-100 hover:bg-red-200 text-red-700 border border-red-300 rounded-lg transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-filter-circle-xmark"></i>
                            <span class="hidden sm:inline">Limpiar</span>
                        </button>
                    </div>
                </div>

                <!-- Right controls -->
                <div class="flex gap-2 w-full sm:w-auto justify-end">
                    @can('incidencias.crear')
                        <a href="{{ route('incidencias.create') }}" 
                            class="bg-lime-600 hover:bg-lime-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-2 min-w-[44px]">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    @endcan

                    @can('incidencias.exportarExcel')
                        <a href="{{ route('incidencias.exportarExcel') }}" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-2 min-w-[44px]">
                            <i class="fa-solid fa-file-excel"></i>
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Desktop table view -->
        <div class="hidden lg:block overflow-x-auto bg-white rounded-lg shadow-sm">
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
                                    <div class="truncate max-w-[120px]">
                                        @if (isset($data->usuario->cargo))
                                            <span title="{{ $data->usuario->cargo->nombre_cargo }}">{{ $data->usuario->cargo->nombre_cargo }}</span>
                                        @elseif (isset($data->usuario->area))
                                            <span title="{{ $data->usuario->area->area_name }}">{{ $data->usuario->area->area_name }}</span>
                                        @elseif (isset($data->usuario->name))
                                            <span title="{{ $data->usuario->name }}">{{ $data->usuario->name }}</span>
                                        @else
                                            <span class="text-gray-400 italic" title="Sin asignar">Sin asignar</span>
                                        @endif
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

        <!-- Mobile card view -->
        <div class="lg:hidden space-y-4">
            @forelse ($datas as $data)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                    <!-- Header with ID and status -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 font-medium">
                                #{{ $data->idincidencia }}
                            </span>
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
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex gap-1">
                            <a href="{{ route('incidencias.justshow', $data->idincidencia) }}"
                                class="inline-flex items-center justify-center w-8 h-8 text-white bg-gray-500 hover:bg-blue-600 rounded-full transition-colors"
                                title="Ver detalles">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('incidencias.show', $data->idincidencia) }}"
                                class="inline-flex items-center justify-center w-8 h-8 text-white bg-gray-500 hover:bg-green-600 rounded-full transition-colors"
                                title="Editar">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Main content -->
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-900 line-clamp-2">
                            {{ $data->asuntoincidencia }}
                        </div>
                        
                        @if ($data->descriincidencia)
                            <div class="text-sm text-gray-600 line-clamp-2">
                                {{ $data->descriincidencia }}
                            </div>
                        @endif

                        <!-- Key info grid -->
                        <div class="grid grid-cols-1 gap-2 mt-3 pt-3 border-t border-gray-100">
                            @if ($visibleColumns['contrato'])
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-500 w-24 flex-shrink-0">Cliente:</span>
                                    <span class="text-gray-900 font-medium truncate">{{ $data->cliente->nombre }}</span>
                                </div>
                            @endif
                            
                            @if ($visibleColumns['usuarioincidencia'])
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-500 w-24 flex-shrink-0">Autor:</span>
                                    <span class="text-gray-700 truncate">{{ $data->usuarioincidencia }}</span>
                                </div>
                            @endif

                            @if ($visibleColumns['usuario'])
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-500 w-24 flex-shrink-0">Responsable:</span>
                                    <span class="text-gray-700 truncate">
                                        @if (isset($data->usuario->cargo))
                                            {{ $data->usuario->cargo->nombre_cargo }}
                                        @elseif (isset($data->usuario->area))
                                            {{ $data->usuario->area->area_name }}
                                        @elseif (isset($data->usuario->name))
                                            {{ $data->usuario->name }}
                                        @else
                                            <span class="text-gray-400 italic">Sin asignar</span>
                                        @endif
                                    </span>
                                </div>
                            @endif

                            @if ($visibleColumns['contacto'])
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-500 w-24 flex-shrink-0">Contacto:</span>
                                    <span class="text-gray-700 truncate">{{ $data->contactoincidencia }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Footer with dates -->
                        <div class="flex justify-between items-center pt-2 mt-3 border-t border-gray-100">
                            @if ($visibleColumns['fecha'])
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    <span>{{ \Carbon\Carbon::parse($data->fechaincidencia)->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            
                            @if ($visibleColumns['resolucion'])
                                <div class="text-xs">
                                    @if ($data->fecharesolucionincidencia)
                                        <div class="flex items-center text-green-600">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <span>{{ \Carbon\Carbon::parse($data->fecharesolucionincidencia)->format('d/m/Y') }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pendiente
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                    <div class="flex flex-col items-center justify-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p class="text-sm">No hay incidencias registradas</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
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
        
        .transition-shadow {
            transition: box-shadow 0.15s ease-in-out;
        }
        
        /* Mejoras de accesibilidad para botones pequeños */
        @media (max-width: 1023px) {
            .mobile-touch-target {
                min-width: 44px;
                min-height: 44px;
            }
        }
        
        /* Optimizaciones para móvil - scroll horizontal oculto */
        @media (max-width: 1023px) {
            .hide-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            
            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }
        }
        
        /* Mejoras para dropdown en móvil */
        @media (max-width: 640px) {
            .mobile-dropdown {
                position: fixed !important;
                left: 1rem !important;
                right: 1rem !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                max-height: 70vh;
                width: calc(100vw - 2rem) !important;
                max-width: none !important;
            }
        }
        
        /* Estados de carga y skeleton */
        .skeleton {
            animation: skeleton-loading 1s linear infinite alternate;
        }
        
        @keyframes skeleton-loading {
            0% {
                background-color: hsl(200, 20%, 80%);
            }
            100% {
                background-color: hsl(200, 20%, 95%);
            }
        }
        
        /* Mejoras para la búsqueda */
        .search-input:focus {
            transform: scale(1.02);
            transition: transform 0.2s ease-in-out;
        }
        
        /* Estados hover mejorados */
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Animaciones para elementos interactivos */
        .btn-animated {
            transition: all 0.2s ease-in-out;
        }
        
        .btn-animated:hover {
            transform: translateY(-1px);
        }
        
        .btn-animated:active {
            transform: translateY(0);
        }
    </style>
</div>
