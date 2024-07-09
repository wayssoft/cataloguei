<?php 
include('../req/conex.php');
$noShopCart = false;
$_error_ = false;
$_error_msg_ = '';
$_SUCCESS = false;

if(!isset($_GET['loja'])){
    //$_error_ = True;
    //die("error não foi passado a variavel loja<p><a href=\"log-in.php\">documentação</a></p>");    
}else{$loja = $_GET['loja'];}

if(!isset($_COOKIE['authorization_id'])) {$noShopCart = true;}
if(!isset($_COOKIE['authorization_type'])) {$noShopCart = true;}

// verifica se foi clicado no botão excluir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados enviados
    $id_pedido_cancela = $_POST['id_pedido'] ?? '';
    $action = $_POST['action'] ?? '';
    if($action == 'CANCELAR_PEDIDO'){
        $sql = "UPDATE venda SET status=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            echo "Erro na preparação da consulta: " . $conn->error;
            return -1;
        }
        $statusAltCod = 'canceled';
        $stmt->bind_param("ss",  $statusAltCod, $id_pedido_cancela);
        // Executa a consulta de atualização
        if ($stmt->execute()) {
            $_retorno_action = 'success';
            $_retorno_action_msg = 'pedido foi cancelado com sucesso';
        } else {
            $_retorno_action = 'error';
            $_retorno_action_msg = "Erro na atualização de dados: " . $stmt->error;
            //echo "Erro na atualização de dados: " . $stmt->error;
        }
        $stmt->close();
        // Define o cabeçalho de resposta como JSON
        header('Content-Type: application/json');  
        // Constrói a resposta JSON      
        echo json_encode([
            'result' => htmlspecialchars($_retorno_action),
            'message' => htmlspecialchars($_retorno_action_msg)
        ]);
        exit; // Termina a execução para evitar renderizar o formulário novamente
    }
}

if($noShopCart == false){
    $sql_code = "SELECT * FROM venda WHERE usuario_id = ".$_COOKIE['authorization_id']." and status='pending'";
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 0){$noShopCart = true;}else{
        $venda = $sql_query->fetch_assoc();
        $id_venda = $venda['id'];
        $id_endereco = $venda['endereco_id'];
        $id_empresa = $venda['empresa_id'];
    }
}

if($noShopCart == false){
    $sql_code = "SELECT * FROM empresa WHERE id = ".$id_empresa;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) {    
        $empresa = $sql_query->fetch_assoc();
        $path_logo  = $empresa['path_logo'];
        $loja = $empresa['dominio'];
        if($path_logo != ''){
            $path_logo = "../painel/".$path_logo;
            if(!file_exists( $path_logo )){ $hasLogo = false; }else{ $hasLogo = true; }
        }else{$hasLogo = false;};
    } else {
        $_error_ = True;
        die("error não foi encontrado a pagina 12<p><a href=\"log-in.php\">documentação</a></p>");
    }
}

// verifica se tem o usuario logado
if(!isset($_COOKIE['authorization_id'])){$login_user=false;}else{
    if(!isset($_COOKIE['authorization_type'])){$login_user=false;}{
        if($_COOKIE['authorization_type'] != 'user'){$login_user=false;}else{
            $login_user=true;
            //busca qual o usuario lagado
            $sql_code = "SELECT * FROM usuario WHERE id = ".$_COOKIE['authorization_id'];
            $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
            $quantidade = $sql_query->num_rows;
            if($quantidade == 0) {
                die("error x006 Você não pode acessar esta página porque não está logado.<p><a href=\"log-in.php\">Entrar</a></p>");
            }
            $usuario = $sql_query->fetch_assoc();
            $usuario_nome = $usuario['nome'];
            $primeira_letra_nome_usuario = substr($usuario_nome, 0, 1);
            $primeira_letra_nome_usuario = strtoupper($primeira_letra_nome_usuario);
        } 
    }
}

//busca o endereco selecionado
if($noShopCart == false){
    $sql_code = "SELECT * FROM endereco WHERE id = ".$id_endereco;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 0){$noSelectEndereco = true;}else{
        $endereco = $sql_query->fetch_assoc();
        $id_endereco   = $endereco['id'];
        $nome_endereco = $endereco['nome'];
        $cep           = $endereco['cep'];
        $bairro        = $endereco['bairro'];
        $rua           = $endereco['rua'];
        $complemento   = $endereco['complemento'];    
        $numero        = $endereco['numero']; 
        $noSelectEndereco = false;
        if(intval($id_endereco) == 1){$noSelectEndereco = true;}
    }
}

// cancelar iten
if(isset($_POST['bt_cancela_iten'])) {
    if(!isset($_GET['id_iten_cancel'])){
        $_error_ = True;
        die("error não foi passado a variavel id_iten_cancel <p><a href=\"log-in.php\">documentação</a></p>");    
    }else{$id_iten_cancel = $_GET['id_iten_cancel'];}
    $sql = "UPDATE venda_detalhe SET status=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo "Erro na preparação da consulta: " . $conn->error;
        return -1;
    }
    $status = 'canceled';
    $stmt->bind_param("ss", $status, $id_iten_cancel);
    // Executa a consulta de atualização
    if ($stmt->execute()) {
        //echo "Dados atualizados com sucesso!";
    } else {
        echo "Erro na atualização de dados: " . $stmt->error;
    }
    $stmt->close();    
}




// busca os itens da venda
if($noShopCart == false){
    $sql_code = "SELECT venda_detalhe.id, venda_detalhe.qtd, venda_detalhe.valor_un, venda_detalhe.valor_total, venda_detalhe.obs, produto.nome FROM venda_detalhe";
    $sql_code = $sql_code . " INNER JOIN produto ON venda_detalhe.produto_id = produto.id";
    $sql_code = $sql_code . " WHERE venda_detalhe.venda_id = ".$id_venda." and venda_detalhe.status = 'added'"; 
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    $venda_detalhe = $sql_query->fetch_all();
    if($quantidade == 0){$noShopCart = true;}else{
        //$id_venda_detalhe = $venda_detalhe['id'];
    }
}


function sendWhatsappUser($mysqli,$value): bool {
    $retorno = false;
    $sql_code = "SELECT * FROM venda WHERE id = ".$value;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) { 
        $venda = $sql_query->fetch_assoc();
        $_id_user = $venda['usuario_id'];
        $sql_code = "SELECT * FROM usuario WHERE id = ".$_id_user;
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
        $quantidade = $sql_query->num_rows;
        if($quantidade == 1) {
            $usuario = $sql_query->fetch_assoc();
            $num_whatsapp = $usuario['numero_whatsapp'];
            $cod = str_pad($value, 8, '0', STR_PAD_LEFT);    
            $msg = "Seu pedido  *#".$cod."* foi enviado com sucesso e está aguardando aceitação pela loja";
            $numero = '55'.$num_whatsapp;
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
            $retorno = false;
            } else {
            //echo $response;
            $retorno = true;
            }
        }
    }
    return $retorno;
} 


function sendWhatsappEmpresa($mysqli,$value): bool {
    $retorno = false;
    $sql_code = "SELECT * FROM venda WHERE id = ".$value;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) { 
        $venda = $sql_query->fetch_assoc();
        $_id_empresa = $venda['empresa_id'];
        $_id_venda = $venda['id'];
        $token = md5($_id_venda."Todos temos luz e trevas dentro de nós."); 
        $sql_code = "SELECT * FROM empresa WHERE id = ".$_id_empresa;
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
        $quantidade = $sql_query->num_rows;
        if($quantidade == 1) {
            $usuario = $sql_query->fetch_assoc();
            $num_whatsapp = $usuario['numero_whatsapp'];
            $cod = str_pad($value, 8, '0', STR_PAD_LEFT);   
            $linkDescription = "Cataloguei pedido: ".$cod; 
            $title = "Pedido:".$cod;
            $link = "https://app.cataloguei.shop/painel/pedido.php?cod=".$_id_venda."&token=".$token;
            $numero = '55'.$num_whatsapp;
            $curl = curl_init();
            curl_setopt_array($curl, array(
            //CURLOPT_URL => "https://api.z-api.io/instances/3A9774DE30F9100EAEE986DC9E7D4A64/token/5C91D4E7085ECA40B610E629/send-text",
            CURLOPT_URL => "https://api.z-api.io/instances/3A9774DE30F9100EAEE986DC9E7D4A64/token/5C91D4E7085ECA40B610E629/send-link",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false, // Desabilitar verificação SSL (não recomendado)
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"phone\": \"$numero\",\"message\": \"Você tem um novo pedido, confira seu pedido: $link\",\"image\": \"https://cataloguei.shop/assets/img/logo-sm.png\",\"linkUrl\": \"$link\",\"title\": \"$title\",\"linkDescription\": \"$linkDescription\"}",
            //CURLOPT_POSTFIELDS => "{\"phone\": \"$numero\", \"message\": \"$msg\"}",
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
    }
    return $retorno;
}

// finalizar pedido
if(isset($_POST['bt_finalizar'])) {
    // verifica se foi informado o tipo de pagamento
    if(!isset($_POST['tipo_pagamento'])){
        $_error_ = true;
        $_error_msg_ = 'Não foi informado o tipo de pagamento';
    } if ($noSelectEndereco == true){
        $_error_ = true;
        $_error_msg_ = 'Selecione o Endereco de entrega';
    } else{
        if ($_error_ == false){
            $tipo_pagamento = $_POST['tipo_pagamento'];
            $sql = "UPDATE venda SET status=?, tipo_pagamento=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "Erro na preparação da consulta: " . $conn->error;
                return -1;
            }
            $status = 'awaiting_approval';
            $stmt->bind_param("sss", $status, $tipo_pagamento, $id_venda);
            // Executa a consulta de atualização
            if ($stmt->execute()) {
                //echo "Dados atualizados com sucesso!";
                $_SUCCESS = true;
            } else {
                echo "Erro na atualização de dados: " . $stmt->error;
            }
            $stmt->close();  
            
            // envia mesame para o whatsapp do usuario e para empresa
            sendWhatsappEmpresa($mysqli,$id_venda);
            sendWhatsappUser($mysqli,$id_venda);
        }
    }
}
// Links para pop-menu
$link = 'd.php?loja='.$loja;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>cataloguei shop</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/main.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/alerts.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/buttons.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/checkbox.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/menu.css'>
    <!--boxicon-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!--sweetalert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src='../assets/js/alerts.js'></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <!-- -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>
<body>
    <div class="b-main-container-topo b-main-shadow-topo">
        <div style="display: <?php if($hasLogo==true){echo("none");}; ?>;" class="logo b-main-centro-total"><img src="../assets/img/cataloguei.shop.logo.png" /></div>
        <div style="display: <?php if($hasLogo==false){echo("none");}; ?>;" class="logo b-main-centro-total"><img class="has-logo" src="<?php echo($path_logo) ?>" /></div>
        <div class="item-menu b-main-centro-total"><a href="../d.php?loja=<?php echo $loja; ?>">Inicio</a></div>
        <div class="search"></div>       
        <!-- menu-->  
        <div class="settings  b-main-centro-total">
            <div class="table_center">
                <div class="drop-down">
                    <div id="dropDown" class="drop-down__button">
                        <span class="drop-down__name"><i class='bx bxs-down-arrow'></i></span>
                    </div>
                    <div class="drop-down__menu-box">
                        <ul class="drop-down__menu">
                            <li onclick="entrar_usuario_menu('<?php echo('../login.php?type=user&redirect='. urlencode($link)); ?>')" data-name="entrar" class="drop-down__item">
                                Entrar
                            </li>
                            <li onclick="criar_usuario_menu('<?php echo('../register/user.php?redirect='. urlencode($link)); ?>')" data-name="criar_conta" class="drop-down__item">
                                Criar conta
                            </li>
                            <li data-name="falar_com_loja" class="drop-down__item">
                                Falar com a loja
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fim menu--> 
        <div style="display: <?php if($login_user == true){echo("none");}; ?>;" class="img-user b-main-centro-total"><img src="../assets/img/img.perfil.jpg" /></div>
        <div style="display: <?php if($login_user == false){echo("none");}; ?>;" class="img-user b-main-centro-total"><div class="avatar-user b-main-centro-total"><p><?php echo $primeira_letra_nome_usuario; ?></p></div></div>
    </div>
    <div style="width: 100%; height: 60px;"></div>
    <div class="b-main-container-produtos b-main-centro-total">
        <div style="width: 600px;" class="display">

            <!-- alerta de erros -->
            <div style="display: <?php if($_error_ == false){ echo('none'); } ?>;" class="warning-no-margin">
                <img src="../assets/img/alert.png" />
                <p><?php echo($_error_msg_); ?></p>
            </div>

            <!-- Endereço de entrega -->
            <p style="display: <?php if($noShopCart == true){ echo('none'); }; if($_SUCCESS == true){ echo('none'); } ?>;" class="b-main-bag-title">Endereço de entrega</p>
            <div style="display: <?php if($noShopCart == true){ echo('none'); }; if($_SUCCESS == true){ echo('none'); } ?>;" class="b-main-bag-entrega">
                <div class="display">
                    <img src="../assets/img/petalmapsicon.svg" />
                    <div style="display: <?php if($noSelectEndereco == true){ echo('none'); } ?>;" class="desc">
                        <p><?php echo($rua." ".$bairro." N° ".$numero);  ?></p>
                        <label><?php echo($complemento);  ?></label>    
                    </div>
                    <div style="display: <?php if($noSelectEndereco == false){ echo('none'); } ?>;" class="desc">
                        <p style="margin-top: 9px; font-size: 13px; color: #B2B2B2;">⚠️Clique em trocar endereço para selecionar</p>  
                    </div>                    
                </div>  
                <button class="bt-trocar-endereco" onclick="trocarEnd('<?php echo($id_venda); ?>')" type="submit">Troca endereço</button> 

                <div style="margin-top: 5px;" class="checkbox-wrapper-4">
                    <input class="inp-cbx" id="morning" type="checkbox"/>
                    <label class="cbx" for="morning"><span>
                    <svg width="12px" height="10px">
                        <use xlink:href="#check-4"></use>
                    </svg></span><span>Vou retirar o produto no local</span></label>
                    <svg class="inline-svg">
                        <symbol id="check-4" viewbox="0 0 12 10">
                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                        </symbol>
                    </svg>
                </div>                     
            </div>
             <!-- Fim Endereço de entrega -->

            <!-- Itens do pedido -->
            <p style="display: <?php if($noShopCart == true){ echo('none'); }; if($_SUCCESS == true){ echo('none'); } ?>;" class="b-main-bag-title">Itens do pedido</p>
            <div style="display: <?php if($noShopCart == true){ echo('none'); }; if($_SUCCESS == true){ echo('none'); } ?>;" class="b-main-bag-container-itens">
                <?php foreach($venda_detalhe as $row){?>
                <form action="bag.php?id_iten_cancel=<?php echo $row[0]; ?>" method="POST">    
                <div class="bag-itens">
                    <div class="bag-itens-desc">
                        <p><?php echo $row[1]; ?>x</p>
                        <label><?php echo $row[5]; ?></label>
                        <p class="preco">R$ <?php echo number_format($row[3],2,",","."); ?></p>
                    </div>
                    <button class="bt-excluir-iten-bag" name="bt_cancela_iten" type="submit">Excluir item</button>
                </div>
                </form>
                <?php }?>
            </div>
            <!-- Fim Itens do pedido -->

            

            <!-- Tipo de pagamento -->
            <form action="" method="POST">
            <p style="display: <?php if($noShopCart == true){ echo('none'); }; if($_SUCCESS == true){ echo('none'); } ?>;" class="b-main-bag-title">Tipo de pagamento</p>
            <div style="display: <?php if($noShopCart == true){ echo('none'); }; if($_SUCCESS == true){ echo('none'); } ?>;" class="b-main-bag-container-pagamento">
                <input type="radio" id="DINHEIRO" name="tipo_pagamento" value="DINHEIRO">
                <label class="bt-radio" for="DINHEIRO">Dinheiro</label><br><br>

                <input type="radio" id="PIX" name="tipo_pagamento" value="PIX">
                <label class="bt-radio" for="PIX">Pix</label><br><br>

                <input type="radio" id="CARTAO_CREDITO" name="tipo_pagamento" value="CARTAO_CREDITO">                
                <label class="bt-radio" for="CARTAO_CREDITO">Cartão de credito</label><br><br>

                <input type="radio" id="CARTAO_DEBITO" name="tipo_pagamento" value="CARTAO_DEBITO">
                <label class="bt-radio" for="CARTAO_DEBITO">Cartão de debito</label>
            </div>
            <!-- Fim Tipo de pagamento -->
            <div style="display: <?php if($noShopCart == true){ echo('none'); }; if($_SUCCESS == true){ echo('none'); } ?>;" class="b-main-bag-container-bt-finalizar">    
                <button style="width: 220px; float: right; margin-top: 10px;" class="button-65" name="bt_finalizar" type="submit">Finalizar pedido</button>
                <button class="button-cancelar-pedido-bag" name="bt_cancelar_pedido" type="button" onclick="cancela_pedido_bag('<?php echo $id_venda; ?>')"><i class='bx bx-trash'></i></button>
            </div>
            </form>
            <!-- Fim Tipo de pagamento -->

            <!-- mensagem carrinho vazio -->        
            <div style="display: <?php if($noShopCart == false){ echo('none'); } ?>;" class="b-main-bag-no-venda">
                <div class="img-no-venda b-main-centro-total"><img src="../assets/img/2762885.png" /></div>  
                <div class="desc-no-venda b-main-centro-total"><p>Seu carrinho está vazio</p></div>  
            </div>
            <!-- fim -->     
            
            <!-- sucesso venda finalizada --> 
            <div style="display: <?php if($_SUCCESS == false){ echo('none'); } ?>;" class="b-main-bag-success-venda">
                <div class="img-success-venda b-main-centro-total"><img src="../assets/img/envio.gif" /></div>  
                <div class="desc-success-venda b-main-centro-total"><p>Seu pedido foi realizado com sucesso!</p></div>  
                <div class="desc-success-venda b-main-centro-total"><label>Enviaremos todo o status do seu pedido para seu whatsapp</label></div> 
            </div>  
            <!-- fim -->            

        </div>
    </div>
</body>
<script src='../assets/js/main.js'></script>
<?php 
if(isset($_GET['produto'])){
    echo "<script type='text/javascript'>
    produtoDetalhe('".$_GET['produto']."');
    </script>";    
}
?>
</html>