<script src="https://cdn.tailwindcss.com"></script>
<nav class="bg-gray-900 text-white shadow-md px-6 py-3">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div id="side-menu" class="absolute top-0 left-0 h-screen z-50 mt-14">
            <div
                class="group flex flex-col bg-gray-800 text-white transition-all duration-300 ease-in-out w-10 hover:w-64 overflow-hidden h-full">
                <nav class="flex flex-col space-y-4 mt-2 pl-auto">
                    <div x-data="{ searchOpen: false }" class="relative">
                        <a href="#" class="flex items-center space-x-3">
                            <span class="px-2 py-1 fa-solid fa-magnifying-glass"></span>
                            <input type="text" placeholder="Buscar ID"
                                class="bg-gray-700 text-white placeholder-gray-400 focus:outline-none rounded-md"
                                @focus="searchOpen = true" @keydown.escape="searchOpen = false">
                        </a>
                        <hr class="my-2 border-gray-700 opacity-50">
                        <div class="space-y-2 group">
                            <a href="/dashboard"
                                class="flex items-center space-x-3 px-2 py-2 rounded-md hover:bg-gray-700 hover:text-white transition-colors duration-200">
                                <span class="w-6 text-center"><i class="fa-solid fa-house"></i></span>
                                <span class="opacity-0 group-hover:opacity-100 transition-opacity">Incidencias</span>
                            </a>
                            <a href="/users"
                                class="flex items-center space-x-3 px-2 py-2 rounded-md hover:bg-gray-700 hover:text-white transition-colors duration-200">
                                <span class="w-6 text-center"><i class="fa-solid fa-user-tie"></i></span>
                                <span class="opacity-0 group-hover:opacity-100 transition-opacity">Usuarios</span>
                            </a>
                            <a href="{{ route('clientes.index') }}"
                                class="flex items-center space-x-3 px-2 py-2 rounded-md hover:bg-gray-700 hover:text-white transition-colors duration-200">
                                <span class="w-6 text-center"><i class="fa-solid fa-users"></i></span>
                                <span class="opacity-0 group-hover:opacity-100 transition-opacity">Clientes</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[placeholder="Buscar ID"]');

        searchInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                const query = searchInput.value.trim();
                if (query) {
                    window.location.href = `/buscar-incidencia?id=${encodeURIComponent(query)}`;
                }
            }
        });
    });
</script>
