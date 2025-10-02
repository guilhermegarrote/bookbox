@if ($loans->isEmpty())
    <div class="data-empty">
        <h1 title="Nenhum empréstimo encontrado">Nenhum empréstimo encontrado.</h1>
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

    <table class="data-table" role="table" aria-label="Tabela de empréstimos">
        <thead>
            <tr>
                <th scope="col">{!! sortLink('title', 'Livro') !!}</th>
                <th scope="col" title="Número do exemplar">Exemplar</th>
                <th scope="col">{!! sortLink('author', 'Autor') !!}</th>
                <th scope="col">{!! sortLink('name', 'Estudante') !!}</th>
                <th scope="col">{!! sortLink('loan_due_date', 'Data de vencimento') !!}</th>
                <th scope="col" class="button-col">
                    <button class="btn-dark btn-add" title="Adicionar novo empréstimo">
                        <x-icons.icon name="plus" class="" />
                    </button>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loans as $loan)
                @php
                    $isActive = is_null($loan->loan_returned_date);
                @endphp
                <tr data-loan-id="{{ $loan->id }}"
                    @if (!$isActive) style="background-color: #f5f5f5; color: #999; opacity: 0.6;" @endif>
                    <td title="Livro: {{ $loan->title }}">{{ $loan->title }}</td>
                    <td title="Exemplar número: {{ $loan->number }}">{{ $loan->number }}</td>
                    <td title="Autor: {{ $loan->author }}">{{ $loan->author }}</td>
                    <td title="Estudante: {{ $loan->name }}">{{ $loan->name }}</td>
                    <td title="Data de vencimento: {{ $loan->loan_due_date }}">{{ $loan->loan_due_date }}</td>
                    <td class="button-col"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
