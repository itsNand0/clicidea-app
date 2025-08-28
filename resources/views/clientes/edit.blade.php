<!-- resources/views/clientes/edit.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <title>Editar Cliente</title>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <x-BarMenu />
    
    <div class="flex-grow flex items-center justify-center px-4">
        <div class="bg-white p-10 rounded-2xl shadow-lg w-full max-w-4xl">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center justify-center gap-3">
                    <i class="fa-solid fa-user-edit text-blue-600"></i>
                    Editar Cliente
                </h1>
                <p class="text-gray-600 mt-2">Modificar la información del cliente: <strong>{{ $cliente->nombre }}</strong></p>
                <div class="mt-2 text-sm text-gray-500">
                    <span class="bg-gray-100 px-3 py-1 rounded-full">
                        ID: {{ $cliente->idcliente }} | ATM ID: {{ $cliente->atm_id }}
                    </span>
                </div>
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

            <!-- Mostrar errores generales -->
            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulario -->
            <form action="{{ route('clientes.update', $cliente->idcliente) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

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
                               value="{{ old('atm_id', $cliente->atm_id) }}"
                               required
                               min="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('atm_id') border-red-300 bg-red-50 @enderror"
                               placeholder="Ej: 12345">
                        @error('atm_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Actual: {{ $cliente->atm_id }}
                        </p>
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
                               value="{{ old('nombre', $cliente->nombre) }}"
                               required
                               maxlength="255"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('nombre') border-red-300 bg-red-50 @enderror"
                               placeholder="Ej: Banco Nacional">
                        @error('nombre')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Actual: {{ $cliente->nombre }}
                        </p>
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
                               value="{{ old('zona', $cliente->zona) }}"
                               maxlength="255"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('zona') border-red-300 bg-red-50 @enderror"
                               placeholder="Ej: Ñemby, San Lorenzo, etc.">
                        @error('zona')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Actual: {{ $cliente->zona ?? 'No especificada' }}
                        </p>
                    </div>
                </div>

                <!-- Información de cambios -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Notas importantes</h3>
                            <ul class="mt-2 text-sm text-blue-700 list-disc list-inside">
                                <li>Al cambiar el ATM ID, asegúrese de que sea único</li>
                                <li>Los cambios afectarán todas las incidencias asociadas a este cliente</li>
                                <li>La zona geográfica es opcional pero recomendada</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit"
                            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center gap-2 font-medium">
                        <i class="fa-solid fa-save"></i>
                        Actualizar Cliente
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
                this.classList.add('border-red-300', 'bg-red-50');
            } else {
                this.setCustomValidity('');
                this.classList.remove('border-red-300', 'bg-red-50');
            }
        });

        // Resaltar cambios
        const originalValues = {
            atm_id: {{ $cliente->atm_id }},
            nombre: "{{ $cliente->nombre }}",
            zona: "{{ $cliente->zona ?? '' }}"
        };

        function highlightChanges() {
            const atmInput = document.getElementById('atm_id');
            const nombreInput = document.getElementById('nombre');
            const zonaSelect = document.getElementById('zona');

            // Verificar cambios y resaltar
            if (atmInput.value != originalValues.atm_id) {
                atmInput.classList.add('border-yellow-400', 'bg-yellow-50');
            } else {
                atmInput.classList.remove('border-yellow-400', 'bg-yellow-50');
            }

            if (nombreInput.value != originalValues.nombre) {
                nombreInput.classList.add('border-yellow-400', 'bg-yellow-50');
            } else {
                nombreInput.classList.remove('border-yellow-400', 'bg-yellow-50');
            }

            if (zonaSelect.value != originalValues.zona) {
                zonaSelect.classList.add('border-yellow-400', 'bg-yellow-50');
            } else {
                zonaSelect.classList.remove('border-yellow-400', 'bg-yellow-50');
            }
        }

        // Escuchar cambios en tiempo real
        document.getElementById('atm_id').addEventListener('input', highlightChanges);
        document.getElementById('nombre').addEventListener('input', highlightChanges);
        document.getElementById('zona').addEventListener('change', highlightChanges);

        // Confirmación antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const atmId = document.getElementById('atm_id').value;
            const nombre = document.getElementById('nombre').value;
            
            if (!confirm(`¿Está seguro de actualizar el cliente "${nombre}" con ATM ID "${atmId}"?`)) {
                e.preventDefault();
            }
        });

        // Advertencia si el usuario intenta salir con cambios sin guardar
        let formChanged = false;
        document.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('change', () => {
                formChanged = true;
            });
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '¿Está seguro de salir sin guardar los cambios?';
            }
        });

        // No mostrar advertencia al enviar el formulario
        document.querySelector('form').addEventListener('submit', () => {
            formChanged = false;
        });
    </script>
</body>

</html>
