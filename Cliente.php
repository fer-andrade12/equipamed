<?php
require_once("Config.php");
require_once("Cliente.php");

class Cliente {
    private $codigoCliente;
    private $cpf;
    private $nome;
    private $telefone;
    private $email;
    private $endereco;
    private $numero;
    private $cep;
    private $complemento;
    
    private $con; // variável para conexão com BD
    
    // Construtor para receber a conexão com o banco
    public function __construct($conexao) {
        $this->con = $conexao;
    }
	

    public function Incluir() {
        try {
            // Ativa exceções para erros do mysqli
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
            $query = "INSERT INTO cliente (cpf, nome, email, endereco, numero, cep, complemento, telefone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("ssssssss", $this->cpf, $this->nome, $this->email, $this->endereco, $this->numero, $this->cep, $this->complemento, $this->telefone);
            $stmt->execute();
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            echo "Erro ao inserir cliente: " . $e->getMessage();
        }
    }
    
    
    
    public function Alterar() {
        // Alteração de dados do cliente
        $query = "UPDATE cliente SET nome = ?, email = ?, endereco = ?, numero = ?, cep = ?, complemento = ?, telefone = ? WHERE cpf = ?";
        
        // Preparando a consulta
        $stmt = $this->con->prepare($query);
    
        // Garantindo que todas as variáveis estejam corretas
        $nome = $this->nome ?: NULL;
        $email = $this->email ?: NULL;
        $endereco = $this->endereco ?: NULL;
        $numero = $this->numero ?: NULL;
        $cep = $this->cep ?: NULL;
        $complemento = $this->complemento ?: NULL;
        $telefone = $this->telefone ?: NULL;
        $cpf = $this->cpf ?: NULL;
    
        // Corrigindo o bind_param (ordem correta dos campos)
        $stmt->bind_param(
            "ssssssss",
            $nome, 
            $email, 
            $endereco, 
            $numero, 
            $cep, 
            $complemento, 
            $telefone, 
            $cpf
        );
    
        // Executando a consulta
        $success = $stmt->execute();
    
        // Fechando a declaração
        $stmt->close();
    
        return $success;
    }
    
    public function Excluir() {
        try {
            // Verifica se cliente possui reservas
            $verifica = $this->con->prepare("SELECT COUNT(*) FROM reservas WHERE cpf = ?");
            $verifica->bind_param("s", $this->cpf);
            $verifica->execute();
            $verifica->bind_result($total);
            $verifica->fetch();
            $verifica->close();
    
            if ($total > 0) {
                throw new Exception("Erro: Este cliente possui reservas e não pode ser excluído.");
            }
    
            $stmt = $this->con->prepare("DELETE FROM cliente WHERE cpf = ?");
            $stmt->bind_param("s", $this->cpf);
            $stmt->execute();
            $stmt->close();
            
        } catch (Exception $e) {
            throw $e; // Propaga a exceção para o código que chamou
        }
    }
    
    
    
    // ---- getters e setters --------
    
    public function getCpf() {
        return $this->cpf;
    }
    public function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    public function getNome() {
        return $this->nome;
    }
    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getEmail() {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEndereco() {
        return $this->endereco;
    }
    public function setEndereco($endereco) {
        $this->endereco = $endereco;
    }

    public function getNumero() {
        return $this->numero;
    }
    public function setNumero($numero) {
        $this->numero = $numero;
    }

    public function getCep() {
        return $this->cep;
    }
    public function setCep($cep) {
        $this->cep = $cep;
    }

    public function getComplemento() {
        return $this->complemento;
    }
    public function setComplemento($complemento) {
        $this->complemento = $complemento;
    }
    
    public function getTelefone() {
        return $this->telefone;
    }
    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

}
?>
