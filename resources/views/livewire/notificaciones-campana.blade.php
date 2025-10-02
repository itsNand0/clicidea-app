<div class="relative" x-data="{ showDropdown: @entangle('mostrarDropdown') }" wire:poll.30s="cargarNotificaciones">
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
            class="absolute right-0 mt-2 w-80 sm:w-96 md:w-80 lg:w-96 xl:w-80 bg-white rounded-lg shadow-xl border z-50 max-h-96 overflow-hidden
                   xs:fixed xs:inset-x-4 xs:top-16 xs:w-auto xs:max-w-sm xs:mx-auto
                   sm:absolute sm:right-0 sm:inset-x-auto sm:top-auto sm:mx-0"
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
            <div class="bg-gray-50 px-3 sm:px-4 py-3 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">
                        <i class="fa-solid fa-bell mr-2"></i>
                        <span class="hidden sm:inline">Notificaciones</span>
                        <span class="sm:hidden">Notif.</span>
                    </h3>
                    @if($noLeidas > 0)
                        <button 
                            wire:click="marcarTodasLeidas"
                            class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 transition-colors"
                        >
                            <span class="hidden sm:inline">Marcar todas leídas</span>
                            <span class="sm:hidden">Leer todas</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Lista de notificaciones -->
            <div class="max-h-64 sm:max-h-72 md:max-h-80 overflow-y-auto">
                @if(count($notificaciones) > 0)
                    @foreach($notificaciones as $notificacion)
                        <div 
                            class="px-3 sm:px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer {{ !$notificacion['leida'] ? 'bg-blue-50' : '' }}"
                            wire:click="marcarComoLeida('{{ $notificacion['id'] }}')"
                            onclick="window.location.href='{{ $notificacion['url'] }}'"
                        >
                            <div class="flex items-start space-x-2 sm:space-x-3">
                                <!-- Icono -->
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-{{ $notificacion['color'] }}-100 rounded-full flex items-center justify-center">
                                        <i class="{{ $notificacion['icono'] }} text-{{ $notificacion['color'] }}-600 text-xs sm:text-sm"></i>
                                    </div>
                                </div>
                                
                                <!-- Contenido -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs sm:text-sm font-semibold text-gray-900 truncate">
                                            {{ $notificacion['titulo'] }}
                                        </p>
                                        @if(!$notificacion['leida'])
                                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-blue-500 rounded-full ml-2 flex-shrink-0"></div>
                                        @endif
                                    </div>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-1 leading-tight" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
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
                    <div class="px-3 sm:px-4 py-6 sm:py-8 text-center">
                        <i class="fa-solid fa-bell-slash text-3xl sm:text-4xl text-gray-300 mb-2 sm:mb-3"></i>
                        <p class="text-gray-500 text-xs sm:text-sm">No tienes notificaciones</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            @if(count($notificaciones) > 0)
                <div class="bg-gray-50 px-3 sm:px-4 py-3 border-t">
                    <a 
                        href="#" 
                        class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 transition-colors font-medium flex items-center justify-center"
                    >
                        <i class="fa-solid fa-eye mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Ver todas las notificaciones</span>
                        <span class="sm:hidden">Ver todas</span>
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
