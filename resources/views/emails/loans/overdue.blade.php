@extends('emails.layouts.master')

@section('content')
    <p class="email-text">
        Olá <strong>{{ $student_name }}</strong>,<br />
        Você está matriculado(a) na <strong>{{ $student_school_class }}</strong>.
    </p>

    <p class="email-text">
        Identificamos que o livro abaixo encontra-se em atraso:
    </p>

    <ul>
        <li><strong>ISBN:</strong> {{ $book_isbn }}</li>
        <li><strong>Título:</strong> {{ $book_title }}</li>
        <li><strong>Autor:</strong> {{ $book_author }}</li>
        <li><strong>Exemplar nº:</strong> {{ $book_copy_number }}</li>
    </ul>

    <p class="email-text">
        O empréstimo foi realizado em <strong>{{ $loan_start_date }}</strong> e a devolução estava prevista para
        <strong>{{ $loan_due_date }}</strong>.
    </p>

    <div class="highlight-overdue">
        O empréstimo está com <strong>{{ $loan_days_late }} dia{{ $loan_days_late > 1 ? 's' : '' }} de atraso</strong>.<br>
        Solicitamos que a devolução seja realizada o quanto antes.
    </div>

    <p class="email-highlight">
        Caso o livro já tenha sido devolvido, por favor, desconsidere este aviso.
    </p>
@endsection
