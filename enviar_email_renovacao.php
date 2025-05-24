<?php
require_once("Config.php");
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numeroReserva = $_POST['numeroReserva'] ?? '';

    $db = new Database();
    $con = $db->conectaBD();

    // Buscar dados da reserva e cliente
    $sql = "SELECT c.nomeCliente, c.email, r.numero, r.valorMensal, r.valorQuinzenal
            FROM reservas r
            JOIN cliente c ON r.codigoCliente = c.codigoCliente
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

    $nomeCliente = $dados['nomeCliente'];
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

    $pdfContent = $dompdf->output();
    $pdfPath = "contrato_renovacao_{$numeroReserva}.pdf";
    file_put_contents($pdfPath, $pdfContent);

    // === Enviar e-mail com PHPMailer ===
    $mail = new PHPMailer(true);

    try {
        // Configurações SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.seudominio.com'; // Ex: smtp.hostinger.com
        $mail->SMTPAuth = true;
        $mail->Username = 'seuemail@seudominio.com'; // Remetente autenticado
        $mail->Password = 'sua-senha'; // Senha do email ou app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remetente e destinatário
        $mail->setFrom('seuemail@seudominio.com', 'Sua Empresa');
        $mail->addAddress($emailCliente, $nomeCliente);

        // Anexo
        $mail->addAttachment($pdfPath);

        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = "Renovação da sua reserva #{$numeroReserva}";
        $mail->Body = "
            <p>Olá <strong>{$nomeCliente}</strong>,</p>
            <p>Sua reserva <strong>#{$numeroReserva}</strong> foi renovada com sucesso.</p>
            <p>Segue em anexo o contrato em PDF com os valores atualizados.</p>
            <p>Atenciosamente,<br><strong>Equipe de Locação</strong></p>
        ";

        $mail->send();

        // Apagar o PDF temporário
        unlink($pdfPath);

        echo "<script>alert('E-mail com contrato enviado com sucesso!'); window.history.back();</script>";
    } catch (Exception $e) {
        unlink($pdfPath);
        echo "<script>alert('Erro ao enviar o e-mail: {$mail->ErrorInfo}'); window.history.back();</script>";
    }
}
?>
