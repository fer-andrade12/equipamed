<?php
require_once("Config.php");
require 'dompdf/vendor/autoload.php';

use Dompdf\Dompdf;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numeroReserva = $_POST['numeroReserva'] ?? '';

    $db = new Database();
    $con = $db->conectaBD();

    // Buscar dados da reserva e cliente
    $sql = "SELECT c.nome, c.email, r.numero, r.valorMensal, r.valorQuinzenal
            FROM reservas r
            JOIN cliente c ON r.cpf = c.cpf
            WHERE r.numero = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $numeroReserva);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $dados = mysqli_fetch_assoc($resultado);

    if (!$dados) {
        echo "Dados não encontrados.";
        exit;
    }

    $nomeCliente = $dados['nome'];
    $emailCliente = $dados['email'];
    $valorMensal = $dados['valorMensal'];
    $valorQuinzenal = $dados['valorQuinzenal'];

    // === Gerar contrato em PDF ===
    $htmlContrato = "
    <h1>Contrato de Renovação</h1>
    <p>Cliente: <strong>{$nomeCliente}</strong></p>
    <p>Número da Reserva: <strong>{$numeroReserva}</strong></p>
    <p>Valor Mensal: R$ " . number_format($valorMensal, 2, ',', '.') . "</p>
    <p>Valor Quinzenal: R$ " . number_format($valorQuinzenal, 2, ',', '.') . "</p>
    <p>Data: " . date("d/m/Y") . "</p>
    ";

    $dompdf = new Dompdf();
    $dompdf->loadHtml($htmlContrato);
    $dompdf->setPaper('A4');
    $dompdf->render();

    // Forçar o download do PDF
    $pdfContent = $dompdf->output();

    // Definir cabeçalhos HTTP para download do PDF
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=contrato_renovacao_{$numeroReserva}.pdf");
    echo $pdfContent;

    exit;
}
?>
