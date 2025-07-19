<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle incidencia</title>
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
                    Creado por: {{ $datas->usuarioincidencia }}<br>
                    Fecha: {{ $datas->fechaincidencia }}
                </h1>
            </div>

            <h2 class="text-3xl text-center mb-4">Detalles de la Incidencia</h2>

            <ul class="list-disc list-inside text-left space-y-2 mb-6">
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

            @if (!empty($archivos) && is_array($archivos))
                @foreach ($archivos as $archivo)
                    @if (!empty($archivo))
                        <a class="inline-block font-handwriting text-lg text-gray-800 px-6 py-2 border-2 border-black rounded-md hover:bg-yellow-100 transition duration-300 relative before:content-[''] before:absolute before:inset-0 before:-translate-x-1 before:-translate-y-1 before:border-2 before:border-black before:rounded-md before:z-[-1]"
                            href="{{ asset(  $archivo )}}" target="_blank">
                            Ver archivo: {{  $archivo }}
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


                @if (session('success'))
                    <div id="success-alert" class="alert alert-success">
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
                @if (session('error'))
                    <div class="text-red-500 text-sm mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                <ul class="flex justify-between items-center space-x-2">
                    <li id="opcionasignar"
                        class="nav-item border border-gray-500 px-4 py-1 rounded-md hover:bg-gray-700">
                        <a id="opcionasignar" class="nav-link text-m " href="#"><i
                                class="fa-regular fa-user"></i>&nbsp;&nbsp;Asignar</a>
                    </li>

                    <!-- Modal (oculto por defecto) -->
                    @can('incidencias.asignar')
                    <div id="modalasignar"
                        class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
                        <div class="modal-content bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                            <h2 class="text-xl font-semibold mb-4 text-gray-700">Selecciona una
                                opcion</h2>
                            <form action="{{ route('incidencias.asignar', $datas->idincidencia) }}" method="POST">
                                @csrf
                                @method('PUT')
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
                                        <label for="opcion2" class="ml-2 block text-sm text-gray-700">Cargo</label>
                                    </div>
                                </div>

                                <!-- Lista de tecnicos para asignar -->

                                <select id="tecnicos-lista" name="user_cargo_id"
                                    class="w-full border border-gray-300 rounded-md p-2 mb-4 text-gray-700 hidden">
                                    <option value="">Seleccione un cargo</option>
                                    @foreach ($datacargos as $datacargo)
                                        @foreach ($datacargo->users as $user)
                                            <option value="{{ $user->id }}" data-cargo="{{ $user->cargo_id }}">
                                                {{ $datacargo->nombre_cargo }} - {{ $user->name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>



                                <!-- Lista de areas para asignar -->

                                <select id="areas-lista" name="user_area_id"
                                    class="w-full border border-gray-300 rounded-md p-2 mb-4 text-gray-700 hidden">
                                    <option value="">Seleccione un área</option>

                                    @foreach ($dataareas as $dataarea)
                                        @foreach ($dataarea->users as $user)
                                            <option value="{{ $user->id }}" data-area="{{ $user->area_id }}">
                                                {{ $dataarea->area_name }} - {{ $user->name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>

                                <script>
                                    document.querySelector('form').addEventListener('submit', function(e) {
                                        const tecnico = document.getElementById('tecnicos-lista');
                                        const area = document.getElementById('areas-lista');

                                        // Si técnico está vacío, borra su atributo "name" para que no se envíe
                                        if (!tecnico.value) {
                                            tecnico.name = '';
                                        }

                                        // Si área está vacía, borra su atributo "name" para que no se envíe
                                        if (!area.value) {
                                            area.name = '';
                                        }
                                    });
                                </script>

                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="closeModal()" id="cancelarBtnAsignar"
                                        class="btncancelar px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200">Cancelar</button>
                                    <button type="submit" id="asignarBtn"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        disabled>Asignar</button>
                                    <script>
                                        const asignarBtn = document.getElementById('asignarBtn');
                                        const cancelarBtnAsignar = document.getElementById('cancelarBtnAsignar');
                                        const asignarForm = asignarBtn.closest('form');
                                        asignarForm.addEventListener('submit', function() {
                                            asignarBtn.disabled = true;
                                            asignarBtn.classList.add('opacity-60', 'pointer-events-none');
                                            cancelarBtnAsignar.disabled = true;
                                            cancelarBtnAsignar.classList.add('opacity-60', 'pointer-events-none');
                                        });
                                        asignarForm.addEventListener('input', function() {
                                            asignarBtn.disabled = false;
                                            asignarBtn.classList.remove('opacity-60', 'pointer-events-none');
                                        });
                                    </script>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endcan

                    <li id='opcioneditar'
                        class="nav-item border border-gray-500 px-3 py-1 rounded-md hover:bg-gray-700">
                        <a class="nav-link text-m" href="#"><i class="fa-solid fa-pen-to-square"></i>&nbsp;&nbsp;
                            Editar</a>
                    </li>

                    @can('incidencias.editar')
                    <div id="modaleditar"
                        class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
                        <div class="modal-content bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                            <h2 class="text-xl font-semibold mb-4 text-gray-700">Modificar Detalle</h2>
                            <form action="{{ route('incidencias.update', $datas->idincidencia) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <label class="ml-2 block text-sm text-black">Asunto: </label>
                                <input type="text" name="asunto"
                                    class="w-full border px-4 py-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 text-gray-700"
                                    value="{{ $datas->asuntoincidencia }}">
                                <label class="ml-2 block text-sm text-black">Descipcion: </label>
                                <textarea name="descripcion"
                                    class="w-full border px-4 py-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 text-gray-700">{{ old('descripcion', $datas->descriincidencia) }}</textarea>
                                <label class="ml-2 block text-sm text-black">Contacto: </label>
                                <textarea name="contacto"
                                    class="w-full border px-4 py-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 text-gray-700">{{ old('descripcion', $datas->contactoincidencia) }}</textarea>

                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="closeModal()" id="cancelarBtnEditar"
                                        class="btncancelar px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200">Cancelar</button>
                                    <button type="submit" id="guardarBtn"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        disabled>Guardar</button>
                                    <script>
                                        const guardarBtn = document.getElementById('guardarBtn');
                                        const cancelarBtnEditar = document.getElementById('cancelarBtnEditar');
                                        const editarForm = guardarBtn.closest('form');
                                        editarForm.addEventListener('submit', function() {
                                            guardarBtn.disabled = true;
                                            guardarBtn.classList.add('opacity-60', 'pointer-events-none');
                                            cancelarBtnEditar.disabled = true;
                                            cancelarBtnEditar.classList.add('opacity-60', 'pointer-events-none');
                                        });
                                        editarForm.addEventListener('input', function() {
                                            guardarBtn.disabled = false;
                                            guardarBtn.classList.remove('opacity-60', 'pointer-events-none');
                                        });
                                    </script>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endcan

                    <li id="opcionadjuntar"
                        class="nav-item border border-gray-500 px-3 py-1 rounded-md hover:bg-gray-700">
                        <a class="nav-link text-m" href="#"><i class="fa-solid fa-paperclip"></i>&nbsp;&nbsp;
                            Adjuntar</a>
                    </li>

                    <div id="modaladjuntar"
                        class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
                        <div class="modal-content bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                            <h2 class="text-xl font-semibold mb-4 text-gray-700">Nuevos Archivos</h2>
                            <form action="{{ route('incidencias.updateFile', $datas->idincidencia) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <label class="ml-2 block text-sm text-black">Seleccionar: </label>
                                <input type="file" name="adjunto[]"
                                    class="w-full border px-4 py-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 text-gray-700"
                                    multiple>
                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="closeModal()" id="cancelarBtnAdjuntar"
                                        class="btncancelar px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200">Cancelar</button>
                                    <button type="submit" id="subirBtn"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        disabled>Subir</button>
                                    <script>
                                        const subirBtn = document.getElementById('subirBtn');
                                        const cancelarBtnAdjuntar = document.getElementById('cancelarBtnAdjuntar');
                                        const subirForm = subirBtn.closest('form');
                                        subirForm.addEventListener('submit', function() {
                                            subirBtn.disabled = true;
                                            subirBtn.classList.add('opacity-60', 'pointer-events-none');
                                            cancelarBtnAdjuntar.disabled = true;
                                            cancelarBtnAdjuntar.classList.add('opacity-60', 'pointer-events-none');
                                        });
                                        subirForm.addEventListener('input', function() {
                                            subirBtn.disabled = false;
                                            subirBtn.classList.remove('opacity-60', 'pointer-events-none');
                                        });
                                    </script>
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

                    <li id="opcioncomentar"
                        class="nav-item border border-gray-500 px-3 py-1 rounded-md hover:bg-gray-700">
                        <a class="nav-link text-m" href="#"><i class="fa-solid fa-comment"></i>&nbsp;&nbsp;
                            Comentario</a>
                    </li>

                    <div id="modalcomentario"
                        class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
                        <div class="modal-content bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                            <form action="{{ route('comentarios.store', $datas->idincidencia) }}" method="POST"
                                class="mb-4">
                                @csrf
                                <textarea name="contenido" class="w-full p-2 border rounded text-black" rows="3"
                                    placeholder="Escribe un comentario..."></textarea>

                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="closeModal()" id="cancelarBtnComentario"
                                        class="btncancelar px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200">Cancelar</button>
                                    <button type="submit" id="comentarBtn"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        disabled>Comentar</button>
                                    <script>
                                        const comentarBtn = document.getElementById('comentarBtn');
                                        const cancelarBtnComentario = document.getElementById('cancelarBtnComentario');
                                        const comentarForm = comentarBtn.closest('form');
                                        comentarForm.addEventListener('submit', function() {
                                            comentarBtn.disabled = true;
                                            comentarBtn.classList.add('opacity-60', 'pointer-events-none');
                                            cancelarBtnComentario.disabled = true;
                                            cancelarBtnComentario.classList.add('opacity-60', 'pointer-events-none');
                                        });
                                        comentarForm.addEventListener('input', function() {
                                            comentarBtn.disabled = false;
                                            comentarBtn.classList.remove('opacity-60', 'pointer-events-none');
                                        });
                                    </script>
                                </div>
                            </form>
                        </div>
                    </div>

                    <li id="opcionestado"
                        class="nav-item border border-gray-500 px-3 py-1 rounded-md hover:bg-gray-700">
                        <a class="nav-link text-m" href="#"><i
                                class="fa-solid fa-bars"></i>&nbsp;&nbsp;Estado</a>
                    </li>

                    <div id="modalestado"
                        class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
                        <div class="modal-content bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                            <h2 class="text-xl font-semibold mb-4 text-gray-700">Selecciona Estado</h2>
                            <form action="{{ route('incidencias.cambiarEstado', $datas->idincidencia) }}"
                                method="POST">
                                @csrf
                                <select name="estado_id"
                                    class="w-full border border-gray-300 rounded-md p-2 mb-4 text-gray-700">
                                    <option value="">Selecciona un estado</option>
                                    @foreach ($estadosincidencias as $estadoincidencia)
                                        @if ($estadoincidencia->descriestadoincidencia !== 'Cerrado')
                                            <option value="{{ $estadoincidencia->idestadoincidencia }}"
                                                for="estadosincidencias{{ $estadoincidencia->idestadoincidencia }}"
                                                class="ml-2 text-sm text-gray-700">
                                                {{ $estadoincidencia->descriestadoincidencia }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>

                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="closeModal()" id="cancelarBtnEstado"
                                        class="btncancelar px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200">Cancelar</button>
                                    <button type="submit" id="cambiarBtn"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        disabled>Cambiar</button>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const cambiarBtn = document.getElementById('cambiarBtn');
                                            if (cambiarBtn) {
                                                const cambiarForm = cambiarBtn.closest('form');
                                                if (cambiarForm) {
                                                    cambiarForm.addEventListener('submit', function() {
                                                        cambiarBtn.disabled = true;
                                                        cambiarBtn.classList.add('opacity-60', 'pointer-events-none');
                                                        cancelarBtnEstado.disabled = true;
                                                        cancelarBtnEstado.classList.add('opacity-60', 'pointer-events-none');
                                                    });
                                                    cambiarForm.addEventListener('input', function() {
                                                        cambiarBtn.disabled = false;
                                                        cambiarBtn.classList.remove('opacity-60', 'pointer-events-none');
                                                    });
                                                }
                                            }
                                        });
                                    </script>
                                </div>
                            </form>
                        </div>
                    </div>

                    <li id="opcionresolver"
                        class="nav-item border border-gray-500 px-3 py-1 rounded-md hover:bg-gray-700">
                        <a class="nav-link text-m" href="#"><i
                                class="fa-solid fa-hand"></i>&nbsp;&nbsp;Resolver</a>
                    </li>

                    <div id="modalresolver"
                        class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
                        <div class="modal-content bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                            <h2 class="text-xl font-semibold mb-4 text-gray-700">Nuevos Archivos</h2>
                            <form id="resolverFormulario"
                                action="{{ route('incidencias.resolverIncidencia', $datas->idincidencia) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <label class="ml-2 block text-sm text-black">Seleccionar: </label>
                                <input type="file" name="adjunto[]"
                                    class="w-full border px-4 py-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 text-gray-700"
                                    multiple>
                                <textarea name="contenido" class="w-full p-2 border rounded text-black" rows="3"
                                    placeholder="Escribe un comentario..."></textarea>
                                <input type="hidden" name="estado_id" value="3">
                                <input type="hidden" name="fecharesolucionincidencia" value="{{ now() }}">
                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="closeModal()" id="cancelarBtnResolver"
                                        class="btncancelar px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200">Cancelar</button>
                                    <button type="submit" id="resolverBtn"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        disabled>Resolver</button>
                                    <script>
                                        const resolverBtn = document.getElementById('resolverBtn');
                                        const resolverForm = resolverBtn.closest('form');
                                        const cancelarBtnResolver = document.getElementById('cancelarBtnResolver');
                                        const resolverFileInput = resolverForm.querySelector('input[type="file"][name="adjunto[]"]');

                                        // Habilita el botón solo si hay al menos 2 archivos seleccionados
                                        resolverFileInput.addEventListener('change', function() {
                                            if (resolverFileInput.files.length >= 2) {
                                                resolverBtn.disabled = false;
                                                resolverBtn.classList.remove('opacity-60', 'pointer-events-none');
                                                cancelarBtnResolver.disabled = false;
                                                cancelarBtnResolver.classList.remove('opacity-60', 'pointer-events-none');
                                            } else {
                                                resolverBtn.disabled = true;
                                                resolverBtn.classList.add('opacity-60', 'pointer-events-none');
                                            }
                                        });

                                        resolverForm.addEventListener('submit', function() {
                                            resolverBtn.disabled = true;
                                            resolverBtn.classList.add('opacity-60', 'pointer-events-none');
                                        });
                                    </script>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Una vez que esta resuelta la incidencia, se deshabilita todo en la vista -->
                    @if ($datas->estadoincidencia->descriestadoincidencia === 'Cerrado')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Deshabilita todos los elementos interactivos excepto el select y botón de estado
                                const elements = document.querySelectorAll('button, input, textarea, [tabindex], select');
                                elements.forEach(el => {
                                    // Permite cambiar el estado aunque esté Cerrado
                                    if (el.name === 'estado_id' || el.id === 'cambiarBtn' || el.id === 'cancelarBtnEstado') {
                                        el.removeAttribute('disabled');
                                        el.classList.remove('pointer-events-none', 'opacity-60');
                                    } else if (!el.classList.contains('close_Sesion') && !el.classList.contains('btncancelar')) {
                                        el.setAttribute('disabled', 'disabled');
                                        el.classList.add('pointer-events-none', 'opacity-60');
                                    }
                                });
                        </script>
                    @endif
                </ul>
            </nav>

            <div class="flex gap-4 mt-4">

                <div class="overflow-hidden w-[500px] border p-4 bg-white rounded-md shadow">
                    <div class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md shadow mb-4">
                        <i>INCIDENCIA - #{{ $datas->idincidencia }}</i>
                    </div>

                    <div
                        class="px-6 py-1 rounded-full text-base text-center
                            @switch($datas->estadoincidencia->descriestadoincidencia)
                                @case('En proceso') bg-green-200 text-gray-800 @break
                                @case('Pendiente') bg-yellow-200 text-gray-800 @break
                                @case('Cerrado') bg-red-200 text-gray-800 @break
                                @default bg-gray-200 text-gray-800
                            @endswitch
                        ">
                        {{ $datas->estadoincidencia->descriestadoincidencia }}
                    </div>

                    <p class="text-lg font-semibold text-gray-800 border-b pb-1 mb-2 mt-2">
                        Comentarios
                    </p>
                    <div id="comentarios-container" class="max-h-64 overflow-y-auto space-y-2 mt-4 pr-1">
                        @if ($comentarios->isEmpty())
                            <p class="text-gray-500 italic">No hay registros de Comentarios.</p>
                        @else
                            @foreach ($comentarios as $comentario)
                                <div
                                    class="border border-gray-200 rounded-xl p-3 bg-white shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="mt-3 text-sm text-gray-500">
                                        Publicado por
                                        <span
                                            class="font-medium text-gray-700">{{ $comentario->usuario->name ?? 'Anónimo' }}</span>
                                        el {{ $comentario->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="flex items-start justify-between">
                                        <p class="text-gray-700 text-base leading-relaxed">
                                            {{ $comentario->contenido }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="max-h-96 overflow-y-auto w-80 border p-4 bg-white rounded-md shadow">
                    <p class="text-lg font-semibold text-gray-800 border-b pb-1 mb-2 mt-2">
                        Registros
                    </p>
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
                                    {{-- Caso especial para editar (cuando son strings planos) --}}
                                    @php

                                        $fuentes = [
                                            'directo' => [
                                                'antes' => $cambios['antes'] ?? [],
                                                'despues' => $cambios['despues'] ?? [],
                                            ],

                                            'estado' => [
                                                'antes' => $cambios['estado']['antes'] ?? null,
                                                'despues' => $cambios['estado']['despues'] ?? null,
                                            ],
                                            'cargo' => [
                                                'antes' => $cambios['cargo']['antes'] ?? null,
                                                'despues' => $cambios['cargo']['despues'] ?? null,
                                            ],
                                            'area' => [
                                                'antes' => $cambios['area']['antes'] ?? null,
                                                'despues' => $cambios['area']['despues'] ?? null,
                                            ],
                                        ];
                                    @endphp
                                    {{-- Caso especial para editar (cuando son strings planos) --}}
                                    @if (isset($cambios['antes']) && is_array($cambios['antes']))
                                        @foreach ($cambios['antes'] as $campo => $valorAntes)
                                            @if (isset($cambios['despues'][$campo]))
                                                <li>
                                                    <strong>{{ $campo }}:</strong>
                                                    <span class="text-red-600">{{ $valorAntes }}</span> →
                                                    <span
                                                        class="text-green-600">{{ $cambios['despues'][$campo] }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif

                                    @if (isset($cambios['estado']) && (isset($cambios['estado']['antes']) || isset($cambios['estado']['despues'])))
                                        @php
                                            $antes = $cambios['estado']['antes'] ?? 'Sin estado';
                                            $despues = $cambios['estado']['despues'] ?? 'Sin estado';
                                        @endphp
                                        @if ($antes !== $despues)
                                            <li>
                                                <strong>Estado:</strong>
                                                <span class="text-red-600">{{ $antes ?? 'Sin estado' }}</span> →
                                                <span class="text-green-600">{{ $despues ?? 'Sin estado' }}</span>
                                            </li>
                                        @endif
                                    @endif

                                    @if (array_key_exists('cargo', $cambios))
                                        @php
                                            $antes = $cambios['cargo']['antes'] ?? 'Sin asignar';
                                            $despues = $cambios['cargo']['despues'] ?? 'Sin asignar';
                                        @endphp
                                        @if ($antes !== $despues)
                                            <li>
                                                <strong>Cargo:</strong>
                                                <span class="text-red-600">{{ $antes }}</span> →
                                                <span class="text-green-600">{{ $despues }}</span>
                                            </li>
                                        @endif
                                    @endif

                                    @if (array_key_exists('area', $cambios))
                                        @php
                                            $antes = $cambios['area']['antes'] ?? 'Sin asignar';
                                            $despues = $cambios['area']['despues'] ?? 'Sin asignar';
                                        @endphp
                                        @if ($antes !== $despues)
                                            <li>
                                                <strong>Área:</strong>
                                                <span class="text-red-600">{{ $antes }}</span> →
                                                <span class="text-green-600">{{ $despues }}</span>
                                            </li>
                                        @endif
                                    @endif
                                    
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
            event.stopPropagation();
            const modal = document.getElementById('modalasignar');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

        });

        document.getElementById('opcioneditar').addEventListener('click', function(event) {
            event.stopPropagation();
            const modal = document.getElementById('modaleditar');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

        });

        document.getElementById('opcionadjuntar').addEventListener('click', function(event) {
            event.stopPropagation();
            const modal = document.getElementById('modaladjuntar');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

        });

        document.getElementById('opcioncomentar').addEventListener('click', function(event) {
            event.stopPropagation();
            const modal = document.getElementById('modalcomentario');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

        });

        document.getElementById('opcionestado').addEventListener('click', function(event) {
            event.stopPropagation();
            const modal = document.getElementById('modalestado');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

        });

        document.getElementById('opcionresolver').addEventListener('click', function(event) {
            event.stopPropagation();
            const modal = document.getElementById('modalresolver');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

        });


        function closeModal() {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            });
        }


        const opcion1 = document.getElementById('opcion1');
        const opcion2 = document.getElementById('opcion2');
        const tecnicosLista = document.getElementById('tecnicos-lista');
        const areasListas = document.getElementById('areas-lista');

        function actualizarVisibilidad() {
            if (opcion2.checked) {
                document.getElementById('tecnicos-lista').style.display = 'block';
                document.getElementById('areas-lista').style.display = 'none';
            } else if (opcion1.checked) {
                document.getElementById('tecnicos-lista').style.display = 'none';
                document.getElementById('areas-lista').style.display = 'block';
            } else {
                document.getElementById('tecnicos-lista').style.display = 'none';
                document.getElementById('areas-lista').style.display = 'none';
            }
        }

        opcion1.addEventListener('change', actualizarVisibilidad);
        opcion2.addEventListener('change', actualizarVisibilidad);

       
        actualizarVisibilidad();
    </script>
</body>

</html>
