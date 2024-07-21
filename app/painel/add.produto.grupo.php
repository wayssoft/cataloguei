<?php 
require_once '../req/conex.php';

$_SUCCESS = false;
$_error_ = False;
$_error_msg_ = '';

# zera as variaveis
$produto_grupo='';

#verifica se tem a variavel na url id do grupo
if(!isset($_GET['id_produto_grupo'])){
    $_error_ = True;
    die("error não foi passado a variavel id do grupo.<p><a href=\"log-in.php\">documentação</a></p>");    
}else{$id_produto_grupo = $_GET['id_produto_grupo'];}

# verifica se tem o ID da empresa
if(!isset($_COOKIE['authorization_id'])){
    $_error_ = True;
    die("error não foi passado a variavel id da empresa.<p><a href=\"log-in.php\">documentação</a></p>");
}else{$id_empresa  = $_COOKIE['authorization_id'];}

# se for para editar busca o produto
if(intval($id_produto_grupo) > 0){
    $sql_code = "SELECT * FROM produto_categoria WHERE id=".$id_produto_grupo;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) {
        $produto_grupo = $sql_query->fetch_assoc();
        $grupo_descricao       = $produto_grupo['descricao'];        
    } else {
        $_error_ = True;
        $_error_msg_ = 'Falha ao logar! E-mail ou senha incorretos';
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {   
    
             $grupo_descricao = $mysqli->real_escape_string($_POST['descricao']);;
             $icon = '';   

            // verifica se vai ser um novo produto ou editar um produto
            if((intval($id_produto_grupo) == 0) 
            and ($_error_ != True))
            {
                // Prepara a consulta SQL para inserção dos dados
                $sql = "INSERT INTO produto_categoria (id_empresa, icon, descricao)  VALUES (?, ?, ?)";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                echo "Erro na preparação da consulta: " . $conn->error;
                return -1;
                }
                // Vincula os parâmetros à consulta preparada
                $stmt->bind_param("sss", $id_empresa, $icon, $grupo_descricao);
                // Executa a consulta
                if ($stmt->execute()) {
                    $id_produto_return = $mysqli->insert_id; // Obtém o ID do registro inserido
                    $_SUCCESS = True;
                } else {
                    echo "Erro na inserção de dados: " . $stmt->error;
                }
                $stmt->close();
            }  


            if((intval($id_produto_grupo) > 0) 
            and ($_error_ != True))
            {
                // edita sem imagem
                if($insertImg == False){
                    $sql = "UPDATE produto_categoria SET icon=?,descricao=? WHERE id = ?";
                    $stmt = $mysqli->prepare($sql);
                    if (!$stmt) {
                        echo "Erro na preparação da consulta: " . $conn->error;
                        return -1;
                    }
                    $stmt->bind_param("sss", $icon, $grupo_descricao, $id_produto_grupo);
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
if($_error_ == True){$show_alert = 'True';}else{$show_alert = 'False';}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add produto</title>
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
    <form action="#" method="POST" enctype="multipart/form-data">  
        <div style="width: 100%; height: 30px;" class="b-main-container-left">
            <label class="roboto-light">Grupo de produtos</label>
        </div>
        <br><br>             
        <div class="container-label"><label>Descrição do grupo</label><div> 
        <div class="container-input "> 
            <input style="width: 100%" class="text-input" type="text" name="descricao" id="descricao" value="<?php echo $grupo_descricao; ?>" placeholder="Bebidas" required>
        </div>                           
        <br><br>        
        <div class="b-main-container-footer b-main-centro-total" style="height: 70px; padding-top: 10px;">
            <button style="width: 220px;" class="button-65" type="submit">Salvar grupo</button>
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
                window.location.href = '../assets/pages/success.html';
            </script>";
        }         
    ?>
</body>
    <script src='../assets/js/main.js'></script>
</html>