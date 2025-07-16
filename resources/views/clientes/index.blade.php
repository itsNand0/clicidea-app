<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <title>Clientes</title>
    @livewireStyles
</head>

<body class="bg-gray-100 min-h-screen font-sans">
    <x-barmenu />
    <main class="max-w-5xl mx-auto mt-8 p-6 bg-white rounded-xl shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-users text-blue-600"></i> Clientes
            </h1>
            @if (session('success'))
                <div id="success-alert"
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative text-sm">
                    {{ session('success') }}
                </div>
                <script>
                    setTimeout(function () {
                        var alert = document.getElementById('success-alert');
                        if (alert) {
                            alert.style.display = 'none';
                        }
                    }, 3000);
                </script>
            @endif
        </div>
        <div class="mt-4">
            @livewire('clientes')
        </div>
    </main>
    @livewireScripts
</body>

</html>