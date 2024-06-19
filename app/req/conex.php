<?php

// conexão local
/*
$usuario = 'root';
$senha = 'masterkey';
$database = 'wayssoft_account';
$host = 'localhost';
*/


// conexão de produção
$usuario = 'admin';
$senha = 'EVBiao11376';
$database = 'cataloguei_shop';
$host = 'database-1.chn5ehp3ipjv.us-west-2.rds.amazonaws.com';

$mysqli = new mysqli($host, $usuario, $senha, $database);

if($mysqli->error) {
    die("Falha ao conectar ao banco de dados: " . $mysqli->error);
}

?>