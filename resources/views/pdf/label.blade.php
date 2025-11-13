<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Etiquetas BookBox</title>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Urbanist:wght@400;600;700&display=swap');

    :root {
      --color-teg-orange: #f39c00;
      --color-teg-blue: #0086f3;
      --color-bg: #2c2c2c;
      --color-text: #333;
      --color-muted: #888;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: var(--color-bg);
      font-family: "Urbanist", sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px;
    }

    .folha {
      width: 2300px;
      height: 3300px;
      background: #585858;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
    }

    .etiqueta {
      display: flex;
      background-color: #fff;
      width: 1145px;
      height: 295px;
      overflow: hidden;
      outline: 3px solid #000;
      position: relative;
      page-break-inside: avoid;
    }

    .lateral {
      background-color: var(--color-teg-orange);
      width: 90px;
      flex-shrink: 0;
    }
    .conteudo {
        display: flex;
        padding: 25px 50px;
        flex: 1;
        position: relative;
        align-items: center;
        gap: 100px;
    }
    .coluna-esquerda,
    .coluna-direita {
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 15px;
      color: var(--color-text);
    }
    .coluna-direita {
        flex-shrink: 0;
        margin-left: 0;
    }

    .rotulo {
      font-weight: 400;
      font-size: 25px;
      color: var(--color-muted);
      margin-bottom: 3px;
    }

    .dado {
      font-weight: 600;
      font-size: 36px;
      color: #000;
      line-height: 1.1;
    }

    .logo {
      position: absolute;
      right: 50px;
      bottom: 20px;
      font-size: 60px;
      font-weight: 700;
      color: var(--color-teg-orange);
      opacity: 0.9;
      letter-spacing: -1px;
    }
  </style>
</head>

<body>
  <div class="folha">
    @php
      for ($i = 0; $i < 11; $i++) {
    @endphp

    <div class="etiqueta">
      <div class="lateral"></div>
      <div class="conteudo">
        <div class="coluna-esquerda">
          <div>
            <div class="rotulo">Código ISBN</div>
            <div class="dado">978-85-7522-632-2</div>
          </div>
          <div>
            <div class="rotulo">Autor</div>
            <div class="dado">Marina Oliveira</div>
          </div>
          <div>
            <div class="rotulo">Categoria</div>
            <div class="dado">Educação</div>
          </div>
        </div>

        <div class="coluna-direita">
          <div>
            <div class="rotulo">Título</div>
            <div class="dado">A Jornada do Conhecimento</div>
          </div>
          <div>
            <div class="rotulo">Editora</div>
            <div class="dado">Nova Era</div>
          </div>
          <div>
            <div class="rotulo">Exemplar</div>
            <div class="dado">003</div>
          </div>
        </div>

        <div class="logo">bookbox</div>
      </div>
    </div>

    <div class="etiqueta">
      <div class="lateral"></div>
      <div class="conteudo">
        <div class="coluna-esquerda">
          <div>
            <div class="rotulo">Código ISBN</div>
            <div class="dado">978-85-1234-567-8</div>
          </div>
          <div>
            <div class="rotulo">Autor</div>
            <div class="dado">João Silva</div>
          </div>
          <div>
            <div class="rotulo">Categoria</div>
            <div class="dado">Tecnologia</div>
          </div>
        </div>

        <div class="coluna-direita">
          <div>
            <div class="rotulo">Título</div>
            <div class="dado">ADA</div>
          </div>
          <div>
            <div class="rotulo">Editora</div>
            <div class="dado">TechPress</div>
          </div>
          <div>
            <div class="rotulo">Exemplar</div>
            <div class="dado">004</div>
          </div>
        </div>

        <div class="logo">bookbox</div>
      </div>
    </div>

    @php
      }
    @endphp
  </div>
</body>
</html>
