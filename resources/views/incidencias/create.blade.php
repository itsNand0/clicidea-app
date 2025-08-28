<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    @livewireStyles
    <title>{{ config('app.name', 'ClicIdea') }} - Crear Incidencia</title>
    
    <style>
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-up { animation: slideUp 0.5s ease-out; }
        
        .input-enhanced:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
        }
        
        .btn-loading { position: relative; pointer-events: none; }
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px; height: 16px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s ease infinite;
            top: 0; left: 0; bottom: 0; right: 0;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .error-border { border-color: #ef4444 !important; }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
    <x-Barmenu />
    
    <div class="min-h-screen pt-20 pb-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            
            <!-- Header -->
            <div class="mb-8 animate-slide-up">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('incidencias.index') }}" class="text-gray-500 hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-home mr-2"></i>Incidencias
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fa-solid fa-chevron-right text-gray-400 mx-2"></i>
                                <span class="text-gray-700 font-medium">Nueva Incidencia</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        <i class="fa-solid fa-ticket text-blue-600 mr-3"></i>
                        Crear Nueva Incidencia
                    </h1>
                    <p class="text-gray-600 text-lg">Completa el formulario para reportar una nueva incidencia</p>
                </div>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-slide-up" style="animation-delay: 0.2s;">
                <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-blue-700">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <i class="fa-solid fa-edit mr-3"></i>
                        Información de la Incidencia
                    </h2>
                </div>
                
                <form action="{{ route('incidencias.store') }}" method="POST" enctype="multipart/form-data" 
                      class="p-8" x-data="formHandler()" @submit="handleSubmit">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        <!-- Columna izquierda -->
                        <div class="space-y-6">
                            <!-- Buscador de contratos -->
                            <div class="form-group">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fa-solid fa-building text-blue-600 mr-2"></i>
                                    Información del Cliente
                                </h3>
                                @livewire('buscador-contratos')
                            </div>

                            <!-- Asunto -->
                            <div class="form-group">
                                <label class="flex items-center text-gray-700 font-semibold mb-2">
                                    <i class="fa-solid fa-tag text-orange-500 mr-2"></i>
                                    Asunto <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="text" 
                                       name="asunto" 
                                       placeholder="Título descriptivo de la incidencia"
                                       value="{{ old('asunto') }}"
                                       required
                                       maxlength="100"
                                       class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all input-enhanced {{ $errors->has('asunto') ? 'error-border' : '' }}">
                                @error('asunto')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <i class="fa-solid fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Contacto -->
                            <div class="form-group">
                                <label class="flex items-center text-gray-700 font-semibold mb-2">
                                    <i class="fa-solid fa-user text-green-500 mr-2"></i>
                                    Persona de Contacto <span class="text-red-500 ml-1">*</span>
                                </label>
                                <textarea name="contacto" 
                                          placeholder="Nombre, teléfono, email del contacto en sitio"
                                          rows="3" 
                                          required
                                          maxlength="250"
                                          class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all input-enhanced resize-none {{ $errors->has('contacto') ? 'error-border' : '' }}">{{ old('contacto') }}</textarea>
                                @error('contacto')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <i class="fa-solid fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Columna derecha -->
                        <div class="space-y-6">
                            <!-- Descripción -->
                            <div class="form-group">
                                <label class="flex items-center text-gray-700 font-semibold mb-2">
                                    <i class="fa-solid fa-file-text text-purple-500 mr-2"></i>
                                    Descripción Detallada <span class="text-red-500 ml-1">*</span>
                                </label>
                                <textarea name="descripcion" 
                                          rows="6" 
                                          placeholder="Describe detalladamente el problema, síntomas, pasos para reproducir, etc."
                                          required
                                          maxlength="1000"
                                          class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all input-enhanced resize-none {{ $errors->has('descripcion') ? 'error-border' : '' }}">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <i class="fa-solid fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <div class="mt-1 text-sm text-gray-500 flex items-center">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    Máximo 1000 caracteres
                                </div>
                            </div>

                            <!-- Adjuntos -->
                            <div class="form-group">
                                <label class="flex items-center text-gray-700 font-semibold mb-2">
                                    <i class="fa-solid fa-paperclip text-indigo-500 mr-2"></i>
                                    Archivos Adjuntos
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-blue-400 transition-colors">
                                    <input type="file" 
                                           name="adjunto[]"
                                           multiple
                                           accept="image/*,.pdf,.doc,.docx,.txt"
                                           class="hidden"
                                           id="file-upload"
                                           @change="handleFileSelect">
                                    <label for="file-upload" class="cursor-pointer">
                                        <div class="text-center">
                                            <i class="fa-solid fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                            <p class="text-gray-600 font-medium">Arrastra archivos aquí o haz clic para seleccionar</p>
                                            <p class="text-sm text-gray-500 mt-2">Imágenes, PDF, Word, Texto (máx. 10MB c/u)</p>
                                        </div>
                                    </label>
                                </div>
                                <div id="file-list" class="mt-3 space-y-2"></div>
                            </div>

                            
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                                :class="{ 'btn-loading': loading }"
                                :disabled="loading">
                            <span x-show="!loading" class="flex items-center justify-center">
                                <i class="fa-solid fa-paper-plane mr-3"></i>
                                Crear Incidencia
                            </span>
                            <span x-show="loading" class="flex items-center justify-center">
                                Procesando...
                            </span>
                        </button>
                        
                        <a href="{{ route('view.dashboard') }}" 
                           class="flex-1 sm:flex-none bg-gray-100 text-gray-700 px-6 py-4 rounded-xl hover:bg-gray-200 transition-all font-semibold text-center border-2 border-gray-200 hover:border-gray-300">
                            <i class="fa-solid fa-times mr-2"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Errores -->
            @if ($errors->any())
                <div class="mt-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-6 animate-slide-up">
                    <div class="flex items-center mb-3">
                        <i class="fa-solid fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                        <h3 class="text-lg font-semibold text-red-800">Se encontraron errores en el formulario</h3>
                    </div>
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-red-700 flex items-center">
                                <i class="fa-solid fa-arrow-right text-red-500 mr-2 text-sm"></i>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mt-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-6 animate-slide-up">
                    <div class="flex items-center">
                        <i class="fa-solid fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @livewireScripts
    
    <script>
        function formHandler() {
            return {
                loading: false,
                
                handleSubmit(event) {
                    this.loading = true;
                },
                
                handleFileSelect(event) {
                    const files = event.target.files;
                    const fileList = document.getElementById('file-list');
                    fileList.innerHTML = '';
                    
                    Array.from(files).forEach((file, index) => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg border';
                        fileItem.innerHTML = `
                            <div class="flex items-center">
                                <i class="fa-solid fa-file text-blue-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700">${file.name}</span>
                                <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        `;
                        fileList.appendChild(fileItem);
                    });
                }
            }
        }
    </script>
</body>

</html>
