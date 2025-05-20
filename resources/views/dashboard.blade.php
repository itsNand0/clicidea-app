<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <title>Home</title>
    @livewireStyles
</head>

<body>
    <x-Barmenu />

    <div class="flex min-h-screen"> <!-- Asegura que Sidebar y contenido estén en línea -->

        <x-Sidebar /> <!-- Sidebar fijo al lado izquierdo -->

        <div class="flex-1 p-8"> <!-- Contenido principal ocupa el resto del espacio -->

            <!-- Contenido principal -->
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold">INCIDENCIAS</h1>
            </div>
            
            @livewire('incidencias')

        </div>

    </div>
 @livewireScripts
</body>

</html>
