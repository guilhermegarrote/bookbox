@if ($loans->isEmpty())
    <div class="data-empty">
        <h1>Nenhum empréstimo encontrado.</h1>
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

    <table class="data-table" role="table" aria-label="Tabela de emprestimos">
        <thead>
            <tr>
                <th scope="col">{!! sortLink('student_id', 'Estudante') !!}</th>
                <th scope="col">Copia</th>
                <th scope="col">Data de inicio</th>
                <th scope="col">Data de vencimento </th>
                <th scope="col">Data de Retorno</th>
                <th scope="col">Ativo</th>
                <th scope="col" class="button-col"><button class="btn-dark btn-add"><x-icons.icon name="plus" class="" /></button></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loans as $loans)
                <tr data-loans-id="{{ $loans->loans_id }}">
                    <td>{{ $loans->student_id }}</td>
                    <td>{{ $loans->copy_id }}</td>
                    <td>{{ $loans->start_date }}</td>
                    <td>{{ $loans->due_date }}</td>
                    <td>{{ $loans->returned_date }}</td>
                    <td>{{ $loans->active }}</td>
                    <td class="button-col"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<!--isbn title author number student-name formatted-class-name dias de vencimento -->