<script src="https://cdn.tailwindcss.com"></script>
<nav class="bg-gray-900 text-white shadow-md px-6 py-3">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <!-- Logo / Nombre -->
        <div class="flex items-center space-x-4">
            <a><img src="{{ asset('images/logo-eglobal.png') }}" class="h-8 w-auto"></a>
            <a href="/dashboard" class="hover:text-blue-400 transition">Dashboard</a>
            <a href="/users" class="hover:text-blue-400 transition">Usuarios</a>
        </div>

        <!-- Usuario y Logout -->
        <div class="flex items-center space-x-4">
            @auth
                <span class="text-sm">Hola, {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-1 rounded">
                        Cerrar sesión
                    </button>
                </form>
            @endauth
            @guest
                <a href="/login" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-1 rounded">
                    Iniciar sesión
                </a>
            @endguest
        </div>
    </div>
</nav>
