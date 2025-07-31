<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        /* Mejoras responsive para móviles */
        @media (max-width: 768px) {
            .modal-content {
                margin: 1rem;
                max-height: 90vh;
                overflow-y: auto;
            }
            
            .nav-buttons {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .nav-buttons li {
                flex: 1;
                min-width: calc(50% - 0.25rem);
            }
            
            .nav-buttons a {
                display: block;
                text-align: center;
                padding: 0.5rem;
                font-size: 0.875rem;
            }
        }

        @media (max-width: 480px) {
            .nav-buttons li {
                min-width: 100%;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <x-BarMenu />
    
    <!-- Contenedor principal responsive -->
    <div class="container mx-auto px-2 sm:px-4 lg:px-6 py-4">
        
        <!-- Layout responsive: columna en móvil, fila en desktop -->
        <div class="flex flex-col xl:flex-row gap-4 lg:gap-6">

            <!-- Tarjeta de detalles de incidencia -->
            <div class="w-full xl:w-auto xl:flex-shrink-0">
                <div class="bg-paper bg-cover border border-gray-300 shadow-xl rounded-xl p-4 sm:p-6 md:p-8 font-handwriting text-gray-800 
                           w-full max-w-lg mx-auto xl:mx-0 xl:w-[450px] min-h-[400px] sm:min-h-[500px]">

                    <!-- Header con información del creador -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                        <div class="text-xs sm:text-sm text-gray-600 w-full sm:w-auto text-center sm:text-right">
                            <p><strong>Creado por:</strong> {{ $datas->usuarioincidencia }}</p>
                            <p><strong>Fecha:</strong> {{ $datas->fechaincidencia }}</p>
                        </div>
                    </div>

                    <!-- Título principal -->
                    <h2 class="text-2xl sm:text-3xl text-center mb-4 sm:mb-6">Detalles de la Incidencia</h2>

                    <!-- Lista de detalles -->
                    <ul class="list-disc list-inside text-left space-y-2 mb-4 sm:mb-6 text-sm sm:text-base">
                        <li><strong>Responsable:</strong>
                            @if ($datas->usuario && $datas->usuario->cargo)
                                <span class="break-words">{{ $datas->usuario->cargo->nombre_cargo }} - {{ $datas->usuario->name }}</span>
                            @elseif ($datas->usuario && $datas->usuario->area)
                                <span class="break-words">{{ $datas->usuario->area->area_name }} - {{ $datas->usuario->name }}</span>
                            @else
                                <span class="text-gray-500 italic">Sin asignar</span>
                            @endif
                        </li>
                        <li><strong>Contrato:</strong> <span class="break-words">{{ $datas->cliente->nombre }}</span></li>
                        <li><strong>Asunto:</strong> <span class="break-words">{{ $datas->asuntoincidencia }}</span></li>
                        <li><strong>Descripción:</strong> <span class="break-words">{{ $datas->descriincidencia }}</span></li>
                        <li><strong>Contacto:</strong> <span class="break-words">{{ $datas->contactoincidencia }}</span></li>
                    </ul>

                    <!-- Archivos adjuntos -->
                    @php
                        $archivos = json_decode($datas->adjuntoincidencia, true);
                    @endphp

                    <div class="mt-4">
                        @if (!empty($archivos) && is_array($archivos))
                            <p class="font-semibold mb-2 text-sm sm:text-base">Archivos adjuntos:</p>
                            <div class="space-y-2">
                                @foreach ($archivos as $archivo)
                                    @if (!empty($archivo))
                                        <a class="block font-handwriting text-sm sm:text-lg text-gray-800 px-4 py-2 border-2 border-black rounded-md 
                                                  hover:bg-yellow-100 transition duration-300 text-center break-all"
                                            href="{{ asset($archivo) }}" target="_blank">
                                            <i class="fa-solid fa-paperclip mr-2"></i>Ver: {{ basename($archivo) }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic text-sm">No hay archivos adjuntos para esta incidencia.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel de acciones y contenido -->
            <div class="flex-1 bg-gray-200 border border-gray-300 rounded-xl shadow-sm p-2 sm:p-4">
                
                <!-- Mensajes de estado -->
                @if (session('success'))
                    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 text-sm">
                        {{ session('success') }}
                    </div>
                    <script>
                        setTimeout(function() {
                            var alert = document.getElementById('success-alert');
                            if (alert) {
                                alert.style.display = 'none';
                            }
                        }, 3000);
                    </script>
                @endif
                
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Barra de navegación de acciones -->
                <nav class="bg-gray-800 text-white px-2 sm:px-4 py-2 shadow-sm rounded-md mb-4">
                    <ul class="nav-buttons flex justify-center items-center space-x-1 sm:space-x-2">
                        
                        <!-- Botón Asignar -->
                        <li id="opcionasignar" class="nav-item border border-gray-500 px-2 sm:px-4 py-1 rounded-md hover:bg-gray-700 cursor-pointer">
                            <a class="nav-link text-xs sm:text-sm block text-center">
                                <i class="fa-regular fa-user"></i>
                                <span class="hidden sm:inline">&nbsp;&nbsp;Asignar</span>
                            </a>
                        </li>

                        <!-- Botón Editar -->
                        <li id='opcioneditar' class="nav-item border border-gray-500 px-2 sm:px-4 py-1 rounded-md hover:bg-gray-700 cursor-pointer">
                            <a class="nav-link text-xs sm:text-sm block text-center">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span class="hidden sm:inline">&nbsp;&nbsp;Editar</span>
                            </a>
                        </li>

                        <!-- Botón Adjuntar -->
                        <li id="opcionadjuntar" class="nav-item border border-gray-500 px-2 sm:px-4 py-1 rounded-md hover:bg-gray-700 cursor-pointer">
                            <a class="nav-link text-xs sm:text-sm block text-center">
                                <i class="fa-solid fa-paperclip"></i>
                                <span class="hidden sm:inline">&nbsp;&nbsp;Adjuntar</span>
                            </a>
                        </li>

                        <!-- Botón Comentario -->
                        <li id="opcioncomentar" class="nav-item border border-gray-500 px-2 sm:px-4 py-1 rounded-md hover:bg-gray-700 cursor-pointer">
                            <a class="nav-link text-xs sm:text-sm block text-center">
                                <i class="fa-solid fa-comment"></i>
                                <span class="hidden sm:inline">&nbsp;&nbsp;Comentario</span>
                            </a>
                        </li>

                        <!-- Botón Estado -->
                        <li id="opcionestado" class="nav-item border border-gray-500 px-2 sm:px-4 py-1 rounded-md hover:bg-gray-700 cursor-pointer">
                            <a class="nav-link text-xs sm:text-sm block text-center">
                                <i class="fa-solid fa-bars"></i>
                                <span class="hidden sm:inline">&nbsp;&nbsp;Estado</span>
                            </a>
                        </li>

                        <!-- Botón Resolver -->
                        <li id="opcionresolver" class="nav-item border border-gray-500 px-2 sm:px-4 py-1 rounded-md hover:bg-gray-700 cursor-pointer">
                            <a class="nav-link text-xs sm:text-sm block text-center">
                                <i class="fa-solid fa-hand"></i>
                                <span class="hidden sm:inline">&nbsp;&nbsp;Resolver</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Contenido principal responsive -->
                <div class="flex flex-col lg:flex-row gap-4">

                    <!-- Panel de información de incidencia -->
                    <div class="flex-1 lg:max-w-md xl:max-w-lg border p-3 sm:p-4 bg-white rounded-md shadow">
                        <div class="bg-blue-600 text-white font-semibold px-3 sm:px-4 py-2 rounded-md shadow mb-4 text-sm sm:text-base">
                            <i class="fa-solid fa-ticket"></i> INCIDENCIA - #{{ $datas->idincidencia }}
                        </div>

                        <!-- Badge de estado responsive -->
                        <div class="px-4 sm:px-6 py-2 rounded-full text-sm sm:text-base text-center mb-4
                                @switch($datas->estadoincidencia->descriestadoincidencia)
                                    @case('En proceso') bg-green-200 text-green-800 @break
                                    @case('Pendiente') bg-yellow-200 text-yellow-800 @break
                                    @case('Cerrado') bg-red-200 text-red-800 @break
                                    @default bg-gray-200 text-gray-800
                                @endswitch">
                            {{ $datas->estadoincidencia->descriestadoincidencia }}
                        </div>

                        <!-- Sección de comentarios -->
                        <div>
                            <p class="text-base sm:text-lg font-semibold text-gray-800 border-b pb-1 mb-2">
                                <i class="fa-solid fa-comments mr-2"></i>Comentarios
                            </p>
                            <div id="comentarios-container" class="max-h-48 sm:max-h-64 overflow-y-auto space-y-2 mt-4 pr-1">
                                @if ($comentarios->isEmpty())
                                    <p class="text-gray-500 italic text-sm">No hay registros de Comentarios.</p>
                                @else
                                    @foreach ($comentarios as $comentario)
                                        <div class="border border-gray-200 rounded-xl p-3 bg-white shadow-sm hover:shadow-md transition-shadow duration-200">
                                            <div class="text-xs sm:text-sm text-gray-500 mb-2">
                                                <i class="fa-solid fa-user mr-1"></i>
                                                <span class="font-medium text-gray-700">{{ $comentario->usuario->name ?? 'Anónimo' }}</span>
                                                <span class="mx-2">•</span>
                                                <span>{{ $comentario->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <p class="text-gray-700 text-sm sm:text-base leading-relaxed break-words">
                                                {{ $comentario->contenido }}
                                            </p>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Panel de auditoría/registros -->
                    <div class="flex-1 lg:max-w-sm xl:max-w-md border p-3 sm:p-4 bg-white rounded-md shadow">
                        <p class="text-base sm:text-lg font-semibold text-gray-800 border-b pb-1 mb-2">
                            <i class="fa-solid fa-history mr-2"></i>Registros
                        </p>
                        <div class="max-h-48 sm:max-h-96 overflow-y-auto">
                            @if ($auditorias->isEmpty())
                                <p class="text-gray-500 italic text-sm">No hay registros de auditoría.</p>
                            @else
                                @foreach ($auditorias as $auditoria)
                                    <div class="mb-4 border-b pb-2 last:border-b-0">
                                        <p class="font-semibold text-gray-700 text-sm sm:text-base">
                                            <i class="fa-solid fa-cog mr-1"></i>{{ ucfirst($auditoria->accion) }}
                                        </p>
                                        @php
                                            $cambios = json_decode($auditoria->cambios, true);
                                        @endphp
                                        <p class="text-xs sm:text-sm text-gray-700 mb-1">
                                            <strong>Usuario:</strong> {{ $auditoria->usuario->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs sm:text-sm text-gray-600 mb-2">
                                            <strong>Fecha:</strong> {{ $auditoria->created_at->format('d/m/Y H:i') }}
                                        </p>
                                        
                                        <!-- Cambios realizados -->
                                        <ul class="ml-2 sm:ml-4 mt-1 text-xs sm:text-sm space-y-1">
                                            @if (isset($cambios['antes']) && is_array($cambios['antes']))
                                                @foreach ($cambios['antes'] as $campo => $valorAntes)
                                                    @if (isset($cambios['despues'][$campo]))
                                                        <li class="break-words">
                                                            <strong>{{ $campo }}:</strong>
                                                            <span class="text-red-600">{{ $valorAntes }}</span> →
                                                            <span class="text-green-600">{{ $cambios['despues'][$campo] }}</span>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            @endif

                                            @if (isset($cambios['estado']))
                                                @php
                                                    $antes = $cambios['estado']['antes'] ?? 'Sin estado';
                                                    $despues = $cambios['estado']['despues'] ?? 'Sin estado';
                                                @endphp
                                                @if ($antes !== $despues)
                                                    <li class="break-words">
                                                        <strong>Estado:</strong>
                                                        <span class="text-red-600">{{ $antes }}</span> →
                                                        <span class="text-green-600">{{ $despues }}</span>
                                                    </li>
                                                @endif
                                            @endif

                                            @if (array_key_exists('cargo', $cambios))
                                                @php
                                                    $antes = $cambios['cargo']['antes'] ?? 'Sin asignar';
                                                    $despues = $cambios['cargo']['despues'] ?? 'Sin asignar';
                                                @endphp
                                                @if ($antes !== $despues)
                                                    <li class="break-words">
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
                                                    <li class="break-words">
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
        </div>
    </div>

    <!-- MODALES (mantengo la misma funcionalidad pero con mejores estilos responsive) -->
    
    <!-- Modal Asignar -->
    @can('incidencias.asignar')
    <div id="modalasignar" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50 p-4">
        <div class="modal-content bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full max-w-md">
            <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-700">Selecciona una opción</h2>
            <form action="{{ route('incidencias.asignar', $datas->idincidencia) }}" method="POST">
                @csrf
                @method('PUT')
                <label class="block mb-2 text-sm font-medium text-gray-700">Asignar a:</label>

                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mb-4">
                    <div class="flex items-center">
                        <input id="opcion1" name="opcion" type="radio" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <label for="opcion1" class="ml-2 block text-sm text-gray-700">Área</label>
                    </div>
                    <div class="flex items-center">
                        <input id="opcion2" name="opcion" type="radio" value="2" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <label for="opcion2" class="ml-2 block text-sm text-gray-700">Cargo</label>
                    </div>
                </div>

                <!-- Lista de técnicos -->
                <select id="tecnicos-lista" name="user_cargo_id" class="w-full border border-gray-300 rounded-md p-2 mb-4 text-gray-700 hidden text-sm">
                    <option value="">Seleccione un cargo</option>
                    @foreach ($datacargos as $datacargo)
                        @foreach ($datacargo->users as $user)
                            <option value="{{ $user->id }}" data-cargo="{{ $user->cargo_id }}">
                                {{ $datacargo->nombre_cargo }} - {{ $user->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>

                <!-- Lista de áreas -->
                <select id="areas-lista" name="user_area_id" class="w-full border border-gray-300 rounded-md p-2 mb-4 text-gray-700 hidden text-sm">
                    <option value="">Seleccione un área</option>
                    @foreach ($dataareas as $dataarea)
                        @foreach ($dataarea->users as $user)
                            <option value="{{ $user->id }}" data-area="{{ $user->area_id }}">
                                {{ $dataarea->area_name }} - {{ $user->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>

                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                    <button type="button" onclick="closeModal()" id="cancelarBtnAsignar" 
                            class="w-full sm:w-auto px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200 text-sm">
                        Cancelar
                    </button>
                    <button type="submit" id="asignarBtn" 
                            class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm" disabled>
                        Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <!-- Modal Editar -->
    @can('incidencias.editar')
    <div id="modaleditar" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50 p-4">
        <div class="modal-content bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full max-w-md">
            <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-700">Modificar Detalle</h2>
            <form action="{{ route('incidencias.update', $datas->idincidencia) }}" method="POST">
                @csrf
                @method('PUT')

                <label class="block text-sm text-gray-700 mb-1">Asunto:</label>
                <input type="text" name="asunto" value="{{ $datas->asuntoincidencia }}"
                       class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 text-gray-700 text-sm">
                
                <label class="block text-sm text-gray-700 mb-1">Descripción:</label>
                <textarea name="descripcion" rows="3"
                          class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 text-gray-700 text-sm">{{ old('descripcion', $datas->descriincidencia) }}</textarea>
                
                <label class="block text-sm text-gray-700 mb-1">Contacto:</label>
                <textarea name="contacto" rows="3"
                          class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-4 text-gray-700 text-sm">{{ old('contacto', $datas->contactoincidencia) }}</textarea>

                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                    <button type="button" onclick="closeModal()" id="cancelarBtnEditar"
                            class="w-full sm:w-auto px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200 text-sm">
                        Cancelar
                    </button>
                    <button type="submit" id="guardarBtn"
                            class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm" disabled>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <!-- Modal Adjuntar -->
    <div id="modaladjuntar" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50 p-4">
        <div class="modal-content bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full max-w-md">
            <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-700">Nuevos Archivos</h2>
            <form action="{{ route('incidencias.updateFile', $datas->idincidencia) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <label class="block text-sm text-gray-700 mb-2">Seleccionar archivos:</label>
                <input type="file" name="adjunto[]" multiple
                       class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-4 text-gray-700 text-sm">
                
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                    <button type="button" onclick="closeModal()" id="cancelarBtnAdjuntar"
                            class="w-full sm:w-auto px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200 text-sm">
                        Cancelar
                    </button>
                    <button type="submit" id="subirBtn"
                            class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm" disabled>
                        Subir
                    </button>
                </div>

                @if ($errors->any())
                    <div class="mt-4 text-red-600 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Modal Comentario -->
    <div id="modalcomentario" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50 p-4">
        <div class="modal-content bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full max-w-md">
            <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-700">Agregar Comentario</h2>
            <form action="{{ route('comentarios.store', $datas->idincidencia) }}" method="POST">
                @csrf
                <textarea name="contenido" rows="4" placeholder="Escribe un comentario..."
                          class="w-full p-3 border rounded text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 mb-4"></textarea>

                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                    <button type="button" onclick="closeModal()" id="cancelarBtnComentario"
                            class="w-full sm:w-auto px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200 text-sm">
                        Cancelar
                    </button>
                    <button type="submit" id="comentarBtn"
                            class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm" disabled>
                        Comentar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Estado -->
    <div id="modalestado" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50 p-4">
        <div class="modal-content bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full max-w-md">
            <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-700">Cambiar Estado</h2>
            <form action="{{ route('incidencias.cambiarEstado', $datas->idincidencia) }}" method="POST">
                @csrf
                <select name="estado_id" class="w-full border border-gray-300 rounded-md p-2 mb-4 text-gray-700 text-sm">
                    <option value="">Selecciona un estado</option>
                    @foreach ($estadosincidencias as $estadoincidencia)
                        @if ($estadoincidencia->descriestadoincidencia !== 'Cerrado')
                            <option value="{{ $estadoincidencia->idestadoincidencia }}">
                                {{ $estadoincidencia->descriestadoincidencia }}
                            </option>
                        @endif
                    @endforeach
                </select>

                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                    <button type="button" onclick="closeModal()" id="cancelarBtnEstado"
                            class="w-full sm:w-auto px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200 text-sm">
                        Cancelar
                    </button>
                    <button type="submit" id="cambiarBtn"
                            class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm" disabled>
                        Cambiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Resolver -->
    <div id="modalresolver" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50 p-4">
        <div class="modal-content bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full max-w-md">
            <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-700">Resolver Incidencia</h2>
            <form id="resolverFormulario" action="{{ route('incidencias.resolverIncidencia', $datas->idincidencia) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <label class="block text-sm text-gray-700 mb-2">Archivos de resolución (mín. 2):</label>
                <input type="file" name="adjunto[]" multiple
                       class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 text-gray-700 text-sm">
                
                <label class="block text-sm text-gray-700 mb-2">Comentario de resolución:</label>
                <textarea name="contenido" rows="4" placeholder="Describe la solución aplicada..."
                          class="w-full p-3 border rounded text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 mb-4"></textarea>
                
                <input type="hidden" name="estado_id" value="3">
                <input type="hidden" name="fecharesolucionincidencia" value="{{ now() }}">
                
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                    <button type="button" onclick="closeModal()" id="cancelarBtnResolver"
                            class="w-full sm:w-auto px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200 text-sm">
                        Cancelar
                    </button>
                    <button type="submit" id="resolverBtn"
                            class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm" disabled>
                        Resolver
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script de funcionalidad -->
    <script>
        // Función para cerrar modales
        function closeModal() {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            });
        }

        // Event listeners para abrir modales
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

        // Cerrar modal al hacer clic fuera
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal();
            }
        });

        // Funcionalidad para mostrar/ocultar listas de asignación
        const opcion1 = document.getElementById('opcion1');
        const opcion2 = document.getElementById('opcion2');

        function actualizarVisibilidad() {
            const tecnicosLista = document.getElementById('tecnicos-lista');
            const areasLista = document.getElementById('areas-lista');
            
            if (opcion2 && opcion2.checked) {
                tecnicosLista.classList.remove('hidden');
                areasLista.classList.add('hidden');
            } else if (opcion1 && opcion1.checked) {
                areasLista.classList.remove('hidden');
                tecnicosLista.classList.add('hidden');
            } else {
                tecnicosLista.classList.add('hidden');
                areasLista.classList.add('hidden');
            }
        }

        if (opcion1) opcion1.addEventListener('change', actualizarVisibilidad);
        if (opcion2) opcion2.addEventListener('change', actualizarVisibilidad);

        // Habilitar/deshabilitar botones según contenido
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                form.addEventListener('input', function() {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-60', 'pointer-events-none');
                });
                
                form.addEventListener('change', function() {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-60', 'pointer-events-none');
                });
            }
        });

        // Funcionalidad específica para resolver (requiere mínimo 2 archivos)
        const resolverFileInput = document.querySelector('#resolverFormulario input[type="file"]');
        const resolverBtn = document.getElementById('resolverBtn');
        
        if (resolverFileInput && resolverBtn) {
            resolverFileInput.addEventListener('change', function() {
                if (resolverFileInput.files.length >= 2) {
                    resolverBtn.disabled = false;
                    resolverBtn.classList.remove('opacity-60', 'pointer-events-none');
                } else {
                    resolverBtn.disabled = true;
                    resolverBtn.classList.add('opacity-60', 'pointer-events-none');
                }
            });
        }

        // Limpiar formularios para evitar envío de campos vacíos
        document.querySelector('form[action*="asignar"]')?.addEventListener('submit', function(e) {
            const tecnico = document.getElementById('tecnicos-lista');
            const area = document.getElementById('areas-lista');
            
            if (!tecnico.value) tecnico.name = '';
            if (!area.value) area.name = '';
        });

        // Deshabilitar interfaz si está cerrado
        @if ($datas->estadoincidencia->descriestadoincidencia === 'Cerrado')
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('button, input, textarea, select');
            elements.forEach(el => {
                if (el.name === 'estado_id' || el.id === 'cambiarBtn' || el.id === 'cancelarBtnEstado') {
                    el.removeAttribute('disabled');
                    el.classList.remove('pointer-events-none', 'opacity-60');
                } else if (!el.classList.contains('close_Sesion') && !el.classList.contains('btncancelar')) {
                    el.setAttribute('disabled', 'disabled');
                    el.classList.add('pointer-events-none', 'opacity-60');
                }
            });
        });
        @endif
    </script>
</body>

</html>
