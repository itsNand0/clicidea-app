<script src="https://cdn.tailwindcss.com"></script>

<nav class="bg-gray-900 text-white shadow-md fixed top-0 left-0 right-0 z-50" x-data="{ mobileMenuOpen: false, sideMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14 sm:h-16">
            
            <!-- Botón hamburguesa para móvil -->
            <div class="flex items-center lg:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <svg class="h-6 w-6" :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6" :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Logo / Nombre - Centrado en móvil -->
            <div class="flex items-center justify-center flex-1 lg:flex-none lg:justify-start">
                <a href="{{ route('view.dashboard') }}" class="flex items-center">
                    <img src="{{ asset('images/logo-eglobal.png') }}" class="h-6 sm:h-8 w-auto">
                </a>
            </div>

            <!-- Menú lateral para desktop (hover) -->
            <div class="hidden lg:block">
                <div id="side-menu" class="fixed top-16 left-0 h-screen z-10 pointer-events-none"
                     @mouseenter="sideMenuOpen = true" @mouseleave="sideMenuOpen = false">
                    <div class="group flex flex-col bg-gray-800 text-white transition-all duration-300 ease-in-out overflow-hidden pointer-events-auto shadow-lg"
                         :class="sideMenuOpen ? 'w-64 h-full' : 'w-12 h-full'">
                        <nav class="flex flex-col space-y-2 mt-4 px-2">
                            
                            <!-- Buscador -->
                            <div class="relative mb-4">
                                <div class="flex items-center space-x-3">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center">
                                        <i class="fa-solid fa-magnifying-glass text-gray-300"></i>
                                    </span>
                                    <input type="text" placeholder="Buscar ID" id="desktop-search"
                                           class="bg-gray-700 text-white placeholder-gray-400 focus:outline-none rounded-md px-2 py-1 text-sm flex-1 transition-all duration-300"
                                           :class="sideMenuOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'"
                                           style="transition-delay: 150ms;">
                                </div>
                                <hr class="my-3 border-gray-600 opacity-50" x-show="sideMenuOpen">
                            </div>

                            <!-- Enlaces de navegación -->
                            <div class="space-y-1">
                                @can('incidencias.ver')
                                <a href="/dashboard" 
                                   class="flex items-center space-x-3 px-2 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 group">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center">
                                        <i class="fa-solid fa-house text-gray-300 group-hover:text-white"></i>
                                    </span>
                                    <span class="text-sm transition-all duration-300" 
                                          :class="sideMenuOpen ? 'opacity-100' : 'opacity-0'"
                                          style="transition-delay: 100ms;">Incidencias</span>
                                </a>
                                @endcan

                                @can('users.ver')
                                <a href="/users" 
                                   class="flex items-center space-x-3 px-2 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 group">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center">
                                        <i class="fa-solid fa-user-tie text-gray-300 group-hover:text-white"></i>
                                    </span>
                                    <span class="text-sm transition-all duration-300" 
                                          :class="sideMenuOpen ? 'opacity-100' : 'opacity-0'"
                                          style="transition-delay: 100ms;">Usuarios</span>
                                </a>
                                @endcan

                                @can('clientes.ver')
                                <a href="{{ route('clientes.index') }}" 
                                   class="flex items-center space-x-3 px-2 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 group">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center">
                                        <i class="fa-solid fa-users text-gray-300 group-hover:text-white"></i>
                                    </span>
                                    <span class="text-sm transition-all duration-300" 
                                          :class="sideMenuOpen ? 'opacity-100' : 'opacity-0'"
                                          style="transition-delay: 100ms;">Clientes</span>
                                </a>
                                @endcan
                            </div>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Usuario y Logout -->
            <div class="flex items-center space-x-2 sm:space-x-4">
                @auth
                    <span class="text-xs sm:text-sm hidden sm:block">Hola, {{ Auth::user()->name }}</span>
                    <span class="text-xs sm:text-sm block sm:hidden">{{ substr(Auth::user()->name, 0, 10) }}{{ strlen(Auth::user()->name) > 10 ? '...' : '' }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="close_Sesion bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm px-2 sm:px-4 py-1 rounded transition-colors duration-200">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span class="hidden sm:inline ml-1">Salir</span>
                        </button>
                    </form>
                @endauth
                
                @guest
                    <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm px-2 sm:px-4 py-1 rounded transition-colors duration-200">
                        Iniciar sesión
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <!-- Menú móvil desplegable -->
    <div class="lg:hidden relative z-40" x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         style="display: none;">
        
        <div class="bg-gray-800 border-t border-gray-700 px-4 py-3 space-y-3">
            
            <!-- Buscador móvil -->
            <div class="relative">
                <div class="flex items-center space-x-3 bg-gray-700 rounded-md px-3 py-2">
                    <i class="fa-solid fa-magnifying-glass text-gray-300 text-sm"></i>
                    <input type="text" placeholder="Buscar ID" id="mobile-search"
                           class="bg-transparent text-white placeholder-gray-400 focus:outline-none text-sm flex-1">
                </div>
            </div>

            <!-- Separador -->
            <hr class="border-gray-600 opacity-50">

            <!-- Enlaces de navegación móvil -->
            <div class="space-y-2">
                @can('incidencias.ver')
                <a href="/dashboard" 
                   class="flex items-center space-x-3 px-3 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200"
                   @click="mobileMenuOpen = false">
                    <i class="fa-solid fa-house text-gray-300 w-5"></i>
                    <span class="text-sm text-white">Incidencias</span>
                </a>
                @endcan

                @can('users.ver')
                <a href="/users" 
                   class="flex items-center space-x-3 px-3 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200"
                   @click="mobileMenuOpen = false">
                    <i class="fa-solid fa-user-tie text-gray-300 w-5"></i>
                    <span class="text-sm text-white">Usuarios</span>
                </a>
                @endcan

                @can('clientes.ver')
                <a href="{{ route('clientes.index') }}" 
                   class="flex items-center space-x-3 px-3 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200"
                   @click="mobileMenuOpen = false">
                    <i class="fa-solid fa-users text-gray-300 w-5"></i>
                    <span class="text-sm text-white">Clientes</span>
                </a>
                @endcan
            </div>

            <!-- Información del usuario en móvil -->
            @auth
            <div class="pt-3 border-t border-gray-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-user text-gray-300 text-sm"></i>
                        </div>
                        <span class="text-sm text-white">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </div>
</nav>

<!-- Overlay para cerrar menú móvil al hacer clic fuera -->
<div class="lg:hidden fixed inset-0 bg-black bg-opacity-25 z-20" 
     x-show="mobileMenuOpen" 
     @click="mobileMenuOpen = false"
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;">
</div>

<!-- Espaciador para el contenido principal -->
<div class="h-14 sm:h-16"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para manejar búsqueda
    function handleSearch(input) {
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

    // Prevenir scroll del body cuando el menú móvil está abierto
    document.addEventListener('alpine:init', () => {
        Alpine.store('menu', {
            mobileOpen: false,
            toggle() {
                this.mobileOpen = !this.mobileOpen;
                if (this.mobileOpen) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            },
            close() {
                this.mobileOpen = false;
                document.body.style.overflow = '';
            }
        });
    });

    // Cerrar menú móvil al redimensionar a desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            document.body.style.overflow = '';
        }
    });
});
</script>

<style>
/* Mejoras adicionales para el hover del menú lateral */
@media (min-width: 1024px) {
    #side-menu:hover .group {
        width: 16rem; /* w-64 */
    }
    
    #side-menu .group {
        width: 3rem; /* w-12 */
    }
}

/* Asegurar que el contenido no se superponga */
body {
    padding-top: 0;
}

/* El menú lateral no debe bloquear clics en el contenido */
#side-menu {
    pointer-events: none;
    max-height: calc(100vh - 4rem);
}

#side-menu .group {
    pointer-events: auto;
}

/* Asegurar que Alpine.js funcione correctamente */
[x-cloak] { 
    display: none !important; 
}

/* Corregir z-index hierarchy */
.navbar-fixed {
    z-index: 50;
}

.sidebar-menu {
    z-index: 10;
}

.mobile-overlay {
    z-index: 20;
}

.mobile-menu {
    z-index: 40;
}

/* Transiciones suaves para elementos */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Estilos para el overlay */
.overlay-blur {
    backdrop-filter: blur(2px);
}

/* Mejoras para el focus en elementos interactivos */
button:focus, 
input:focus, 
a:focus {
    outline: 2px solid rgba(59, 130, 246, 0.5);
    outline-offset: 2px;
}

/* Animación personalizada para el menú hamburguesa */
.hamburger-line {
    transition: all 0.3s ease-in-out;
    transform-origin: center;
}

/* Estilos para mejorar la accesibilidad */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
