<?php
require_once("Config.php");
require_once("Reservas.php");

// Criando a conexão com o banco
$db = new Database();
$conexao = $db->conectaBD();

// Criando um objeto Reserva e passando a conexão
$res = new Reservas($conexao);

$codigo = $_REQUEST["codigo"];
$consulta = "SELECT * FROM reservas WHERE numero = " . intval($codigo);
$conx = mysqli_query($conexao, $consulta);
$dado = mysqli_fetch_assoc($conx);

// Buscar a última renovação, se existir
$consultaRenovacao = "SELECT novaDataDevolucao FROM renovacoes WHERE numeroReserva = $codigo ORDER BY dataRenovacao DESC LIMIT 1";
$resultRenovacao = mysqli_query($conexao, $consultaRenovacao);

if ($rowRenovacao = mysqli_fetch_assoc($resultRenovacao)) {
    $dataRetorno = $rowRenovacao['novaDataDevolucao'];
} else {
    $dataRetorno = $dado['retorno'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registro de Retorno</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="funcoes.js"></script>
    <style>
        /* Estilo para o Toast */
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
    <h2 class="text-center mb-4">Registrar Retorno da Reserva</h2>

    <form name="res" action="reser_310.php" method="post">
        <div class="card p-4 shadow-sm">
            <div class="form-group">
                <label>Reserva:</label>
                <input type="text" name="numero" class="form-control" readonly value="<?php echo $codigo; ?>">
            </div>

            <div class="form-group">
                <label>Data de Saída:</label>
                <input type="text" name="saida" id="saida" class="form-control" readonly value="<?php echo date("d/m/Y", strtotime($dado['saida'])); ?>">
                <input type="hidden" name="saida_h" id="saida_h" value="<?php echo date("Y-m-d", strtotime($dado['saida'])); ?>">
            </div>

            <div class="form-group">
                <label>Data de Retorno:</label>
                <input type="date" name="ret" id="ret" class="form-control" required value="<?php echo date('Y-m-d', strtotime($dataRetorno)); ?>">
            </div>

            <div class="form-group">
                <label>CPF do Cliente:</label>
                <input type="text" name="cpf" class="form-control" readonly value="<?php echo $dado['cpf']; ?>">
            </div>

            <div class="form-group">
                <label>Código do Equipamento:</label>
                <input type="text" name="equipamento" class="form-control" readonly value="<?php echo $dado['codigoEquipamento']; ?>">
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" id="butvalida" class="btn btn-info" onclick="validaret()">Validar Data de Retorno</button>
                <button type="submit" id="subgrava" class="btn btn-success" disabled>Gravar Retorno</button>
            </div>
        </div>
    </form>

    <div class="mt-4 text-center">
        <form action="reser_100.php" method="get">
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </button>
        </form>
    </div>
</div>

<!-- Toast de feedback (Mensagem de sucesso ou erro) -->
<div id="feedbackToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
  <div class="toast-header">
    <strong id="toastTitle" class="mr-auto">Validação de Data</strong>
    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Fechar">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="toast-body" id="toastMessage">
    A data de retorno está válida.
  </div>
</div>

<!-- Bootstrap e FontAwesome -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>

<script>
// Função para validar a data de retorno
function validaret() {
    var saida = document.getElementById('saida_h').value; // formato YYYY-MM-DD
    var ret = document.getElementById('ret').value;       // formato YYYY-MM-DD

    if (!ret) {
        showToast("Por favor, informe a data de retorno.", "error");
        return;
    }

    // Quebra as datas no formato ano, mês e dia
    var partsSaida = saida.split("-");
    var partsRet = ret.split("-");

    var dataSaida = new Date(partsSaida[0], partsSaida[1] - 1, partsSaida[2]);
    var dataRet = new Date(partsRet[0], partsRet[1] - 1, partsRet[2]);

    if (dataRet < dataSaida) {
        showToast("A data de retorno não pode ser anterior à data de saída.", "error");
        document.getElementById('subgrava').disabled = true;
    } else {
        showToast("Data de retorno válida!", "success");
        document.getElementById('subgrava').disabled = false;
    }
}

// Função para mostrar o Toast com a mensagem personalizada
function showToast(message, type) {
    var toast = document.getElementById('feedbackToast');
    var toastMessage = document.getElementById('toastMessage');
    var toastTitle = document.getElementById('toastTitle');

    toastMessage.textContent = message;

    if (type === "error") {
        toast.classList.remove('bg-success');
        toast.classList.add('bg-danger');
        toastTitle.textContent = "Erro na Validação";
    } else if (type === "success") {
        toast.classList.remove('bg-danger');
        toast.classList.add('bg-success');
        toastTitle.textContent = "Validação de Data";
    }

    // Exibe o toast
    $(toast).toast('show');
}
</script>
</body>
</html>

<?php
mysqli_close($conexao);
?>
