// Classe Contrato
class Contrato {
    public static function gerarContrato(Locacao $locacao) {
        $contrato = "CONTRATO DE LOCAÇÃO\n";
        $contrato .= "Cliente: {$locacao->cliente->nome}\n";
        $contrato .= "Email: {$locacao->cliente->email}\n";
        $contrato .= "Equipamento: {$locacao->equipamento->nome} - Modelo: {$locacao->equipamento->modelo}\n";
        $contrato .= "Período: " . $locacao->dataInicio->format('d/m/Y') . " até " . $locacao->dataFim->format('d/m/Y') . "\n";
        $contrato .= "Caução: R$ {$locacao->valorCaucao}\n";

        echo "Contrato gerado:\n";
        echo $contrato;

        return $contrato;
    }
}
