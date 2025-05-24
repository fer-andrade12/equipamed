<?php
// Evita redefinir a classe Database caso já tenha sido declarada
if (!class_exists('Database')) {
    class Database {
        private $host = "localhost";
        private $usuario = "root";
        private $senha = "root";
        private $bd = "equipamed";
        private $porta = 3306; // Porta do MySQL (confirme no Workbench)
        public $con;

        public function __construct() {
            // Conectar ao MySQL no servidor correto
            $this->con = new mysqli($this->host, $this->usuario, $this->senha, $this->bd, $this->porta);
    
            if ($this->con->connect_error) {
                die("Erro na conexão com MySQL: " . $this->con->connect_error);
            }
        }

        public function conectaBD() {
            return $this->con;
        }
    }
}
?>
