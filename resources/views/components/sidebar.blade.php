@vite('resources/css/app.css') 
<div class="h-screen flex">
    <div class="group flex flex-col bg-gray-800 text-white transition-all duration-300 ease-in-out w-16 hover:w-64 overflow-hidden">
        <nav class="flex flex-col space-y-4 mt-8 pl-4">
            <a href="/dashboard" class="flex items-center space-x-4">
                <span class="px-2 py-1 fa-solid fa-house"></span>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity">Dashboard</span>
            </a>
            <a href="/users" class="flex items-center space-x-4">
                <span class="px-2 py-1 fa-solid fa-users"></span>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity">Usuarios</span>
            </a>
        </nav>
    </div>