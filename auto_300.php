<?php
require_once("Config.php");
require_once("Equipamento.php");

// Criando a conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();

// Criando um objeto Equipamento e passando a conexão
$equipamento = new Equipamento($conexao);

// Capturando o código do equipamento da requisição
$codigo = $_REQUEST["codigo"];

// Consulta para buscar os dados do equipamento
$consulta = "SELECT * FROM equipamento WHERE codigoEquipamento = " . $codigo; 
$conx = mysqli_query($conexao, $consulta); 
$dado = mysqli_fetch_assoc($conx);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Alterar Equipamento</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilos.css"> <!-- seu CSS adicional -->
</head>
<body>

<div class="container mt-5">
  <div class="card shadow rounded-4">
    <div class="card-header bg-primary text-white text-center">
      <h4>Cadastro de Equipamento - Alteração</h4>
    </div>
    <div class="card-body">

      <form name="auto" action="auto_310.php" method="post" class="row g-3">
        
        <div class="col-md-2">
          <label for="codigoEquipamento" class="form-label">Código Equipamento</label>
          <input type="text" class="form-control" id="codigoEquipamento" name="codigoEquipamento" readonly value="<?php echo $codigo; ?>">
        </div>

        <div class="col-md-6">
          <label for="nome" class="form-label">Nome Equipamento</label>
          <select name="nome" id="nome" class="form-select" required>
            <option value="">--- Escolha uma nome ---</option>
            <option value="Cadeira de rodas" <?php echo $dado['marca'] == 'jaguaribe' ? 'selected' : ''?>>Cadeira de rodas</option>
            <option value="Cadeira de banho" <?php echo $dado['marca'] == 'praxis' ? 'selected' : ''?>>Cadeira de banho</option>
            <option value="Andador" <?php echo $dado['marca'] == 'ortobras' ? 'selected' : ''?>>Andador</option>
            <option value="Andador c/Rodas" <?php echo $dado['marca'] == 'ortobras' ? 'selected' : ''?>>Andador c/Rodas</option>
            <option value="Muleta axilar" <?php echo $dado['marca'] == 'ortobras' ? 'selected' : ''?>>Muleta axilar</option>
            <option value="Muleta canadence fixa" <?php echo $dado['marca'] == 'ortobras' ? 'selected' : ''?>>Muleta canadence fixa</option>
            <option value="Muleta canadence articular" <?php echo $dado['marca'] == 'ortobras' ? 'selected' : ''?>>Muleta canadence articular</option>
            <option value="outros" <?php echo $dado['marca'] == 'outros' ? 'selected' : ''?>>Outros</option>
          </select>
        </div>

        <div class="col-md-6">
          <label for="marca" class="form-label">Marca</label>
          <select name="marca" id="marca" class="form-select" required>
            <option value="">--- Escolha uma marca ---</option>
            <option value="jaguaribe" <?php echo $dado['marca'] == 'jaguaribe' ? 'selected' : ''?>>Jaguaribe</option>
            <option value="praxis" <?php echo $dado['marca'] == 'praxis' ? 'selected' : ''?>>Praxis</option>
            <option value="ortobras" <?php echo $dado['marca'] == 'ortobras' ? 'selected' : ''?>>Ortobras</option>
            <option value="outros" <?php echo $dado['marca'] == 'outros' ? 'selected' : ''?>>Outros</option>
          </select>
        </div>

        <div class="col-md-4">
          <label for="modelo" class="form-label">Modelo</label>
          <select name="modelo" id="modelo" class="form-select" required>
            <option value="">--- Escolha um modelo ---</option>
            <option value="1009" <?php echo $dado['modelo'] == '1009' ? 'selected' : ''?>>1009</option>
            <option value="1016" <?php echo $dado['modelo'] == '1016' ? 'selected' : ''?>>1016</option>
            <option value="alumínio" <?php echo $dado['modelo'] == 'aluminio' ? 'selected' : ''?>>Alumínio</option>
            <option value="POP" <?php echo $dado['modelo'] == 'aluminio' ? 'selected' : ''?>>POP</option>
            <option value="outros" <?php echo $dado['modelo'] == 'outros' ? 'selected' : ''?>>Outros</option>
          </select>
        </div>

        <div class="col-md-6">
          <label for="peso" class="form-label">Peso Suportável</label>
          <select name="peso" id="peso" class="form-select" required>
            <option value="">--- Escolha o peso ---</option>
            <option value="100" <?php echo $dado['peso'] == '100' ? 'selected' : ''?>>100kg</option>
            <option value="110" <?php echo $dado['peso'] == '110' ? 'selected' : ''?>>110kg</option>
            <option value="150" <?php echo $dado['peso'] == '150' ? 'selected' : ''?>>150kg</option>
            <option value="200" <?php echo $dado['peso'] == '200' ? 'selected' : ''?>>200kg</option>
            <option value="200" <?php echo $dado['peso'] == '200' ? 'selected' : ''?>>300kg</option>
            <option value="Outros" <?php echo $dado['peso'] == 'Outros' ? 'selected' : ''?>>Outros</option>
          </select>
        </div>

        <div class="col-md-3">
          <label for="preco" class="form-label">Preço Quinzenal</label>
          <input type="text" class="form-control text-end" id="valorQuinzenal" name="valorQuinzenal" required value="<?php echo number_format($dado['valorQuinzenal'], 2, ',', '.'); ?>">
          <label for="preco" class="form-label">Preço Mensal</label>
          <input type="text" class="form-control text-end" id="valorMensal" name="valorMensal" required value="<?php echo number_format($dado['valorMensal'], 2, ',', '.'); ?>">
        </div>

        <div class="col-md-3">
          <label for="valorCaucao" class="form-label">Valor Caução</label>
          <input type="text" class="form-control text-end" id="valorCaucao" name="valorCaucao" required value="<?php echo number_format($dado['valorCaucao'], 2, ',', '.'); ?>">
        </div>

        <div class="col-12 text-center mt-4">
          <button type="submit" class="btn btn-success me-3">Gravar</button>
          <a href="index.php" class="btn btn-secondary">Voltar</a>
        </div>

      </form>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
