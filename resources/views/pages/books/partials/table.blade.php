@if ($books->isEmpty())
    <div class="data-empty">
        <h1 title="Nenhum livro encontrado">Nenhum livro encontrado.</h1>
    </div>
@else
    @php
        function sortLink($column, $label)
        {
            $isCurrent = request('sort') === $column;
            $currentDirection = request('direction', 'asc');
            $newDirection = $isCurrent && $currentDirection === 'asc' ? 'desc' : 'asc';
            $icon = $isCurrent ? ($currentDirection === 'asc' ? '↑' : '↓') : '';
            $query = array_merge(request()->all(), ['sort' => $column, 'direction' => $newDirection]);
            $url = request()->url() . '?' . http_build_query($query);

            return '<a href="' .
                e($url) .
                '" class="sort-link" data-column="' .
                e($column) .
                '" data-direction="' .
                e($newDirection) .
                '" title="Ordenar por ' .
                e($label) .
                ' (' .
                e($newDirection) .
                ')">' .
                '<span title="Ordenar por ' .
                e($label) .
                '">' .
                e($label) .
                ' ' .
                $icon .
                '</span></a>';
        }
    @endphp

    <table class="data-table" role="table" aria-label="Tabela de livros">
        <thead>
            <tr>
                <th scope="col" class="copy-col" title="Quantidade de livros disponíveis">Disponíveis</th>
                <th scope="col" title="Número ISBN do livro">ISBN</th>
                <th scope="col">{!! sortLink('title', 'Título') !!}</th>
                <th scope="col">{!! sortLink('author', 'Autor') !!}</th>
                <th scope="col" title="Gênero do livro">Gênero</th>
                <th scope="col">{!! sortLink('publisher', 'Editora') !!}</th>
                <th scope="col" class="button-col">
                    <button class="btn-dark btn-add" title="Adicionar novo livro">
                        <x-icons.icon name="plus" class="" />
                    </button>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($books as $book)
                <tr data-book-id="{{ $book->id }}">
                    <td class="copy-col" title="Disponíveis: {{ $book->available_copies }}/{{ $book->total_copies }}">
                        {{ $book->available_copies }}
                    </td>
                    <td title="ISBN: {{ $book->isbn }}">{{ $book->isbn }}</td>
                    <td title="Título: {{ $book->title }}">{{ $book->title }}</td>
                    <td title="Autor: {{ $book->author }}">{{ $book->author }}</td>
                    <td title="Gênero: {{ $book->genre_name }}">
                        {{ $book->genre_name }}
                    </td>
                    <td title="Editora: {{ $book->publisher }}">{{ $book->publisher }}</td>
                    <td class="button-col"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
