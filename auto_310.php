<?php
require_once("Config.php");
require_once("Equipamento.php");

$db = new Database();
$conexao = $db->conectaBD();

$equipamento = new Equipamento($conexao);
$equipamento->setCodigoEquipamento($_REQUEST["codigoEquipamento"]);
$equipamento->setNome($_REQUEST["nome"]);
$equipamento->setMarca($_REQUEST["marca"]);
$equipamento->setModelo($_REQUEST["modelo"]);
$equipamento->setPeso($_REQUEST["peso"]);
$equipamento->setValorMensal($_REQUEST["valorMensal"]);
$equipamento->setValorQuinzenal($_REQUEST["valorQuinzenal"]);
$equipamento->setValorCaucao($_REQUEST["valorCaucao"]);
$equipamento->Alterar();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alteração de Equipamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Cadastro de Equipamento - Alteração</h4>
        </div>
        <div class="card-body">

            <div class="alert alert-success">
                Equipamento alterado com sucesso!
            </div>

            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Peso</th>
                        <th>Valor Mensal</th>
                        <th>Valor Quinzenal</th>
                        <th>Valor Caução</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $equipamento->getCodigoEquipamento(); ?></td>
                        <td><?= $equipamento->getNome(); ?></td>
                        <td><?= $equipamento->getMarca(); ?></td>
                        <td><?= $equipamento->getModelo(); ?></td>
                        <td><?= $equipamento->getPeso(); ?></td>
                        <td>R$ <?=$equipamento->getValorMensal(); ?></td>
                        <td>R$ <?= $equipamento->getValorQuinzenal(); ?></td>
                        <td>R$ <?=$equipamento->getValorCaucao(); ?></td>
                    </tr>
                </tbody>
            </table>

            <form action="auto_100.php" method="get">
                <button type="submit" class="btn btn-secondary">Voltar</button>
            </form>

        </div>
    </div>
</div>

</body>
</html>
