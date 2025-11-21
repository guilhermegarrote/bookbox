@extends('emails.layouts.master')

@section('content')

<p class="email-text">
    Olá <strong>{{ $name }}</strong>,
</p>

<p class="email-text">
    Seu código de recuperação é:
</p>

<div class="email-code">
    {{ $code }}
</div>

<p class="email-text">
    Digite este código na página de redefinição para criar uma nova senha.<br>
    Caso você não tenha solicitado a recuperação, desconsidere esta mensagem.
</p>

@endsection
