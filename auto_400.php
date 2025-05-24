<?php 
require_once("Config.php");
require_once("Equipamento.php");

// Criando a conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();

// Criando um objeto Equipamento e passando a conexão
$equipamento = new Equipamento($conexao);
$equipamento->setCodigoEquipamento($_REQUEST["codigo"]);

// Tenta excluir e guarda o resultado
$resultadoExclusao = $equipamento->Excluir();
?>

<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="stylesheet" href="estilos.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h3>Cadastro de Equipamentos - Exclusão</h3>
  <br><br>

  <?php if ($resultadoExclusao): ?>
    <div class="alert alert-success">
      Equipamento <strong><?php echo $equipamento->getCodigoEquipamento(); ?></strong> excluído com sucesso.
    </div>
  <?php else: ?>
    <div class="alert alert-danger">
      Este equipamento <strong>não pode ser excluído</strong> pois está vinculado a uma ou mais reservas.
    </div>
  <?php endif; ?>

  <br><br>
  <form action="auto_100.php" method="get">
      <input type="submit" value="Voltar" class="btn btn-secondary">
  </form>
</body>
</html>
