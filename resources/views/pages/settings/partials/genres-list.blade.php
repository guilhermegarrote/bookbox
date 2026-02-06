@foreach ($genres as $genre)
    @php
        $hasNoBooksOrCopies = $genre->books_count == 0 && $genre->copies_count == 0;

        $booksLabel = $genre->books_count === 1 ? 'livro' : 'livros';
        $copiesLabel = $genre->copies_count === 1 ? 'exemplar' : 'exemplares';
    @endphp

    <div class="settings-card" style="padding: 0;">
        <div style="display: flex; align-items: center; gap: 16px; height: 100%;">
            <span
                style="
                    width: 25px;
                    height: 100%;
                    background-color: #{{ $genre->color_hex }};
                    display: inline-block;
                    border-radius: 0.5rem 0 0 0.5rem;
                ">
            </span>

            <div>
                <strong>{{ $genre->name }}</strong><br>

                @if ($hasNoBooksOrCopies)
                    <small>Sem livros ou exemplares</small>
                @else
                    <small>
                        {{ $genre->books_count }} {{ $booksLabel }}
                        |
                        {{ $genre->copies_count }} {{ $copiesLabel }}
                    </small>
                @endif
            </div>
        </div>

        <div class="settings-actions" style="margin: 1rem">
            <button data-icon="pencil" class="btn-list-config" title="Editar gênero">
                <x-icons.icon name="pencil" />
            </button>

            @if ($hasNoBooksOrCopies)
                <button data-icon="trash" class="btn-list-config" title="Excluir gênero">
                    <x-icons.icon name="trash" />
                </button>
            @endif
        </div>
    </div>
@endforeach
