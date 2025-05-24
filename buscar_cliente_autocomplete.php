<?php
require_once("Config.php");
require_once("Cliente.php");

if (isset($_GET['term'])) {
    $term = $_GET['term'];

    $db = new Database();
    $conexao = $db->conectaBD();

    $consulta = "SELECT cpf FROM cliente WHERE cpf LIKE '$term%'";
    $result = mysqli_query($conexao, $consulta);
    
    $clientes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $clientes[] = $row['cpf'];
    }

    echo json_encode($clientes);

    mysqli_close($conexao);
}
?>
