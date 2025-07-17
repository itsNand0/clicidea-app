<script src="https://cdn.tailwindcss.com"></script>
<nav class="bg-gray-900 text-white shadow-md px-6 py-3">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div id="side-menu" class="absolute top-0 left-0 h-screen z-50 mt-14">
            <div
                class="group flex flex-col bg-gray-800 text-white transition-all duration-300 ease-in-out w-10 hover:w-64 overflow-hidden h-full">
                <nav class="flex flex-col space-y-4 mt-2 pl-auto">
                    <div x-data="{ open: false }" class="relative">
                        <a href="/dashboard" class="flex items-center space-x-4">
                            <span class="px-2 py-1 fa-solid fa-house"></span>
                            <span class="opacity-0 group-hover:opacity-100 transition-opacity">Dashboard</span>
                        </a>
                        <hr class="my-2 border-gray-700 opacity-50">
                        <button @click="open = !open" class="flex items-center space-x-4 w-full focus:outline-none">
                            <span class="px-2 py-1 fa-solid fa-gears"></span>
                            <span class="opacity-0 group-hover:opacity-100 transition-opacity">Administrar</span>
                            <span class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity fa-solid"
                                :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></span>
                        </button>
                        <div x-show="open" @click.away="open = false" class="pl-8 mt-2 flex flex-col space-y-2">
                            <a href="/users" class="flex items-center space-x-4">
                                <span class="px-2 py-1 fa-solid fa-user-tie"></span>
                                <span class="opacity-0 group-hover:opacity-100 transition-opacity">Usuarios</span>
                            </a>
                            <a href="{{ route('clientes.index') }}" class="flex items-center space-x-4">
                                <span class="px-2 py-1 fa-solid fa-users"></span>
                                <span class="opacity-0 group-hover:opacity-100 transition-opacity">Clientes</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const menuToggle = document.getElementById('menu-toggle');
                const sideMenu = document.getElementById('side-menu');

                menuToggle.addEventListener('click', function() {
                    sideMenu.classList.toggle('w-64');
                });

                // Ensure the side menu always shows icons when not expanded
                sideMenu.classList.add('w-16');
            });
        </script>
        <!-- Logo / Nombre -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('view.dashboard') }}"><img src="{{ asset('images/logo-eglobal.png') }}"
                    class="h-8 w-auto"></a>
        </div>

        <!-- Usuario y Logout -->
        <div class="flex items-center space-x-4">
            @auth
                <span class="text-sm">Hola, {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="close_Sesion bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-1 rounded fa-solid fa-arrow-right-from-bracket">
                    </button>
                </form>
            @endauth
            @guest
                <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-1 rounded">
                    Iniciar sesión
                </a>
            @endguest
        </div>
    </div>
</nav>
