<?php 
header("Access-Control-Allow-Origin: *");
header('Cache-Control: no-cache, must-revalidate'); 
header("Content-Type: application/json; charset=UTF-8");
//header("HTTP/1.1 200 OK");

require_once 'conex.php';

// function log-in
function login($mysqli, $num_whatsapp, $senha){
    // Inicializa uma resposta padrão
    $response = array('status' => 'error', 'message' => 'Login falhou');
    // trada os dados de entrada
    $num  = $mysqli->real_escape_string($num_whatsapp);
    $pass = $mysqli->real_escape_string($senha);
    $num  = preg_replace('/\D/', '', $num);
    $pass = md5($pass);
    $pass = strtoupper($pass);
    // faz a consulta no banco de dados
    $sql_code = "SELECT * FROM empresa WHERE upper(numero_whatsapp) = '$num' AND upper(senha) = '$pass'";
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    $usuario = $sql_query->fetch_assoc();
    // verifica se foi encontrado o usuario
    if($quantidade > 0)
    {
        $response['status'] = 'success';
        $response['message'] = 'Login bem-sucedido';
        $response['user'] = $usuario; // Inclui os dados do usuário, de acordo com a query
    } else {
        // Login falhou
        $response['message'] = 'Número de WhatsApp ou senha incorretos';
    }

    // Retorna a resposta como JSON
    return json_encode($response);
}

// Verifica se a solicitação é um POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // captura os dados do body
    $json = file_get_contents('php://input');
    // Decodifica o JSON em um array associativo
    $data = json_decode($json, true);
    if ($data === null)
    {
        header("HTTP/1.1 400 Invalid request");
        exit;
    }else{

        $num_whatsapp =         $data['num_whatsapp'];
        $senha        =         $data['senha'];

        //chama a função de verificação
        echo(login($mysqli, $num_whatsapp, $senha));
        header("HTTP/1.1 200 OK");

    }

}else{
    header("HTTP/1.1 405 Request Method Not Accepted");
    exit;
}



?>