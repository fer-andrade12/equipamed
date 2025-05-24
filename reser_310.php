<?php
require_once("Config.php");
require_once("Reservas.php");

// Criando a conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();

// Criando um objeto Reserva e passando a conexão
$res = new Reservas($conexao);
$res->setNumero($_REQUEST["numero"]);
$res->setRetorno($_REQUEST["ret"]);
$res->Fechar();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registro de Retorno</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }
        .toast-body {
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Reserva Alterada com Sucesso!</h2>

    <div class="card p-4 shadow-sm">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Reserva</th>
                    <th>Retorno</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo($res->getNumero()) ?></td>
                    <td><?php echo($res->getRetorno()) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-center">
        <form name="manut" action="reser_100.php" method="get">
            <button type="submit" class="btn btn-secondary">
                Voltar
            </button>
        </form>
    </div>
</div>

<!-- Toast de feedback -->
<div id="feedbackToast" class="toast bg-success" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
  <div class="toast-header">
    <strong class="mr-auto">Sucesso!</strong>
    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Fechar">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="toast-body">
    Reserva alterada com sucesso.
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Exibe o Toast de sucesso
    $(document).ready(function() {
        $('#feedbackToast').toast('show');
    });
</script>

</body>
</html>
