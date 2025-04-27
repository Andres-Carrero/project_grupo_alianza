@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="d-flex align-items-center h-100 justify-content-center">
        <div class="card p-4 shadow-sm" style="width: 22rem;">
            <div class="card-body">
                <h5 class="card-title text-center mb-4">Iniciar Sesión</h5>

                <form id="loginForm">
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
                        <button type="submit" class="btn btn-primary">Ingresar</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <a href="/register">¿Aun no te has registrado?</a>
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

            $('#loginForm').submit(function(event) {
                event.preventDefault();
                $('#error-email').text('').addClass('hidden');
                $('#error-password').text('').addClass('hidden');

                const email = $('#email').val();
                const password = $('#password').val();
                let hasError = false;

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
                    url: "{{ route('login') }}",
                    method: "POST",
                    data: {
                        email: email,
                        password: password,
                        _token: '{{ csrf_token() }}'
                    },
                    success: async function(response) {
                        await alertToast({
                            text: response.message,
                            icon: 'success'
                        });

                        window.location.href = '/cocktails';
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
