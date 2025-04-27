@extends('layouts.app')

@section('title', 'Registro')

@section('content')
    <div class="d-flex align-items-center h-100 justify-content-center">
        <div class="card p-4 shadow-sm" style="width: 22rem;">
            <div class="card-body">
                <h5 class="card-title text-center mb-4">Registro</h5>

                <form id="registerForm">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name">
                        <div class="text-danger fs-10 hidden" id="error-name"></div>
                    </div>

                    <div class="mb-3">
                        <label for="lastName" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="lastName" name="lastName">
                        <div class="text-danger fs-10 hidden" id="error-lastName"></div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="text" class="form-control" id="email" name="email">
                        <div class="text-danger fs-10 hidden" id="error-email"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div class="text-danger fs-10 hidden" id="error-password"></div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Registrarse</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('viewLogin') }}">¿Ya tienes cuenta? Inicia sesión</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const verificationEmail = (correo) => {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return regex.test(correo);
            }

            $('#registerForm').submit(function(event) {
                event.preventDefault();
                $('#error-name').text('').addClass('hidden');
                $('#error-lastName').text('').addClass('hidden');
                $('#error-email').text('').addClass('hidden');
                $('#error-password').text('').addClass('hidden');

                const name = $('#name').val();
                const lastName = $('#lastName').val();
                const email = $('#email').val();
                const password = $('#password').val();
                let hasError = false;

                if (!name) {
                    $('#error-name').text('Este campo es obligatorio').removeClass('hidden');
                    hasError = true;
                }

                if (!lastName) {
                    $('#error-lastName').text('Este campo es obligatorio').removeClass('hidden');
                    hasError = true;
                }

                if (!email) {
                    $('#error-email').text('Este campo es obligatorio').removeClass('hidden');
                    hasError = true;
                } else if (!verificationEmail(email)) {
                    $('#error-email').text('Este campo debe contener un correo válido.').removeClass(
                        'hidden');
                    hasError = true;
                }

                if (!password) {
                    $('#error-password').text('Este campo es obligatorio').removeClass('hidden');
                    hasError = true;
                }

                if (hasError)
                    return;

                $.ajax({
                    url: "{{ route('register') }}",
                    method: "POST",
                    data: {
                        name: name,
                        lastName: lastName,
                        email: email,
                        password: password,
                        _token: '{{ csrf_token() }}'
                    },
                    success: async function(response) {
                        await alertToast({
                            text: response.message,
                            icon: 'success'
                        });

                        document.location = '/';
                    },
                    error: async function(xhr) {
                        if (xhr?.responseJSON?.message)
                            await alertToast({
                                text: xhr.responseJSON.message,
                                icon: 'error'
                            });
                        else
                            await alertToast({
                                text: "Hubo un error inesperado",
                                icon: 'error'
                            });
                    }
                });
            });

        });
    </script>
@endpush
