<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="containerTI">
        <div class="contentTI">
            <div class="tableContainerTI">
                <table class="tableTI">
                    <thead>
                        <tr>
                            <th> <button class="selectAllBtn" onclick="toggleSelection()">⬜</button></th>
                            <th>Aluno</th>
                            <th>Sala</th>
                            <th>Livro</th>
                            <th>Exemplar</th>
                            <th>Data de Empréstimo</th>
                            <th>Data de Devolução</th>
                            <th>Atraso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox" class="select-row"></td>
                            <td>Ana Julia</td>
                            <td>2° Desenvolvimento Sist</td>
                            <td>A culpa é das estrelas</td>
                            <td>1</td>
                            <td>30/03/2024</td>
                            <td>10/04/2024</td>
                            <td>2</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="buttonsTI">
                <button class="btnTI">Cadastrar</button>
                <button class="btnTI">Editar</button>
                <button class="btnTI">Finalizar</button>
                <button class="btnTI">Excluir</button>
            </div>
        </div>
    </div>
    <script>
        function toggleSelection() {
            const checkboxes = document.querySelectorAll('.select-row');
            checkboxes.forEach(cb => cb.checked = !cb.checked);
        }
    </script>
</body>

</html>