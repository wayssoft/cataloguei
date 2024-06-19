<?php 
require_once '../req/conex.php';

$_SUCCESS = false;
$_error_ = false;
$_error_msg_ = '';
// zera variaveis
$numWhatsapp = '';
$nome        = '';
$email       = '';
if(isset($_POST['bt-prosseguir'])) {
    
    $numWhatsapp = $mysqli->real_escape_string($_POST['whatsapp']);
    $nome        = $mysqli->real_escape_string($_POST['nome']);
    $email       = $mysqli->real_escape_string($_POST['email']);
    $senha       = $mysqli->real_escape_string($_POST['password']);
    $senha = md5($senha);
    $numWhatsapp = preg_replace('/\D/', '', $numWhatsapp);

    if(strlen($_POST['email']) == 0) {
        $_error_ = true;
        $_error_msg_ = 'Preencha seu e-mail';
    } else if(strlen($_POST['password']) == 0) {
        $_error_ = true;
        $_error_msg_ = 'Preencha sua senha';
    } else if(strlen($_POST['whatsapp']) == 0){
        $_error_ = true;
        $_error_msg_ = 'Não foi informado o numero do whatsapp';
    } else if(strlen($_POST['nome']) == 0){
        $_error_ = true;
        $_error_msg_ = 'Não foi informado o nome';
    }  

    // Função para verificar se um CNPJ já está cadastrado
    function ckNumWhatsapp($mysqli,$value): bool {
        // Prepara a consulta SQL para verificar o CNPJ
        $sql = "SELECT numero_whatsapp FROM empresa WHERE numero_whatsapp = ?";
        $stmt = $mysqli->prepare($sql);
        // Vincula o parâmetro à consulta preparada
        $stmt->bind_param("s", $value);
        // Executa a consulta
        $stmt->execute();
        // Armazena o resultado
        $stmt->store_result();
        // Verifica se o CNPJ já está cadastrado
        $_existente = $stmt->num_rows > 0;
        // Fecha a consulta e a conexão
        $stmt->close();
        return $_existente;
    }      
    // Função para verificar se um email está cadastrado
    function ckEmail($mysqli,$value): bool {
        // Prepara a consulta SQL para verificar o CNPJ
        $sql = "SELECT email FROM empresa WHERE email = ?";
        $stmt = $mysqli->prepare($sql);
        // Vincula o parâmetro à consulta preparada
        $stmt->bind_param("s", $value);
        // Executa a consulta
        $stmt->execute();
        // Armazena o resultado
        $stmt->store_result();
        // Verifica se o CNPJ já está cadastrado
        $_existente = $stmt->num_rows > 0;
        // Fecha a consulta e a conexão
        $stmt->close();
        return $_existente;
    } 

    // verifica se o email ja esta cadastrado para este usuario
    if( $_error_ != true){
        if (ckEmail($mysqli,$email)) {
            $_error_ = true;
            $_error_msg_ = 'Email já cadastrado';
        } 
    }    
    // verifica se o email ja esta cadastrado para este usuario
    if( $_error_ != true){
        if (ckNumWhatsapp($mysqli,$numWhatsapp)) {
            $_error_ = true;
            $_error_msg_ = 'Numero de whatsapp já esta cadastrado';
        }  
    }   
    
    if( $_error_ != true){
        // Prepara a consulta SQL para inserção dos dados
        $sql = "INSERT INTO usuario (nome, numero_whatsapp, email, status,cod_validate_whatsapp,senha,cidade_id)  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            echo "Erro na preparação da consulta: " . $conn->error;
            return -1;
        }
        // Vincula os parâmetros à consulta preparada
        $status = 'pending_validate_whatsApp';
        $idCidade = 1;
        $codigo = mt_rand(100000, 999999);
        $stmt->bind_param("sssssss", $nome, $numWhatsapp, $email, $status, $codigo, $senha, $idCidade);
        // Executa a consulta
        if ($stmt->execute()) {
            $id_user = $mysqli->insert_id; // Obtém o ID do registro inserido
            $_SUCCESS = true;
            setcookie('authorization_type','user', time() + (86400 * 30), "/");
            setcookie('authorization_id',$id_user, time() + (86400 * 30), "/");

            if(!isset($_GET['redirect'])){$redirect='null';}
            else{$redirect=$_GET['redirect'];}

        } else {
            echo "Erro na inserção de dados: " . $stmt->error;
        }
        $stmt->close();  
    }  
}
if($_error_ == true){$show_alert = 'True';}else{$show_alert = 'False';}
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
        <div class="container-label"><label>Nome</label><div> 
        <div class="container-input "> 
            <input style="width: 100%;" class="text-input" type="text" name="nome" id="nome" placeholder="ex: Maria de Jesus" value="<?php echo $nome; ?>" required>
        </div> 
        <div class="container-label"><label>Email</label><div> 
        <div class="container-input "> 
            <input style="width: 100%;" class="text-input" type="text" name="email" id="email" placeholder="ex: mariadejesus@email.com" value="<?php echo $email; ?>" required>
        </div> 
        <div class="container-label"><label>Whatsapp</label><div> 
        <div class="container-input">      
            <input style="width: 250px;" class="text-input" type="text" name="whatsapp" id="whatsapp" placeholder="ex: (00) 0 0000-0000" value="<?php echo $numWhatsapp; ?>" required>
        </div>  
        <div class="container-label"><label>Senha</label><div> 
        <div class="container-input">      
            <input style="width: 250px;" class="text-input" type="password" name="password" id="password" placeholder="" required>
        </div>                                                  
        <div class="b-main-container-footer b-main-centro-total" style="height: 70px; padding-top: 10px;">
            <button style="width: 220px;" name="bt-prosseguir" class="button-65" type="submit">Prosseguir</button>
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
                window.parent.location.href = 'validate_whatsapp.php?redirect=".urlencode($redirect)."';
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