<?php
require_once("Config.php");
require_once("Cobrancas.php");

// Criando a conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();

// Criando objeto da classe e passando a conexão
$cob = new Cobrancas($conexao);

// Verifica se a conexão foi estabelecida corretamente
if (!$conexao) {
    die("Erro ao conectar ao banco de dados.");
}

if (isset($_REQUEST["codigo"])) {
    $cob->setNumero($_REQUEST["codigo"]);
    $cob->Selecionar();
    $cob->Calcular();
} else {
    die("Código da reserva não informado.");
}
?>

<html>
<body>
<head>
<style>
    tr {background:silver; font: 14pt arial}
</style>
</head>
<body>
<table>
<tr><th colspan=3>Número da Reserva: <?php echo htmlspecialchars($cob->getNumero())?></th></tr>
<tr><th colspan=3>Registro Locação</th></tr>
<tr><td colspan=2><b>Cliente: <?php echo htmlspecialchars($cob->getNome())?></b></td> 
<th>Data Vencimento</th></tr>
<tr><th>Data Locação: <?php echo date("d/m/Y", strtotime($cob->getSaida())) ?></th>
<th>Data Retorno: <?php echo date("d/m/Y", strtotime($cob->getRetorno())) ?></th>
<th><?php echo htmlspecialchars($cob->getVenc()) ?></th></tr>
<tr><th>Valor Mensal: <?php echo number_format($cob->getValorMensal(), 2, ',', '.') ?></th>
<tr><th>Valor Quinzenal: <?php echo number_format($cob->getValorQuinzenal(), 2, ',', '.') ?></th>
<th>Total de dias: <?php echo $cob->getDias() ?></th>
<th>Valor a Cobrar: <?php echo number_format($cob->getTotal(), 2, ',', '.') ?></th></tr>
</table><p>
<form name="manut" action="cobr_100.php" method="get">
<input name="volta" type="submit" value="Voltar">
</form>
</body>
</html>
