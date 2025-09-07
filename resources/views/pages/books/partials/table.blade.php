@if ($books->isEmpty())
    <div class="data-empty">
        <h1>Nenhum livro encontrado.</h1>
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
                '<span>' .
                e($label) .
                ' ' .
                $icon .
                '</span></a>';
        }
    @endphp

    <table class="data-table" role="table" aria-label="Tabela de livros">
        <thead>
            <tr>
                <th scope="col">Disponiveis</th>
                <th scope="col">ISBN</th>
                <th scope="col">{!! sortLink('title', 'Titulo') !!}</th>
                <th scope="col">{!! sortLink('author', 'Autor') !!}</th>
                <th scope="col">Genero Nome</th>
                <th scope="col">{!! sortLink('publishe', 'Editora') !!}</th>
                <th scope="col" class="button-col"><button class="btn-dark btn-add"><x-icons.icon name="plus" class="" /></button></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($books as $book)
                <tr data-book-id="{{ $book->book_id }}">
                    <td>{{ $book->available }}</td>
                    <td>{{ $book->isbn }}</td>
                    <td>{{ $book->title }}</td>
                    <td>{{ $book->author }}</td>
                    <td>{{ $book->genre_name }}</td>
                    <td>{{ $book->publisher }}</td>
                    <td class="button-col"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
