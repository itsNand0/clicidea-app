<!-- resources/views/users/index.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <title>Usuarios</title>
    @livewireStyles
</head>

<body>
    <x-barmenu />
    <x-Sidebar />
    <div class="flex-1 p-8">
        <!-- Contenido principal -->
        <h1 class="text-2xl font-bold">Usuarios</h1>
        @if (session('success'))
            <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(function() {
                    var alert = document.getElementById('success-alert');
                    if (alert) {
                        alert.style.display = 'none';
                    }
                }, 3000); // 3 segundos
            </script>
        @endif
        @livewire('usuarios')
    </div>
    @livewireScripts
</body>

</html>
