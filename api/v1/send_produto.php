<?php 
header("Access-Control-Allow-Origin: *");
header('Cache-Control: no-cache, must-revalidate'); 
header("Content-Type: application/json; charset=UTF-8");
//header("HTTP/1.1 200 OK");

require_once 'conex.php';

// Inicializa uma resposta padrão
$response = array('status' => 'error', 'message' => 'falhou');

function retornaIdProduto($mysqli,$value,$id_empresa): int 
{
    $sql_code = "SELECT * FROM produto WHERE identificador = '".$value."' AND id_empresa = ".$id_empresa;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) { 
        $produto = $sql_query->fetch_assoc();
        $_id = $produto['id'];
    }else{$_id = 0;};
    return $_id;
} 

function retornaIdEmpresa($mysqli,$value): int 
{
    $sql_code = "SELECT * FROM empresa WHERE token = '".$value."'";
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) { 
        $empresa = $sql_query->fetch_assoc();
        $_id = $empresa['id'];
    }else{$_id = 0;};
    return $_id;
} 

// verifica o token
if(!isset($_GET['token']))
{
    header("HTTP/1.1 400 Invalid request token");
    exit;
}else{
    $id_empresa = retornaIdEmpresa($mysqli,$_GET['token']);
    if($id_empresa == 0)
    {
        header("HTTP/1.1 400 Invalid request token");
        exit;        
    }
}

// Verifica se a solicitação é um POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // captura os dados do body
    $json = file_get_contents('php://input');
    // Decodifica o JSON em um array associativo
    $data = json_decode($json, true);
    if ($data === null){
        header("HTTP/1.1 400 Invalid request");
        exit;
    }else{


        $Identificador =        $data['identificador'];
        $codigo_barras =        $data['codigo_barras'];
        $nome =                 $data['nome'];
        $descricao =            $data['descricao'];
        $preco =                $data['preco'];
        $estoque =              $data['estoque'];
        $imgB64 =               $data['imgB64'];

        //chama a função de verificação para a ação de insert ou edit
        $id_produto = retornaIdProduto($mysqli,$Identificador,$id_empresa);
        if ($id_produto == 0)
        {
            $_ACTION = 'insert';            
        }
        else{
            $_ACTION = 'edit';
        }

        //faz inserção dos dados
        if($_ACTION == 'insert')
        {

            // Prepara a consulta SQL para inserção dos dados
            $sql = "INSERT INTO produto (codigo_barras, nome, descricao, preco, estoque, identificador, id_empresa)  VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
            //echo "Erro na preparação da consulta: " . $conn->error;
            $response['status']  = 'error'; $response['message'] = 'Interno db cod[001]';
            return -1;
            }
            $stmt->bind_param("sssssss", $codigo_barras, $nome, $descricao, $preco, $estoque, $Identificador, $id_empresa);
            // Executa a consulta
            if ($stmt->execute()) {
                $id = $mysqli->insert_id; // Obtém o ID do registro inserido
                $response['status']     = 'success';
                $response['message']    = 'produto inserido com sucesso';
                $response['id_produto'] = $id;
            } else {
                //echo "Erro na inserção de dados: " . $stmt->error;
                $response['status']  = 'success'; $response['message'] = 'Interno db cod[002]';
            }
            $stmt->close();

            
            
        }


        if($_ACTION == 'edit')
        {

            $sql = "UPDATE produto SET codigo_barras=?, nome=?, descricao=?, preco=?, estoque=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                //echo "Erro na preparação da consulta: " . $conn->error;
                $response['status']  = 'error'; $response['message'] = 'Interno db cod[003]';
                return -1;
            }
            $status = 'awaiting_approval';
            $stmt->bind_param("ssssss", $codigo_barras, $nome, $descricao,$preco,$estoque,$id_produto);
            // Executa a consulta de atualização
            if ($stmt->execute()) {
                $response['status']     = 'success';
                $response['message']    = 'produto atualizado com sucesso';
                $response['id_produto'] = $id_produto;
            } else {
                //echo "Erro na atualização de dados: " . $stmt->error;
                $response['status']  = 'error'; $response['message'] = 'Interno db cod[004]';
            }
            $stmt->close(); 


        }

        // Retorna a resposta como JSON
        echo json_encode($response);
        header("HTTP/1.1 200 OK");

    }

}else{
    header("HTTP/1.1 405 Request Method Not Accepted");
    exit;
}



?>