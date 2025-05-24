<?php
require_once("Config.php");
require_once("Reservas.php");

class Reservas
{
    private $numero;
    private $saida;
    private $retorno;
    private $cpf;
    private $codigoEquipamento;
    private $valorMensal;
    private $valorQuinzenal;
    private $tipoLocacao;
    private $reservaCancelada;
    private $motivo_cancelamento;  // Corrigido para atributo correto
    private $dataCancelamento;
    private $dataRenovacao;
    private $valorRenovacao;
    private $reservaFinalizada;
    private $dataFinalizada;


    public function __construct(
        $conexao, 
        $numero = null, 
        $saida = null, 
        $retorno = null, 
        $cpf = null, 
        $codigoEquipamento = null, 
        $valorMensal = null, 
        $valorQuinzenal = null, 
        $tipoLocacao = null, 
        $reservaCancelada = null, 
        $motivo_cancelamento = null, 
        $dataCancelamento = null, 
        $dataRenovacao = null, 
        $valorRenovacao = null,
        $reservaFinalizada = null,   // << adicionado
        $dataFinalizada = null       // << adicionado
    ) {
        $this->con = $conexao;
        $this->numero = $numero;
        $this->saida = $saida;
        $this->retorno = $retorno;
        $this->cpf = $cpf;
        $this->codigoEquipamento = $codigoEquipamento;
        $this->valorMensal = $valorMensal;
        $this->valorQuinzenal = $valorQuinzenal;
        $this->tipoLocacao = $tipoLocacao;
        $this->reservaCancelada = $reservaCancelada;
        $this->motivo_cancelamento = $motivo_cancelamento;
        $this->dataCancelamento = $dataCancelamento;
        $this->dataRenovacao = $dataRenovacao;
        $this->valorRenovacao = $valorRenovacao;
        $this->reservaFinalizada = $reservaFinalizada;   // << adicionado
        $this->dataFinalizada = $dataFinalizada;         // << adicionado
    }
    

    public function getReservaCancelada() {
        return $this->reservaCancelada ?? false;
    }    

    // registrar locação
    public function Registrar() {
        if (empty($this->codigoEquipamento)) {
            return "Erro: Código do equipamento não fornecido.";
        }
    
        // Buscar preço atual do equipamento
        $query1 = $query1 = "SELECT valorMensal, valorQuinzenal FROM equipamento WHERE codigoEquipamento = ?";
        if ($stmt = mysqli_prepare($this->con, $query1)) {
            mysqli_stmt_bind_param($stmt, "i", $this->codigoEquipamento);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_bind_result($stmt, $valorMensal, $valorQuinzenal);
                if (mysqli_stmt_fetch($stmt)) {
                    $this->valorMensal = $valorMensal;
                    $this->valorQuinzenal = $valorQuinzenal;
                } else {
                    return "Erro: Equipamento não encontrado.";
                }
            } else {
                return "Erro ao executar a consulta de preço.";
            }
            mysqli_stmt_close($stmt);
        } else {
            return "Erro ao preparar a consulta de preço.";
        }

         // Cálculo automático dos valores com base no tipo de locação
        if ($this->tipoLocacao == "mensal") {
            $this->valorMensal = $this->valorMensal;  // O valor mensal deve ser mantido
            $this->valorQuinzenal = 0.00;  // O valor quinzenal será 0 se for mensal
        } elseif ($this->tipoLocacao == "quinzenal") {
            $this->valorQuinzenal = $this->valorQuinzenal;  // O valor quinzenal será mantido
            $this->valorMensal = 0.00;  // O valor mensal será 0 se for quinzenal
        } else {
            return "Tipo de locação inválido.";
        }
    
        // Alteração na query para usar valorMensal e valorQuinzenal em vez de preco
        $query = "INSERT INTO reservas (saida, cpf, codigoEquipamento, valorMensal, valorQuinzenal, tipoLocacao, retorno) 
          VALUES (?, ?, ?, ?, ?, ?, ?)";
    
        if ($stmt = mysqli_prepare($this->con, $query)) {
            mysqli_stmt_bind_param($stmt, "ssiddss",
                $this->saida,
                $this->cpf,
                $this->codigoEquipamento,
                $this->valorMensal,
                $this->valorQuinzenal,
                $this->tipoLocacao,
                $this->retorno
            );
        
            if (mysqli_stmt_execute($stmt)) {
                $numeroReserva = mysqli_insert_id($this->con);
                mysqli_stmt_close($stmt);
                mysqli_close($this->con);
                return $numeroReserva;
            } else {
                mysqli_stmt_close($stmt);
                mysqli_close($this->con);
                return "Erro ao registrar a reserva!";
            }
        } else {
            mysqli_close($this->con);
            return "Erro ao preparar a consulta de inserção.";
        }
    }

   // renovar locação
public function Renovar() {
    if (empty($this->numero) || empty($this->retorno)) {
        return "Erro: número da reserva ou nova data de devolução não definidos.";
    }

    // Busca o código do equipamento da reserva
    $sql = "SELECT codigoEquipamento FROM reservas WHERE numero = ?";
    $stmt = mysqli_prepare($this->con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $this->numero);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $codigoEquipamento);

    if (!mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        return "Reserva não encontrada.";
    }
    mysqli_stmt_close($stmt);

    // Busca valor mensal e quinzenal atual do equipamento
    $queryEquip = "SELECT valorMensal, valorQuinzenal FROM equipamento WHERE codigoEquipamento = ?";
    $stmt = mysqli_prepare($this->con, $queryEquip);
    mysqli_stmt_bind_param($stmt, "i", $codigoEquipamento);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $valorMensal, $valorQuinzenal);

    if (!mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        return "Erro: Equipamento não encontrado.";
    }
    mysqli_stmt_close($stmt);

    // Define o valor da renovação com base no tipo de locação
    $this->valorRenovacao = match ($this->tipoLocacao) {
        'mensal'    => $valorMensal,
        'quinzenal' => $valorQuinzenal,
        default     => null
    };

    if (is_null($this->valorRenovacao)) {
        return "Tipo de locação inválido.";
    }

    $dataRenovacao = date('Y-m-d H:i:s');

    // Atualiza a data de retorno da reserva
    $updateSql = "UPDATE reservas SET retorno = ? WHERE numero = ?";
    $updateStmt = mysqli_prepare($this->con, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "si", $this->retorno, $this->numero);

    if (!mysqli_stmt_execute($updateStmt)) {
        $erro = mysqli_error($this->con);
        mysqli_stmt_close($updateStmt);
        return "Erro ao atualizar a data de retorno: $erro";
    }
    mysqli_stmt_close($updateStmt);

    // Registra renovação
    $queryRenovacao = "INSERT INTO renovacoes (numeroReserva, dataRenovacao, novaDataDevolucao, valorRenovacao)
                       VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($this->con, $queryRenovacao);
    mysqli_stmt_bind_param($stmt, "issd", $this->numero, $dataRenovacao, $this->retorno, $this->valorRenovacao);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return "Renovação registrada com sucesso!";
    } else {
        $erro = mysqli_error($this->con);
        mysqli_stmt_close($stmt);
        return "Erro ao executar a renovação: $erro";
    }
}

    
    
    
    // encerrar locação
    // Encerrar locação
    public function Fechar() {
        if (empty($this->numero) || empty($this->retorno)) {
            return "Erro: número da reserva ou data de retorno não definidos.";
        }

        $dataFinalizada = date('Y-m-d H:i:s'); // Data e hora atual

        $query = "UPDATE reservas 
                SET retorno = ?, reservaFinalizada = TRUE, dataFinalizada = ? 
                WHERE numero = ?";

        if ($stmt = mysqli_prepare($this->con, $query)) {
            mysqli_stmt_bind_param($stmt, "ssi", $this->retorno, $dataFinalizada, $this->numero);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_close($this->con);
                return "Reserva fechada com sucesso.";
            } else {
                $erro = mysqli_error($this->con);
                mysqli_stmt_close($stmt);
                mysqli_close($this->con);
                return "Erro ao atualizar a reserva: $erro";
            }
        } else {
            mysqli_close($this->con);
            return "Erro ao preparar a consulta de atualização.";
        }
    }


    //canelamento 
    public function cancelarReserva($numeroReserva, $motivoCancelamento) {
        // Consulta para atualizar a reserva
        $query = "UPDATE reservas SET reservaCancelada = TRUE, motivoCancelamento = ?, dataCancelamento = NOW() WHERE numero = ?";
    
        // Preparando a consulta
        if ($stmt = mysqli_prepare($this->con, $query)) {
            // Vinculando os parâmetros
            mysqli_stmt_bind_param($stmt, "si", $motivoCancelamento, $numeroReserva);
    
            // Executando a consulta
            if (mysqli_stmt_execute($stmt)) {
                // Atualizando os dados da reserva localmente
                $this->setReservaCancelada(true);
                $this->setMotivoCancelamento($motivoCancelamento);
                $this->setDataCancelamento(date('Y-m-d H:i:s'));
    
                // Fechando a declaração
                mysqli_stmt_close($stmt);
                return true;  // Reserva cancelada com sucesso
            } else {
                mysqli_stmt_close($stmt);
                return false;  // Falha ao executar a consulta
            }
        } else {
            return false;  // Erro ao preparar a consulta
        }
    }

    // Buscar a última data de renovação
    public function buscarDataRenovacao($numeroReserva)
    {
        $query = "SELECT dataRenovacao 
                FROM renovacoes 
                WHERE numeroReserva = ? 
                ORDER BY dataRenovacao DESC 
                LIMIT 1";

        if ($stmt = mysqli_prepare($this->con, $query)) {
            mysqli_stmt_bind_param($stmt, "i", $numeroReserva);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_bind_result($stmt, $dataRenovacao);
                if (mysqli_stmt_fetch($stmt)) {
                    mysqli_stmt_close($stmt);
                    return $dataRenovacao;
                } else {
                    mysqli_stmt_close($stmt);
                    return null; // Nenhuma renovação encontrada
                }
            } else {
                mysqli_stmt_close($stmt);
                return null; // Erro na execução
            }
        } else {
            return null; // Erro na preparação
        }
    }

    
    // ---- Getters e Setters --------
    public function getNumero() {
        return $this->numero;
    }

    public function setNumero($numero) {
        $this->numero = $numero;
    }

    public function getSaida() {
        return $this->saida;
    }

    public function setSaida($saida) {
        $this->saida = $saida;
    }

    public function getRetorno() {
        return $this->retorno;
    }

    public function setRetorno($retorno) {
        $this->retorno = $retorno;
    }

    public function getCpf() {
        return $this->cpf;
    }

    public function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    public function getCodigoEquipamento() {
        return $this->codigoEquipamento;
    }

    public function setCodigoEquipamento($codigoEquipamento) {
        $this->codigoEquipamento = $codigoEquipamento;
    }

    public function getValorMensal() {
        return $this->valorMensal;
    }

    public function setValorMensal($valorMensal) {
        $this->valorMensal = $valorMensal;
    }

    public function getValorQuinzenal() {
        return $this->valorQuinzenal;
    }

    public function setValorQuinzenal($valorQuinzenal) {
        $this->valorQuinzenal = $valorQuinzenal;
    }

    public function getTipoLocacao() {
        return $this->tipoLocacao;
    }

    public function setTipoLocacao($tipoLocacao) {
        $this->tipoLocacao = $tipoLocacao;
    }

    public function getDataRenovacao() {
        return $this->dataRenovacao;
    }

    public function setDataRenovacao($dataRenovacao) {
        $this->dataRenovacao = $dataRenovacao;
    }

    public function getValorRenovacao() {
        return $this->valorRenovacao;
    }

    public function setValorRenovacao($valorRenovacao) {
        $this->valorRenovacao = $valorRenovacao;
    }

    public function getMotivoCancelamento() {
        return $this->motivo_cancelamento;
    }

    public function setMotivoCancelamento($motivo_cancelamento) {
        $this->motivo_cancelamento = $motivo_cancelamento;
    }

    public function getDataCancelamento() {
        return $this->dataCancelamento;
    }

    public function setDataCancelamento($dataCancelamento) {
        $this->dataCancelamento = $dataCancelamento;
    }

    // Getter e Setter para reservaFinalizada
    public function getReservaFinalizada() {
        return $this->reservaFinalizada;
    }

    public function setReservaFinalizada($reservaFinalizada) {
        $this->reservaFinalizada = $reservaFinalizada;
    }

    // Getter e Setter para dataFinalizada
    public function getDataFinalizada() {
        return $this->dataFinalizada;
    }

    public function setDataFinalizada($dataFinalizada) {
        $this->dataFinalizada = $dataFinalizada;
    }


}
?>
