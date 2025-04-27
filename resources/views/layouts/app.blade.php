<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Mi Aplicación') }} - @yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/alerts.js') }}"></script>
    @stack('scripts')
</head>

<body class="background">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Prueba Técnica</a>
            <div class="d-flex">
                @auth
                    <!-- El formulario para logout se gestiona con una confirmación -->
                    <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-inline-block">
                        @csrf
                        <button type="button" class="btn btn-danger btn-sm ms-2" id="logout-btn">Cerrar sesión</button>
                    </form>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="btn btn-success btn-sm ms-2">Iniciar sesión</a>
                @endguest
            </div>
        </div>
    </nav>
    <div class="styleBody">
        @yield('content')
    </div>

    <script>
        $(document).ready(function() {
            // Confirmación para logout con SweetAlert2
            $('#logout-btn').on('click', function(e) {
                e.preventDefault();

                alertConfirm({
                    text: "¿Deseas cerrar sesión?",
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si el usuario confirma, enviamos el formulario para hacer logout
                        $('#logout-form').submit();
                    }
                });
            });
        });
    </script>
</body>

</html>
