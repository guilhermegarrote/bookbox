@extends('emails.layouts.master')

@section('content')
    <p class="email-text">
        Olá <strong>{{ $student_name }}</strong> (Turma: <strong>{{ $student_school_class }}</strong>),
    </p>

    <p class="email-text">
        Este é o seu comprovante de empréstimo referente ao livro abaixo:
    </p>

    <ul>
        <li><strong>Título:</strong> {{ $book_title }}</li>
        <li><strong>Autor:</strong> {{ $book_author }}</li>
        <li><strong>Exemplar:</strong> {{ $book_copy_number }}</li>
        <li><strong>Data do Empréstimo:</strong> {{ $loan_start_date }}</li>
        <li><strong>Data de Devolução:</strong> {{ $loan_due_date }}</li>
    </ul>

    <div class="email-highlight">
        Por favor, mantenha este e-mail como comprovante do empréstimo.
    </div>
@endsection
