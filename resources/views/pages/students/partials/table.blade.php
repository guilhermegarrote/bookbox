@push('styles')
    @vite('resources/css/components/table.css')
@endpush

@if ($students->isEmpty())
    <div class="data-empty">
        <h1>Nenhum aluno encontrado.</h1>
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

    <table class="data-table" role="table" aria-label="Tabela de alunos">
        <thead>
            <tr>
                <th scope="col" class="status-col">Status</th>
                <th scope="col">{!! sortLink('name', 'Nome') !!}</th>
                <th scope="col">Email</th>
                <th scope="col">Telefone</th>
                <th scope="col">{!! sortLink('formatted_class_name', 'Turma') !!}</th>
                <th scope="col" class="button-col"><button class="btn-dark btn-add"><x-icons.icon name="plus" class="" /></button></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr data-student-id="{{ $student->student_id }}">
                    <td class="status-col">
                        @if ($student->can_borrow)
                            <x-icons.icon name="status-active" class="status-icon status-active" />
                        @else
                            <x-icons.icon name="status-blocked" class="status-icon status-blocked" />
                        @endif
                    </td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->phone }}</td>
                    <td>{{ $student->formatted_class_name }}</td>
                    <td class="button-col"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
