<?php
require_once("Config.php");
require_once("Reservas.php");
require_once("Cliente.php");
require_once("Equipamento.php");

class Cobrancas
{
    private $numero; //numero da reserva
    private $saida; // data da locação 
    private $retorno;
    private $valorMensal;
    private $valorQuinzenal;
    private $valorTotal;
    private $cpf;
    private $codigoEquipamento;
    private $nome;
    private $dias;
    private $dataPagamento;
    private $metodoPagamento; // Ex: 'pix', 'boleto', 'cartao'
    private $status; // 'pago', 'pendente', 'atrasado'
    private $dataFinalizada;

    private $conexao; // variável para conexão com BD

    // Construtor para armazenar a conexão
    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }

    // método Selecionar
    public function Selecionar()
    {
        // Certifique-se de que a conexão com o banco esteja ativa
        if (!$this->conexao) {
            die("Erro de conexão com o banco de dados.");
        }

        // Preparando a consulta com prepared statement para evitar SQL injection
        $query = "SELECT r.numero, r.saida, r.retorno, 
                  r.valorMensal, r.valorQuinzenal, r.cpf, r.codigoEquipamento, c.nome 
                  FROM reservas AS r 
                  INNER JOIN cliente AS c ON r.cpf = c.cpf 
                  WHERE r.numero = ?";

        // Preparando a declaração
        if ($stmt = mysqli_prepare($this->conexao, $query)) {
            // Vinculando o parâmetro
            mysqli_stmt_bind_param($stmt, "i", $this->numero);

            // Executando a consulta
            mysqli_stmt_execute($stmt);

            // Armazenando o resultado
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            if ($row) {
                $this->saida = $row['saida'];
                $this->retorno = $row['retorno'];
                $this->preco = $row['valorMensal'];
                $this->preco = $row['valorQuinzenal'];
                $this->cpf = $row['cpf'];
                $this->codigoEquipamento = $row['codigoEquipamento'];
                $this->nome = $row['nome'];
            }

            // Fechando a declaração
            mysqli_stmt_close($stmt);
        } else {
            die("Erro ao preparar a consulta: " . mysqli_error($this->conexao));
        }
    }

    public function Calcular()
    {
        // Calcula diferença de dias entre datas
        $dtinicio = date_create($this->saida);
        $dtfinal = date_create($this->retorno);
        $dif = date_diff($dtinicio, $dtfinal);
        $this->dias = $dif->format("%a");

        // Calcula data vencimento (+10 dias da data de retorno)
        $this->venc = date('d/m/Y', strtotime('+10 days', strtotime($this->retorno)));

        // Calcula total a pagar
        $this->total = $this->preco * $this->dias;
    }

    // ---- getters e setters --------

    public function getNumero() { return $this->numero; }
    public function setNumero($numero) { $this->numero = $numero; }

    public function getSaida() { return $this->saida; }
    public function setSaida($saida) { $this->saida = $saida; }

    public function getRetorno() { return $this->retorno; }
    public function setRetorno($retorno) { $this->retorno = $retorno; }

    public function getMensal() { return $this->valorMensal; }
    public function setMensal($valorMensal) { $this->valorMensal = $valorMensal; }

    public function getQuinzenal() { return $this->valorQuinzenal; }
    public function setQuinzenal($valorQuinzenal) { $this->valorQuinzenal = $valorQuinzenal; }

    public function getCpf() { return $this->cpf; }
    public function setCpf($cpf) { $this->cpf = $cpf; }

    public function getCodigoEquipamento() { return $this->codigoEquipamento; }
    public function setCodigoEquipamento($codigoEquipamento) { $this->codigoEquipamento = $codigoEquipamento; }

    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }

    public function getDias() { return $this->dias; }
    public function setDias($dias) { $this->dias = $dias; }

    public function getVenc() { return $this->venc; }
    public function setVenc($venc) { $this->venc = $venc; }

    public function getTotal() { return $this->total; }
    public function setTotal($total) { $this->total = $total; }

    public function getDataFinalizada() { return $this->dataFinalizada; }
    public function setDataFinalizada($dataFinalizada) { $this->dataFinalizada = $dataFinalizada; }
}
?>
