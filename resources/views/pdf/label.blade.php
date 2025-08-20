<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta de Empréstimo de Livro</title>
    <style>
        .etiqueta {
            width: 300px;
            height: 250px;
            border: 2px solid #000;
            padding: 15px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .campo {
            margin-bottom: 10px;
        }
        .campo span {
            font-weight: bold;
        }
        .titulo {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        .linha {
            border-top: 1px dashed #ccc;
            margin-top: 10px;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<div class="etiqueta">
    <div class="titulo">Empréstimo de Livro</div>
    <div class="campo"><span>Título do Livro:</span> <span id="nome-livro"></span>Leviatã</div>
    <div class="campo"><span>Nome do Aluno:</span> <span id="nome-aluno">Ana Júlia Miura</span></div>
    <div class="campo"><span>ISBN:</span> <span id="isbn">978-3-16-148410-0</span></div>
    <div class="campo"><span>Número do Exemplar:</span> <span id="num-exemplar">003</span></div>
    <div class="campo"><span>Data do Empréstimo:</span> <span id="data-emprestimo">15/08/2025</span></div>
    <div class="campo"><span>Data da Devolução:</span> <span id="data-devolucao">30/08/2025</span></div>
    <div class="linha"></div>
</div>

</body>
</html>
