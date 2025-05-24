<?php 
require_once "Config.php";
require_once "Reservas.php";

$db = new Database();
$conexao = $db->conectaBD();

$numeroReserva = trim($_GET['numero'] ?? '');

if (empty($numeroReserva)) {
    die("Número da reserva não fornecido.");
}

$mensagem = "";

// Busca reserva
$queryReserva = "SELECT codigoEquipamento, retorno FROM reservas WHERE numero = ?";
$stmtReserva = mysqli_prepare($conexao, $queryReserva);
mysqli_stmt_bind_param($stmtReserva, "s", $numeroReserva);
mysqli_stmt_execute($stmtReserva);
mysqli_stmt_bind_result($stmtReserva, $codigoEquipamento, $dataDevolucaoOriginal);
mysqli_stmt_fetch($stmtReserva);
mysqli_stmt_close($stmtReserva);

if (empty($codigoEquipamento)) {
    die("Reserva não encontrada.");
}

// Busca equipamento
$queryEquipamento = "SELECT valorMensal, valorQuinzenal FROM equipamento WHERE codigoEquipamento = ?";
$stmtEquipamento = mysqli_prepare($conexao, $queryEquipamento);
mysqli_stmt_bind_param($stmtEquipamento, "s", $codigoEquipamento);
mysqli_stmt_execute($stmtEquipamento);
mysqli_stmt_bind_result($stmtEquipamento, $valorMensal, $valorQuinzenal);
mysqli_stmt_fetch($stmtEquipamento);
mysqli_stmt_close($stmtEquipamento);

if (empty($valorMensal) && empty($valorQuinzenal)) {
    die("Equipamento não encontrado.");
}

// Se enviou o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valorRenovacao = floatval($_POST['valorRenovacao'] ?? 0.00);
    $tipoLocacao = $_POST['tipoLocacao'] ?? '';
    $dataRenovacao = date('Y-m-d');

    if (empty($valorRenovacao) || empty($tipoLocacao)) {
        $mensagem = "Preencha todos os campos!";
    } else {
        $novaDataDevolucao = match ($tipoLocacao) {
            'mensal'    => date("Y-m-d", strtotime("+30 days", strtotime($dataDevolucaoOriginal))),
            'quinzenal' => date("Y-m-d", strtotime("+15 days", strtotime($dataDevolucaoOriginal))),
            default     => null
        };

        if (!$novaDataDevolucao) {
            $mensagem = "Tipo de locação inválido.";
        } else {
            // Verifica se já renovou hoje
            $queryVerifica = "SELECT COUNT(*) FROM renovacoes WHERE numeroReserva = ? AND dataRenovacao = ?";
            $stmtVerifica = mysqli_prepare($conexao, $queryVerifica);
            mysqli_stmt_bind_param($stmtVerifica, "ss", $numeroReserva, $dataRenovacao);
            mysqli_stmt_execute($stmtVerifica);
            mysqli_stmt_bind_result($stmtVerifica, $totalRenovacoes);
            mysqli_stmt_fetch($stmtVerifica);
            mysqli_stmt_close($stmtVerifica);

            if ($totalRenovacoes > 0) {
                $mensagem = "Já existe uma renovação registrada nesta mesma data!";
            } else {
                $reservaObj = new Reservas($conexao);
                $reservaObj->setNumero($numeroReserva);
                $reservaObj->setDataRenovacao($dataRenovacao);
                $reservaObj->setRetorno($novaDataDevolucao);
                $reservaObj->setValorRenovacao($valorRenovacao);
                $reservaObj->setTipoLocacao($tipoLocacao);

                $mensagem = $reservaObj->Renovar();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Renovação - Reserva <?php echo htmlspecialchars($numeroReserva); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Detalhes da Reserva</h5>
            </div>
            <div class="card-body">
                <p><strong>Número da Reserva:</strong> <?php echo htmlspecialchars($numeroReserva); ?></p>
                <p><strong>Data de Devolução Original:</strong> <?php echo date("d/m/Y", strtotime($dataDevolucaoOriginal)); ?></p>
                <p><strong>Código do Equipamento:</strong> <?php echo htmlspecialchars($codigoEquipamento); ?></p>
                <p><strong>Valor Mensal:</strong> R$ <?php echo number_format($valorMensal, 2, ',', '.'); ?></p>
                <p><strong>Valor Quinzenal:</strong> R$ <?php echo number_format($valorQuinzenal, 2, ',', '.'); ?></p>
            </div>
        </div>

        <div class="mb-4">
            <h4 class="text-primary">Renovar Reserva #<?php echo htmlspecialchars($numeroReserva); ?></h4>
        </div>

        <?php if (!empty($mensagem)) : ?>
            <div class="alert alert-<?php echo (strpos($mensagem, 'sucesso') !== false) ? 'success' : 'warning'; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensagem; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

        <!-- Botão de pagamento após renovação com sucesso -->
        <?php if (strpos($mensagem, 'sucesso') !== false): ?>
            <a href="cobr_100.php?numero=<?php echo urlencode($numeroReserva); ?>" class="btn btn-primary mb-4">
                Registrar Pagamento da Renovação
            </a>
        <?php endif; ?>

        <form action="reser_410.php?numero=<?php echo urlencode($numeroReserva); ?>" method="POST" class="card shadow-sm p-4 bg-white">
            <div class="mb-3">
                <label for="valorRenovacao" class="form-label">Valor da Renovação (R$)</label>
                <input type="text" class="form-control" id="valorRenovacaoVisivel" value="" readonly>
                <input type="hidden" name="valorRenovacao" id="valorRenovacao">
            </div>

            <div class="mb-3">
                <label for="novaDataDevolucao" class="form-label">Nova Data de Devolução</label>
                <input type="text" class="form-control" id="novaDataDevolucao" name="novaDataDevolucao" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Locação</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipoLocacao" id="locacaoMensal" value="mensal" data-preco="<?php echo $valorMensal; ?>" required>
                    <label class="form-check-label" for="locacaoMensal">
                        Mensal - R$ <?php echo number_format($valorMensal, 2, ',', '.'); ?>
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipoLocacao" id="locacaoQuinzenal" value="quinzenal" data-preco="<?php echo $valorQuinzenal; ?>" required>
                    <label class="form-check-label" for="locacaoQuinzenal">
                        Quinzenal - R$ <?php echo number_format($valorQuinzenal, 2, ',', '.'); ?>
                    </label>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" id="btnSubmit" class="btn btn-success" disabled>Registrar Renovação</button>
            </div>
        </form>

        <div class="mt-3">
            <a href="reser_100.php?numero=<?php echo urlencode($numeroReserva); ?>" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </div>

    <!-- Scripts do Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script de lógica -->
    <script>
        const dataDevolucaoOriginal = new Date("<?php echo $dataDevolucaoOriginal; ?>");

        document.querySelectorAll('input[name="tipoLocacao"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const preco = this.getAttribute('data-preco');
                const precoFormatado = parseFloat(preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                document.getElementById('valorRenovacaoVisivel').value = precoFormatado;
                document.getElementById('valorRenovacao').value = preco;

                let novaData = new Date(dataDevolucaoOriginal);
                if (this.value === "mensal") {
                    novaData.setDate(novaData.getDate() + 30);
                } else if (this.value === "quinzenal") {
                    novaData.setDate(novaData.getDate() + 15);
                }

                document.getElementById('btnSubmit').disabled = false;
                const novaDataFormatada = novaData.toISOString().split('T')[0];
                document.getElementById('novaDataDevolucao').value = novaDataFormatada;
            });
        });
    </script>
</body>
</html>

<?php
mysqli_close($conexao);
?>
