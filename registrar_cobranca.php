<?php
require_once("Config.php");

$db = new Database();
$conexao = $db->conectaBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numeroReserva = intval($_POST['numeroReserva']);
    $formaPagamento = $_POST['formaPagamento'];
    $observacoes = trim($_POST['observacoes'] ?? '');

    // Buscar valores da reserva
    $sql = "SELECT valorMensal, valorQuinzenal FROM reservas WHERE numero = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "i", $numeroReserva);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $dados = mysqli_fetch_assoc($resultado);

    if (!$dados) {
        exibirMensagem("Reserva não encontrada.", "danger", $numeroReserva);
        exit;
    }

    // Calcular valor total com base nos valores cadastrados
    $valorTotal = 0;
    if ($dados['valorMensal'] > 0) $valorTotal += $dados['valorMensal'];
    if ($dados['valorQuinzenal'] > 0) $valorTotal += $dados['valorQuinzenal'];

    // Inserir nova cobrança (permite múltiplas para a mesma reserva = renovação)
    $insert = "INSERT INTO cobrancas (numeroReserva, formaPagamento, valorTotal, observacoes, quitado, dataCobranca)
               VALUES (?, ?, ?, ?, 1, NOW())";
    $stmt2 = mysqli_prepare($conexao, $insert);
    mysqli_stmt_bind_param($stmt2, "isds", $numeroReserva, $formaPagamento, $valorTotal, $observacoes);
    $executado = mysqli_stmt_execute($stmt2);

    if ($executado) {
        exibirMensagem("Cobrança registrada com sucesso!", "success", $numeroReserva);
    } else {
        exibirMensagem("Erro ao registrar cobrança.", "danger", $numeroReserva);
    }
}

function exibirMensagem($mensagem, $tipo, $numeroReserva) {
    ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="container mt-5">
        <div class="alert alert-<?php echo $tipo; ?>" role="alert">
            <?php echo $mensagem; ?>
        </div>
        <div class="d-flex gap-2">
            <a href="listar_cobrancas.php?numero=<?php echo $numeroReserva; ?>" class="btn btn-primary">Voltar</a>

            <!-- Botão de impressão -->
            <form action="gerar_termo_renovacao.php" method="post">
            <input type="hidden" name="numeroReserva" value="<?php echo $numeroReserva; ?>">
            <button type="submit" class="btn btn-primary">Imprimir Contrato de Renovação em PDF</button>
        </form>


            <!-- Botão de envio de e-mail -->
            <form action="enviar_email_renovacao.php" method="post" style="display:inline;">
                <input type="hidden" name="numeroReserva" value="<?php echo $numeroReserva; ?>">
                <button type="submit" class="btn btn-outline-success">Enviar por E-mail</button>
            </form>
        </div>
    </div>
    <?php
}
?>
