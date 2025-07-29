<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Telas de Configuração</title>
    <style>
        body {
            font-family: 'Urbanist';
            color: #000;
            margin: 0;
            padding: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Urbanist';
        }

        .configTela-container {
            width: 400px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        .configTela-tabs {
            display: flex;
            background-color: #333;
        }

        .configTela-tab {
            flex: 1;
            padding: 10px;
            text-align: center;
            color: #ccc;
            cursor: pointer;
            background-color: #333;
        }

        .configTela-tab.active {
            background-color: #fff;
            color: #000;
            font-weight: bold;
        }

        .configTela-box {
            padding: 20px;
            display: none;
        }

        .configTela-box.active {
            display: block;
        }

        .configTela-search-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
                align-items: center;
        }

        .configTela-search-bar input {
            border-radius: 8px;
            border: 1px solid #000;
            flex: 1;
            padding: 10px;
        }

        .configTela-search-bar button {
            margin-left: 5px;
        }

        .configTela-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .configTela-table,
        .configTela-table th,
        .configTela-table td {
            border: 1px solid #ccc;
        }

        .configTela-table th,
        .configTela-table td {
            padding: 8px;
            text-align: left;
        }

        .configTela-icon-btn {
            margin-left: 5px;
            cursor: pointer;
        }

        .configTela-close-btn {
            color: white;
            font-size: 1.2em;
            width: 30px;
            height: 30px;
            border: none;
            background: red;
            border-radius: 50%;
            cursor: pointer;
            right: 2%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .configTela-label {
            display: block;
            margin-top: 15px;
        }

        .configTela-input-number {
            width: 100%;
            padding: 5px;
        }

        .configTela-button {
            margin-right: 5px;
            padding: 10px 10px;
            background: #fff;
            color: #000;
            border: 1px solid #000;
            border-radius: 8px;
            cursor: pointer;
        }

        .configTela-button:hover {
            background: #282828;
            color: #fff;
            border: 1px solid #fff;
            transition: 1s ease;
        }
    </style>
</head>

<body>
    <div class="configTela-container">
        <div class="configTela-tabs">
            <div class="configTela-tab active" onclick="switchTab(event, 'genero')">Gerenciar Gêneros</div>
            <div class="configTela-tab" onclick="switchTab(event, 'turmas')">Gerenciar Turmas</div>
            <div class="configTela-tab" onclick="switchTab(event, 'usuarios')">Gerenciar Usuários</div>
            <div class="configTela-tab" onclick="switchTab(event, 'config')">Configurações</div>
        </div>

        <!-- Gêneros -->
        <div id="genero" class="configTela-box active">
            <div class="configTela-search-bar">
                <input type="text" placeholder="Pesquisar">
                <button class="configTela-button">Adicionar Gênero</button>
                <span class="configTela-close-btn">✖</span>
            </div>
            <table class="configTela-table">
                <tr>
                    <th>Nome do Gênero</th>
                    <th>Ações</th>
                </tr>
                <tr>
                    <td>Ficção</td>
                    <td><span class="configTela-icon-btn">✎</span></td>
                </tr>
                <tr>
                    <td>Terror</td>
                    <td><span class="configTela-icon-btn">✎</span></td>
                </tr>
                <tr>
                    <td>Romance</td>
                    <td><span class="configTela-icon-btn">✎</span></td>
                </tr>
            </table>
        </div>

        <!-- Turmas -->
        <div id="turmas" class="configTela-box">
            <div class="configTela-search-bar">
                <input type="text" placeholder="Pesquisar">
                <button class="configTela-button">Adicionar Turma</button>
                <span class="configTela-close-btn">✖</span>
            </div>
            <table class="configTela-table">
                <tr>
                    <th>Nome das Turmas</th>
                    <th>Ações</th>
                </tr>
                <tr>
                    <td>Turma A</td>
                    <td><span class="configTela-icon-btn">✎</span></td>
                </tr>
                <tr>
                    <td>Turma B</td>
                    <td><span class="configTela-icon-btn">✎</span></td>
                </tr>
            </table>
        </div>

        <!-- Usuários -->
        <div id="usuarios" class="configTela-box">
            <div class="configTela-search-bar">
                <input type="text" placeholder="Pesquisar">
                <button class="configTela-button">Adicionar Usuário</button>
                <span class="configTela-close-btn">✖</span>
            </div>
            <table class="configTela-table">
                <tr>
                    <th>Nome do Usuário</th>
                    <th>Ações</th>
                </tr>
                <tr>
                    <td>João</td>
                    <td><span class="configTela-icon-btn">✎</span></td>
                </tr>
                <tr>
                    <td>Maria</td>
                    <td><span class="configTela-icon-btn">✎</span></td>
                </tr>
            </table>
        </div>

        <!-- Configurações -->
        <div id="config" class="configTela-box">
            <form>
                <label class="configTela-label" for="example-number">Exemplo Número</label>
                <input class="configTela-input-number" type="number" id="example-number" name="example-number">
                <button type="submit" class="configTela-button">Salvar</button>
            </form>
        </div>
    </div>

    <script>
        function switchTab(event, tabId) {
            const tabs = document.querySelectorAll('.configTela-tab');
            const boxes = document.querySelectorAll('.configTela-box');

            tabs.forEach(tab => tab.classList.remove('active'));
            boxes.forEach(box => box.classList.remove('active'));

            event.currentTarget.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }
    </script>
</body>

</html>
