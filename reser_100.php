<?php
require_once("Config.php");
require_once("Reservas.php");

// Conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();

// Instancia o objeto Reservas
$res = new Reservas($conexao);

// Filtros
$filtro = $_GET['filtro'] ?? 'ativas';
$numeroReserva = $_GET['numeroReserva'] ?? '';

// Monta a consulta SQL
$sql = "
    SELECT 
        r.numero, r.saida, r.retorno, r.cpf, r.codigoEquipamento,
        r.valorMensal, r.valorQuinzenal,
        IFNULL(ren.id, 'Nenhuma Renovação') AS renovacao,
        r.reservaFinalizada
    FROM reservas r
    LEFT JOIN renovacoes ren ON r.numero = ren.numeroReserva
";

// Condição para filtro
if ($filtro === 'finalizadas') {
    $sql .= " WHERE r.reservaFinalizada = 1";
} else {
    $sql .= " WHERE (r.reservaFinalizada = 0 OR r.reservaFinalizada IS NULL)";
}

// Filtro por número da reserva
if (!empty($numeroReserva)) {
    $numeroReserva = mysqli_real_escape_string($conexao, $numeroReserva);
    $sql .= " AND r.numero LIKE '%$numeroReserva%'";
}

$sql .= " ORDER BY r.numero DESC";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    die("Erro ao buscar dados: " . mysqli_error($conexao));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Reservas - Sistema de Locação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">


    <style>
        body {
            background-color: #f4f7fa;
        }

        .navbar {
            background-color: #006f72;
            min-height: 70px; /* Aumenta a altura */
            padding-top: 50px; /* Espaçamento superior */
            padding-bottom: 10px; /* Espaçamento inferior */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }


        .navbar-nav .nav-link {
            font-size: 1.1rem;
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
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

        .btn-custom {
            background-color: #006f72;
            color: white;
        }

        .btn-custom:hover {
            background-color: #004d52;
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
<div class="container mt-4 mb-5">
    <h2 class="text-center text-primary mb-4"><i class="bi bi-calendar-check"></i> Gerenciar Reservas</h2>

    <!-- Filtros -->
    <div class="d-flex justify-content-between mb-3">
        <form action="reser_100.php" method="get">
            <input type="hidden" name="filtro" value="ativas">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-hourglass-split"></i> Ver Ativas
            </button>
        </form>
        <form action="reser_100.php" method="get">
            <input type="hidden" name="filtro" value="finalizadas">
            <button type="submit" class="btn btn-dark">
                <i class="bi bi-check2-circle"></i> Ver Finalizadas
            </button>
        </form>
        <form action="reser_200.php" method="get">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-plus"></i> Nova Reserva
            </button>
        </form>
    </div>

    <!-- Barra de Pesquisa -->
    <form action="reser_100.php" method="get" class="input-group mb-3">
        <input type="hidden" name="filtro" value="<?php echo htmlspecialchars($filtro); ?>">
        <input type="text" class="form-control" name="numeroReserva" placeholder="Buscar por número da reserva"
               value="<?php echo htmlspecialchars($numeroReserva); ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></i></button>
    </form>

    <!-- Tabela -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Reserva</th>
                    <th>Saída</th>
                    <th>Retorno</th>
                    <th>CPF</th>
                    <th>Cód. Equipamento</th>
                    <th>Valor Mensal</th>
                    <th>Valor Quinzenal</th>
                    <th>Renovação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($dado = mysqli_fetch_assoc($resultado)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($dado['numero']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($dado['saida'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($dado['retorno'])); ?></td>
                        <td><?php echo htmlspecialchars($dado['cpf']); ?></td>
                        <td><?php echo htmlspecialchars($dado['codigoEquipamento']); ?></td>
                        <td style="text-align: right;"><?php echo number_format($dado['valorMensal'], 2, ',', '.'); ?></td>
                        <td style="text-align: right;"><?php echo number_format($dado['valorQuinzenal'], 2, ',', '.'); ?></td>
                        <td>
                            <?php if ($dado['renovacao'] != 'Nenhuma Renovação') { ?>
                                <a href="reser_400.php?numero=<?php echo urlencode($dado['numero']); ?>" 
                                   class="btn btn-info btn-sm">
                                   Ver Renovação
                                </a>
                            <?php } else { ?>
                                Nenhuma Renovação
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($filtro != 'finalizadas') { ?>
                                <a href="reser_300.php?codigo=<?php echo urlencode($dado['numero']); ?>" 
                                   class="btn btn-success btn-sm">
                                   Devolução
                                </a>
                                <a href="reser_400.php?numero=<?php echo urlencode($dado['numero']); ?>" 
                                   class="btn btn-warning btn-sm">
                                   Renovação
                                </a>
                            <?php } ?>
                            <a href="reser_500.php?numero=<?php echo urlencode($dado['numero']); ?>" 
                               class="btn btn-secondary btn-sm">
                               Detalhes
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Rodapé -->
<div class="footer">
    <p>&copy; 2025 Sistema de Locação de Equipamentos Médicos. Todos os direitos reservados.</p>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Fecha a conexão
mysqli_close($conexao);
?>
