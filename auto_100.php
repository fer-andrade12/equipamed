<?php
require_once("Config.php");
require_once("Equipamento.php");

// Criando a conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();

// Criando um objeto Equipamento e passando a conexão
$equipamento = new Equipamento($conexao);

$consulta = "SELECT * FROM equipamento"; 
$conx = mysqli_query($conexao, $consulta);

// Verifica se a consulta foi bem-sucedida
if (!$conx) {
    die("Erro ao buscar dados: " . mysqli_error($conexao));
}
?> 

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Manutenção de Equipamentos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  
  <style>
    body {
      background: linear-gradient(to right, #e0f7fa, #ffffff), url('hospital_background.jpg') no-repeat center center fixed;
      background-size: cover;
      color: #333;
      font-family: 'Arial', sans-serif;
    }

    .navbar {
      background-color: #006f72;
    }

    .navbar-nav .nav-link {
      font-size: 1.2rem;
    }

    .container {
      padding-top: 40px;
    }

    .table th, .table td {
      text-align: center;
    }

    .btn-custom {
      background-color: #006f72;
      color: white;
    }

    .btn-custom:hover {
      background-color: #004d52;
    }

    .footer {
      background-color: #006f72;
      color: white;
      text-align: center;
      padding: 10px;
      position: fixed;
      bottom: 0;
      width: 100%;
    }

    .search-bar {
      display: flex;
      justify-content: flex-start;
      margin-bottom: 20px;
    }

    .search-bar input {
      width: 250px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="bi bi-hospital"></i> Locação de Equipamentos</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="condut_100.php"><i class="bi bi-people"></i> Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="auto_100.php"><i class="bi bi-gear"></i> Equipamentos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="reser_100.php"><i class="bi bi-calendar-check"></i> Reservas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="listar_cobrancas.php"><i class="bi bi-currency-dollar"></i> Cobrança</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Conteúdo -->
<div class="container">
    <h1 class="text-center text-primary mb-4"><i class="bi bi-gear"></i> Manutenção de Equipamentos</h1>
    
    <!-- Botões de Inclusão e Voltar -->
    <div class="d-flex justify-content-between mb-3">
        <form name="Equipamento" action="auto_200.php" method="post">
            <input type="submit" name="butinc" value="Incluir Novo Equipamento" class="btn btn-custom btn-lg">
        </form>
        <form name="volta" action="index.php" method="get">
            <input type="submit" name="butvolta" value="Voltar" class="btn btn-secondary btn-lg">
        </form>
    </div>

    <!-- Tabela de Equipamentos -->
    <table class="table table-striped table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Código</th>
                <th>Equipamento</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Peso Suportável</th>
                <th>Preço Quinzenal (R$)</th>
                <th>Preço Mensal (R$)</th>
                <th>Valor Caução (R$)</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php while($dado = mysqli_fetch_assoc($conx)) { ?> 
                <tr>
                    <td><?php echo $dado['codigoEquipamento']; ?></td>
                    <td><?php echo ucfirst($dado['nome']); ?></td>
                    <td><?php echo ucfirst($dado['marca']); ?></td>
                    <td><?php echo ucfirst($dado['modelo']); ?></td>
                    <td><?php echo $dado['peso']; ?></td>
                    <td><?php echo number_format($dado['valorQuinzenal'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format($dado['valorMensal'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format($dado['valorCaucao'], 2, ',', '.'); ?></td>
                    <td>
                        <a href="auto_300.php?codigo=<?php echo $dado['codigoEquipamento']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="auto_400.php?codigo=<?php echo $dado['codigoEquipamento']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirma Exclusão?')">Excluir</a>
                    </td>
                </tr> 
            <?php } ?> 
        </tbody>
    </table>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2025 Sistema de Locação de Equipamentos Médicos. Todos os direitos reservados.</p>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Fecha a conexão com o banco de dados
mysqli_close($conexao);
?>
