<?php 
include('../req/conex.php');
$_error_ = False;
$_error_msg_ = '';
//zera variaveis
$email       = '';
$nome        = '';
$dominio     = '';
$numWhatsapp = '';
if(isset($_POST['enviar'])) {

  $email       = $mysqli->real_escape_string($_POST['email']);
  $senha       = $mysqli->real_escape_string($_POST['password']);
  $nome        = $mysqli->real_escape_string($_POST['nome']);
  $dominio     = $mysqli->real_escape_string($_POST['dominio']);
  $dominio     = preg_replace('/[^a-zA-Z0-9]/', '', $dominio);
  $numWhatsapp = $mysqli->real_escape_string($_POST['numWhatsapp']);
  $numWhatsapp = preg_replace('/\D/', '', $numWhatsapp);
  $senha = md5($senha);

  if(strlen($_POST['email']) == 0) {
      $_error_ = True;
      $_error_msg_ = 'Preencha seu e-mail';
  } else if(strlen($_POST['password']) == 0) {
      $_error_ = True;
      $_error_msg_ = 'Preencha sua senha';
  } else if(strlen($_POST['numWhatsapp']) == 0){
      $_error_ = True;
      $_error_msg_ = 'Não foi informado o numero do whatsapp';
  } else if(strlen($_POST['dominio']) == 0){
    $_error_ = True;
    $_error_msg_ = 'Não foi informado o domínio da sua loja';
  } else if(strlen($_POST['nome']) == 0){
    $_error_ = True;
    $_error_msg_ = 'Não foi informado o nome da sua loja';
  } else {

      // Função para verificar se um CNPJ já está cadastrado
      function ckDominio($mysqli,$value): bool {
        // Prepara a consulta SQL para verificar o CNPJ
          $sql = "SELECT dominio FROM empresa WHERE dominio = ?";
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
      // verifica se o cnpj ja esta cadastrado
      if (ckDominio($mysqli,$dominio)) {
        $_error_ = True;
        $_error_msg_ = 'CNPJ já cadastrado';
      }
      // verifica se o email ja esta cadastrado para este usuario
      if (ckEmail($mysqli,$email)) {
        $_error_ = True;
        $_error_msg_ = 'Email já cadastrado';
      }     
      // verifica se o email ja esta cadastrado para este usuario
      if (ckNumWhatsapp($mysqli,$numWhatsapp)) {
        $_error_ = True;
        $_error_msg_ = 'Numero de whatsapp já esta cadastrado';
      }               
      // cria empresa na base de dados
      if($_error_ == False){
          // Prepara a consulta SQL para inserção dos dados
          $sql = "INSERT INTO empresa (email, senha, nome, numero_whatsapp, dominio, status, cidade_id, validate_whatsapp, cod_validate_whatsapp)  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
          $stmt = $mysqli->prepare($sql);
          if (!$stmt) {
            echo "Erro na preparação da consulta: " . $conn->error;
            return -1;
          }
          // Vincula os parâmetros à consulta preparada
          $status = 'pending_validate_whatsApp';
          $idCidade = 1;
          $codigo = mt_rand(100000, 999999);
          $validate_whatsapp = 'N';
          $stmt->bind_param("sssssssss", $email, $senha, $nome, $numWhatsapp, $dominio, $status, $idCidade, $validate_whatsapp, $codigo);
          // Executa a consulta
          if ($stmt->execute()) {
              $idEmpresa = $mysqli->insert_id; // Obtém o ID do registro inserido
              setcookie('authorization_type','company', time() + (86400 * 30), "/");
              setcookie('authorization_id',$idEmpresa, time() + (86400 * 30), "/");
              header("Location: validate_whatsapp.php");
          } else {
              echo "Erro na inserção de dados: " . $stmt->error;
          }
          $stmt->close();
      }
  }
  if($_error_ == True){$show_alert = 'True';}else{$show_alert = 'False';}
}
?>
<!doctype html>
<html lang="pt-BR">
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
    <link rel="stylesheet" href="../assets/css/module/button.css">

    <!-- sweetalert2 -->    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css" rel="stylesheet">

    <title>Criar conta | Cataloguei.shop</title>
  </head>
  <body>
  

  <div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-image: url('../assets/login.register/images/0.png');"></div>
    <div class="contents order-2 order-md-1">

      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-7">
            <h3>Criar conta <strong>Cataloguei.shop</strong></h3>
            <p class="mb-4">Cadastre-se hoje mesmo e aproveite todas as funcionalidades do Cataloguei.shop. É rápido, fácil e gratuito!</p>
            <form action="" method="POST">
              <div class="form-group first">
                <label for="nome">Nome fantasia da empresa</label>
                <input type="text" class="form-control" placeholder="ex. loja da Maria" name="nome" value="<?php echo $nome ?>">
              </div>
              <div style="border-radius: 0;" class="form-group first">
                <label for="dominio">Domínio</label>
                <input type="text" class="form-control" placeholder="lojamaria" pattern="[a-zA-Z0-9]*" name="dominio" value="<?php echo $dominio ?>" pattern="[a-zA-Z0-9]*" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')">
              </div>   
              <div style="border-radius: 0;" class="form-group first">
                <label for="email">Email de usuario</label>
                <input type="text" class="form-control" placeholder="Seu-email@gmail.com" name="email" value="<?php echo $email ?>">
              </div>        
              <div style="border-radius: 0;" class="form-group first">
                <label for="numWhatsapp">Numero whatsapp</label>
                <input id="numWhatsapp" type="text" class="form-control" placeholder="(00) 0 0000-0000" name="numWhatsapp" value="<?php echo $numWhatsapp ?>">
              </div>                     
              <div class="form-group last mb-3">
                <label for="password">Senha de usuario</label>
                <input type="password" class="form-control" placeholder="Sua senha" name="password">
              </div>
              
              <div class="d-flex mb-5 align-items-center">
                <label class="control control--checkbox mb-0"><span class="caption">Lê os termos e condições do cataloguei.shop</span>
                  <input type="checkbox" checked="checked"/>
                  <div class="control__indicator"></div>
                </label>
              </div>

              <input type="submit" name="enviar" value="Criar conta" class="btn btn-block btn-primary">

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