<!-- resources/views/clientes/create.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <title>Crear Cliente</title>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <x-BarMenu />
    
    <div class="flex-grow flex items-center justify-center px-4">
        <div class="bg-white p-10 rounded-2xl shadow-lg w-full max-w-4xl">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center justify-center gap-3">
                    <i class="fa-solid fa-user-plus text-blue-600"></i>
                    Crear Nuevo Cliente
                </h1>
                <p class="text-gray-600 mt-2">Complete la información del cliente</p>
            </div>

            <!-- Mostrar errores de validación -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-exclamation-triangle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Por favor corrija los siguientes errores:
                            </h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulario -->
            <form action="{{ route('clientes.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- ATM ID -->
                    <div class="space-y-2">
                        <label for="atm_id" class="block text-sm font-medium text-gray-700">
                            <i class="fa-solid fa-hashtag text-gray-500 mr-2"></i>
                            ATM ID <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="atm_id" 
                               id="atm_id" 
                               value="{{ old('atm_id') }}"
                               required
                               min="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('atm_id') border-red-300 @enderror"
                               placeholder="Ej: 12345">
                        @error('atm_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre del Cliente -->
                    <div class="space-y-2">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">
                            <i class="fa-solid fa-building text-gray-500 mr-2"></i>
                            Nombre del Cliente <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nombre" 
                               id="nombre" 
                               value="{{ old('nombre') }}"
                               required
                               maxlength="255"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('nombre') border-red-300 @enderror"
                               placeholder="Ej: Banco Nacional">
                        @error('nombre')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Zona -->
                    <div class="space-y-2 md:col-span-2">
                        <label for="zona" class="block text-sm font-medium text-gray-700">
                            <i class="fa-solid fa-map-marker-alt text-gray-500 mr-2"></i>
                            Zona Geográfica
                        </label>
                        <input type="text" 
                               name="zona"
                               id="zona" 
                               value="{{ old('zona') }}"
                               maxlength="100"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('zona') border-red-300 @enderror"
                               placeholder="Ej: Asuncion, Capiata, etc.">
                        @error('zona')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm">Campo opcional - Puede dejarlo vacío si no conoce la zona</p>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Información importante</h3>
                            <ul class="mt-2 text-sm text-blue-700 list-disc list-inside">
                                <li>El ATM ID debe ser único en el sistema</li>
                                <li>El nombre del cliente es obligatorio</li>
                                <li>La zona geográfica es opcional pero recomendada para mejor organización</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit"
                            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center gap-2 font-medium">
                        <i class="fa-solid fa-save"></i>
                        Crear Cliente
                    </button>
                    
                    <a href="{{ route('clientes.index') }}"
                       class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors duration-200 flex items-center justify-center gap-2 font-medium">
                        <i class="fa-solid fa-arrow-left"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript para mejoras de UX -->
    <script>
        // Auto-focus en el primer campo
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('atm_id').focus();
        });

        // Validación del formulario en tiempo real
        document.getElementById('atm_id').addEventListener('input', function() {
            const value = this.value;
            if (value && value <= 0) {
                this.setCustomValidity('El ATM ID debe ser un número positivo');
            } else {
                this.setCustomValidity('');
            }
        });

        // Confirmación antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const atmId = document.getElementById('atm_id').value;
            const nombre = document.getElementById('nombre').value;
            
            if (!confirm(`¿Está seguro de crear el cliente "${nombre}" con ATM ID "${atmId}"?`)) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>