@foreach ($classes as $class)
    @if ($class->students_count > 0)
        @php
            $termLabel = $class->term === 'Annual' ? 'ano' : 'semestre';

            $studentLabel = $class->students_count === 1 ? 'aluno' : 'alunos';
        @endphp

        <div class="settings-card improved-class-card">
            <div>
                <strong>{{ $class->course }}</strong><br>
                <small>
                    {{ $class->period }}° {{ $termLabel }}
                    @if ($class->end_date < now())
                        - Encerrado em {{ \Carbon\Carbon::parse($class->end_date)->format('d/m/Y') }}
                    @endif
                </small>
            </div>

            @if ($class->end_date >= now())
                <div class="settings-actions">
                    <button data-icon="pencil" class="btn-list-config" title="Editar turma">
                        <x-icons.icon name="pencil" />
                    </button>
                </div>
            @else
                <strong>{{ $class->students_count }} {{ $studentLabel }} com empréstimos</strong>
            @endif
        </div>
    @endif
@endforeach
