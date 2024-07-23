<?php 
require_once '../req/conex.php';

$_SUCCESS = false;
$_error_ = False;
$_error_msg_ = '';

# zera as variaveis
$cod = '';
$taxa_desconto= 0;
$qtd= 0;
$ativa_influ='N';
$ativa_cupom='N';
$whats_influ='';
$nome_influ='';
$taxa_influ=0;


#verifica se tem a variavel na url id
if(!isset($_GET['id'])){
    $_error_ = True;
    die("error não foi passado a variavel id do produto.<p><a href=\"log-in.php\">documentação</a></p>");    
}else{$id_cupom = $_GET['id'];}

# verifica se tem o ID da empresa
if(!isset($_COOKIE['authorization_id'])){
    $_error_ = True;
    die("error não foi passado a variavel id da empresa.<p><a href=\"log-in.php\">documentação</a></p>");
}else{$id_empresa  = $_COOKIE['authorization_id'];}


if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    
    $cod             = $mysqli->real_escape_string($_POST['cod']);
    $taxa_desconto   = $mysqli->real_escape_string($_POST['taxa_desconto']);
    $qtd             = $mysqli->real_escape_string($_POST['qtd_cupom']);
    $nome_influ      = $mysqli->real_escape_string($_POST['nome_influ']);
    $whats_influ     = $mysqli->real_escape_string($_POST['whats_influ']);
    $whats_influ     = preg_replace('/\D/', '', $whats_influ);
    $taxa_influ      = $mysqli->real_escape_string($_POST['taxa_influ']);

    // verifica checkbox 
    if (isset($_POST['ckAtivaCupom'])) 
    {
        $ativa_cupom = 'S';
    }else{
        $ativa_cupom = 'N';
    }  

    if (isset($_POST['ckAtivaInflu'])) 
    {
        $ativa_influ = 'S';
    }else{
        $ativa_influ = 'N';
    } 

    // verifica se vai ser um novo produto ou editar um produto
    if(intval($id_cupom) == 0){

        // Prepara a consulta SQL para inserção dos dados
        $sql = "INSERT INTO cupom (cod_cupom, taxa_desconto, qtd_cupom, ativa_influ, whatsapp_notifica_influ, nome_influ, taxa_influ, status, id_empresa)  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            echo "Erro na preparação da consulta: " . $conn->error;
            return -1;
        }
        // Vincula os parâmetros à consulta preparada
        $stmt->bind_param("sssssssss", $cod, $taxa_desconto, $qtd, $ativa_influ, $whats_influ,  $nome_influ, $taxa_influ, $ativa_influ,$id_empresa);
        // Executa a consulta
        if ($stmt->execute()) {
            $id_cupom_return = $mysqli->insert_id; // Obtém o ID do registro inserido
            $_SUCCESS = True;
        } else {
            echo "Erro na inserção de dados: " . $stmt->error;
        }
        $stmt->close();

    }      

    if(intval($id_cupom) > 0)
    {        
    
        // grava o registro na base de dados
        if ($_error_ == False){
            $sql = "UPDATE cupom SET cod_cupom=?,
                                     taxa_desconto=?,
                                     qtd_cupom=?,
                                     ativa_influ=?,
                                     whatsapp_notifica_influ=?,
                                     nome_influ=?,
                                     taxa_influ=?,
                                     status=?
                                     WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "Erro na preparação da consulta: " . $conn->error;
                return -1;
            }
            $stmt->bind_param("sssssssss", $cod, $taxa_desconto, $qtd, $ativa_influ, $whats_influ, $nome_influ, $taxa_influ, $ativa_influ, $id_cupom);
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


# se for para editar busca o produto
if(intval($id_cupom) > 0){
    $sql_code = "SELECT * FROM cupom WHERE id=".$id_cupom;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) {
        $cupom = $sql_query->fetch_assoc();
        $cod               = $cupom['cod_cupom'];
        $taxa_desconto     = $cupom['taxa_desconto'];
        $qtd               = $cupom['qtd_cupom'];
        $ativa_influ       = $cupom['ativa_influ'];   
        $ativa_cupom       = $cupom['status'];  
        $whats_influ       = $cupom['whatsapp_notifica_influ'];
        $nome_influ        = $cupom['nome_influ']; 
        $taxa_influ        = $cupom['taxa_influ'];   
    } else {
        $_error_ = True;
        $_error_msg_ = 'Falha ao logar! E-mail ou senha incorretos';
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
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/checkbox.css'>
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
        #cod{
            text-transform: uppercase;
        }                          
    </style>
</head>
<body>
    <form action="#" method="POST" enctype="multipart/form-data">  
        <div style="width: 100%; height: 30px;" class="b-main-container-left">
            <label class="roboto-light">Cupom</label>
        </div>
        <br> 
        <div class="container-label"><label>Código cupom</label><div> 
        <div class="container-input "> 
            <input style="width: 100%;" class="text-input" type="text" name="cod" id="cod" value="<?php echo $cod ?>" placeholder="" required>
        </div>                    
        <div class="container-label"><label>Taxa desconto</label><div> 
        <div class="container-input "> 
            <input style="width: 120px;" class="text-input" type="text" name="taxa_desconto" id="taxa_desconto" value="<?php echo number_format($taxa_desconto,2,",","."); ?>" placeholder="0.0" required>
        </div> 
        <div class="container-label"><label>Quantidade cupom</label><div> 
        <div class="container-input "> 
            <input style="width: 120px;" class="text-input" type="text" name="qtd_cupom" id="qtd_cupom" value="<?php echo number_format($qtd,2,",","."); ?>" placeholder="0.0" required>
        </div>                                   
        <br> 
        
        <div style="height:30px;" class="container-input">      
            <div style="margin-top: 5px;" class="checkbox-wrapper-4">
                <input class="inp-cbx" id="ckAtivaCupom" name="ckAtivaCupom" type="checkbox" <?php if($ativa_cupom == 'S'){echo('checked');} ?>/>
                <label class="cbx" for="ckAtivaCupom"><span>
                <svg width="12px" height="10px">
                    <use xlink:href="#check-4"></use>
                </svg></span><span>Ativar cupom</span></label>
                <svg class="inline-svg">
                    <symbol id="check-4" viewbox="0 0 12 10">
                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                    </symbol>
                </svg>
            </div>             
        </div>        


        <div style="height:30px;" class="container-input">      
            <div style="margin-top: 5px;" class="checkbox-wrapper-4">
                <input class="inp-cbx" id="ckAtivaInflu" name="ckAtivaInflu" type="checkbox" <?php if($ativa_influ == 'S'){echo('checked');} ?>/>
                <label class="cbx" for="ckAtivaInflu"><span>
                <svg width="12px" height="10px">
                    <use xlink:href="#check-4"></use>
                </svg></span><span>Ativar influencer</span></label>
                <svg class="inline-svg">
                    <symbol id="check-4" viewbox="0 0 12 10">
                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                    </symbol>
                </svg>
            </div>             
        </div>
        
        <!-- separa -->
        <div style="height:20px;" class="container-input "></div> 

        <div class="container-label"><label>Nome influencer</label><div> 
        <div class="container-input "> 
            <input style="width: 100%;" class="text-input" type="text" name="nome_influ" id="nome_influ" value="<?php echo $nome_influ ?>" placeholder="" required>
        </div>   
        
        <div class="container-label"><label>Whatsapp influencer</label><div> 
        <div class="container-input "> 
            <input style="width: 100%;" class="text-input" type="text" name="whats_influ" id="whats_influ" value="<?php echo $whats_influ ?>" placeholder="" required>
        </div> 
        
        <div class="container-label"><label>Taxa influencer</label><div> 
        <div class="container-input "> 
            <input style="width: 120px;" class="text-input" type="text" name="taxa_influ" id="taxa_influ" value="<?php echo number_format($taxa_influ,2,",","."); ?>" placeholder="ex: 6,99" required>
        </div>  
        
        <!-- separa os inputs do botão de salvar -->
        <div style="height:80px;" class="container-input "></div>         

        <div class="b-main-container-footer b-main-centro-total" style="height: 70px; padding-top: 10px;">
            <button style="width: 220px;" class="button-65" type="submit">Salvar cupom</button>
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
        let campoTelefone = document.getElementById("whats_influ");
        
        // Adiciona um ouvinte de evento para detectar mudanças no campo de entrada
        campoTelefone.addEventListener("input", aplicarMascaraTelefone);
    </script>

    <script>
        document.getElementById('cod').addEventListener('input', function (e) {
            const value = e.target.value;
            const regex = /^[a-zA-Z0-9_]*$/;

            if (!regex.test(value)) {
                e.target.value = value.replace(/[^a-zA-Z0-9_]/g, '');
            }
        });
    </script>    
    <script src='../assets/js/main.js'></script>
</html>