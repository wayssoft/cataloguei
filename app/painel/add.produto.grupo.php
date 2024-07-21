<?php 
require_once '../req/conex.php';

$_SUCCESS = false;
$_error_ = False;
$_error_msg_ = '';

# zera as variaveis
$preco       = '';
$promocao    = 'N';
$preco_promocional = 0;
$nome = '';

#verifica se tem a variavel na url id
if(!isset($_GET['id'])){
    $_error_ = True;
    die("error não foi passado a variavel id do produto.<p><a href=\"log-in.php\">documentação</a></p>");    
}else{$id_produto = $_GET['id'];}

# se for para editar busca o produto
if(intval($id_produto) > 0){
    $sql_code = "SELECT * FROM produto WHERE id=".$id_produto;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) {
        $produto = $sql_query->fetch_assoc();
        $nome              = $produto['nome'];
        $promocao          = $produto['promocao'];
        $preco             = $produto['preco'];
        $preco_promocional = $produto['preco_promocional'];        
    } else {
        $_error_ = True;
        $_error_msg_ = 'Falha ao logar! E-mail ou senha incorretos';
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {    
    if(strlen($_POST['preco']) == 0){
        $_error_ = True;
        $_error_msg_ = 'Não foi informado o preço promocional';
    } else { 
        $preco_promocional_informado = $mysqli->real_escape_string($_POST['preco']);
        if($preco_promocional_informado >= $preco){
            $_error_ = True;
            $_error_msg_ = 'Valor promocional não pode ser maior que o preço atual';                
        }    
        if($preco_promocional_informado > 0){$promocao = 'S';}else{$promocao = 'N';}     
        // grava o registro na base de dados
        if ($_error_ == False){
            $sql = "UPDATE produto SET promocao=?,preco_promocional=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "Erro na preparação da consulta: " . $conn->error;
                return -1;
            }
            $stmt->bind_param("sss", $promocao, $preco_promocional_informado, $id_produto);
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
            <label class="roboto-light">Aplicar preço promocional para: <?php echo  $nome ;  ?></label>
        </div>
        <br><br>
        <div style="width: 100%; height: 30px;" class="b-main-container-left">
            <label class="roboto-light-2">Preço atual: <b><?php echo number_format($preco,2,",","."); ?></b></label>
        </div>               
        <div class="container-label"><label>Preço promocional</label><div> 
        <div class="container-input "> 
            <input style="width: 120px;" class="text-input" type="text" name="preco" id="preco" value="<?php echo number_format($preco_promocional,2,",","."); ?>" placeholder="ex: 6,99" required>
        </div>                           
        <br><br>        
        <div class="b-main-container-footer b-main-centro-total" style="height: 70px; padding-top: 10px;">
            <button style="width: 220px;" class="button-65" type="submit">Salvar preço</button>
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
    <script>
        // Função para aplicar a máscara de telefone
        function aplicarMascaraTelefone(event) {
            // Obtém o valor atual do campo de entrada
            let input = event.target;
            let valor = input.value;
            
            // Remove tudo exceto números
            valor = valor.replace(/\D/g, '');
            
            // Aplica a máscara
            if (valor.length > 0) {
                valor = "(" + valor.substring(0, 2) + ") " + valor.substring(2, 3) + " " + valor.substring(3, 7) + "-" + valor.substring(7, 11);
            }
            
            // Atualiza o valor do campo de entrada
            input.value = valor;
        }
        
        // Seleciona o campo de entrada
        let campoTelefone = document.getElementById("telefone");
        
        // Adiciona um ouvinte de evento para detectar mudanças no campo de entrada
        campoTelefone.addEventListener("input", aplicarMascaraTelefone);
    </script>
    <script src='../assets/js/main.js'></script>
</html>