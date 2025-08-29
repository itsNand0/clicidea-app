<div class="relative" x-data="{ showDropdown: @entangle('mostrarDropdown') }">
    <!-- Campana de notificaciones -->
    <button 
        @click="$wire.toggleDropdown()"
        class="relative p-2 text-gray-300 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded-lg"
        type="button"
    >
        <i class="fa-solid fa-bell text-xl"></i>
        
        <!-- Badge de notificaciones no leídas -->
        @if($noLeidas > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                {{ $noLeidas > 9 ? '9+' : $noLeidas }}
            </span>
        @endif
    </button>

    <!-- Dropdown de notificaciones -->
    @if($mostrarDropdown)
        <div 
            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border z-50 max-h-96 overflow-hidden"
            x-show="showDropdown"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-1 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-1 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.away="$wire.toggleDropdown()"
        >
            <!-- Header -->
            <div class="bg-gray-50 px-4 py-3 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fa-solid fa-bell mr-2"></i>
                        Notificaciones
                    </h3>
                    @if($noLeidas > 0)
                        <button 
                            wire:click="marcarTodasLeidas"
                            class="text-sm text-blue-600 hover:text-blue-800 transition-colors"
                        >
                            Marcar todas leídas
                        </button>
                    @endif
                </div>
            </div>

            <!-- Lista de notificaciones -->
            <div class="max-h-64 overflow-y-auto">
                @if(count($notificaciones) > 0)
                    @foreach($notificaciones as $notificacion)
                        <div 
                            class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer {{ !$notificacion['leida'] ? 'bg-blue-50' : '' }}"
                            wire:click="marcarComoLeida('{{ $notificacion['id'] }}')"
                            onclick="window.location.href='{{ $notificacion['url'] }}'"
                        >
                            <div class="flex items-start space-x-3">
                                <!-- Icono -->
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-8 h-8 bg-{{ $notificacion['color'] }}-100 rounded-full flex items-center justify-center">
                                        <i class="{{ $notificacion['icono'] }} text-{{ $notificacion['color'] }}-600 text-sm"></i>
                                    </div>
                                </div>
                                
                                <!-- Contenido -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $notificacion['titulo'] }}
                                        </p>
                                        @if(!$notificacion['leida'])
                                            <div class="w-2 h-2 bg-blue-500 rounded-full ml-2"></div>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $notificacion['mensaje'] }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $notificacion['fecha'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="px-4 py-8 text-center">
                        <i class="fa-solid fa-bell-slash text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 text-sm">No tienes notificaciones</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            @if(count($notificaciones) > 0)
                <div class="bg-gray-50 px-4 py-3 border-t">
                    <a 
                        href="#" 
                        class="text-sm text-blue-600 hover:text-blue-800 transition-colors font-medium flex items-center justify-center"
                    >
                        <i class="fa-solid fa-eye mr-2"></i>
                        Ver todas las notificaciones
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
