<?php
require_once("Config.php");
require_once("Reservas.php");

$db = new Database();
$conexao = $db->conectaBD();

$numeroReserva = isset($_GET['numero']) ? intval($_GET['numero']) : 0;

if ($numeroReserva <= 0) {
    die("Número da reserva não informado.");
}

// Consulta a reserva, cliente e equipamento
$query = "
    SELECT r.*, c.nome, c.cpf, e.marca, e.modelo, e.valorCaucao
    FROM reservas r
    JOIN cliente c ON r.cpf = c.cpf
    JOIN equipamento e ON r.codigoEquipamento = e.codigoEquipamento
    WHERE r.numero = ?
";

$stmt = mysqli_prepare($conexao, $query);
mysqli_stmt_bind_param($stmt, "i", $numeroReserva);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$reserva = mysqli_fetch_assoc($resultado);

if (!$reserva) {
    die("Reserva não encontrada.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cobrança - Reserva <?php echo $numeroReserva; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="text-center mb-4">Registrar Pagamento - Reserva Nº <?php echo $numeroReserva; ?></h2>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Detalhes da Reserva</div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>Número da Reserva</th><td><?php echo $reserva['numero']; ?></td></tr>
                <tr><th>Cliente</th><td><?php echo htmlspecialchars($reserva['nome']) . " (CPF: " . htmlspecialchars($reserva['cpf']) . ")"; ?></td></tr>
                <tr><th>Data de Saída</th><td><?php echo date('d/m/Y', strtotime($reserva['saida'])); ?></td></tr>
                <tr><th>Data de Retorno</th><td><?php echo date('d/m/Y', strtotime($reserva['retorno'])); ?></td></tr>
                <tr><th>Equipamento</th><td><?php echo htmlspecialchars($reserva['marca'] . " " . $reserva['modelo']); ?></td></tr>
                <tr><th>Valor Locação Mensal</th><td>R$ <?php echo number_format($reserva['valorMensal'], 2, ',', '.'); ?></td></tr>
                <tr><th>Valor Locação Quinzenal</th><td>R$ <?php echo number_format($reserva['valorQuinzenal'], 2, ',', '.'); ?></td></tr>
                <tr><th>Valor Caução</th><td>R$ <?php echo number_format($reserva['valorCaucao'], 2, ',', '.'); ?></td></tr>
            </table>
        </div>
    </div>

    <form action="registrar_cobranca.php" method="post" class="card p-4 shadow-sm bg-white">
        <input type="hidden" name="numeroReserva" value="<?php echo $numeroReserva; ?>">

        <div class="mb-3">
            <label for="formaPagamento" class="form-label">Forma de Pagamento</label>
            <select name="formaPagamento" id="formaPagamento" class="form-select" required>
                <option value="">Selecione</option>
                <option value="dinheiro">Dinheiro</option>
                <option value="cartao_credito">Cartão de Crédito</option>
                <option value="cartao_debito">Cartão de Débito</option>
                <option value="pix">PIX</option>
                <option value="cheque">Cheque</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="observacoes" class="form-label">Observações (opcional)</label>
            <textarea name="observacoes" id="observacoes" class="form-control" rows="3" placeholder="Ex: Pagamento realizado com desconto."></textarea>
        </div>

        <div class="d-flex justify-content-between">
            <a href="reser_100.php" class="btn btn-secondary">Voltar</a>
            <button type="submit" class="btn btn-success">Registrar Cobrança</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
