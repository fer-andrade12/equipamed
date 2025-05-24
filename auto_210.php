<?php 
require_once("Config.php");
require_once("Equipamento.php");

// Criando conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();

// Criando instância de Equipamento passando a conexão
$equipamento = new Equipamento($conexao);

// Função para formatar valores corretamente
function formatarValor($valor) {
    // Substitui a vírgula por ponto e converte para float
    return floatval(str_replace(',', '.', $valor));
}

// Verificar se os valores foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Definindo os valores recebidos do formulário e formatando os valores corretamente
    $equipamento->setNome($_POST["nome"]);
    $equipamento->setMarca($_POST["marca"]);
    $equipamento->setModelo($_POST["modelo"]);
    $equipamento->setPeso($_POST["peso"]);
    $equipamento->setValorCaucao(formatarValor($_POST["valorCaucao"])); // Convertendo caução
    $equipamento->setValorMensal(formatarValor($_POST["valorMensal"])); // Convertendo valor mensal
    $equipamento->setValorQuinzenal(formatarValor($_POST["valorQuinzenal"])); // Convertendo quinzenal

    // Inserindo no banco
    $equipamento->Incluir();
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
  
  <!-- Seus estilos (opcional) -->
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <div class="container mt-5">
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
    <div class="alert alert-success text-center" role="alert">
      <h4 class="alert-heading">Equipamento incluído com sucesso!</h4>
    </div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>Código</th>
            <th>Equipamento</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Peso</th>
            <th>Caução (R$)</th>
            <th>Mensal (R$)</th>
            <th>Quinzenal (R$)</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
          <tr>
            <td><?php echo htmlspecialchars($equipamento->getCodigoEquipamento()); ?></td>
            <td><?php echo htmlspecialchars($equipamento->getNome()); ?></td>
            <td><?php echo htmlspecialchars($equipamento->getMarca()); ?></td>
            <td><?php echo htmlspecialchars($equipamento->getModelo()); ?></td>
            <td><?php echo htmlspecialchars($equipamento->getPeso()); ?></td>
            <td><?php echo number_format($equipamento->getValorCaucao(), 2, ',', '.'); ?></td>
            <td><?php echo number_format($equipamento->getValorMensal(), 2, ',', '.'); ?></td>
            <td><?php echo number_format($equipamento->getValorQuinzenal(), 2, ',', '.'); ?></td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4">
      <form action="index.php" method="get">
        <button type="submit" class="btn btn-primary">Voltar</button>
      </form>
    </div>
  </div>
</body>
</html>
