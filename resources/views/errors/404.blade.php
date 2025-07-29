<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Erro 404 - Página não encontrada</title>
    @vite(['resources/css/pages/404.css'])
</head>

<body>
    <main class="error-page" role="main" aria-labelledby="error-page-title">
        <h1 id="error-page-title" class="error-page__title">
            Ops! Página não encontrada.
        </h1>

        <p class="error-page__subtitle">
            A página que você procura pode ter sido removida, renomeada ou está temporariamente indisponível.
        </p>

        <img
            src="{{ asset('images/errors/error-404-sign.svg') }}"
            alt="Imagem decorativa: Erro 404"
            class="error-page__image"
            role="presentation" />

        <a href="{{ route('loans.view') }}" class="error-page__button">
            Voltar à página inicial
        </a>
    </main>
</body>

</html>
