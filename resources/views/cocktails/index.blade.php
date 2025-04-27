@extends('layouts.app')

@section('title', 'Cócteles')

@section('content')
    <div class="card m-5">
        <h2 class="text-center mb-4">Cócteles</h2>

        <div class="d-flex justify-content-end mb-3 me-3">
            <a href="{{ route('cocktails.saved') }}" class="btn btn-secondary">Ver cócteles Guardados</a>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-center flex-wrap">
                @foreach (range('A', 'Z') as $letterOption)
                    <a href="{{ route('cocktails.index', ['letter' => $letterOption]) }}"
                        class="btn btn-outline-primary m-1 letter-btn {{ $letterOption === $letter ? 'active' : '' }}">
                        {{ $letterOption }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="row m-3">
            @foreach ($cocktails as $cocktail)
                <div class="col-xs-12 col-xs-4 col-md-3 col-lg-3 col-xl-2 mb-3">
                    <div class="card d-flex flex-column" style="height: 100%;">
                        <img src="{{ $cocktail['strDrinkThumb'] }}" class="card-img-top" alt="{{ $cocktail['strDrink'] }}">
                        <div class="card-body d-flex flex-column pRelative">
                            <h5 class="card-title titleCards">
                                {{ $cocktail['strDrink'] }}
                                <div class="titleId ms-2">
                                    ({{ $cocktail['idDrink'] }})
                                </div>
                            </h5>
                            <p class="card-text justify mb-5">
                                {{ !empty($cocktail['strInstructionsES']) ? $cocktail['strInstructionsES'] : $cocktail['strInstructions'] }}
                            </p>

                            @if (empty($cocktail['is_saved']))
                                <button class="btn btn-primary save-cocktail w-25 abs" data-id="{{ $cocktail['idDrink'] }}"
                                    data-name="{{ $cocktail['strDrink'] }}"
                                    data-description="{{ !empty($cocktail['strInstructionsES']) ? $cocktail['strInstructionsES'] : $cocktail['strInstructions'] }}"
                                    data-image="{{ $cocktail['strDrinkThumb'] }}">
                                    <i class="mdi mdi-download"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.save-cocktail').on('click', function() {
                const button = $(this);

                $.ajax({
                    url: '/cocktails/store',
                    method: 'POST',
                    data: {
                        id: button.data('id'),
                        name: button.data('name'),
                        description: button.data('description'),
                        image_url: button.data('image'),
                        _token: '{{ csrf_token() }}'
                    },
                    success: async function(response) {
                        button.fadeOut();

                        await alertToast({
                            text: response.msg,
                            icon: 'success'
                        });
                    },
                    error: async function(xhr) {
                        if (xhr?.responseJSON?.message)
                            await alertToast({
                                text: xhr.responseJSON.message,
                                icon: 'error'
                            });
                        else
                            await alertToast({
                                text: "Hubo un error al guardar el cóctel",
                                icon: 'error'
                            });
                    }
                });
            });
        });
    </script>
@endpush
