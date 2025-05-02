<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nota estilo papel</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

<body>
    <x-BarMenu />
    <div class="flex flex-col lg:flex-row w-full gap-4">
        
        <div
            class="bg-paper bg-cover border border-gray-300 shadow-xl rounded-xl p-4 sm:p-6 md:p-8 font-handwriting text-gray-800 w-full sm:w-[450px] md:w-[500px] lg:w-[450px] h-auto sm:h-[500px] md:h-[500px] lg:h-[590px]">

            <div class="flex justify-between items-center mb-3">
                <h1 class="text-sm text-right text-gray-600 w-1/3">
                    Creado por: {{ $datas->usuarioIncidencia }}<br>
                    Fecha: {{ $datas->fechaIncidencia }}
                </h1>
            </div>

            <h2 class="text-3xl text-center mb-4">Detalles de la Incidencia</h2>

            <ul class="list-disc list-inside text-left space-y-2 mb-6">
                <li><strong>Técnico:</strong> {{ $datas->tecnico->nombreTecnico }}</li>
                <li><strong>Contrato:</strong> {{ $datas->cliente->nombre }}</li>
                <li><strong>Asunto:</strong> {{ $datas->asuntoIncidencia }}</li>
                <li><strong>Descripción:</strong> {{ $datas->descriIncidencia }}</li>
                <li><strong>Contacto:</strong> {{ $datas->contactoIncidencia }}</li>
            </ul>

            @php
                $archivos = json_decode($datas->adjuntoIncidencia, true);
            @endphp

            @foreach ($archivos as $archivo)
                <a class="inline-block font-handwriting text-lg text-gray-800 px-6 py-2 border-2 border-black rounded-md hover:bg-yellow-100 transition duration-300 relative before:content-[''] before:absolute before:inset-0 before:-translate-x-1 before:-translate-y-1 before:border-2 before:border-black before:rounded-md before:z-[-1]" href="{{ asset('storage/adjuntos/' . $archivo) }}" target="_blank">Ver archivo:
                    {{ $archivo }}</a><br><br/>
            @endforeach

        </div>
        
        <div class="flex-1 bg-white border border-gray-300 rounded-xl shadow-xl p-6">
            <h2 class="text-xl font-semibold mb-4">Otra Información</h2>
            <p>Aquí puedes colocar otros campos, adjuntos, comentarios, etc.</p>
        </div>
    </div>

</body>

</html>
