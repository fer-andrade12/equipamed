<?php
require_once("Config.php");
require_once("dompdf/autoload.inc.php");

var_dump($_GET);

use Dompdf\Dompdf;

$db = new Database();
$conexao = $db->conectaBD();

$numeroReserva = isset($_GET['numero']) ? intval($_GET['numero']) : 0;

if ($numeroReserva <= 0) {
    die("Número da reserva não informado.");
}


$query = "
    SELECT r.*, c.nome, c.cpf, c.email, c.telefone, e.marca, e.modelo, e.valorCaucao,
           co.formaPagamento, co.valorTotal, co.observacoes, co.dataRegistro
    FROM reservas r
    JOIN cliente c ON r.cpf = c.cpf
    JOIN equipamento e ON r.codigoEquipamento = e.codigoEquipamento
    JOIN cobrancas co ON r.numero = co.numeroReserva
    WHERE r.numero = ?
";

$stmt = mysqli_prepare($conexao, $query);
mysqli_stmt_bind_param($stmt, "i", $numeroReserva);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$dados = mysqli_fetch_assoc($res);

if (!$dados) {
    die("Cobrança não encontrada.");
}

$html = '
    <h2>Detalhamento da Cobrança</h2>
    <table border="1" cellspacing="0" cellpadding="6" width="100%">
        <tr><th align="left">Número da Reserva</th><td>' . $dados['numero'] . '</td></tr>
        <tr><th align="left">Cliente</th><td>' . $dados['nome'] . ' (CPF: ' . $dados['cpf'] . ')</td></tr>
        <tr><th align="left">Contato</th><td>' . $dados['email'] . ' / ' . $dados['telefone'] . '</td></tr>
        <tr><th align="left">Equipamento</th><td>' . $dados['marca'] . ' - ' . $dados['modelo'] . '</td></tr>
        <tr><th align="left">Data de Saída</th><td>' . date('d/m/Y', strtotime($dados['saida'])) . '</td></tr>
        <tr><th align="left">Data de Retorno</th><td>' . date('d/m/Y', strtotime($dados['retorno'])) . '</td></tr>
        <tr><th align="left">Valor Mensal</th><td>R$ ' . number_format($dados['valorMensal'], 2, ',', '.') . '</td></tr>
        <tr><th align="left">Valor Quinzenal</th><td>R$ ' . number_format($dados['valorQuinzenal'], 2, ',', '.') . '</td></tr>
        <tr><th align="left">Valor Caução</th><td>R$ ' . number_format($dados['valorCaucao'], 2, ',', '.') . '</td></tr>
        <tr><th align="left">Forma de Pagamento</th><td>' . ucfirst(str_replace("_", " ", $dados['formaPagamento'])) . '</td></tr>
        <tr><th align="left">Valor Total Cobrado</th><td><strong>R$ ' . number_format($dados['valorTotal'], 2, ',', '.') . '</strong></td></tr>
        <tr><th align="left">Observações</th><td>' . nl2br($dados['observacoes']) . '</td></tr>
        <tr><th align="left">Data do Registro</th><td>' . date('d/m/Y H:i', strtotime($dados['dataRegistro'])) . '</td></tr>
    </table>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream("cobranca_reserva_{$numeroReserva}.pdf", ["Attachment" => false]);
exit;
