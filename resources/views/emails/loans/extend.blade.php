@extends('emails.layouts.master')

@section('content')
    <p class="email-text">
        Olá <strong>{{ $student_name }}</strong>,<br />
        Você está matriculado(a) na <strong>{{ $student_school_class }}</strong>.
    </p>

    <p class="email-text">
        A data de devolução do seu empréstimo foi <strong>prorrogada com sucesso</strong>.
    </p>

    <p class="email-text">
        Seguem os detalhes atualizados do empréstimo:
    </p>

    <ul>
        <li><strong>ISBN:</strong> {{ $book_isbn }}</li>
        <li><strong>Título:</strong> {{ $book_title }}</li>
        <li><strong>Autor:</strong> {{ $book_author }}</li>
        <li><strong>Exemplar nº:</strong> {{ $book_copy_number }}</li>
        <li><strong>Data do Empréstimo:</strong> {{ $loan_start_date }}</li>
        <li><strong>Antiga Data de Devolução:</strong> {{ $loan_old_due_date }}</li>
    </ul>

    <p class="highlight-due-date">
        <strong>Nova data prevista para devolução:</strong> <span class="due-date">{{ $loan_new_due_date }}</span>
    </p>

    <div class="email-highlight">
        <p>Guarde este e-mail como comprovante do seu empréstimo.</p>
    </div>
@endsection
