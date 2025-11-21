@extends('emails.layouts.master')

@section('content')
    <p class="email-text">
        Olá <strong>{{ $student_name }}</strong> (Turma: <strong>{{ $student_school_class }}</strong>),
    </p>

    <p class="email-text">
        A data de devolução do seu empréstimo foi <strong>prorrogada com sucesso</strong>.
    </p>

    <p class="email-text">
        Seguem os detalhes atualizados do empréstimo:
    </p>

    <ul>
        <li><strong>Título:</strong> {{ $book_title }}</li>
        <li><strong>Autor:</strong> {{ $book_author }}</li>
        <li><strong>Exemplar:</strong> {{ $book_copy_number }}</li>
        <li><strong>Data do Empréstimo:</strong> {{ $loan_start_date }}</li>
        <li><strong>Antiga Data de Devolução:</strong> {{ $loan_old_due_date }}</li>
        <li><strong>Nova Data de Devolução:</strong> {{ $loan_new_due_date }}</li>
    </ul>

    <div class="email-highlight">
        Por favor, mantenha este e-mail como comprovante da prorrogação.
    </div>

    <p class="email-text">
        Caso tenha qualquer dúvida, procure a biblioteca.
    </p>
@endsection
