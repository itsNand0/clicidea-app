<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <title>Incidencias</title>
    @livewireStyles
</head>

<body class="bg-gray-100 min-h-screen font-sans">
    <x-Barmenu />
    <main class="max-w-7xl mx-auto mt-6 p-6 bg-white rounded-xl shadow-md">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2 ml-6">
                <i class="fa-solid fa-list-check text-blue-600"></i> Incidencias
            </h1>
        </div>
        <div class="mt-4">
            @livewire('incidencias')
        </div>
    </main>
    @livewireScripts
</body>

</html>
