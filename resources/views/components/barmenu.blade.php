<!-- Navigation Bar -->
<div x-data="{ 
    mobileMenuOpen: false, 
    sideMenuOpen: false,
    toggleMobileMenu() {
        this.mobileMenuOpen = !this.mobileMenuOpen;
        document.body.style.overflow = this.mobileMenuOpen ? 'hidden' : '';
    },
    closeMobileMenu() {
        this.mobileMenuOpen = false;
        document.body.style.overflow = '';
    }
}" x-cloak class="antialiased">
    
    <!-- Top Navigation Bar -->
    <nav class="bg-gray-900 text-white shadow-lg fixed top-0 left-0 right-0 z-50">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                
                <!-- Mobile Menu Button -->
                <div class="flex items-center lg:hidden">
                    <button @click="toggleMobileMenu()" 
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition-colors duration-200"
                            :aria-expanded="mobileMenuOpen">
                        <span class="sr-only">Abrir menú principal</span>
                        <svg class="h-6 w-6 transition-transform duration-200" 
                             :class="mobileMenuOpen ? 'rotate-90' : ''" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  :d="mobileMenuOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'" />
                        </svg>
                    </button>
                </div>

                <!-- Logo / Brand -->
                <div class="flex items-center justify-center flex-1 lg:flex-none lg:justify-start">
                    <a href="{{ route('view.dashboard') }}" class="flex items-center hover:opacity-80 transition-opacity">
                        <img src="{{ asset('images/logo-eglobal.png') }}" class="h-6 sm:h-8 w-auto" alt="Logo">
                    </a>
                </div>

                <!-- User Actions -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    @auth
                        <div class="hidden sm:flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-user text-gray-300 text-sm"></i>
                            </div>
                            <span class="text-sm text-gray-300">{{ Auth::user()->name }}</span>
                        </div>
                        <div class="block sm:hidden">
                            <span class="text-xs text-gray-300">{{ substr(Auth::user()->name, 0, 10) }}{{ strlen(Auth::user()->name) > 10 ? '...' : '' }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline-block">
                            @csrf
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm px-2 sm:px-4 py-1.5 rounded-md transition-colors duration-200 flex items-center space-x-1">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                <span class="hidden sm:inline">Salir</span>
                            </button>
                        </form>
                    @endauth
                    
                    @guest
                        <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm px-3 sm:px-4 py-1.5 rounded-md transition-colors duration-200">
                            Iniciar sesión
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Desktop Sidebar -->
    <div class="hidden lg:block">
        <div class="fixed top-16 left-0 h-full z-30" 
             @mouseenter="sideMenuOpen = true" 
             @mouseleave="sideMenuOpen = false">
            <div class="bg-gray-800 text-white shadow-xl transition-all duration-300 ease-in-out h-full"
                 :class="sideMenuOpen ? 'w-64' : 'w-12'">
                <div class="flex flex-col h-full">
                    
                    <!-- Search Section -->
                    <div class="p-3 border-b border-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                                <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                            </div>
                            <input type="text" 
                                   placeholder="Buscar ID..." 
                                   id="desktop-search"
                                   class="bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md px-2 py-1.5 text-sm transition-all duration-300"
                                   :class="sideMenuOpen ? 'opacity-100 w-full' : 'opacity-0 w-0 pointer-events-none'"
                                   style="transition-delay: 150ms;">
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="flex-1 py-4 space-y-1 overflow-y-auto">
                        
                        @can('incidencias.ver')
                        <a href="/dashboard" 
                           class="flex items-center space-x-3 px-3 py-2.5 mx-2 rounded-md hover:bg-gray-700 transition-colors duration-200 group {{ request()->is('dashboard') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                            <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                                <i class="fa-solid fa-house text-current"></i>
                            </div>
                            <span class="text-sm font-medium transition-all duration-300" 
                                  :class="sideMenuOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                                  style="transition-delay: 100ms;">Incidencias</span>
                        </a>
                        @endcan

                        @can('users.ver')
                        <a href="/users" 
                           class="flex items-center space-x-3 px-3 py-2.5 mx-2 rounded-md hover:bg-gray-700 transition-colors duration-200 group {{ request()->is('users*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                            <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                                <i class="fa-solid fa-user-tie text-current"></i>
                            </div>
                            <span class="text-sm font-medium transition-all duration-300" 
                                  :class="sideMenuOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                                  style="transition-delay: 100ms;">Usuarios</span>
                        </a>
                        @endcan

                        @can('clientes.ver')
                        <a href="{{ route('clientes.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 mx-2 rounded-md hover:bg-gray-700 transition-colors duration-200 group {{ request()->is('index') || request()->is('clientes*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                            <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                                <i class="fa-solid fa-users text-current"></i>
                            </div>
                            <span class="text-sm font-medium transition-all duration-300" 
                                  :class="sideMenuOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                                  style="transition-delay: 100ms;">Clientes</span>
                        </a>
                        @endcan

                        @can('incidencias.crear')
                        <a href="{{ route('incidencias.create') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 mx-2 rounded-md hover:bg-gray-700 transition-colors duration-200 group {{ request()->is('incidencias/create') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                            <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                                <i class="fa-solid fa-plus text-current"></i>
                            </div>
                            <span class="text-sm font-medium transition-all duration-300" 
                                  :class="sideMenuOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                                  style="transition-delay: 100ms;">Nueva Incidencia</span>
                        </a>
                        @endcan

                    </nav>

                    <!-- Footer Info -->
                    <div class="p-3 border-t border-gray-700" x-show="sideMenuOpen" x-transition>
                        <div class="text-xs text-gray-400 text-center">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Sistema v1.0
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40" 
         x-show="mobileMenuOpen" 
         @click="closeMobileMenu()"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Mobile Menu Sidebar -->
    <div class="lg:hidden fixed top-0 left-0 h-full w-80 max-w-sm bg-gray-800 text-white shadow-xl z-50 transform transition-transform duration-300 ease-in-out"
         :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'">
        
        <!-- Mobile Menu Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-700">
            <img src="{{ asset('images/logo-eglobal.png') }}" class="h-8 w-auto" alt="Logo">
            <button @click="closeMobileMenu()" 
                    class="p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>

        <!-- Mobile User Info -->
        @auth
        <div class="p-4 border-b border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-user text-gray-300"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>
        @endauth

        <!-- Mobile Search -->
        <div class="p-4 border-b border-gray-700">
            <div class="relative">
                <div class="flex items-center space-x-3 bg-gray-700 rounded-lg px-3 py-2.5">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                    <input type="text" 
                           placeholder="Buscar ID..." 
                           id="mobile-search"
                           class="bg-transparent text-white placeholder-gray-400 focus:outline-none text-sm flex-1">
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <nav class="flex-1 overflow-y-auto py-4">
            <div class="space-y-1 px-4">
                
                @can('incidencias.ver')
                <a href="{{ route('view.dashboard') }}" 
                   @click="closeMobileMenu()"
                   class="flex items-center space-x-3 px-3 py-3 rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->is('dashboard') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                    <i class="fa-solid fa-house w-5 text-current"></i>
                    <span class="text-sm font-medium">Incidencias</span>
                </a>
                @endcan

                @can('users.ver')
                <a href="/users" 
                   @click="closeMobileMenu()"
                   class="flex items-center space-x-3 px-3 py-3 rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->is('users*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                    <i class="fa-solid fa-user-tie w-5 text-current"></i>
                    <span class="text-sm font-medium">Usuarios</span>
                </a>
                @endcan

                @can('clientes.ver')
                <a href="{{ route('clientes.index') }}" 
                   @click="closeMobileMenu()"
                   class="flex items-center space-x-3 px-3 py-3 rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->is('index') || request()->is('clientes*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                    <i class="fa-solid fa-users w-5 text-current"></i>
                    <span class="text-sm font-medium">Clientes</span>
                </a>
                @endcan

                @can('incidencias.crear')
                <a href="{{ route('incidencias.create') }}" 
                   @click="closeMobileMenu()"
                   class="flex items-center space-x-3 px-3 py-3 rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->is('incidencias/create') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                    <i class="fa-solid fa-plus w-5 text-current"></i>
                    <span class="text-sm font-medium">Nueva Incidencia</span>
                </a>
                @endcan

            </div>
        </nav>

        <!-- Mobile Menu Footer -->
        <div class="p-4 border-t border-gray-700">
            <div class="text-xs text-gray-400 text-center mb-3">
                <i class="fa-solid fa-info-circle mr-1"></i>
                Sistema de Incidencias v1.0
            </div>
            @auth
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" 
                        class="w-full bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
            @endauth
        </div>
    </div>
</div>

<!-- Espaciador para el contenido principal -->
<div class="h-14 sm:h-16"></div>
                <script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para manejar búsqueda
    function handleSearch(input) {
        if (!input) return;
        
        input.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                const query = input.value.trim();
                if (query) {
                    window.location.href = `/buscar-incidencia?id=${encodeURIComponent(query)}`;
                }
            }
        });
    }

    // Aplicar a ambos inputs de búsqueda
    const desktopSearch = document.getElementById('desktop-search');
    const mobileSearch = document.getElementById('mobile-search');
    
    if (desktopSearch) handleSearch(desktopSearch);
    if (mobileSearch) handleSearch(mobileSearch);

    // Mejorar la accesibilidad - cerrar menú con Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            // Verificar si hay algún menú abierto y cerrarlo
            const component = document.querySelector('[x-data]');
            if (component && component.__x && component.__x.$data.mobileMenuOpen) {
                component.__x.$data.mobileMenuOpen = false;
                document.body.style.overflow = '';
            }
        }
    });

    // Cerrar menú móvil al redimensionar a desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            document.body.style.overflow = '';
            const component = document.querySelector('[x-data]');
            if (component && component.__x) {
                component.__x.$data.mobileMenuOpen = false;
            }
        }
    });
});
</script>

<style>
/* Asegurar que Alpine.js funcione correctamente */
[x-cloak] { 
    display: none !important; 
}

/* Mejoras de performance para transiciones */
* {
    box-sizing: border-box;
}

/* Suavizar transiciones del sidebar */
.sidebar-transition {
    transition: width 300ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* Asegurar que el contenido no se desborde horizontalmente */
.container-responsive {
    max-width: 100%;
    overflow-x: hidden;
}

/* Mejoras para el focus en elementos interactivos */
button:focus-visible, 
input:focus-visible, 
a:focus-visible {
    outline: 2px solid #3B82F6;
    outline-offset: 2px;
    border-radius: 4px;
}

/* Estilo para indicador de página activa */
.active-page {
    background-color: #374151 !important;
    color: #ffffff !important;
    font-weight: 600;
}

/* Mejoras para el overlay del menú móvil */
.mobile-overlay {
    backdrop-filter: blur(2px);
}

/* Transiciones suaves para elementos */
.smooth-transition {
    transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* Estilos para scrollbar en el sidebar */
.sidebar-scroll::-webkit-scrollbar {
    width: 4px;
}

.sidebar-scroll::-webkit-scrollbar-track {
    background: #374151;
}

.sidebar-scroll::-webkit-scrollbar-thumb {
    background: #6B7280;
    border-radius: 2px;
}

.sidebar-scroll::-webkit-scrollbar-thumb:hover {
    background: #9CA3AF;
}

/* Mejoras para la accesibilidad */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

@media (prefers-color-scheme: dark) {
    /* El diseño ya está en dark mode, pero se puede ajustar aquí si es necesario */
}

/* Ajustes para dispositivos con pantallas muy pequeñas */
@media (max-width: 480px) {
    .mobile-menu-sidebar {
        width: 100vw !important;
        max-width: none !important;
    }
}

/* Prevenir overflow horizontal en toda la aplicación */
html, body {
    overflow-x: hidden;
    max-width: 100vw;
}

/* Asegurar que las imágenes no se desborden */
img {
    max-width: 100%;
    height: auto;
}

/* Mejorar el espaciado en dispositivos muy pequeños */
@media (max-width: 360px) {
    .nav-spacing {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
}
</style>
