<?php 
$_error_ = False;
$_error_msg_ = '';
include('../req/conex.php');
if(!isset($_COOKIE['authorization_id'])) {
  die("error x001 Você não pode acessar esta página porque não está logado.<p><a href=\"log-in.php\">Entrar</a></p>");
}else{$_authorization_id = $_COOKIE['authorization_id'];}
if(!isset($_COOKIE['authorization_type'])) {
  die("error x001 Você não pode acessar esta página porque não está logado.<p><a href=\"log-in.php\">Entrar</a></p>");
}else{$_authorization_type = $_COOKIE['authorization_type'];}

// busca dados da empresa
if( $_authorization_type == 'company'){
  $sql_code = "SELECT * FROM empresa WHERE id = ".$_authorization_id;
  $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
  $quantidade = $sql_query->num_rows;
  if($quantidade == 1) {
    $empresa = $sql_query->fetch_assoc();
    $numWhatsappBase = $empresa['numero_whatsapp'];
  }
}
// busca dados de usuario
if( $_authorization_type == 'user'){
  $sql_code = "SELECT * FROM usuario WHERE id = ".$_authorization_id;
  $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
  $quantidade = $sql_query->num_rows;
  if($quantidade == 1) {
    $user = $sql_query->fetch_assoc();
    $numWhatsappBase = $user['numero_whatsapp'];
  }
}

/*
|--------------------------------------------------------------------
| Enviar link do catalago para empresa
|--------------------------------------------------------------------
| 
| Esta functio fica responsavel por enviar o link do 
| Catalago digital para empresa assim que o codigo 
| for validado
|
*/
function send_link_catalago($mysqli,$id_empresa): bool {
  $retorno = false;
  $sql_code = "SELECT * FROM empresa WHERE id = ".$id_empresa;
  $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
  $quantidade = $sql_query->num_rows;
  if($quantidade == 1) {

      $empresa       = $sql_query->fetch_assoc();
      $num_whatsapp  = $empresa['numero_whatsapp'];
      $dominio       = $empresa['dominio'];
      $nome          = $empresa['nome'];

      $linkDescription = "Link Cataloguei para ".$nome; 
      $title = $nome;
      $link = "https://app.cataloguei.shop/d?loja=".$dominio;
      $numero = '55'.$num_whatsapp;

      $curl = curl_init();
      curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.z-api.io/instances/3A9774DE30F9100EAEE986DC9E7D4A64/token/5C91D4E7085ECA40B610E629/send-link",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_SSL_VERIFYPEER => false, // Desabilitar verificação SSL (não recomendado)
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\"phone\": \"$numero\",\"message\": \"Esse é o link do seu catálogo digital: $link\",\"image\": \"https://cataloguei.shop/assets/img/logo-sm.png\",\"linkUrl\": \"$link\",\"title\": \"$title\",\"linkDescription\": \"$linkDescription\"}",
      CURLOPT_HTTPHEADER => array(
          "client-token: F3b7c9eafb4b64139a5d5bb54e6a41273S",
          "content-type: application/json"
      ),
      ));
      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
      //echo "cURL Error #:" . $err;
      $retorno = false;
      } else {
      //echo $response;
      $retorno = true;
      }
  }



  return $retorno;
}



/*
|--------------------------------------------------------------------
| Enviar o codigo para validar o numero
|--------------------------------------------------------------------
| 
| Esta functio fica responsavel por enviar o codigo 
| ao usuario via whatsapp para validação do numero de whatsapp 
|
*/
function send_codigo($numero,$cod){
  $msg = "Olá, samos a cataloguei, uma plataforma de catálogo digital, e para validar seu cadastro use o código: *".$cod."*";
  $numero = '55'.$numero;
  $curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.z-api.io/instances/3A9774DE30F9100EAEE986DC9E7D4A64/token/5C91D4E7085ECA40B610E629/send-text",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_SSL_VERIFYPEER => false, // Desabilitar verificação SSL (não recomendado)
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"phone\": \"$numero\", \"message\": \"$msg\"}",
  CURLOPT_HTTPHEADER => array(
      "client-token: F3b7c9eafb4b64139a5d5bb54e6a41273S",
      "content-type: application/json"
  ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
  //echo "cURL Error #:" . $err;
  return false;
  } else {
  //echo $response;
  return true;
  }
}




// repassa o numero da base para o numero atula do whatsapp antes do da rotina para reenviar codigo e validar codigo
$numWhatsapp = $numWhatsappBase;

if(isset($_POST['valida_cod'])) {
  if(strlen($_POST['cod']) == 0) {
      $_error_ = True;
      $_error_msg_ = 'Preencha o código';
  } else if(strlen($_POST['numWhatsapp']) == 0){
      $_error_ = True;
      $_error_msg_ = 'Não foi informado o numero do whatsapp';
  } else {

      // // Função para verificar se o codigo e verdadeiro
      function ckCodWhatsEmpresa($mysqli,$id,$cod): bool {
        // Prepara a consulta SQL para verificar o CNPJ
          $sql = "SELECT id,cod_validate_whatsapp FROM empresa WHERE id = ? and cod_validate_whatsapp = ?";
          $stmt = $mysqli->prepare($sql);
          // Vincula o parâmetro à consulta preparada
          $stmt->bind_param("ss", $id,$cod);
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

      // Função para verificar se o codigo e verdadeiro
      function ckCodWhatsUser($mysqli,$id,$cod): bool {
        // Prepara a consulta SQL para verificar o CNPJ
          $sql = "SELECT id,cod_validate_whatsapp FROM usuario WHERE id = ? and cod_validate_whatsapp = ?";
          $stmt = $mysqli->prepare($sql);
          // Vincula o parâmetro à consulta preparada
          $stmt->bind_param("ss", $id,$cod);
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
        
      $cod         = $mysqli->real_escape_string($_POST['cod']);
      $numWhatsapp = $mysqli->real_escape_string($_POST['numWhatsapp']);
      $numWhatsapp = preg_replace('/\D/', '', $numWhatsapp);

      // verifica se é empresa
      if( $_authorization_type == 'company'){
        if (ckCodWhatsEmpresa($mysqli,$_authorization_id, $cod)) {
            
            $sql = "UPDATE empresa SET validate_whatsapp=?, status=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                $_error_ = True;
                $_error_msg_ = 'Error interno'; 
                echo "Erro na preparação da consulta: " . $conn->error;
                return -1;
            }
            $validate_whatsapp = 'S';
            $status = 'active';
            $stmt->bind_param("sss", $validate_whatsapp, $status, $_authorization_id);
            // Executa a consulta de atualização
            if ($stmt->execute()) 
            {

              if(send_link_catalago($mysqli,$_authorization_id))
              {
                  header("Location: ../painel/vendas.php");// redirecionar para pagina de vendas
              }else{
                  $_error_ = True;
                  $_error_msg_ = 'Error interno: erro ao tentar enviar o link do catalago';
              }
              

            } else {
                echo "Erro na atualização de dados: " . $stmt->error;
                $_error_ = True;
                $_error_msg_ = 'Error interno'; 
            }
            $stmt->close();   
        }else{
          $_error_ = True;
          $_error_msg_ = 'Código não confere';          
        }
      }


      // verifica se é usuario
      if( $_authorization_type == 'user'){
        if (ckCodWhatsUser($mysqli,$_authorization_id, $cod)) {
            
            $sql = "UPDATE usuario SET validate_whatsapp = ? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                $_error_ = True;
                $_error_msg_ = 'Error interno'; 
                echo "Erro na preparação da consulta: " . $conn->error;
                return -1;
            }
            $validate_whatsapp = 'S';
            $stmt->bind_param("ss", $validate_whatsapp, $_authorization_id);
            // Executa a consulta de atualização
            if ($stmt->execute()) {
                // redirecionar para pagina de vendas
                if(!isset($_GET['redirect'])){header("Location: ../painel/vendas.php");}
                else{
                  $redirect_url = urldecode($_GET['redirect']);
                  header("Location: ../".$redirect_url);
                }                
            } else {
                echo "Erro na atualização de dados: " . $stmt->error;
                $_error_ = True;
                $_error_msg_ = 'Error interno'; 
            }
            $stmt->close();               
        }else{
          $_error_ = True;
          $_error_msg_ = 'Código não confere';          
        }
      }      

  }
}



if(isset($_POST['send_cod'])) {
  if(strlen($_POST['numWhatsapp']) == 0){
    $_error_ = True;
    $_error_msg_ = 'Não foi informado o numero do whatsapp';
  } else {
    // Função para verificar se o numero do whatsapp já está cadastrado
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
    $numWhatsapp = $mysqli->real_escape_string($_POST['numWhatsapp']);
    $numWhatsapp = preg_replace('/\D/', '', $numWhatsapp);
    $codigo = mt_rand(100000, 999999);
    // verifica se o usuario trocou de numero 
    if($numWhatsappBase != $numWhatsapp){
        // verifica se o numero ja esta cadastrado para este usuario
        if (ckNumWhatsapp($mysqli,$numWhatsapp)) {
            $_error_ = True;
            $_error_msg_ = 'Numero já esta cadastrado';
            $numWhatsapp = $numWhatsappBase;
        }    
    }  
    // verifica se é empresa
    if($_error_ == False){ 
      if( $_authorization_type == 'company'){
          $sql = "UPDATE empresa SET numero_whatsapp = ?, cod_validate_whatsapp = ? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
          $stmt = $mysqli->prepare($sql);
          if (!$stmt) {
              $_error_ = True;
              $_error_msg_ = 'Error interno'; 
              echo "Erro na preparação da consulta: " . $conn->error;
              return -1;
          }
          $validate_whatsapp = 'S';
          $stmt->bind_param("sss", $numWhatsapp, $codigo, $_authorization_id);
          // Executa a consulta de atualização
          if ($stmt->execute()) {
              //echo "Dados atualizados com sucesso!";
          } else {
              echo "Erro na atualização de dados: " . $stmt->error;
              $_error_ = True;
              $_error_msg_ = 'Error interno'; 
          }
          $stmt->close();  
      }

      if( $_authorization_type == 'user'){
        $sql = "UPDATE usuario SET numero_whatsapp = ?, cod_validate_whatsapp = ? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            $_error_ = True;
            $_error_msg_ = 'Error interno'; 
            echo "Erro na preparação da consulta: " . $conn->error;
            return -1;
        }
        $validate_whatsapp = 'S';
        $stmt->bind_param("sss", $numWhatsapp, $codigo, $_authorization_id);
        // Executa a consulta de atualização
        if ($stmt->execute()) {
            //echo "Dados atualizados com sucesso!";
        } else {
            echo "Erro na atualização de dados: " . $stmt->error;
            $_error_ = True;
            $_error_msg_ = 'Error interno'; 
        }
        $stmt->close();  
      }    

      // envia o codig para o whatsapp
      if(send_codigo($numWhatsapp,$codigo) == true){
        // mostra mensagem que o codigo foi enviado com sucesso
      }  
    }  
  }
}

/*
|--------------------------------------------------------------------
| Envia o codigo assim que carregar a pagina 
|--------------------------------------------------------------------
| 
| Para facilitar para o usuario pode ser enviado o codigo assim que  
| a pagina for carregada passando uma valor no cookie sinalizando que
| que ja foi enviado a mensagem
|
*/

if(!isset($_COOKIE['send_whatsapp_code']))
{

    $codigo = mt_rand(100000, 999999);

    if( $_authorization_type == 'company')
    {

        # Altera o codigo de validação na base de dados
        $sql = "UPDATE empresa SET cod_validate_whatsapp = ? WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            $_error_ = True;
            $_error_msg_ = 'Error interno'; 
            echo "Erro na preparação da consulta: " . $conn->error;
            return -1;
        }
        $validate_whatsapp = 'S';
        $stmt->bind_param("ss", $codigo, $_authorization_id);
        # Executa a consulta de atualização
        if ($stmt->execute()) {
            //echo "Dados atualizados com sucesso!";
        } else {
            echo "Erro na atualização de dados: " . $stmt->error;
            $_error_ = True;
            $_error_msg_ = 'Error interno'; 
        }
        $stmt->close();  


    }


    if( $_authorization_type == 'user')
    {

        # Altera o codigo de validação na base de dados
        $sql = "UPDATE usuario SET cod_validate_whatsapp = ? WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            $_error_ = True;
            $_error_msg_ = 'Error interno'; 
            echo "Erro na preparação da consulta: " . $conn->error;
            return -1;
        }
        $validate_whatsapp = 'S';
        $stmt->bind_param("ss", $codigo, $_authorization_id);
        // Executa a consulta de atualização
        if ($stmt->execute()) {
            //echo "Dados atualizados com sucesso!";
        } else {
            echo "Erro na atualização de dados: " . $stmt->error;
            $_error_ = True;
            $_error_msg_ = 'Error interno'; 
        }
        $stmt->close();  


    }  
    
    #envia o codigo para o whatsapp
    if(send_codigo($numWhatsapp,$codigo) == true)
    {

        # importante passar para o cookie que ja foi enviado o codigo 
        # para não enviar novamente quando a pagina for atualizada
        setcookie('send_whatsapp_code','true', time() + (86400 * 30), "/");

    }    


}


if($_error_ == True){$show_alert = 'True';}else{$show_alert = 'False';}
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/login.register/fonts/icomoon/style.css">

    <link rel="stylesheet" href="../assets/login.register/css/owl.carousel.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/login.register/css/bootstrap.min.css">
    
    <!-- Style -->
    <link rel="stylesheet" href="../assets/login.register/css/style.css?v=1.0">
    <link rel="stylesheet" href="../assets/css/module/button.css?v=1.0">

    <!-- sweetalert2 -->    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css" rel="stylesheet">

    <title>Validar whatsapp | Cataloguei.shop</title>
  </head>
  <body>
  

  <div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-image: url('../assets/login.register/images/0.png');"></div>
    <div class="contents order-2 order-md-1">

      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-7">
            <h3>Validar <strong>Whatsapp</strong></h3>
            <p class="mb-4">Vamos validar seu whatsapp para lhe notificar quando tiver um novo pedido</p>
            <form action="" method="POST">

       
              <div style="border-radius: 0;" class="form-group first">
                <label for="numWhatsapp">Numero whatsapp</label>
                <input id="numWhatsapp" type="text" class="form-control" placeholder="(00) 0 0000-0000" name="numWhatsapp" value="<?php echo($numWhatsapp); ?>">
              </div>                     
              <div class="form-group last mb-3">
                <label for="cod">Código</label>
                <input type="cod" class="form-control" placeholder="Código" type="text" name="cod">
              </div>
              
              <input type="submit" name="valida_cod" value="Validar código" class="btn btn-block btn-primary">
              <br><br>
              <label style="font-size: 14px; color: #9DA8A8;">⚠️Caso não tenha recebido o código clique em reenviar código novamente.</label>

              <input type="submit" name="send_cod" style="font-weight: 600;" value="Reenviar código novamente" class="btn btn-block btn-secondary">

            </form>
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
    <script src="../assets/login.register/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/login.register/js/popper.min.js"></script>
    <script src="../assets/login.register/js/bootstrap.min.js"></script>
    <script src="../assets/login.register/js/main.js"></script>
    <script>
        alert_error('<?php echo $show_alert ?>','<?php echo $_error_msg_ ?>');
    </script>
  </body>

</html>