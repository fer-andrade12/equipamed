<?php
require_once("Config.php");
require_once("Reservas.php");

// Criando a conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();

// Obtendo o número da reserva a partir do parâmetro GET
$numeroReserva = isset($_GET["numero"]) ? trim($_GET["numero"]) : '';

// Verificando se o número da reserva foi passado
if (empty($numeroReserva)) {
    die("Número da reserva não fornecido.");
}

// Consultando as renovações e o status de cancelamento para essa reserva
$query = "
    SELECT r.numeroReserva, r.dataRenovacao, r.valorRenovacao, r.tipoLocacao, r.novaDataDevolucao, 
           re.motivoCancelamento, re.dataCancelamento
    FROM renovacoes r
    LEFT JOIN reservas re ON r.numeroReserva = re.numero
    WHERE r.numeroReserva = '$numeroReserva'
    ORDER BY r.dataRenovacao DESC
";
$result = mysqli_query($conexao, $query);

// Verifica se a consulta foi bem-sucedida
if (!$result) {
    die("Erro ao buscar renovações: " . mysqli_error($conexao));
}
?> 

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renovações da Reserva</title>
    <!-- Link para o Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Renovações da Reserva - Número <?php echo $numeroReserva; ?></h2>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Data da Renovação</th>
                    <th>Preço</th>
                    <th>Tipo de Locação</th>
                    <th>Nova Devolução</th>
                    <th>Motivo de Cancelamento</th>
                    <th>Data de Cancelamento</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($renovacao = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo date('Y/m/d', strtotime($renovacao['dataRenovacao'])); ?></td>
                    <td style="text-align:right;"><?php echo number_format($renovacao['valorRenovacao'], 2, ',', '.'); ?></td>
                    <td><?php echo ucfirst($renovacao['tipoLocacao']); ?></td>
                    <td>
                        <?php 
                            if (!empty($renovacao['novaDataDevolucao'])) {
                                echo date('Y/m/d', strtotime($renovacao['novaDataDevolucao']));
                            } else {
                                echo '-';
                            }
                        ?>
                    </td>
                    <!-- Exibir motivo de cancelamento se houver -->
                    <td>
                        <?php 
                            if (!empty($renovacao['motivoCancelamento'])) {
                                echo htmlspecialchars($renovacao['motivoCancelamento']);
                            } else {
                                echo '-';
                            }
                        ?>
                    </td>
                    <!-- Exibir data de cancelamento se houver -->
                    <td>
                        <?php 
                            if (!empty($renovacao['dataCancelamento'])) {
                                echo date('Y/m/d', strtotime($renovacao['dataCancelamento']));
                            } else {
                                echo '-';
                            }
                        ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="reser_100.php" class="btn btn-secondary">Voltar para as Reservas</a>
        <a href="reser_410.php?numero=<?php echo $numeroReserva; ?>" class="btn btn-success ml-2">Nova Renovação</a>
    </div>

    <!-- Scripts do Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
mysqli_close($conexao);
?>
