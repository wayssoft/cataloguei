<?php 
require_once '../req/conex.php';

$_SUCCESS = false;
$_error_ = False;
$_error_msg_ = '';

# zera as variaveis
$nome = '';

#verifica se tem a variavel na url id
if(!isset($_GET['id']))
{
    $_error_ = True;
    die("error não foi passado a variavel id do produto.<p><a href=\"log-in.php\">documentação</a></p>");    
}else{$id_produto = $_GET['id'];}

# se for para editar busca o produto
if(intval($id_produto) > 0)
{
    $sql_code = "SELECT * FROM produto WHERE id=".$id_produto;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) {
        $produto = $sql_query->fetch_assoc();
        $nome              = $produto['nome'];      
    } else {
        $_error_ = True;
        $_error_msg_ = 'Falha ao logar! E-mail ou senha incorretos';
    }
}


if(isset($_POST['bt-add-variacao'])) 
{
    if(strlen($_POST['preco']) == 0){
        $_error_ = True;
        $_error_msg_ = 'Não foi informado o preço';
    }

    if(strlen($_POST['estoque']) == 0){
        $_error_ = True;
        $_error_msg_ = 'Não foi informado o estoque';
    }

    if(strlen($_POST['descricao']) == 0){
        $_error_ = True;
        $_error_msg_ = 'Não foi informado a descrição';
    }

    $preco = $mysqli->real_escape_string($_POST['preco']);
    $estoque = $mysqli->real_escape_string($_POST['estoque']);
    $descricao = $mysqli->real_escape_string($_POST['descricao']);
    $status = 'A';

    if($_error_ != True){
        // Prepara a consulta SQL para inserção dos dados
        $sql = "INSERT INTO variacao_produto (descricao, estoque, preco, status, id_produto)  VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
        echo "Erro na preparação da consulta: " . $conn->error;
        return -1;
        }
        // Vincula os parâmetros à consulta preparada
        $stmt->bind_param("sssss", $descricao, $estoque, $preco, $status, $id_produto);
        // Executa a consulta
        if ($stmt->execute()) {
            $id_return = $mysqli->insert_id; // Obtém o ID do registro inserido
            $_SUCCESS = True;
        } else {
            echo "Erro na inserção de dados: " . $stmt->error;
        }
        $stmt->close();
    }    
}


if(isset($_POST['bt-editar-variacao'])) 
{
    if(strlen($_POST['preco']) == 0){
        $_error_ = True;
        $_error_msg_ = 'Não foi informado o preço';
    }

    if(strlen($_POST['estoque']) == 0){
        $_error_ = True;
        $_error_msg_ = 'Não foi informado o estoque';
    }

    if(strlen($_POST['descricao']) == 0){
        $_error_ = True;
        $_error_msg_ = 'Não foi informado a descrição';
    }

    $custId = $mysqli->real_escape_string($_POST['custId']);
    $preco = $mysqli->real_escape_string($_POST['preco']);
    $estoque = $mysqli->real_escape_string($_POST['estoque']);
    $descricao = $mysqli->real_escape_string($_POST['descricao']);

    if($_error_ != True){
        // Prepara a consulta SQL para inserção dos dados
        $sql = "UPDATE variacao_produto SET descricao=?,estoque=?,preco=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            echo "Erro na preparação da consulta: " . $conn->error;
            return -1;
        }
        $stmt->bind_param("ssss", $descricao, $estoque, $preco, $custId);
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

if(isset($_POST['bt-excluir-variacao'])) 
{

    $custId = $mysqli->real_escape_string($_POST['custId']);
    $status = 'E';

    if($_error_ != True){
        // Prepara a consulta SQL para inserção dos dados
        $sql = "UPDATE variacao_produto SET status=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            echo "Erro na preparação da consulta: " . $conn->error;
            return -1;
        }
        $stmt->bind_param("ss", $status, $custId);
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


#Filtra a variação do produto
$sql_code = "SELECT id, descricao, estoque, preco, status 
             FROM variacao_produto 
             WHERE id_produto = ".$id_produto."
             AND status = 'A'
             ORDER BY estoque";
$sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
$variacao = $sql_query->fetch_all(MYSQLI_ASSOC);



if($_error_ == True){$show_alert = 'True';}else{$show_alert = 'False';}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Variação de produto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/main.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/buttons.css?v=1.1'> 
    <!--boxicon-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'> 
    <style>
        .roboto-light {
            font-family: "Roboto", sans-serif;
            font-weight: 500;
            font-style: normal;
            font-size: 16px;
        }
        .container-input{
            width: 90px; height: 75px;
            position: relative;
            float: left;
        }

        .container-input label{
            font-family: "Roboto", sans-serif;
            font-weight: 500;
            font-style: normal;
            font-size: 11px;
        }    
        .text-input{
            height: 45px;
            border-radius: 10px;
            border: 1px solid #E8E9E7;
            font-family: "Roboto", sans-serif;
            font-weight: 400;
            padding-left: 5px;
            width: 95%;
        }   

        .text-input-v2{
            height: 45px;
            border: none;
            border-bottom: 1px solid #F3F3F3;
            font-family: "Roboto", sans-serif;
            font-weight: 400;
            padding-left: 5px;
            width: 95%;
        }         
        
        .button-add-variacao{
            cursor: pointer;
            position: relative; float: left;
            width: 40px; height: 40px;
            border-radius: 6px;
            border: solid 1px #E8E9E7;
            top: 22px; background-color: #FFFFFF;
        }

        .button-add-variacao i{
            font-size: 20px;
            color: #1366d6;
        }
        

        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
        .container-menu{
            width: 100%; height: 50px;
            position: relative; float: left;
        }

    </style>
</head>
<body>
    <form action="#" method="POST" enctype="multipart/form-data">  
        <div style="width: 100%; height: 30px;" class="b-main-container-left">
            <label class="roboto-light">Aplicar preço promocional para: <?php echo  $nome ;  ?></label>
        </div>
        <br><br>            
        <div class="container-input "> 
            <label>Preço</label>
            <input class="text-input" type="text" name="preco" id="preco" value="" placeholder="ex: 6,99" required>
        </div>
        <div class="container-input "> 
            <label>Estoque</label>
            <input class="text-input" type="text" name="estoque" id="estoque" value="" placeholder="ex: 10" required>
        </div> 
        <div style="width: calc(100% - 230px);" class="container-input "> 
            <label>Descrição</label>
            <input class="text-input" type="text" name="descricao" id="descricao" value="" placeholder="ex: Caixa com 12un" required>
        </div>  
        <div style="float: right; width: 45px;" class="container-input "> 
            <button class="button-add-variacao" name="bt-add-variacao" type="submit"><i class='bx bx-add-to-queue'></i></button>
        </div>                         
    </form>


    <br><br>

    <?php foreach($variacao as $row){?>
        <form action="#" method="POST" enctype="multipart/form-data"> 
            <div class="container-menu">
                <input type="hidden" id="custId" name="custId" value="<?php echo $row['id']; ?>">
                <div class="container-input "> 
                    <input class="text-input-v2" type="text" name="preco" id="preco" value="<?php echo number_format($row['preco'],2,",","."); ?>" placeholder="ex: 6,99" required>
                </div>
                <div class="container-input "> 
                    <input class="text-input-v2" type="text" name="estoque" id="estoque" value="<?php echo number_format($row['estoque'],2,",","."); ?>" placeholder="ex: 6,99" required>
                </div> 
                <div style="width: calc(100% - 270px);" class="container-input "> 
                    <input class="text-input-v2" type="text" name="descricao" id="descricao" value="<?php echo $row['descricao']; ?>" placeholder="ex: 6,99" required>
                </div>  
                <div style="float: right; width: 90px;" class="container-input "> 
                    <button style="top: 3px;" class="button-add-variacao" name="bt-editar-variacao" type="submit"><i class='bx bx-save' ></i></button>
                    <button style="top: 3px; margin-left: 3px;" class="button-add-variacao" name="bt-excluir-variacao" type="submit"><i class='bx bx-trash' ></i></button>
                </div>  
            
            </div> 
        </form>
    <?php } ?>


    
    <?php 
        if($_SUCCESS == true){
            //echo('<label class="roboto-light">✅ Sucesso os dados foram salvo na base de dados</label>');
        }
        if($_error_ == true){
            echo('<label class="roboto-light">⚠️ '.$_error_msg_.'</label>');
        }      
        
        if (($_error_ == false) and ($_SUCCESS == true)){
            echo "<script type='text/javascript'>
                window.location.href = '../assets/pages/success.html';
            </script>";
        }         
    ?>
</body>
    <script src='../assets/js/main.js'></script>
</html>