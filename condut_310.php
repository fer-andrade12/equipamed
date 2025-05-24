<?php
require_once("Config.php");
require_once("Cliente.php");

// Criando a conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();

// Criando um objeto Cliente e passando a conexão
$cliente = new Cliente($conexao);
$cliente->setCpf($_REQUEST["cpf"] ?? null);
$cliente->setNome($_REQUEST["nome"] ?? null);
$cliente->setEmail($_REQUEST["email"] ?? null);
$cliente->setEndereco($_REQUEST["endereco"] ?? null);
$cliente->setCep($_REQUEST["cep"] ?? null);
$cliente->setNumero($_REQUEST["numero"] ?? null);
$cliente->setComplemento($_REQUEST["complemento"] ?? null);
$cliente->setTelefone($_REQUEST["telefone"] ?? null);

$resultado = $cliente->Alterar();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Cliente - Alteração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .container {
            padding-top: 40px;
        }

        .table th {
            background-color: #0056b3;
            color: white;
            text-align: center;
        }

        .table td {
            text-align: center;
        }

        .success-message {
            text-align: center;
            font-size: 1.5rem;
            color: green;
            margin-top: 20px;
        }

        .btn-voltar {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center text-primary mb-4">Cadastro de Cliente - Alteração</h1>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>CPF</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Endereço</th>
                    <th>CEP</th>
                    <th>Número</th>
                    <th>Complemento</th>
                    <th>Telefone</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($cliente->getCpf()); ?></td>
                    <td><?php echo htmlspecialchars($cliente->getNome()); ?></td>
                    <td><?php echo htmlspecialchars($cliente->getEmail()); ?></td>
                    <td><?php echo htmlspecialchars($cliente->getEndereco()); ?></td>
                    <td><?php echo htmlspecialchars($cliente->getCep()); ?></td>
                    <td><?php echo htmlspecialchars($cliente->getNumero()); ?></td>
                    <td><?php echo htmlspecialchars($cliente->getComplemento()); ?></td>
                    <td><?php echo htmlspecialchars($cliente->getTelefone()); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if ($resultado): ?>
    <div class="alert alert-success text-center" role="alert">
        Cliente alterado com sucesso!
    </div>
    <?php else: ?>
        <div class="alert alert-danger text-center" role="alert">
            Erro ao alterar cliente. Tente novamente.
        </div>
    <?php endif; ?>


    <div class="text-center btn-voltar">
        <form action="condut_100.php" method="get">
            <button type="submit" class="btn btn-secondary btn-lg">Voltar</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
