<?php
require_once("Config.php");
require_once("Equipamento.php");

if (isset($_POST['codigoEquipamento'])) {
    $codigoEquipamento = $_POST['codigoEquipamento'];

    $db = new Database();
    $conexao = $db->conectaBD();

    // Prepara a consulta SQL com placeholder (evita SQL Injection)
    $stmt = $conexao->prepare("SELECT * FROM equipamento WHERE codigoEquipamento = ? LIMIT 1");

    if ($stmt) {
        // Associa o parâmetro e executa a consulta
        $stmt->bind_param("s", $codigoEquipamento);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo json_encode($row);
        } else {
            echo json_encode([]); // Retorna array vazio se não encontrar
        }

        // Fecha o statement
        $stmt->close();
    } else {
        echo json_encode(["error" => "Erro na preparação da consulta."]);
    }

    // Fecha a conexão
    $conexao->close();
}
?>
