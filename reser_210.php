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

// Criando o objeto da reserva
$res = new Reservas($conexao);

// Inicializa o número da reserva
$numeroReserva = null;

// Verifica se os parâmetros foram enviados corretamente
$saida             = isset($_REQUEST["saida"]) ? trim($_REQUEST["saida"]) : '';
$cpf               = isset($_REQUEST["cpf"]) ? trim($_REQUEST["cpf"]) : '';
$codigoEquipamento = isset($_REQUEST["codigoEquipamento"]) ? trim($_REQUEST["codigoEquipamento"]) : '';
$valorMensal       = isset($_REQUEST["valorMensal"]) ? floatval($_REQUEST["valorMensal"]) : 0.00;
$valorQuinzenal    = isset($_REQUEST["valorQuinzenal"]) ? floatval($_REQUEST["valorQuinzenal"]) : 0.00;
$tipoLocacao       = isset($_REQUEST["tipoLocacao"]) ? trim($_REQUEST["tipoLocacao"]) : 'mensal';

// Verifica se os campos essenciais não estão vazios
if (!empty($saida) && !empty($cpf) && !empty($codigoEquipamento)) {
    $res->setSaida($saida);
    $res->setCpf($cpf);
    $res->setCodigoEquipamento($codigoEquipamento);
    $res->setTipoLocacao($tipoLocacao);

    $dataSaida = new DateTime($saida);
    $dataSaida->setTime(0, 0, 0); // Garante que a hora seja 00:00:00

    if ($tipoLocacao === 'mensal') {
        $dataSaida->modify('+30 days');
    } elseif ($tipoLocacao === 'quinzenal') {
        $dataSaida->modify('+15 days');
    }

    $retornoCalculado = $dataSaida->format('Y-m-d H:i:s');
    $res->setRetorno($retornoCalculado);

    if ($tipoLocacao === "mensal") {
        $res->setValorMensal($valorMensal);
        $res->setValorQuinzenal(0.00);
    } else if ($tipoLocacao === "quinzenal") {
        $res->setValorMensal(0.00);
        $res->setValorQuinzenal($valorQuinzenal);
    }

    $numeroReserva = $res->Registrar();
}

// Verifica se a solicitação de cancelamento foi feita
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['cancelarReserva'], $_POST['numeroReserva'], $_POST['motivoCancelamento'])
) {
    $numeroReservaCancelamento = $_POST['numeroReserva'];
    $motivoCancelamento = $_POST['motivoCancelamento'];

    if ($res->cancelarReserva($numeroReservaCancelamento, $motivoCancelamento)) {
        header("Location: ".$_SERVER['PHP_SELF']."?mensagem=" . urlencode("Reserva cancelada com sucesso!"));
        exit;
    } else {
        header("Location: ".$_SERVER['PHP_SELF']."?mensagem=" . urlencode("Erro ao cancelar a reserva!"));
        exit;
    }
}

// Captura mensagens via GET após redirecionamento
$mensagemCancelamento = isset($_GET['mensagem']) ? urldecode($_GET['mensagem']) : null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Reserva de Equipamento - Inclusão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="text-center mb-4">Reserva de Equipamento - Inclusão</h2>

        <!-- Mensagem de sucesso ou erro -->
        <?php if (!empty($mensagemCancelamento)) { ?>
            <div class="alert alert-info text-center" role="alert">
                <?php echo htmlspecialchars($mensagemCancelamento); ?>
            </div>
        <?php } ?>

        <?php if ($numeroReserva) { ?>
            <!-- Tabela com detalhes da reserva -->
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Saída</th>
                        <th>CPF</th>
                        <th>Código Equipamento</th>
                        <th>Valor Mensal</th>
                        <th>Valor Quinzenal</th>
                        <th>Tipo de Locação</th>
                        <th>Cancelada</th>
                        <th>Motivo do Cancelamento</th>
                        <th>Data de Cancelamento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($numeroReserva); ?></td>
                        <td><?php echo htmlspecialchars($res->getSaida()); ?></td>
                        <td><?php echo htmlspecialchars($res->getCpf()); ?></td>
                        <td><?php echo htmlspecialchars($res->getCodigoEquipamento()); ?></td>
                        <td><?php echo "R$ " . number_format($res->getValorMensal(), 2, ',', '.'); ?></td>
                        <td><?php echo "R$ " . number_format($res->getValorQuinzenal(), 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($res->getTipoLocacao()); ?></td>
                        <td><?php echo $res->getReservaCancelada() ? 'Sim' : 'Não'; ?></td>
                        <td><?php echo htmlspecialchars($res->getMotivoCancelamento()); ?></td>
                        <td><?php echo htmlspecialchars($res->getDataCancelamento()); ?></td>
                        <td>
                            <?php if (!$res->getReservaCancelada()) { ?>
                                <form method="post" action="">
                                    <input type="hidden" name="numeroReserva" value="<?php echo $numeroReserva; ?>">
                                    <textarea name="motivoCancelamento" class="form-control" placeholder="Motivo do cancelamento" required></textarea>
                                    <button type="submit" name="cancelarReserva" class="btn btn-danger mt-2">Cancelar Reserva</button>
                                </form>
                            <?php } else { ?>
                                <span class="text-muted">Reserva já cancelada</span>
                            <?php } ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Botões para registrar cobrança -->
            <div class="d-flex justify-content-between mt-4">
                <a href="cobr_100.php?numero=<?php echo urlencode($numeroReserva); ?>" class="btn btn-primary">Registrar cobrança</a>
                <a href="reser_100.php" class="btn btn-secondary">Voltar</a>
            </div>
        <?php } else { ?>
            <p class="text-center text-muted mt-4">Nenhuma reserva registrada.</p>
        <?php } ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
