<div class="overflow-x-auto shadow-md sm:rounded-lg">

    <input type="text" placeholder="Buscar" class="border p-2 rounded-lg mb-4" wire:model.live.debounce.300ms="search">
    <a href="{{ route('users.create') }}" class="bg-lime-600 text-white px-4 py-2 rounded-lg">
        <i class="fa-solid fa-plus"></i>
    </a>
    <table class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
            <tr>
                <th scope="col" class="px-6 py-3">
                    #
                </th>
                <th scope="col" class="px-6 py-3">
                    Nombre
                </th>
                <th scope="col" class="px-6 py-3">
                    Usuario
                </th>
                <th scope="col" class="px-6 py-3">
                    Email
                </th>
                <th scope="col" class="px-6 py-3">
                    Cargo
                </th>
                <th scope="col" class="px-6 py-3">
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody class="text-gray-700 text-sm font-light">
            @foreach ($users as $user)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">
                        {{ $user->id }}
                    </td>
                    <td class="py-3 px-6 text-left whitespace-nowrap">
                        {{ $user->name }}
                    </td>
                    <td class="py-3 px-6 text-left whitespace-nowrap">
                        {{ $user->usuario }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $user->email }}
                    </td>
                    <td class="px-6 py-4">
                        @if (isset($user->area->area_name))
                        Encargado de: {{ $user->area->area_name }}
                        @elseif (isset($user->cargo->nombre_cargo))
                            {{ $user->cargo->nombre_cargo }}
                        @else
                            Sin asignar
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('users.show', $user->id) }}"
                            class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-lime-500 fa-solid fa-eye"></a>
                        <a href="{{ route('users.edit', $user->id) }}"
                            class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-cyan-400 fa-solid fa-pen-to-square"></a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-red-400 fa-solid fa-trash"></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
