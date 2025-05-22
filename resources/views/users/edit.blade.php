<!-- resources/views/users/edit.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
</head>
<body>
    <h1>Editar Usuario</h1>
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="name">Nombre</label>
        <input type="text" name="name" id="name" value="{{ $user->name }}" required>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ $user->email }}" required>
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" required>
        <label for="password_confirmation">Confirmar Contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>
        <button type="submit">Actualizar Usuario</button>
    </form>
</body>
</html>
