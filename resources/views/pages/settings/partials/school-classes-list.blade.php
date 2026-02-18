@foreach ($schoolClasses as $schoolClass)
    @if ($schoolClass->students_count != 0 || $schoolClass->end_date >= now())
        @php
            $termLabel = $schoolClass->term === 'Annual' ? 'ano' : 'semestre';

            $studentLabel = $schoolClass->students_count === 1 ? 'aluno' : 'alunos';
        @endphp

        <div class="settings-card improved-class-card" data-id="{{ $schoolClass->id }}">
            <div>
                <strong>{{ $schoolClass->period }}° {{ $termLabel }} - {{ $schoolClass->course }}</strong><br>
                <small>
                    @if ($schoolClass->end_date < now())
                        Encerrado em {{ \Carbon\Carbon::parse($schoolClass->end_date)->format('d/m/Y') }}
                    @else
                        {{ \Carbon\Carbon::parse($schoolClass->start_date)->format('d/m/Y') }} →
                        {{ \Carbon\Carbon::parse($schoolClass->end_date)->format('d/m/Y') }}
                    @endif
                </small>
                <br>
                <small>
                    @if ($schoolClass->students_count == 0)
                        Sem alunos
                    @else
                        {{ $schoolClass->students_count }} {{ $studentLabel }}
                    @endif
                </small>
            </div>

            @if ($schoolClass->end_date >= now())
                <div class="settings-actions">
                    <button class="btn-list-config" data-action="edit" title="Editar turma">
                        <x-icons.icon name="pencil" />
                    </button>
                    @if ($schoolClass->students_count == 0)
                        <button class="btn-list-config" data-action="delete" title="Excluir turma">
                            <x-icons.icon name="trash" />
                        </button>
                    @endif
                </div>
            @else
                <strong>{{ $schoolClass->students_count }} {{ $studentLabel }} com empréstimos</strong>
            @endif
        </div>
    @endif
@endforeach
