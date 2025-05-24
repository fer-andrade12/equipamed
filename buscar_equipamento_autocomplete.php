<?php
require_once("Config.php");
require_once("Equipamento.php");

if (isset($_GET['term'])) {
    $term = $_GET['term'];

    $db = new Database();
    $conexao = $db->conectaBD();

    $consulta = "SELECT codigoEquipamento FROM equipamento WHERE codigoEquipamento LIKE '$term%'";
    $result = mysqli_query($conexao, $consulta);
    
    $equipamento = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $equipamento[] = $row['codigoEquipamento'];
    }

    echo json_encode($equipamento);

    mysqli_close($conexao);
}
?>
