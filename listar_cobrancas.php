<?php
require_once("Config.php");

// Criando a conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();

// Receber filtros
$filtroReserva = $_GET['numeroReserva'] ?? '';
$filtroCPF = $_GET['cpf'] ?? '';

// Montar query com filtros
$sql = "
    SELECT cb.*, r.numero, c.nome AS nomeCliente, c.cpf
    FROM cobrancas cb
    JOIN reservas r ON cb.numeroReserva = r.numero
    JOIN cliente c ON r.cpf = c.cpf
    WHERE 1=1
";

$params = [];

if (!empty($filtroReserva)) {
    $sql .= " AND r.numero = ?";
    $params[] = $filtroReserva;
}

if (!empty($filtroCPF)) {
    $sql .= " AND c.cpf LIKE ?";
    $params[] = "%$filtroCPF%";
}

$sql .= " ORDER BY cb.id DESC";

// Preparar e executar a consulta
$stmt = mysqli_prepare($conexao, $sql);

if (!empty($params)) {
    $tipos = str_repeat("s", count($params));
    mysqli_stmt_bind_param($stmt, $tipos, ...$params);
}

mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Listagem de Cobranças</title>
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
    <h1 class="text-center text-primary mb-4"><i class="bi bi-currency-dollar"></i> Listagem de Cobranças</h1>
    
    <!-- Barra de Pesquisa por Reserva e CPF -->
    <div class="search-bar">
        <form action="listar_cobrancas.php" method="get" class="d-flex">
            <input type="text" class="form-control" name="numeroReserva" value="<?php echo $filtroReserva; ?>" placeholder="Pesquisar por Número de Reserva">
            <input type="text" class="form-control ms-2" name="cpf" value="<?php echo $filtroCPF; ?>" placeholder="Pesquisar por CPF">
            <button type="submit" class="btn btn-info ms-2">
                <i class="bi bi-search"></i> Buscar
            </button>
        </form>
    </div>

    <!-- Tabela de Cobranças -->
    <table class="table table-striped table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Nº Reserva</th>
                <th>Cliente</th>
                <th>CPF</th>
                <th>Forma de Pagamento</th>
                <th>Valor Total</th>
                <th>Observações</th>
                <th>Status</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($cobranca = mysqli_fetch_assoc($resultado)) { ?>
                <tr>
                    <td><?php echo $cobranca['id']; ?></td>
                    <td><?php echo $cobranca['numeroReserva']; ?></td>
                    <td><?php echo htmlspecialchars($cobranca['nomeCliente']); ?></td>
                    <td><?php echo htmlspecialchars($cobranca['cpf']); ?></td>
                    <td><?php echo ucfirst(str_replace('_', ' ', $cobranca['formaPagamento'])); ?></td>
                    <td>R$ <?php echo number_format($cobranca['valorTotal'], 2, ',', '.'); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($cobranca['observacoes'])); ?></td>
                    <td><?php echo $cobranca['quitado'] ? 'Quitado' : 'Pendente'; ?></td>
                    <td><?php echo isset($cobranca['dataRegistro']) ? date('d/m/Y H:i', strtotime($cobranca['dataRegistro'])) : '-'; ?></td>
                    <td>
                        <a href="detalhe_cobranca_pdf.php?numeroReserva=<?php echo $cobranca['numeroReserva']; ?>" target="_blank" class="btn btn-info btn-sm">Imprimir PDF</a>
                        <a href="index.php" class="btn btn-secondary btn-sm">Voltar</a>
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
