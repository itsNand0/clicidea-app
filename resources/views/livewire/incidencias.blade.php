<div>
    <!-- Table -->
    <main class="p-6">
        <div class="overflow-x-auto">
          <table class="min-w-full bg-white rounded shadow-md">
            <thead>
              <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">#</th>
                <th class="py-3 px-6 text-left">Título</th>
                <th class="py-3 px-6 text-left">Descripción</th>
                <th class="py-3 px-6 text-left">Responsable</th>
                <th class="py-3 px-6 text-left">Estado</th>
                <th class="py-3 px-6 text-left">Fecha de Creación</th>
                <th class="py-3 px-6 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody class="text-gray-700 text-sm font-light">
              <tr class="border-b border-gray-200 hover:bg-gray-100">
                <td class="py-3 px-6 text-left whitespace-nowrap">1</td>
                <td class="py-3 px-6 text-left">Problema de Red</td>
                <td class="py-3 px-6 text-left">Falla en conexión de internet</td>
                <td class="py-3 px-6 text-left">Juan Pérez</td>
                <td class="py-3 px-6 text-left">Pendiente</td>
                <td class="py-3 px-6 text-left">2025-04-27</td>
                <td class="px-6 py-4">
                  <a href="#" class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-lime-500 fa-solid fa-eye"></a>
                  <a href="#" class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-cyan-400 fa-solid fa-pen-to-square"></a>
                  <form action="#" method="POST" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-red-400 fa-solid fa-trash"></button>
                  </form>
              </td>
              </tr>
              <!-- Más filas -->
            </tbody>
          </table>
        </div>
      </main>{{-- If    you look to others for fulfillment, you will never truly be fulfilled. --}}
</div>
