<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <title>Crear Incidencia</title>
</head>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<body class="bg-gray-100 min-h-screen">
    <x-Barmenu />
    <div class="flex flex-col items-center justify-center min-h-screen pt-15 px-2">
        <form action="{{ route('incidencias.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-white px-6 py-8 rounded-xl shadow-lg w-full max-w-2xl">
            @csrf
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Crear Incidencia</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-5">
                    <!-- Menú desplegable del contrato -->
                    <div x-data="{ search: '', selected: null }">
                        <label class="block text-gray-700 font-semibold mb-1">Buscar Contrato</label>
                        <input type="text" x-model="search" placeholder="Buscar contrato..."
                            class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 mb-2">

                        <div class="max-h-40 overflow-y-auto border rounded-lg">
                            @foreach ($clientes as $cliente)
                                <template x-if="'{{ strtolower($cliente->nombre) }}'.includes(search.toLowerCase()) || '{{ $cliente->atm_id }}'.includes(search.toLowerCase())">
                                    <div @click="selected = '{{ $cliente->idcliente }}'; search = '{{ $cliente->nombre }}';"
                                        class="px-4 py-2 cursor-pointer hover:bg-blue-100">
                                        {{ $cliente->atm_id }} - {{ $cliente->nombre }}
                                    </div>
                                </template>
                            @endforeach
                        </div>

                        <input type="hidden" name="contrato" :value="selected">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Asunto</label>
                        <input type="text" name="asunto" placeholder="Asunto"
                            class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Contacto</label>
                        <textarea name="contacto" placeholder="Contacto" rows="2"
                            class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none"></textarea>
                    </div>
                </div>
                <div class="space-y-5">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Descripción detallada</label>
                        <textarea name="descripcion" rows="5" placeholder="Descripción detallada"
                            class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Imagen o Adjunto</label>
                        <input type="file" name="adjunto[]"
                            class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                            multiple>
                    </div>
                </div>
            </div>
            <div class="mt-8">
                <button type="submit"
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold text-lg shadow">
                    <i class="fa-solid fa-plus mr-2"></i>Agregar Incidencia
                </button>
            </div>
        </form>
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mt-6 w-full max-w-2xl">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

</body>

</html>
