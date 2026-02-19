@extends('emails.layouts.master')

@section('content')
    <p class="email-text">
        Olá <strong>{{ $student_name }}</strong>,<br />
        Você está matriculado(a) no <strong>{{ $student_school_class }}</strong>.
    </p>

    <p class="email-text">
        Abaixo está o comprovante de empréstimo do livro solicitado:
    </p>

    <ul>
        <li><strong>ISBN:</strong> {{ $book_isbn }}</li>
        <li><strong>Título:</strong> {{ $book_title }}</li>
        <li><strong>Autor:</strong> {{ $book_author }}</li>
        <li><strong>Exemplar nº:</strong> {{ $book_copy_number }}</li>
        <li><strong>Data do empréstimo:</strong> {{ $loan_start_date }}</li>
    </ul>

    <p class="highlight-due-date">
        <strong>Data prevista para devolução:</strong> <span class="due-date">{{ $loan_due_date }}</span>
    </p>

    <div class="email-highlight">
        <p>Guarde este e-mail como comprovante do seu empréstimo.</p>
    </div>
@endsection
