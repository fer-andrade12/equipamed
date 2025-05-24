<?php
require_once("Config.php");

$db = new Database();
$conexao = $db->conectaBD();

// Verifica conexão
if (!$conexao) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}

// Buscar o maior código atual e somar +1
$query = "SELECT MAX(codigoEquipamento) AS ultimoCodigo FROM equipamento";
$resultado = mysqli_query($conexao, $query);

$proximoCodigo = 1; // valor inicial caso a tabela esteja vazia

if ($resultado) {
    $row = mysqli_fetch_assoc($resultado);
    if ($row && !empty($row['ultimoCodigo'])) {
        $proximoCodigo = $row['ultimoCodigo'] + 1;
    }
} else {
    echo "<div class='alert alert-danger'>Erro na consulta: " . mysqli_error($conexao) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Equipamento</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- IMask.js para máscara de moeda -->
  <script src="https://unpkg.com/imask"></script>

  <!-- Caso queira usar seus próprios estilos -->
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <div class="container mt-5">
    <h3 class="mb-4">Cadastro de Equipamento - Inclusão</h3>

    <?php if ($proximoCodigo !== null): ?>
    <div class="alert alert-info">
      <strong>Próximo código do equipamento:</strong> <?php echo $proximoCodigo; ?>
    </div>
    <?php endif; ?>

    <form name="auto" action="auto_210.php" method="post" id="equipamentoForm">
      <div class="mb-3">
        <input type="hidden" name="codigoEquipamento" value="<?php echo $proximoCodigo; ?>">
      </div>
      <div class="mb-3">
        <label for="nome" class="form-label">Nome Equipamento</label>
        <select class="form-select" name="nome" id="nome" required>
          <option value="">--- Escolha uma nome ---</option>
          <option value="Cadeira de rodas">Cadeira de rodas</option>
          <option value="Cadeira de banho">Cadeira de banho</option>
          <option value="Andador">Andador</option>
          <option value="Andador c/Rodas">Andador c/Rodas</option>
          <option value="Muleta axilar">Muleta axilar</option>
          <option value="Muleta canadence fixa">Muleta canadence fixa</option>
          <option value="Muleta canadence articular">Muleta canadence articular</option>
          <option value="outros">Outros</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="marca" class="form-label">Marca</label>
        <select class="form-select" name="marca" id="marca" required>
          <option value="">--- Escolha uma marca ---</option>
          <option value="Jaguaribe">Jaguaribe</option>
          <option value="praxis">Praxis</option>
          <option value="ortobras">Ortobras</option>
          <option value="outros">Outros</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="modelo" class="form-label">Modelo</label>
        <select class="form-select" name="modelo" id="modelo" required>
          <option value="">--- Escolha um modelo ---</option>
          <option value="1009">1009</option>
          <option value="1016">1016</option>
          <option value="alumínio">Alumínio</option>
          <option value="POP">POP</option>
          <option value="outros">Outros</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="peso" class="form-label">Limite de peso</label>
        <select class="form-select" name="peso" id="peso" required>
          <option value="">--- Escolha qual Peso ---</option>
          <option value="100">100kg</option>
          <option value="110">110kg</option>
          <option value="150">150kg</option>
          <option value="200">200kg</option>
          <option value="310">310kg</option>
          <option value="Outros">Outros</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="valorCaucao" class="form-label">Valor Caução</label>
        <input type="text" class="form-control" name="valorCaucao" id="valorCaucao" required>
      </div>

      <div class="mb-3">
        <label for="valorMensal" class="form-label">Valor Mensal</label>
        <input type="text" class="form-control" name="valorMensal" id="valorMensal" required>
      </div>

      <div class="mb-3">
        <label for="valorQuinzenal" class="form-label">Valor Quinzenal</label>
        <input type="text" class="form-control" name="valorQuinzenal" id="valorQuinzenal" required>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" name="butinc" class="btn btn-primary">Gravar</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
      </div>
    </form>
  </div>

  <script>
    const maskConfig = {
      mask: Number,
      scale: 2,
      signed: false,
      thousandsSeparator: '.',
      padFractionalZeros: true,
      normalizeZeros: true,
      radix: ',',
      mapToRadix: [',']
    };

    const caucaoInput = IMask(document.getElementById('valorCaucao'), maskConfig);
    const mensalInput = IMask(document.getElementById('valorMensal'), maskConfig);
    const quinzenalInput = IMask(document.getElementById('valorQuinzenal'), maskConfig);

    // Limpar as máscaras antes de enviar os dados
    document.getElementById('equipamentoForm').addEventListener('submit', function(event) {
      document.getElementById('valorCaucao').value = caucaoInput.unmaskedValue;
      document.getElementById('valorMensal').value = mensalInput.unmaskedValue;
      document.getElementById('valorQuinzenal').value = quinzenalInput.unmaskedValue;
    });
  </script>
</body>
</html>
