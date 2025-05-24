<?php
require_once("Config.php");
require_once("Cliente.php");

$db = new Database();
$conexao = $db->conectaBD();
$cliente = new Cliente($conexao);

if (!empty($_POST["cpf"]) && !empty($_POST["nome"]) && !empty($_POST["email"]) && !empty($_POST["cep"]) && !empty($_POST["endereco"]) && !empty($_POST["numero"]) && !empty($_POST["complemento"]) && !empty($_POST["telefone"])) {
    $cpf = preg_replace('/\D/', '', $_POST["cpf"]);
    $cliente->setCpf($cpf);
    $nome = substr($_POST["nome"], 0, 50);
    $cliente->setNome($nome);
    $cliente->setEmail($_POST["email"]);
    $cliente->setEndereco($_POST["endereco"]);
    $cep = preg_replace('/\D/', '', $_POST["cep"]);
    $cliente->setCep($cep);
    $cliente->setNumero($_POST["numero"]);
    $cliente->setComplemento($_POST["complemento"]);
    $cliente->setTelefone($_POST["telefone"]);

    $query = "SELECT cpf FROM cliente WHERE cpf = ?";
    $stmt = $conexao->prepare($query);
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("<div class='alert alert-danger text-center mt-5'>Erro: Este CPF já está cadastrado!</div>");
    }

    $cliente->Incluir();
} else {
    die("<div class='alert alert-danger text-center mt-5'>Erro: Todos os campos são obrigatórios!</div>");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Cliente - Inclusão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4">Cadastro de Cliente - Inclusão</h2>

    <div class="alert alert-success text-center">
        Registro inserido com sucesso!
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-primary">
            <tr>
                <th>CPF</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Endereço</th>
                <th>CEP</th>
                <th>Número</th>
                <th>Complemento</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($cliente->getCpf()); ?></td>
                <td><?php echo htmlspecialchars($cliente->getNome()); ?></td>
                <td><?php echo htmlspecialchars($cliente->getEmail()); ?></td>
                <td><?php echo htmlspecialchars($cliente->getTelefone()); ?></td>
                <td><?php echo htmlspecialchars($cliente->getEndereco()); ?></td>
                <td><?php echo htmlspecialchars($cliente->getCep()); ?></td>
                <td><?php echo htmlspecialchars($cliente->getNumero()); ?></td>
                <td><?php echo htmlspecialchars($cliente->getComplemento()); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <form action="condut_100.php" method="get">
            <button type="submit" class="btn btn-primary">Voltar</button>
        </form>
    </div>
</div>

</body>
</html>
