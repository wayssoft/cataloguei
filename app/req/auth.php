<?php
header("Access-Control-Allow-Origin: *");
header('Cache-Control: no-cache, must-revalidate'); 
header("Content-Type: application/json; charset=UTF-8");
header("HTTP/1.1 200 OK");

// Inicializa uma resposta padrão
$response = array('status' => 'error', 'message' => 'Login falhou');


include('conex.php');
if(!isset($_COOKIE['authorization_id'])) 
{
    $response['message'] = 'Não foi encontrado o id no cookie';
}

if(!isset($_COOKIE['authorization_type'])) 
{
    $response['message'] = 'Não foi encontrado o type no cookie';
}

// verifica se tem o usuario na base de dados
if ($_COOKIE['authorization_type'] == 'company'){
    $sql_code = "SELECT * FROM empresa WHERE id = ".$_COOKIE['authorization_id'];
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 0) 
    {
        $response['message'] = 'Não foi encontrado a empresa';
    }else{
        $response['status'] = 'success';
        $response['message'] = 'Login bem-sucedido';
    }   
}

// Retorna a resposta como JSON
echo(json_encode($response));

?>