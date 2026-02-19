@extends('emails.layouts.master')

@section('content')
    <p class="email-text">
        Olá <strong>{{ $student_name }}</strong>,<br />
        Você está matriculado(a) no <strong>{{ $student_school_class }}</strong>.
    </p>

    <p class="email-text">
        O livro abaixo está atualmente sob seu empréstimo:
    </p>

    <ul>
        <li><strong>ISBN:</strong> {{ $book_isbn }}</li>
        <li><strong>Título:</strong> {{ $book_title }}</li>
        <li><strong>Autor:</strong> {{ $book_author }}</li>
        <li><strong>Exemplar nº:</strong> {{ $book_copy_number }}</li>
    </ul>

    <p class="email-text">
        O empréstimo foi realizado em <strong>{{ $loan_start_date }}</strong> e a devolução está prevista para
        <strong>{{ $loan_due_date }}</strong>.
    </p>

    <div class="highlight-remaining">
        Resta{{ $loan_remaining_days > 1 ? 'm' : '' }}
        <strong>{{ $loan_remaining_days }} dia{{ $loan_remaining_days > 1 ? 's' : '' }}</strong> até a data limite de
        devolução.<br>
        Solicitamos que a entrega seja realizada dentro do prazo.
    </div>

    <p class="email-highlight">
        Caso o livro já tenha sido devolvido, por favor, desconsidere este aviso.
    </p>
@endsection
