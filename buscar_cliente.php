<?php
require_once("Config.php");
require_once("Cliente.php");

if (isset($_POST['cpf'])) {
    $cpf = $_POST['cpf'];

    $db = new Database();
    $conexao = $db->conectaBD();

    $consulta = "SELECT * FROM cliente WHERE cpf = '$cpf'";
    $result = mysqli_query($conexao, $consulta);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row); // Retorna os dados do cliente em formato JSON
    } else {
        echo json_encode([]); // Retorna um array vazio se não encontrar o cliente
    }

    mysqli_close($conexao);
}
?>
