<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen flex items-center justify-center bg-gray-100">

    <div class="w-full max-w-sm text-center space-y-6 bg-white p-8 rounded-xl shadow-lg">
        <img src="{{ asset('images/lateral01.png') }}" alt="Imagen lateral" class="mx-auto w-4/5">

        <form method="POST" action="" accept-charset="UTF-8" id="loginForm" class="space-y-4">
            @csrf

            @if ($errors->any())
                <div class="mb-4 text-red-500">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div>
                <input
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ingrese Usuario" autofocus id="usuario" name="usuario" type="text">
            </div>

            <div>
                <input
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ingrese Contraseña" id="password" name="password" type="password">
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                    Acceder
                </button>
            </div>
        </form>

        <div>
            <button title="Presione aquí para restablecer su contraseña" id="button_link"
                class="text-blue-600 hover:underline text-sm">
                ¿Olvidaste tu contraseña?
            </button>
        </div>
    </div>

</body>

</html>
