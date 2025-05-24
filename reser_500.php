<?php
require_once("Config.php");
require_once("Reservas.php");
require_once("Equipamento.php");
require_once("Cliente.php");

// Criando a conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();

// Verifica se a conexão foi estabelecida corretamente
if (!$conexao) {
    die("Erro ao conectar com o banco de dados.");
}

// Obtém o número da reserva da URL
$numeroReserva = isset($_GET['numero']) ? trim($_GET['numero']) : '';

if (empty($numeroReserva)) {
    die("Número da reserva não fornecido.");
}

// Consulta os dados da reserva
$queryReserva = "SELECT * FROM reservas WHERE numero = '" . mysqli_real_escape_string($conexao, $numeroReserva) . "'";
$resultadoReserva = mysqli_query($conexao, $queryReserva);

if ($resultadoReserva && mysqli_num_rows($resultadoReserva) > 0) {
    $reserva = mysqli_fetch_assoc($resultadoReserva);

    // Consulta os dados do cliente associado à reserva
    $queryCliente = "SELECT * FROM cliente WHERE cpf = '" . mysqli_real_escape_string($conexao, $reserva['cpf']) . "'";
    $resultadoCliente = mysqli_query($conexao, $queryCliente);
    $cliente = mysqli_fetch_assoc($resultadoCliente);

    // Consulta os dados do equipamento associado à reserva
    $queryEquipamento = "SELECT * FROM equipamento WHERE codigoEquipamento = '" . mysqli_real_escape_string($conexao, $reserva['codigoEquipamento']) . "'";
    $resultadoEquipamento = mysqli_query($conexao, $queryEquipamento);
    $equipamento = mysqli_fetch_assoc($resultadoEquipamento);

    // Consulta as renovações associadas à reserva
    $queryRenovacoes = "SELECT * FROM renovacoes WHERE numeroReserva = '" . mysqli_real_escape_string($conexao, $numeroReserva) . "'";
    $resultadoRenovacoes = mysqli_query($conexao, $queryRenovacoes);
} else {
    die("Reserva não encontrada.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Reserva - Número <?php echo htmlspecialchars($numeroReserva); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="text-center mb-4">Detalhes da Reserva - Número <?php echo htmlspecialchars($numeroReserva); ?></h2>

    <div class="mb-5">
        <h3>Dados do Cliente</h3>
        <table class="table table-bordered table-striped">
            <tr>
                <th>Nome</th>
                <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
            </tr>
            <tr>
                <th>CPF</th>
                <td><?php echo htmlspecialchars($cliente['cpf']); ?></td>
            </tr>
            <tr>
                <th>Telefone</th>
                <td><?php echo htmlspecialchars($cliente['telefone']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
            </tr>
            <tr>
                <th>Endereço</th>
                <td><?php echo htmlspecialchars($cliente['endereco']); ?></td>
            </tr>
            <tr>
                <th>CEP</th>
                <td><?php echo htmlspecialchars($cliente['cep']); ?></td>
            </tr>
            <tr>
                <th>Complemento</th>
                <td><?php echo htmlspecialchars($cliente['complemento']); ?></td>
            </tr>
        </table>
    </div>

    <div class="mb-5">
        <h3>Dados do Equipamento</h3>
        <table class="table table-bordered table-striped">
            <tr>
                <th>Código Equipamento</th>
                <td><?php echo htmlspecialchars($equipamento['codigoEquipamento']); ?></td>
            </tr>
            <tr>
                <th>Marca</th>
                <td><?php echo htmlspecialchars($equipamento['marca']); ?></td>
            </tr>
            <tr>
                <th>Modelo</th>
                <td><?php echo htmlspecialchars($equipamento['modelo']); ?></td>
            </tr>
            <tr>
                <th>Peso</th>
                <td><?php echo htmlspecialchars($equipamento['peso']); ?></td>
            </tr>
            <tr>
                <th>Valor Caução</th>
                <td><?php echo "R$ " . number_format($equipamento['valorCaucao'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Valor Mensal</th>
                <td><?php echo "R$ " . number_format($equipamento['valorMensal'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Valor Quinzenal</th>
                <td><?php echo "R$ " . number_format($equipamento['valorQuinzenal'], 2, ',', '.'); ?></td>
            </tr>
        </table>
    </div>

    <div class="mb-5">
        <h3>Dados da Reserva</h3>
        <table class="table table-bordered table-striped">
            <tr>
                <th>Número da Reserva</th>
                <td><?php echo htmlspecialchars($reserva['numero']); ?></td>
            </tr>
            <tr>
                <th>Data de Saída</th>
                <td><?php echo htmlspecialchars($reserva['saida']); ?></td>
            </tr>
            <tr>
                <th>Tipo de Locação</th>
                <td><?php echo htmlspecialchars($reserva['tipoLocacao']); ?></td>
            </tr>
            <tr>
                <th>Valor Mensal</th>
                <td><?php echo "R$ " . number_format($reserva['valorMensal'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Valor Quinzenal</th>
                <td><?php echo "R$ " . number_format($reserva['valorQuinzenal'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Retorno</th>
                <td><?php echo htmlspecialchars($reserva['retorno']); ?></td>
            </tr>
            <tr>
                <th>Reserva Cancelada</th>
                <td><?php echo $reserva['reservaCancelada'] ? 'Sim' : 'Não'; ?></td>
            </tr>
            <tr>
                <th>Motivo do Cancelamento</th>
                <td><?php echo htmlspecialchars($reserva['motivoCancelamento']); ?></td>
            </tr>
            <tr>
                <th>Data de Cancelamento</th>
                <td><?php echo htmlspecialchars($reserva['dataCancelamento']); ?></td>
            </tr>
        </table>
    </div>

    <div class="mb-5">
        <h3>Renovações</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data da Renovação</th>
                    <th>Preço</th>
                    <th>Tipo de Locação</th>
                    <th>Nova Data de Devolução</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($renovacao = mysqli_fetch_assoc($resultadoRenovacoes)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($renovacao['id']); ?></td>
                        <td><?php echo htmlspecialchars($renovacao['dataRenovacao']); ?></td>
                        <td><?php echo "R$ " . number_format($renovacao['valorRenovacao'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($renovacao['tipoLocacao']); ?></td>
                        <td><?php echo htmlspecialchars($renovacao['novaDataDevolucao']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <a href="reser_100.php" class="btn btn-secondary">Voltar</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Fecha a conexão com o banco de dados
