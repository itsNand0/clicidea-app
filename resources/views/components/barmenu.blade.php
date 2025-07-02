<script src="https://cdn.tailwindcss.com"></script>
<nav class="bg-gray-900 text-white shadow-md px-6 py-3">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <button id="menu-toggle" class="text-white hover:text-gray-300 focus:outline-none">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div id="side-menu" class="fixed top-0 left-0 h-screen z-50 hidden mt-12">
            <div class="group flex flex-col bg-gray-800 text-white transition-all duration-300 ease-in-out w-16 hover:w-64 overflow-hidden h-full">
            <nav class="flex flex-col space-y-4 mt-16 pl-4">
                <a href="/dashboard" class="flex items-center space-x-4">
                    <span class="px-2 py-1 fa-solid fa-house"></span>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity">Dashboard</span>
                </a>
                <a href="/users" class="flex items-center space-x-4">
                    <span class="px-2 py-1 fa-solid fa-users"></span>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity">Usuarios</span>
                </a>
            </nav>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const menuToggle = document.getElementById('menu-toggle');
                const sideMenu = document.getElementById('side-menu');

                // Helper to animate hide/show
                function showMenu() {
                    sideMenu.classList.remove('hidden');
                    sideMenu.classList.remove('opacity-0');
                    sideMenu.classList.add('opacity-100');
                    sideMenu.style.transition = 'opacity 0.3s';
                }
                function hideMenu() {
                    sideMenu.classList.remove('opacity-100');
                    sideMenu.classList.add('opacity-0');
                    sideMenu.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        sideMenu.classList.add('hidden');
                    }, 300);
                }

                menuToggle.addEventListener('click', function () {
                    if (sideMenu.classList.contains('hidden')) {
                        showMenu();
                    } else {
                        hideMenu();
                    }
                });

                sideMenu.addEventListener('mouseleave', function () {
                    hideMenu();
                });

                // Set initial state
                sideMenu.classList.add('opacity-0');
            });
        </script>
        <!-- Logo / Nombre -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('view.dashboard') }}"><img src="{{ asset('images/logo-eglobal.png') }}" class="h-8 w-auto"></a>
        </div>

        <!-- Usuario y Logout -->
        <div class="flex items-center space-x-4">
            @auth
                <span class="text-sm">Hola, {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="close_Sesion bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-1 rounded fa-solid fa-arrow-right-from-bracket">
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
