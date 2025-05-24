<?php 
require_once("Config.php");
require_once("Equipamento.php");

class Equipamento
{
    private $codigoEquipamento;
    private $nome;
    private $marca;
    private $modelo;
    private $peso;
    private $valorCaucao;
    private $valorMensal; // Corrigido nome
    private $valorQuinzenal;

    public $con; // variável para conexão com BD

    // Construtor para receber a conexão com o banco
    public function __construct($conexao) {
        $this->con = $conexao;
    }

    public function Incluir()
    {
        try {
            // Preparar a instrução
            $stmt = $this->con->prepare("INSERT INTO equipamento (nome, marca, modelo, peso, valorCaucao, valorMensal, valorQuinzenal) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
            if (!$stmt) {
                throw new Exception("Erro ao preparar a query: " . $this->con->error);
            }
    
            // Vincular os parâmetros
            if (!$stmt->bind_param("sssdddd", $this->nome, $this->marca, $this->modelo, $this->peso, $this->valorCaucao, $this->valorMensal, $this->valorQuinzenal)) {
                throw new Exception("Erro ao vincular os parâmetros: " . $stmt->error);
            }
    
            // Executar a query
            if (!$stmt->execute()) {
                throw new Exception("Erro ao executar a query: " . $stmt->error);
            }
    
            // Verificar resultado e salvar o ID gerado
            if ($stmt->affected_rows > 0) {
                $this->codigoEquipamento = $this->con->insert_id; // <- ESSENCIAL
                $stmt->close();
                return true;
            } else {
                echo "⚠️ Nenhum registro foi incluído.";
            }
    
            $stmt->close();
            return false;
        } catch (Exception $e) {
            echo "❌ Ocorreu um erro: " . $e->getMessage();
            return false;
        }
    }

    public function Alterar()
    {
        try {
            $stmt = $this->con->prepare("UPDATE equipamento SET nome=?, marca = ?, modelo = ?, peso = ?, valorCaucao = ?, valorMensal = ?, valorQuinzenal = ? WHERE codigoEquipamento = ?");
            $stmt->bind_param("sssddddi", $this->nome, $this->marca, $this->modelo, $this->peso, $this->valorCaucao, $this->valorMensal, $this->valorQuinzenal, $this->codigoEquipamento);
            $stmt->execute();
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            echo "<div class='alert alert-danger'>Erro ao alterar equipamento: " . $e->getMessage() . "</div>";
        }
    }

    public function Excluir()
    {
        try {
            $stmt = $this->con->prepare("DELETE FROM equipamento WHERE codigoEquipamento = ?");
            $stmt->bind_param("i", $this->codigoEquipamento);
            $stmt->execute();
            $stmt->close();
            return true;
        } catch (mysqli_sql_exception $e) {
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                // Log ou tratamento silencioso, retorna false para controle no front
                return false;
            } else {
                throw $e; // relança exceções inesperadas
            }
        }
    }


    // Métodos Getters e Setters
    public function getCodigoEquipamento() { return $this->codigoEquipamento; }
    public function setCodigoEquipamento($codigoEquipamento) { $this->codigoEquipamento = $codigoEquipamento; }

    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }

    public function getMarca() { return $this->marca; }
    public function setMarca($marca) { $this->marca = $marca; }

    public function getModelo() { return $this->modelo; }
    public function setModelo($modelo) { $this->modelo = $modelo; }

    public function getPeso() { return $this->peso; }
    public function setPeso($peso) { $this->peso = $peso; }

    public function getValorCaucao() { return $this->valorCaucao; }
    public function setValorCaucao($valorCaucao) { $this->valorCaucao = $valorCaucao; }

    public function getValorMensal() { return $this->valorMensal; }
    public function setValorMensal($valorMensal) { $this->valorMensal = $valorMensal; }

    public function getValorQuinzenal() { return $this->valorQuinzenal; }
    public function setValorQuinzenal($valorQuinzenal) { $this->valorQuinzenal = $valorQuinzenal; }
}
?>
