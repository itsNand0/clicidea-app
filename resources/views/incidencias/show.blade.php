<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle incidencia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <li><strong>Responsable:</strong> {{ $datas->tecnico->nombreTecnico }}</li>
                <li><strong>Contrato:</strong> {{ $datas->cliente->nombre }}</li>
                <li><strong>Asunto:</strong> {{ $datas->asuntoIncidencia }}</li>
                <li><strong>Descripción:</strong> {{ $datas->descriIncidencia }}</li>
                <li><strong>Contacto:</strong> {{ $datas->contactoIncidencia }}</li>
            </ul>

            @php
                $archivos = json_decode($datas->adjuntoIncidencia, true);
            @endphp

            @if (!empty($archivos) && is_array($archivos))
                @foreach ($archivos as $archivo)
                    @if (!empty($archivo))
                        <a class="inline-block font-handwriting text-lg text-gray-800 px-6 py-2 border-2 border-black rounded-md hover:bg-yellow-100 transition duration-300 relative before:content-[''] before:absolute before:inset-0 before:-translate-x-1 before:-translate-y-1 before:border-2 before:border-black before:rounded-md before:z-[-1]"
                            href="{{ asset('storage/adjuntos/' . $archivo) }}" target="_blank">
                            Ver archivo: {{ $archivo }}
                        </a>
                        <br><br />
                    @endif
                @endforeach
            @else
                <p class="text-gray-500 italic">No hay archivos adjuntos para esta incidencia.</p>
            @endif
        </div>

        <!-- Espacio para el menú de acciones -->

        <div class="flex-1 flex flex-col bg-gray-200 border border-gray-300 rounded-xl shadow-sm p-2">
            <nav class="bg-gray-800 text-white px-4 py-2 shadow-sm rounded-md">
                <ul class="flex justify-between items-center space-x-2">

                    <li class="nav-item border border-gray-500 px-4 py-1 rounded-md hover:bg-gray-700">
                        <a id="opcionasignar" class="nav-link text-m " href="#"><i
                                class="fa-regular fa-user"></i>&nbsp;&nbsp;Asignar</a>
                    </li>

                    <!-- Modal (oculto por defecto) -->
                    <div id="modalasignar"
                        class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                            <h2 class="text-xl font-semibold mb-4 text-gray-700">Selecciona una
                                opcion</h2>
                            <form action="{{ route('incidencias.asignar', $datas->idIncidencia) }}" method="POST">
                                @csrf
                                <label class="block mb-2 text-sm font-medium text-gray-700">Asignar a:</label>

                                <div class="flex items-center space-x-4 mb-2">
                                    <div class="flex items-center">
                                        <input id="opcion1" name="opcion" type="radio" value="1"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="opcion1" class="ml-2 block text-sm text-gray-700">Area</label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="opcion2" name="opcion" type="radio" value="2"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="opcion2" class="ml-2 block text-sm text-gray-700">Usuario</label>
                                    </div>
                                </div>

                                <!-- Lista de tecnicos para asiganar -->

                                <select id="tecnicos-lista" name="tecnico_id"
                                    class="w-full border border-gray-300 rounded-md p-2 mb-4 text-gray-700 hidden">
                                    @foreach ($datatecnicos as $datatecnico)
                                        <option value="{{ $datatecnico->idTecnico }}"
                                            for="tecnico{{ $datatecnico->idTecnico }}"
                                            class="ml-2 text-sm text-gray-700">
                                            {{ $datatecnico->nombreTecnico }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="closeModal()"
                                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Asignar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <li id="opcioneditar"
                        class="nav-item border border-gray-500 px-3 py-1 rounded-md hover:bg-gray-700">
                        <a class="nav-link text-m" href="#"><i class="fa-solid fa-pen-to-square"></i>&nbsp;&nbsp;
                            Editar</a>
                    </li>

                    <div id="modaleditar"
                        class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                            <h2 class="text-xl font-semibold mb-4 text-gray-700">Modificar Detalle</h2>
                            <form action="{{ route('incidencias.update', $datas->idIncidencia) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <label class="ml-2 block text-sm text-black">Asunto: </label>
                                <input type="text" name="asunto"
                                    class="w-full border px-4 py-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 text-gray-700"
                                    value="{{ $datas->asuntoIncidencia }}">
                                <label class="ml-2 block text-sm text-black">Descipcion: </label>
                                <textarea name="descripcion"
                                    class="w-full border px-4 py-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 text-gray-700">{{ old('descripcion', $datas->descriIncidencia) }}</textarea>
                                <label class="ml-2 block text-sm text-black">Contacto: </label>
                                <textarea name="contacto"
                                    class="w-full border px-4 py-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 text-gray-700">{{ old('descripcion', $datas->contactoIncidencia) }}</textarea>

                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="closeModal()"
                                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <li id="opcionadjuntar"
                        class="nav-item border border-gray-500 px-3 py-1 rounded-md hover:bg-gray-700">
                        <a class="nav-link text-m" href="#"><i class="fa-solid fa-paperclip"></i>&nbsp;&nbsp;
                            Adjuntar</a>
                    </li>

                    <div id="modaladjuntar"
                        class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                            <h2 class="text-xl font-semibold mb-4 text-gray-700">Nuevos Archivos</h2>
                            <form action="{{ route('incidencias.updateFile', $datas->idIncidencia) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <label class="ml-2 block text-sm text-black">Seleccionar: </label>
                                <input type="file" name="adjunto[]"
                                    class="w-full border px-4 py-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 text-gray-700"
                                    multiple>
                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="closeModal()"
                                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Subir</button>
                                </div>
                                @if ($errors->any())
                                    <div>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>

                    <li class="nav-item border border-gray-500 px-3 py-1 rounded-md hover:bg-gray-700">
                        <a class="nav-link text-m" href="#"><i class="fa-solid fa-comment"></i>&nbsp;&nbsp;
                            Comentario</a>
                    </li>
                    <li class="nav-item border border-gray-500 px-3 py-1 rounded-md hover:bg-gray-700">
                        <a class="nav-link text-m" href="#"><i
                                class="fa-solid fa-bars"></i>&nbsp;&nbsp;Estado</a>
                    </li>
                    <li class="nav-item border border-gray-500 px-3 py-1 rounded-md hover:bg-green-700">
                        <a class="nav-link text-sm text-white" href="#">Resolver</a>
                    </li>
                </ul>
            </nav>

            <div class="flex gap-4 mt-4">

                <div class="max-h-96 overflow-y-auto w-[500px] border p-4 bg-white rounded-md shadow">
                    <div class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md shadow mb-4">
                        <i>INCIDENCIA - #{{ $datas->idIncidencia }}</i>
                    </div>
                    <div
                        class="
                    px-6 py-1 rounded-full text-base text-center
                        @switch($datas->estadoincidencia->descriEstadoIncidencia)
                        
                            @case('En proceso') bg-green-200 text-gray-800 @break
                            @case('Pendiente') bg-yellow-200 text-gray-800 @break
                            @case('cerrado') bg-red-200 text-gray-800 @break
                            @default bg-gray-200 text-gray-800
                        @endswitch
                    ">
                        {{ $datas->estadoincidencia->descriEstadoIncidencia }}
                    </div>
                </div>


                <div class="max-h-96 overflow-y-auto w-80 border p-4 bg-white rounded-md shadow">
                    @if ($auditorias->isEmpty())
                        <p class="text-gray-500 italic">No hay registros de auditoría.</p>
                    @else
                        @foreach ($auditorias as $auditoria)
                            <p class="font-semibold text-gray-700">Acción: {{ ucfirst($auditoria->accion) }}</p>
                            @php
                                $cambios = json_decode($auditoria->cambios, true);
                            @endphp
                            <div class="mb-4 border-b pb-2">
                                <p class="text-sm text-gray-700"><strong>Usuario:</strong>
                                    {{ $auditoria->usuario->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600"><strong>Fecha:</strong>
                                    {{ $auditoria->created_at->format('d/m/Y H:i') }}</p>
                                <ul class="ml-4 mt-1 text-sm">
                                    @foreach ($cambios['antes'] as $campo => $valorAntes)
                                        @if (isset($cambios['despues'][$campo]))
                                            <li>
                                                <strong>{{ $campo }}:</strong>
                                                <span class="line-through text-red-600">{{ $valorAntes }}</span> →
                                                <span class="text-green-600">{{ $cambios['despues'][$campo] }}</span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>


        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Abrir modal oculto -->
    <script>
        document.getElementById('opcionasignar').addEventListener('click', function(event) {
            const modal = document.getElementById('modalasignar');
            modal.classList.remove('hidden');
            modal.classList.add('flex'); // si no querés que redireccione

        });

        document.getElementById('opcioneditar').addEventListener('click', function(event) {
            const modal = document.getElementById('modaleditar');
            modal.classList.remove('hidden');
            modal.classList.add('flex'); // si no querés que redireccione

        });

        document.getElementById('opcionadjuntar').addEventListener('click', function(event) {
            const modal = document.getElementById('modaladjuntar');
            modal.classList.remove('hidden');
            modal.classList.add('flex'); // si no querés que redireccione

        });

        function closeModal() {
            const modal = document.getElementById('modalasignar');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            const modaleditar = document.getElementById('modaleditar');
            modaleditar.classList.remove('flex');
            modaleditar.classList.add('hidden');
            const modaladjuntar = document.getElementById('modaladjuntar');
            modaladjuntar.classList.remove('flex');
            modaladjuntar.classList.add('hidden'); // lo vuelve a ocultar
        }

        const opcion1 = document.getElementById('opcion1');
        const opcion2 = document.getElementById('opcion2');
        const tecnicosLista = document.getElementById('tecnicos-lista');

        function actualizarVisibilidad() {
            if (opcion2.checked) {
                tecnicosLista.style.display = 'block';
            } else {
                tecnicosLista.style.display = 'none';
            }
        }

        function actualizarVisibilidad() {
            if (opcion2.checked) {
                tecnicosLista.style.display = 'block';
            } else {
                tecnicosLista.style.display = 'none';
            }
        }

        opcion1.addEventListener('change', actualizarVisibilidad);
        opcion2.addEventListener('change', actualizarVisibilidad);

        // Ejecutar al cargar la página
        actualizarVisibilidad();
    </script>
</body>

</html>
