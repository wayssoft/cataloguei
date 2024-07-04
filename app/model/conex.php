<?php

// conexão local
/*
$usuario = 'root';
$senha = 'masterkey';
$database = 'wayssoft_account';
$host = 'localhost';
*/

class ConexaoBD {
    private static $host = 'database-1.chn5ehp3ipjv.us-west-2.rds.amazonaws.com';
    private static $usuario = 'admin';
    private static $senha = 'EVBiao11376';
    private static $database = 'cataloguei_shop';

    private $mysqli;

    // Construtor da classe - abre a conexão com o banco de dados
    public function __construct() {
        $this->mysqli = new mysqli(self::$host, self::$usuario, self::$senha, self::$database);

        if ($this->mysqli->connect_error) {
            die("Falha ao conectar ao banco de dados: " . $this->mysqli->connect_error);
        }
    }

    // Método para executar consultas SQL
    public function query($sql) {
        return $this->mysqli->query($sql);
    }

    // Método para preparar consultas SQL
    public function prepare($sql) {
        return $this->mysqli->prepare($sql);
    }  

    // Método para escapar strings
    public function real_escape_string($string) {
        return $this->mysqli->real_escape_string($string);
    }

    // Método para fechar a conexão com o banco de dados
    public function fecharConexao() {
        $this->mysqli->close();
    }

    // Destrutor da classe - fecha a conexão com o banco de dados quando o objeto é destruído
    public function __destruct() {
        $this->fecharConexao();
    }
}




?>