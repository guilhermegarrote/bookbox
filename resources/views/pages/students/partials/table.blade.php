@if ($students->isEmpty())
    <div class="data-empty">
        <h1 title="Nenhum aluno encontrado">Nenhum aluno encontrado.</h1>
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

    <table class="data-table" role="table" aria-label="Tabela de alunos">
        <thead>
            <tr>
                <th scope="col" class="status-col" title="Status do aluno">Status</th>
                <th scope="col">{!! sortLink('name', 'Nome') !!}</th>
                <th scope="col" title="Email do aluno">Email</th>
                <th scope="col" title="Telefone do aluno">Telefone</th>
                <th scope="col">{!! sortLink('formatted_class_name', 'Turma') !!}</th>
                <th scope="col" class="button-col">
                    <button class="btn-dark btn-add" title="Adicionar aluno">
                        <x-icons.icon name="plus" />
                    </button>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr data-student-id="{{ $student->student_id }}">
                    <td class="status-col">
                        @if ($student->can_borrow)
                            <x-icons.icon name="status-active" class="status-icon status-active"
                                title="Aluno ativo para empréstimos" />
                        @else
                            <x-icons.icon name="status-blocked" class="status-icon status-blocked"
                                title="Aluno bloqueado para empréstimos" />
                        @endif
                    </td>
                    <td title="Nome do aluno: {{ $student->name }}">{{ $student->name }}</td>
                    <td title="Email do aluno: {{ $student->email }}">{{ $student->email }}</td>
                    <td title="Telefone do aluno: {{ $student->phone }}">{{ $student->phone }}</td>
                    <td title="Turma: {{ $student->formatted_class_name }}">{{ $student->formatted_class_name }}</td>
                    <td class="button-col"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
