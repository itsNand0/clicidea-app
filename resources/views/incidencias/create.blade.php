<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <title>Home</title>
</head>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<body">
    <x-Barmenu />
    <x-Sidebar />

    <div class="flex-1 flex items-center justify-center bg-gray-100">
        <form action="/index" method="POST" class="bg-white px-8 py-6 rounded-lg shadow-md w-full max-w-3xl">
            @csrf
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Crear Incidencia</h2>
    
            <!-- Menú desplegable del contrato -->
            <div x-data="dropdown()" class="relative w-full mb-4">
                <input type="text" x-model="search" @focus="open = true" @keydown.escape="open = false"
                    placeholder="Contrato"
                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
    
                <ul x-show="open"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded mt-1 max-h-60 overflow-y-auto shadow-lg">
                    <template x-for="item in filtered" :key="item">
                        <li @click="select(item)" class="px-4 py-2 hover:bg-blue-100 cursor-pointer" x-text="item"></li>
                    </template>
                    <li x-show="filtered.length === 0" class="px-4 py-2 text-gray-400">Sin resultados</li>
                </ul>
            </div>
    
            <div class="mb-4">
                <input type="text" name="asunto" placeholder="Asunto"
                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
    
            <div class="mb-4">
                <textarea name="descripcion" rows="4" placeholder="Descripción detallada"
                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none"></textarea>
            </div>
    
            <div class="mb-4">
                <textarea name="contacto" placeholder="Contacto"
                    rows="2"
                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none"></textarea>
            </div>
    
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-1">Adjuntos</label>
                <input type="text" name="proveedor" placeholder="Adjuntos"
                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
    
            <button type="submit"
                class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">Agregar</button>
        </form>
    </div>
    

    <script>
        function dropdown() {
            return {
                open: false,
                search: '',
                get filtered() {
                    return this.items.filter(i => i.toLowerCase().includes(this.search.toLowerCase()));
                },
                select(item) {
                    this.search = item;
                    this.open = false;
                }
            };
        }
    </script>




</body>

</html>
