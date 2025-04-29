<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <title>Home</title>
</head>
<body>
    <x-Barmenu />
    <x-Sidebar />

    <div class="flex-1 p-8">
        <!-- Contenido principal -->
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">INCIDENCIAS</h1>
            <a href="{{route('incidencias.create')}}" class="bg-lime-600 text-white px-4 py-2 rounded-lg">
                <i class="fa-solid fa-plus"></i>
            </a>
        </div>
        @livewire('incidencias')
    </div>
</div>
</body>
</html>
