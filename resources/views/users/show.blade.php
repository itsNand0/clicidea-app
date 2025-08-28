<!-- resources/views/users/show.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Usuario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body>
    <x-barmenu />
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-user text-blue-600"></i> Detalles del Usuario
            </h1>
            <div class="mb-4">
                {{-- <pre>{{ var_export($user, true) }}</pre> --}}
                <p class="text-lg text-gray-700 mb-2"><span class="font-semibold">Nombre:</span>
                    {{ $user->name }}</p>
                <p class="text-lg text-gray-700 mb-2"><span class="font-semibold">Email:</span>
                    {{ $user->email }}</p>
                @if($user->cargo && $user->cargo->nombre_cargo)
                    <p class="text-lg text-gray-700 mb-2"><span class="font-semibold">Cargo:</span>
                        {{ $user->cargo->nombre_cargo }}</p>
                @elseif($user->area && $user->area->area_name)
                    <p class="text-lg text-gray-700 mb-2"><span class="font-semibold">Encargado de:</span>
                        {{ $user->area->area_name }}</p>
                @else
                    <p class="text-lg text-gray-700 mb-2 text-red-500"><span class="font-semibold">Cargo/Área:</span>
                        No registrado</p>
                @endif
                
            </div>
            <a href="{{ route('users.index') }}"
                class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors duration-200">
                <i class="fa-solid fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </div>
</body>

</html>
