<?php
require_once("Config.php");
require_once("Cliente.php");

$db = new Database();
$conexao = $db->conectaBD();
$cliente = new Cliente($conexao);
$cliente->setCpf($_REQUEST["codigo"]);

$mensagemErro = "";
$mensagemSucesso = "";

try {
    $cliente->Excluir();
    $mensagemSucesso = "CPF do cliente <strong>" . htmlspecialchars($cliente->getCpf()) . "</strong> excluído com sucesso.";
} catch (Exception $e) {
    $mensagemErro = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Exclusão de Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0">Cadastro de Cliente - Exclusão</h4>
        </div>
        <div class="card-body">

            <?php if ($mensagemErro): ?>
                <div class="alert alert-danger"><?php echo $mensagemErro; ?></div>
            <?php elseif ($mensagemSucesso): ?>
                <div class="alert alert-success"><?php echo $mensagemSucesso; ?></div>
            <?php endif; ?>

            <form action="condut_100.php" method="get">
                <button type="submit" class="btn btn-primary">Voltar</button>
            </form>

        </div>
    </div>
</div>

</body>
</html>
