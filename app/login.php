<?php 
require_once 'req/conex.php';

$_SUCCESS = false;
$_error_ = False;
$_error_msg_ = '';

// verifica se tem redirect
if(!isset($_GET['redirect'])){$redirect='null';}
else{$redirect=$_GET['redirect'];}

if ($_SERVER["REQUEST_METHOD"] == "POST") {    
    if(!isset($_GET['type'])){
        $_error_ = True;
        $_error_msg_ = 'verifique o tipo de log-in';
    } else if(strlen($_POST['telefone']) == 0) {
        $_error_ = True;
        $_error_msg_ = 'Preencha seu e-mail';
    } else if(strlen($_POST['senha']) == 0) {
        $_error_ = True;
        $_error_msg_ = 'Preencha sua senha';
    } else {

        $numWhatsapp = $mysqli->real_escape_string($_POST['telefone']);
        $senha = $mysqli->real_escape_string($_POST['senha']);
        $numWhatsapp = preg_replace('/\D/', '', $numWhatsapp);
        $type  = $_GET['type'];
        $senha = md5($senha);
        $senha = strtoupper($senha);
        if($_error_ == False){
            if($type == 'company'){
                $sql_code = "SELECT * FROM empresa WHERE upper(numero_whatsapp) = '$numWhatsapp' AND upper(senha) = '$senha'";
                $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
                $quantidade = $sql_query->num_rows;
                if($quantidade == 1) {
                    $usuario = $sql_query->fetch_assoc();
                    $token = md5($usuario['id'].'wayssoft');
                    setcookie('authorization_id',$usuario['id'], time() + (86400 * 30), "/");
                    setcookie('authorization_type',$type, time() + (86400 * 30), "/");
                    setcookie('authorization_token',$token, time() + (86400 * 30), "/"); 
                    $_SUCCESS = true;   
                    //header("Location: ./painel/vendas.php");
                } else {
                    $_error_ = True;
                    $_error_msg_ = 'Falha ao logar! E-mail ou senha incorretos';
                }
            }
            if($type == 'user'){
                $sql_code = "SELECT * FROM usuario WHERE upper(numero_whatsapp) = '$numWhatsapp' AND upper(senha) = '$senha'";
                $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
                $quantidade = $sql_query->num_rows;
                if($quantidade == 1) {
                    $usuario = $sql_query->fetch_assoc();
                    $token = md5($usuario['id'].'wayssoft');
                    setcookie('authorization_id',$usuario['id'], time() + (86400 * 30), "/");
                    setcookie('authorization_type',$type, time() + (86400 * 30), "/");
                    setcookie('authorization_token',$token, time() + (86400 * 30), "/"); 
                    $_SUCCESS = true;   
                    //header("Location: ./painel/vendas.php");
                } else {
                    $_error_ = True;
                    $_error_msg_ = 'Falha ao logar! E-mail ou senha incorretos';
                }
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
    <title>Formulário de Telefone</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel='stylesheet' type='text/css' media='screen' href='assets/css/main.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='assets/css/buttons.css'>
    <style>
        .roboto-light {
            font-family: "Roboto", sans-serif;
            font-weight: 500;
            font-style: normal;
            font-size: 16px;
        }
        .container-input{
            width: 100%; height: 60px;
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
        <div class="container-input b-main-centro-total">      
            <input style="width: 280px;" class="text-input" type="text" name="telefone" id="telefone" placeholder="Numero whatsapp" required>
        </div> 
        <div class="container-input b-main-centro-total"> 
            <input style="width: 280px;" class="text-input" type="password" name="senha" id="senha" placeholder="Sua senha" required>
        </div> 
        <br><br>        
        <div style="width: 100%; height: 50px;" class="b-main-container-left b-main-centro-total">
            <button style="width: 220px;" class="button-65" type="submit">Acessar minha conta</button>
        </div>
        <div style="width: 100%; height: 50px; margin-top: 8px;" class="b-main-container-left b-main-centro-total">
            <button style="width: 220px;" class="button-65-v2" type="button" onclick="openPageRegister('<?php echo($_GET['type']); ?>', '<?php echo($redirect) ?>')">Criar minha conta</button>
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
            if($type == 'user'){
                $redirect_url = urldecode($_GET['redirect']);
                echo "<script type='text/javascript'>
                    window.parent.location.href = '".$redirect_url."';
                </script>";                
            }
            if($type == 'company'){
                echo "<script type='text/javascript'>
                    window.parent.location.href = './painel/vendas.php';
                </script>";
            }            
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
    <script src='assets/js/main.js'></script>
</html>