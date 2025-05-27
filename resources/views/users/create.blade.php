<!-- resources/views/users/create.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-3xl">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Crear Usuario</h1>
        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="flex items-center space-x-4">
                <label for="name" class="w-40 text-right text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="name" id="name" required
                    class="flex-1 px-4 py-2 border rounded-xl shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex items-center space-x-4">
                <label for="email" class="w-40 text-right text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required
                    class="flex-1 px-4 py-2 border rounded-xl shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex items-center space-x-4">
                <label for="password" class="w-40 text-right text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" name="password" id="password" required
                    class="flex-1 px-4 py-2 border rounded-xl shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex items-center space-x-4">
                <label for="password_confirmation" class="w-40 text-right text-sm font-medium text-gray-700">Confirmar
                    Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="flex-1 px-4 py-2 border rounded-xl shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <input id="opcion1" name="opcion" type="radio" value="1"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                        onchange="actualizarVisibilidad()"checked>
                    <label for="opcion1" class="ml-2 block text-sm text-gray-700">Área</label>
                </div>

                <!-- Radio para seleccionar Técnico -->
                <div class="flex items-center">
                    <input id="opcion2" name="opcion" type="radio" value="2"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                        onchange="actualizarVisibilidad()" >
                    <label for="opcion2" class="ml-2 block text-sm text-gray-700">Cargo</label>
                </div>

                <select id="areas-lista" name="area_id"
                    class="flex-1 px-4 py-2 border rounded-xl shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 hidden">
                    <option value="">Seleccione un área</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}" for="area{{ $area->id }}">{{ $area->area_name }}
                        </option>
                    @endforeach
                </select>

                <select id="cargo-lista" name="cargo_id"
                    class="flex-1 px-4 py-2 border rounded-xl shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 hidden">
                    <option value="">Seleccione un cargo</option>
                    @foreach ($cargos as $cargo)
                        <option value="{{ $cargo->id }}" for="cargo{{ $cargo->id }}">{{ $cargo->nombre_cargo }}
                        </option>
                    @endforeach
                </select>
                <script>
                    document.querySelector('form').addEventListener('submit', function(e) {
                        const cargo = document.getElementById('cargo-lista');
                        const area = document.getElementById('areas-lista');

                        // Si técnico está vacío, borra su atributo "name" para que no se envíe
                        if (!cargo.value) {
                            cargo.name = '';
                        }

                        // Si área está vacía, borra su atributo "name" para que no se envíe
                        if (!area.value) {
                            area.name = '';
                        }
                    });
                </script>
            </div>

            <div class="pt-6 text-center">
                <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-2 rounded-xl hover:bg-indigo-700 transition">
                    Crear Usuario
                </button>
            </div>
            @if ($errors->any())
                <div class="mt-4 text-red-600">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>
    <script>
        function actualizarVisibilidad() {
            const opcion1 = document.getElementById('opcion1');
            const opcion2 = document.getElementById('opcion2');
            const areasLista = document.getElementById('areas-lista');
            const cargoLista = document.getElementById('cargo-lista');

            if (opcion2.checked) {
                cargoLista.style.display = 'block';
                areasLista.style.display = 'none';
            } else if (opcion1.checked) {
                areasLista.style.display = 'block';
                cargoLista.style.display = 'none';
            } else {
                areasLista.style.display = 'none';
                cargoLista.style.display = 'none';
            }
        }

        opcion1.addEventListener('change', actualizarVisibilidad);
        opcion2.addEventListener('change', actualizarVisibilidad);

        // Ejecutar al cargar la página
        actualizarVisibilidad();
    </script>
</body>

</html>
