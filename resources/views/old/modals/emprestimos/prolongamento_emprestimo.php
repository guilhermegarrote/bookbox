<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prolongar Devolução</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    .pron-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
    }

    .pron-modal {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      max-width: 400px;
      width: 90%;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .pron-modal h2 {
      font-size: 1.2rem;
      font-weight: bold;
      margin-bottom: 15px;
    }

    .pron-input-container {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 15px;
    }

    .pron-input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      text-align: center;
      font-size: 1rem;
    }

    .pron-divider {
      width: 100%;
      height: 1px;
      background: #ccc;
      margin: 10px 0;
    }

    .pron-buttons {
      display: flex;
      justify-content: space-around;
      margin-top: 10px;
    }

    .pron-btn {
      padding: 10px 20px;
      border: 1px solid #000;
      border-radius: 5px;
      background: #fff;
      font-size: 1rem;
      cursor: pointer;
    }

    .pron-btn:hover {
      background: #f0f0f0;
    }

    @media (max-width: 400px) {
      .pron-modal {
        max-width: 90%;
      }
    }
  </style>
</head>
<body>

  <!-- Botão para abrir o modal -->
  <button onclick="openModal()">Prolongar Devolução</button>

  <!-- Modal -->
  <div class="pron-overlay" id="pronModal">
    <div class="pron-modal">
      <h2>Tem certeza que deseja prolongar?</h2>
      
      <div class="pron-input-container">
        <label>Data de devolução atual:</label>
        <input type="date" class="pron-input" disabled> <!-- o disabled faz com que a edit fique desabilitada é so tirar ela para voltar -->
        <div class="pron-divider"></div>
        <label>Data de devolução prolongada:</label>
        <input type="date" class="pron-input" disabled>
      </div>

      <div class="pron-buttons">
        <button class="pron-btn" onclick="closeModal()">Cancelar</button>
        <button class="pron-btn">Sim</button>
      </div>
    </div>
  </div>

  <script>
    function openModal() {
      document.getElementById("pronModal").style.display = "flex";
    }

    function closeModal() {
      document.getElementById("pronModal").style.display = "none";
    }
  </script>

</body>
</html>
