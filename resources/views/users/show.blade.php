<!-- resources/views/users/show.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Usuario</title>
</head>

<body>
    <h1>Detalles del Usuario</h1>
    <p>Nombre: {{ $user->name }}</p>
    <p>Email: {{ $user->email }}</p>
    <a href="{{ route('users.index') }}">Volver a la lista</a>

    <!-- <script>
        const options = {
            method: 'GET',
            _token: 'mxZCY7h57DTYReKLj5riHYGDsU5Q2Xivc4zifAGl'
        };

        fetch('https://cms.eglobalt.com.py/dashboard/atms_general', options)
            .then(res => res.json())
            .then(response => {
                console.log('Respuesta de la API:', response);
                
            })
            .catch(error => {
                console.error('Error al obtener datos:', error);
            });
    </script> --> 
</body>

</html>
