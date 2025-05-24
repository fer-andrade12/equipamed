<?php
require_once("Config.php");
require_once("Cliente.php");

// Criando a conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();  // Agora a conexão é criada antes de ser usada

// Criando um objeto Cliente e passando a conexão
$cliente = new Cliente($conexao);
$codigo = $_REQUEST["codigo"];
$consulta = "SELECT * FROM cliente WHERE cpf = '" . $codigo . "'"; 
$conx = mysqli_query($conexao, $consulta); 
$dado = mysqli_fetch_assoc($conx);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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

        .form-title {
            text-align: center;
            margin-bottom: 20px;
            color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="form-title">Cadastro de Cliente - Alteração</h1>

    <!-- Formulário de Alteração -->
    <form name="clienteutores" action="condut_310.php" method="post">
        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input type="text" name="cpf" id="cpf" class="form-control" size="15" maxlength="14" oninput="mascaraCPF(this)" readonly value="<?php echo $codigo; ?>">
        </div>

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" size="30" maxlength="30" required value="<?php echo $dado['nome']; ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" size="30" maxlength="50" required value="<?php echo $dado['email']; ?>">
        </div>

        <div class="mb-3">
            <label for="endereco" class="form-label">Endereço</label>
            <input type="text" name="endereco" id="endereco" class="form-control" size="30" maxlength="50" required value="<?php echo $dado['endereco']; ?>">
        </div>

        <div class="mb-3">
            <label for="numero" class="form-label">Número</label>
            <input type="text" name="numero" id="numero" class="form-control" size="30" maxlength="30" required value="<?php echo $dado['numero']; ?>">
        </div>

        <div class="mb-3">
            <label for="cep" class="form-label">CEP</label>
            <input type="text" name="cep" id="cep" class="form-control" size="30" maxlength="10" required value="<?php echo $dado['cep']; ?>" placeholder="XXXXX-XXX" onblur="consultaCep()">
        </div>

        <div class="mb-3">
            <label for="complemento" class="form-label">Complemento</label>
            <input type="text" name="complemento" id="complemento" class="form-control" size="30" maxlength="50" required value="<?php echo $dado['complemento']; ?>">
        </div>

        <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="text" name="telefone" id="telefone" class="form-control" oninput="mascaraTelefone(this)" maxlength="15" required value="<?php echo $dado['telefone']; ?>">
        </div>

        <div class="text-center">
            <input type="submit" value="Gravar" class="btn btn-success btn-lg">
        </div>
    </form>

    <!-- Botão Voltar -->
    <div class="text-center mt-4">
        <form action="condut_100.php" method="get">
            <input type="submit" value="Voltar" class="btn btn-secondary btn-lg">
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Máscara de CPF
    function mascaraCPF(campo) {
        var cpf = campo.value.replace(/\D/g, '');
        if (cpf.length <= 11) {
            cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
            cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
            cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            campo.value = cpf;
        }
    }

    // Máscara de telefone (formato: (XX) XXXXX-XXXX ou (XX) XXXX-XXXX)
    function mascaraTelefone(campo) {
        var tel = campo.value.replace(/\D/g, '');

        if (tel.length > 11) tel = tel.slice(0, 11); // Limita a 11 dígitos

        if (tel.length >= 10) {
            campo.value = tel.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3'); // (99) 99999-9999
        } else if (tel.length >= 6) {
            campo.value = tel.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3'); // (99) 9999-9999
        } else if (tel.length >= 3) {
            campo.value = tel.replace(/(\d{2})(\d{0,5})/, '($1) $2');
        } else {
            campo.value = tel;
        }
    }

    // Função para consultar o CEP e preencher os campos de endereço
    function consultaCep() {
        var cep = document.getElementById('cep').value.replace(/\D/g, '');
        if (cep != "") {
            var validacep = /^[0-9]{8}$/;
            if(validacep.test(cep)) {
                document.getElementById('endereco').value = "...";
                document.getElementById('complemento').value = "...";
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.erro) {
                            alert("CEP não encontrado.");
                        } else {
                            document.getElementById('endereco').value = data.logradouro;
                            document.getElementById('complemento').value = data.complemento || '';
                        }
                    })
                    .catch(error => {
                        alert("Erro ao buscar o CEP.");
                    });
            } else {
                alert("CEP inválido.");
            }
        }
    }
</script>

</body>
</html>
