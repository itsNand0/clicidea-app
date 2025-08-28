<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle incidencia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        .font-handwriting {
            font-family: 'Patrick Hand', cursive;
        }

        .bg-paper {
            background-image: url('/images/papel-unaraya.png');
        }
    </style>
</head>

<body class="min-h-screen bg-gray-100 flex flex-col">
    <x-BarMenu />
    <main class="flex-1 flex items-center justify-center p-4">
        <div class="w-full max-w-lg md:max-w-2xl lg:max-w-3xl mx-auto">
            <div
                class="bg-paper bg-cover border border-gray-300 shadow-xl rounded-xl p-4 sm:p-6 md:p-8 font-handwriting text-gray-800 w-full h-auto flex flex-col justify-center items-center">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-3 w-full">
                    <h1 class="text-sm text-gray-600 text-center sm:text-right w-full sm:w-1/3 mb-2 sm:mb-0">
                        Creado por: {{ $datas->usuarioincidencia }}<br>
                        Fecha: {{ $datas->fechaincidencia }}
                    </h1>
                </div>
                <h2 class="text-3xl text-center mb-4 w-full">Detalles de la Incidencia</h2>
                <ul class="list-disc list-inside text-left space-y-2 mb-6 w-full">
                    <li><strong>Responsable:</strong>
                        @if ($datas->usuario && $datas->usuario->cargo)
                            {{ $datas->usuario->cargo->nombre_cargo }} - {{ $datas->usuario->name }}
                        @elseif ($datas->usuario && $datas->usuario->area)
                            {{ $datas->usuario->area->area_name }} - {{ $datas->usuario->name }}
                        @else
                            <span class="text-gray-500 italic">Sin asignar</span>
                        @endif
                    </li>
                    <li><strong>Contrato:</strong> {{ $datas->cliente->nombre }}</li>
                    <li><strong>Asunto:</strong> {{ $datas->asuntoincidencia }}</li>
                    <li><strong>Descripción:</strong> {{ $datas->descriincidencia }}</li>
                    <li><strong>Contacto:</strong> {{ $datas->contactoincidencia }}</li>
                </ul>
                @php
                    $archivos = json_decode($datas->adjuntoincidencia, true);
                @endphp
                <div class="w-full flex flex-col items-center">
                    @if (!empty($archivos) && is_array($archivos))
                        @foreach ($archivos as $archivo)
                            @if (!empty($archivo))
                                <a
                                    class="inline-block font-handwriting text-lg text-gray-800 px-6 py-2 border-2 border-black rounded-md hover:bg-yellow-100 transition duration-300 mb-2 w-full text-center break-words"
                                    href="{{ asset($archivo) }}" target="_blank">
                                    <i class="fa-solid fa-paperclip mr-2"></i>Ver archivo: {{ basename($archivo) }}
                                </a>
                            @endif
                        @endforeach
                    @else
                        <p class="text-gray-500 italic">No hay archivos adjuntos para esta incidencia.</p>
                    @endif
                </div>
            </div>
        </div>
    </main>
</body>

</html>
