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
    } else if(strlen($_POST['numWhatsapp']) == 0) {
        $_error_ = True;
        $_error_msg_ = 'Preencha seu e-mail';
    } else if(strlen($_POST['senha']) == 0) {
        $_error_ = True;
        $_error_msg_ = 'Preencha sua senha';
    } else {

        $numWhatsapp = $mysqli->real_escape_string($_POST['numWhatsapp']);
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
<!doctype html>
<html lang="pt-BR">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/login.register/fonts/icomoon/style.css">

    <link rel="stylesheet" href="assets/login.register/css/owl.carousel.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/login.register/css/bootstrap.min.css">
    
    <!-- Style -->
    <link rel="stylesheet" href="assets/login.register/css/style.css?v=1.0">
    <link rel="stylesheet" href="assets/css/module/button.css">

    <!-- sweetalert2 -->    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css" rel="stylesheet">

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
        .topo{
          width: 100%; height: 100px;
          margin-bottom: -100px;
          padding-top: 50px; padding-left: 15px;
        }  
        .topo img{
            height: 100%;
        }         
        .b-main-centro-total{
            align-items: center;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
        }                      
    </style>    

    <title>Login | Cataloguei.shop</title>
  </head>
  <body>
  
  <div class="topo">
        <img src="assets/img/logo-lange.png" />
  </div>

  <div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-image: url('../assets/login.register/images/0.png');"></div>
    <div class="contents order-2 order-md-1">

      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-7">
            <h3>Login <strong>Cataloguei.shop</strong></h3>
            <p class="mb-4">Cadastre-se hoje mesmo e aproveite todas as funcionalidades do Cataloguei.shop. É rápido, fácil e gratuito!</p>
            <form action="" method="POST">       
              <div style="border-radius: 0;" class="form-group first">
                <label for="numWhatsapp">Numero whatsapp</label>
                <input id="numWhatsapp" type="text" class="form-control" placeholder="(00) 0 0000-0000" name="numWhatsapp" value="">
              </div>                     
              <div class="form-group last mb-3">
                <label for="senha">Senha de usuario</label>
                <input type="password" class="form-control" placeholder="Sua senha" name="senha">
              </div>
              
              <div class="d-flex mb-5 align-items-center">
                <label class="control control--checkbox mb-0"><span class="caption">Manter conectado</span>
                  <input type="checkbox" checked="checked"/>
                  <div class="control__indicator"></div>
                </label>
              </div>

              <input type="submit" name="enviar" value="Entrar na conta" class="btn btn-block btn-primary">

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
          </div>
        </div>
      </div>
    </div>

    
  </div>
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
        let campoTelefone = document.getElementById("numWhatsapp");
        
        // Adiciona um ouvinte de evento para detectar mudanças no campo de entrada
        campoTelefone.addEventListener("input", aplicarMascaraTelefone);
    </script>
    <script src="assets/login.register/js/jquery-3.3.1.min.js"></script>
    <script src="assets/login.register/js/popper.min.js"></script>
    <script src="assets/login.register/js/bootstrap.min.js"></script>
    <script src="assets/login.register/js/main.js"></script>
  </body>

</html>