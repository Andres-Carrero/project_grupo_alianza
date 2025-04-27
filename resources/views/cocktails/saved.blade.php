@extends('layouts.app')

@section('title', 'Cócteles Guardados')

@section('content')
    <div class="card m-3">
        <h2 class="text-center">Cócteles Guardados</h2>
        <div class="m-3">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('cocktails.index') }}" class="btn btn-secondary">Volver a buscar cócteles</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>Imagen</th>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Fecha de Creación</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cocktail-table-body">
                        @forelse ($cocktails as $cocktail)
                            <tr id="cocktail-row-{{ $cocktail->id }}">
                                <td class="d-flex justify-content-center">
                                    <img src="{{ $cocktail->image_url }}" alt="{{ $cocktail->name }}" width="60"
                                        class="rounded">
                                </td>
                                <td>{{ $cocktail->drink_id }}</td>
                                <td>
                                    <span class="cocktail-name" data-id="{{ $cocktail->id }}">{{ $cocktail->name }}</span>
                                </td>
                                <td class="cocktail-description" data-id="{{ $cocktail->id }}">{{ $cocktail->description }}
                                </td>
                                <td>{{ $cocktail->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $cocktail->user->name ?? 'Desconocido' }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm edit-cocktail" data-id="{{ $cocktail->id }}">
                                        Editar
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-cocktail" data-id="{{ $cocktail->id }}">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay cócteles guardados aún.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.edit-cocktail').on('click', function() {
                const button = $(this);
                const cocktailId = button.data('id');
                const name = button.closest('tr').find('.cocktail-name').text();
                const description = button.closest('tr').find('.cocktail-description').text();

                const editForm = `
                    <tr id="edit-row-${cocktailId}">
                        <td colspan="7">
                            <form id="edit-cocktail-form-${cocktailId}">
                                <div class="mb-2">
                                    <input type="text" class="form-control" name="name" value="${name}">
                                </div>
                                <div class="mb-2">
                                    <textarea class="form-control" name="description">${description}</textarea>
                                </div>
                                <div class="mb-2">
                                    <button type="submit" class="btn btn-success">Guardar cambios</button>
                                    <button type="button" class="btn btn-secondary cancel-edit">Cancelar</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                `;

                $(`#cocktail-row-${cocktailId}`).after(editForm);
                button.prop('disabled', true);
            });

            $(document).on('click', '.cancel-edit', function() {
                const cocktailId = $(this).closest('tr').prev().find('.edit-cocktail').data('id');
                $(`#edit-row-${cocktailId}`).remove();
                $(`#cocktail-row-${cocktailId}`).find('.edit-cocktail').prop('disabled', false);
            });

            $(document).on('submit', 'form[id^="edit-cocktail-form-"]', function(event) {
                event.preventDefault();

                const cocktailId = $(this).closest('form').attr('id').split('-')[3];
                const name = $(this).find('[name="name"]').val();
                const description = $(this).find('[name="description"]').val();

                $.ajax({
                    url: `/cocktails/${cocktailId}`,
                    method: 'PUT',
                    data: {
                        name: name,
                        description: description,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $(`#cocktail-row-${cocktailId}`).find('.cocktail-name').text(name);
                        $(`#cocktail-row-${cocktailId}`).find('.cocktail-description').text(description);

                        $(`#edit-row-${cocktailId}`).remove();

                        alertToast({
                            text: response.msg,
                            icon: 'success'
                        });
                    },
                    error: function(xhr) {
                        alertToast({
                            text: "Hubo un error al guardar los cambios.",
                            icon: 'error'
                        });
                    }
                });
            });

            $('.delete-cocktail').on('click', function() {
                const cocktailId = $(this).data('id');

                alertConfirm({
                    text: '¿Estás seguro de que deseas eliminar este cóctel?'
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: `/cocktails/${cocktailId}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                $(`#cocktail-row-${cocktailId}`).remove();

                                if ($('#cocktail-table-body').children().length === 0) {
                                    $('#cocktail-table-body').html('<tr><td colspan="7" class="text-center">No hay cócteles guardados aún.</td></tr>');
                                }

                                alertToast({
                                    text: response.msg,
                                    icon: 'success'
                                });
                            },
                            error: function(xhr) {
                                alertToast({
                                    text: "Hubo un error al eliminar el cóctel.",
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
