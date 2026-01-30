@php
    use Carbon\Carbon;

    $loggedUserName = auth()->user()?->name ?? 'Biblioteca';

    $phone = preg_replace('/\D/', '', $loan->phone);

    $startDate = Carbon::parse($loan->loan_start_date);
    $dueDate = Carbon::parse($loan->loan_due_date);
    $today = Carbon::today();

    $loanDuration = max(1, $startDate->diffInDays($dueDate));

    $daysLate = $today->greaterThan($dueDate) ? $dueDate->diffInDays($today) : 0;

    $lateRatio = $daysLate / $loanDuration;

    $header = "Olá, {$loan->name}!";

    $loanInfo =
        "* *Empréstimo:* {$loan->barcode_code}\n" .
        "* *Livro:* {$loan->title}\n" .
        "* *Autor:* {$loan->author}\n" .
        "* *Data do empréstimo:* {$loan_start_date}\n" .
        "* *Data prevista para devolução:* {$loan_due_date}\n\n";

    $signature = "\n\nAtenciosamente,\n" . "{$loggedUserName}";

    if ($daysLate <= 0) {
        $body =
            "\n\n" .
            "Este é apenas um lembrete referente ao empréstimo do livro abaixo:\n\n" .
            $loanInfo .
            'Pedimos, por gentileza, que realize a devolução até a data informada ' .
            'ou nos avise caso precise de prorrogação.';
    } elseif ($lateRatio <= 0.25) {
        $body =
            "\n\n" .
            "Identificamos que o prazo de devolução do livro abaixo venceu recentemente:\n\n" .
            $loanInfo .
            'Pedimos, por gentileza, que realize a devolução o quanto antes ou ' .
            'entre em contato caso necessite de prorrogação.';
    } elseif ($lateRatio <= 0.75) {
        $body =
            "\n\n" .
            "Consta em nosso sistema que o livro abaixo encontra-se em atraso:\n\n" .
            $loanInfo .
            'Solicitamos a devolução o quanto antes ou que entre em contato com a biblioteca ' .
            'para regularizar a situação.';
    } else {
        $body =
            "\n\n" .
            'Até o momento, não registramos a devolução do livro abaixo, ' .
            "cujo prazo de devolução já foi excedido de forma significativa:\n\n" .
            $loanInfo .
            'Solicitamos que entre em contato com a biblioteca com urgência ' .
            'para regularização da pendência.';
    }

    $message = rawurlencode($header . $body . $signature);
@endphp

<x-modals.modal id="loanMenuModal" title="Menu do Empréstimo {{ $loan->loan_returned_date ? '(Finalizado)' : '' }}">

    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome do estudante" width="450px" :value="$loan->name" readonly
                title="Nome do estudante" />
            <x-modals.input-field id="cpf" label="CPF" width="150px" :value="$loan->cpf" readonly
                title="CPF do aluno" />
            <x-modals.input-field id="formatted_class_name" label="Turma" width="650px" :value="$loan->formatted_class_name" readonly
                title="Turma do aluno" />
            <x-modals.input-field id="isbn" label="Código ISBN" width="160px" :value="$loan->isbn" readonly
                title="Código ISBN do livro" />
            <x-modals.input-field id="title" label="Título" width="440px" :value="$loan->title" readonly
                title="Título do livro" />
            <x-modals.input-field id="number" label="Exemplar" width="120px" :value="$loan->number" readonly
                title="Número do exemplar" />
            <x-modals.input-field id="author" label="Autor" width="480px" :value="$loan->author" readonly
                title="Autor do livro" />
            <div class="form-group" style="width: 200px; display: flex; align-items: center;">
                <input type="text" id="genre_name" name="genre_name" class="form-input"
                    value="{{ $loan->genre_name }}" readonly
                    style="width: 165px; border-radius: 6px 0 0 6px;" title="Gênero do livro">
                <label class="form-label" for="genre_name"
                    style="display: flex; justify-content: space-between; align-items: center; padding-left: 8px;">
                    <span>Gênero do livro</span>
                </label>
                <div id="genre-color-preview"
                    style="width: 35px; height: 35px; background-color: {{ '#' . ($loan->genre_color_hex ?? 'ccc') }}; border: 1px solid #000; border-left: none; border-radius: 0 6px 6px 0;"
                    title="Cor do gênero"></div>
            </div>
            <x-modals.input-field id="loan_start_date" label="Data de empréstimo" width="195px" :value="$loan_start_date"
                readonly title="Data do empréstimo do livro" />
            <x-modals.input-field id="loan_due_date" label="Data da devolução" width="195px" :value="$loan_due_date" readonly
                title="Data da devolução do livro" />
        </div>
    </x-slot>

    <x-slot name="footer">
        @unless ($loan->loan_returned_date)
            <button type="button" class="modal-button" id="submit-finalize"
                title="Finalizar este empréstimo" aria-label="Finalizar empréstimo">Finalizar</button>
            <button type="button" class="modal-button" id="open-extend-modal" title="Prorrogar devolução" aria-label="Prorrogar devolução">Prorrogar
                devolução</button>

            <a href="https://web.whatsapp.com/send?phone=55{{ $phone }}&text={{ $message }}" target="_blank"
                rel="noopener noreferrer" class="modal-button" style="text-decoration: none;"
                title="Avisar aluno pelo WhatsApp">
                Avisar no WhatsApp
            </a>
        @endunless

        <button type="button" class="modal-button" id="btn-close" title="Fechar o menu do empréstimo" aria-label="Fechar o menu do empréstimo">Fechar</button>
    </x-slot>
</x-modals.modal>
