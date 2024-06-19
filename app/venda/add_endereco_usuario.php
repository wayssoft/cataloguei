<?php 
require_once '../req/conex.php';
require_once '../req/protect.php';

$_SUCCESS = false;
$_error_ = False;
$_error_msg_ = '';

# zera as variaveis
$nome_endereco   = '';
$cep             = '';
$bairro          = '';
$rua             = '';
$complemento     = '';
$numero          = '';

#verifica se tem a variavel na url id
if(!isset($_GET['id'])){
    $_error_ = True;
    die("error não foi passado a variavel id do endereco.<p><a href=\"log-in.php\">documentação</a></p>");    
}else{$id_endereco = $_GET['id'];}
if(!isset($_GET['id_venda'])){
    $_error_ = True;
    die("error não foi passado a variavel id_venda <p><a href=\"log-in.php\">documentação</a></p>");    
}else{$id_venda = $_GET['id_venda'];}
# se for para editar busca o produto
if(intval($id_endereco) > 0){
    $sql_code = "SELECT * FROM endereco WHERE id=".$id_endereco;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) {
        $endereco = $sql_query->fetch_assoc();
        $nome_endereco = $endereco['nome'];
        $cep           = $endereco['cep'];
        $bairro        = $endereco['bairro'];
        $rua           = $endereco['rua'];
        $complemento   = $endereco['complemento'];    
        $numero        = $endereco['numero'];    
    } else {
        $_error_ = True;
        $_error_msg_ = 'Falha ao logar! E-mail ou senha incorretos';
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {    
    if(strlen($_POST['nome_endereco']) == 0){
        $_error_ = True;
        $_error_msg_ = 'Não foi informado o nome do endereco';
    } else if(strlen($_POST['cep']) == 0) {
        $_error_ = True;
        $_error_msg_ = 'Não foi informado o cep';
    } else if(strlen($_POST['bairro']) == 0) {
        $_error_ = True;
        $_error_msg_ = 'Não foi informado o bairro';
    } else if(strlen($_POST['rua']) == 0) {
        $_error_ = True;
        $_error_msg_ = 'Não foi informado a rua';
    } else if(strlen($_POST['complemento']) == 0) {
        $_error_ = True;
        $_error_msg_ = 'Não foi informado o complemento';
    } else { 
        
        // grava o registro na base de dados
        if ($_error_ == False){
            $nome_endereco  = $mysqli->real_escape_string($_POST['nome_endereco']);
            $cep            = $mysqli->real_escape_string($_POST['cep']);
            $bairro         = $mysqli->real_escape_string($_POST['bairro']);
            $rua            = $mysqli->real_escape_string($_POST['rua']);
            $complemento    = $mysqli->real_escape_string($_POST['complemento']);
            $numero         = $mysqli->real_escape_string($_POST['numero']);
            $id_user  = $_COOKIE['authorization_id'];


            // verifica se vai ser um novo produto ou editar um produto
            if(intval($id_endereco) == 0){
                // Prepara a consulta SQL para inserção dos dados
                $sql = "INSERT INTO endereco (cep, bairro, rua, complemento, usuario_id, status, cidade_id, nome, numero)  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                echo "Erro na preparação da consulta: " . $conn->error;
                return -1;
                }
                // Vincula os parâmetros à consulta preparada
                $status = 'active';
                $id_cidade = 1;
                $stmt->bind_param("sssssssss", $cep, $bairro, $rua, $complemento, $id_user, $status, $id_cidade, $nome_endereco, $numero);
                // Executa a consulta
                if ($stmt->execute()) {
                    $id_endereco_return = $mysqli->insert_id; // Obtém o ID do registro inserido
                    $_SUCCESS = True;
                } else {
                    echo "Erro na inserção de dados: " . $stmt->error;
                }
                $stmt->close();
            }  
        
            
            if(intval($id_endereco) > 0){
                // edita sem imagem
                $sql = "UPDATE endereco SET cep=?,bairro=?,rua=?,complemento=?,nome=?,numero=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    echo "Erro na preparação da consulta: " . $conn->error;
                    return -1;
                }
                $stmt->bind_param("sssssss", $cep, $bairro, $rua, $complemento, $nome_endereco, $numero, $id_endereco);
                // Executa a consulta de atualização
                if ($stmt->execute()) {
                    //echo "Dados atualizados com sucesso!";
                    $_SUCCESS = True;
                } else {
                    echo "Erro na atualização de dados: " . $stmt->error;
                }
                $stmt->close();
            }
        }


    }
}
if($_error_ == True){$show_alert = 'True';}else{$show_alert = 'False';}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar novo endereço</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/main.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/buttons.css'>
    <style>
        .roboto-light {
            font-family: "Roboto", sans-serif;
            font-weight: 500;
            font-style: normal;
            font-size: 16px;
        }
        .container-input{
            width: 100%; height: 60px;
            position: relative;
            float: left;
        }
        .container-label{
            width: 100%; height: 20px;
            position: relative;
            float: left;
        }        
        .container-label label{
            font-family: "Roboto", sans-serif;
            font-weight: 500;
            font-style: normal;
            font-size: 12px;
        }    
        .text-input{
            height: 45px;
            border-radius: 10px;
            border: 1px solid #E8E9E7;
            font-family: "Roboto", sans-serif;
            font-weight: 400;
            padding-left: 5px;
        }                    
    </style>
</head>
<body>
    <form action="#" method="POST"> 
        <div class="container-label"><label>Nome do endereco</label><div> 
        <div class="container-input">      
            <input style="width: 100%;" class="text-input" type="text" name="nome_endereco" id="nome_endereco" placeholder="ex: Endereço casa" value="<?php echo($nome_endereco); ?>" required>
        </div>     
        <div class="container-label"><label>CEP</label><div> 
        <div class="container-input "> 
            <input style="width: 120px;" class="text-input" type="text" name="cep" id="cep" value="<?php echo($cep); ?>" placeholder="ex: 00000000" required>
        </div>   
        <div class="container-label"><label>Bairro</label><div> 
        <div class="container-input "> 
            <input style="width: 100%;" class="text-input" type="text" name="bairro" id="bairro" placeholder="ex: Centro" value="<?php echo($bairro); ?>" required>
        </div> 
        <div class="container-label"><label>Rua</label><div> 
        <div class="container-input "> 
            <input style="width: 100%;" class="text-input" type="text" name="rua" id="rua" value="<?php echo($rua); ?>" placeholder="ex: Avenida Paulista" required>
        </div>
        <div class="container-label"><label>Numero</label><div> 
        <div class="container-input "> 
            <input style="width: 100px;" class="text-input" type="text" name="numero" id="numero" value="<?php echo($numero); ?>" placeholder="ex: 5023" required>
        </div>         
        <div class="container-label"><label>Complemento</label><div> 
        <div class="container-input "> 
            <input style="width: 100%;" class="text-input" type="text" name="complemento" id="complemento" value="<?php echo($complemento); ?>" placeholder="ex: apartamento 02" required>
        </div>                            
        <br><br>        
        <div class="b-main-container-footer b-main-centro-total" style="height: 70px; padding-top: 10px;">
            <button style="width: 220px;" class="button-65" type="submit">Salvar endereço</button>
        </div>
    </form>
    <?php 
        if($_SUCCESS == true){
            //echo('<label class="roboto-light">✅ Sucesso os dados foram salvo na base de dados</label>');
        }
        if($_error_ == true){
            echo('<label class="roboto-light">⚠️ '.$_error_msg_.'</label>');
        }      
        
        if (($_error_ == false) and ($_SUCCESS == true)){
            echo "<script type='text/javascript'>
                window.location.href = 'select_endereco_venda.php?id_venda=".$id_venda."';
            </script>";
        }         
    ?>
</body>
    <script src='../assets/js/main.js'></script>
</html>