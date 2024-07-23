<?php 
include('../req/conex.php');
include('../model/settings.php');
$_error_ = false;
$_error_ = false;
$_error_msg_ = '';
$_SUCCESS = false;
if(!isset($_GET['cod'])){
    $_error_ = true;
}else{$cod = $_GET['cod'];}


function sendWhatsappEmpresa($mysqli,$value): bool 
{

    $retorno = false;
    $sql_code = "SELECT * FROM venda WHERE id = ".$value;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) { 
        $venda = $sql_query->fetch_assoc();
        $_id_empresa = $venda['empresa_id'];
        $sql_code = "SELECT * FROM empresa WHERE id = ".$_id_empresa;
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
        $quantidade = $sql_query->num_rows;
        if($quantidade == 1) {
            $usuario = $sql_query->fetch_assoc();
            $num_whatsapp = $usuario['numero_whatsapp'];
            $cod = str_pad($value, 8, '0', STR_PAD_LEFT);    
            $msg = "Você tem um novo pedido no cataloguei.shop cod. pedido: *#".$cod."* click no link para mais informações www.cataloguei.shop/pedido/pedido.php";
            $numero = '55'.$num_whatsapp;

            // seta os dados da z-api
            $zapi_client_token = 'F3b7c9eafb4b64139a5d5bb54e6a41273S'; 
            $zapi_token        = '5C91D4E7085ECA40B610E629';
            $zapi_instances    = '3A9774DE30F9100EAEE986DC9E7D4A64';

            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.z-api.io/instances/$zapi_instances/token/$zapi_token/send-text",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false, // Desabilitar verificação SSL (não recomendado)
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"phone\": \"$numero\", \"message\": \"$msg\"}",
            CURLOPT_HTTPHEADER => array(
               "client-token: $zapi_client_token",
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


function sendWhatsappUser($mysqli,$value,$status,$id_empresa): bool 
{
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
            if( strtoupper($status) == strtoupper('preparation')){
                $msg = "Seu pedido  *#".$cod."* foi aceito pela empresa é esta em preparação";
            }
            if( strtoupper($status) == strtoupper('delivery')){
                $msg = "Seu pedido  *#".$cod."* está a caminho";
            } 
            if( strtoupper($status) == strtoupper('finalized')){
                $msg = "Obrigado pelo seu pedido, você poderia avaliar nossa loja";
            }
            $numero = '55'.$num_whatsapp;

            // seta os dados da z-api
            $settings = new Settings($id_empresa);
            if($settings->getZapi_ativado() == 'S')
            {
                $zapi_client_token = $settings->getZapi_client_token(); 
                $zapi_token        = $settings->getZapi_token();
                $zapi_instances    = $settings->getZapi_instances();
            }else{
                $zapi_client_token = 'F3b7c9eafb4b64139a5d5bb54e6a41273S'; 
                $zapi_token        = '5C91D4E7085ECA40B610E629';
                $zapi_instances    = '3A9774DE30F9100EAEE986DC9E7D4A64';                
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.z-api.io/instances/$zapi_instances/token/$zapi_token/send-text",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false, // Desabilitar verificação SSL (não recomendado)
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"phone\": \"$numero\", \"message\": \"$msg\"}",
            CURLOPT_HTTPHEADER => array(
               "client-token: $zapi_client_token",
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


function sendWhatsappInflu($mysqli,$value): bool 
{

    $retorno = false;
    $sql_code = "SELECT * FROM venda WHERE id = ".$value;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) { 
        $venda = $sql_query->fetch_assoc();
        $_id_cupom = $venda['id_cupom'];
        $sql_code = "SELECT * FROM cupom WHERE id = ".$_id_cupom;
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
        $quantidade = $sql_query->num_rows;
        if($quantidade == 1) {
            $cupom = $sql_query->fetch_assoc();
            $num_whatsapp = $cupom['whatsapp_notifica_influ'];   
            $msg = "Pedido: *#".$value."* Finalizado com seu cupom";
            $numero = '55'.$num_whatsapp;

            // seta os dados da z-api
            $zapi_client_token = 'F3b7c9eafb4b64139a5d5bb54e6a41273S'; 
            $zapi_token        = '5C91D4E7085ECA40B610E629';
            $zapi_instances    = '3A9774DE30F9100EAEE986DC9E7D4A64';

            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.z-api.io/instances/$zapi_instances/token/$zapi_token/send-text",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false, // Desabilitar verificação SSL (não recomendado)
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"phone\": \"$numero\", \"message\": \"$msg\"}",
            CURLOPT_HTTPHEADER => array(
               "client-token: $zapi_client_token",
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



function baixa_estoque($mysqli,$id_venda): bool {
    $sql_code = "SELECT produto_id,qtd,variacao,id_variacao FROM venda_detalhe WHERE status = 'added' AND venda_id = ".$id_venda;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    //$quantidade = $sql_query->num_rows;
    $venda_detalhe = $sql_query->fetch_all();    
    foreach($venda_detalhe as $row)
    {

        // pega valores para verificar a variação
        $variacao    = $row[2];
        $id_variacao = $row[3];
        $qtd_pedido  = $row[1];    

        // busca quantidade atual do estoque
        $sql_code = "SELECT id,estoque FROM produto WHERE id = ".$row[0];
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
        $quantidade = $sql_query->num_rows;
        if($quantidade > 0){
            $ds_produto = $sql_query->fetch_assoc();
            $estoque_atual = $ds_produto['estoque'];
        }else{return false;}
        $estoque = $estoque_atual - $qtd_pedido;
        // altera o estoque do produto
        $sql = "UPDATE produto SET estoque=? WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {return false;}
        $stmt->bind_param("ss", $estoque, $row[0]);
        // Executa a consulta de atualização
        if ($stmt->execute()) {
            // update com sucesso
        } else {
            echo "Erro na atualização de dados: " . $stmt->error; return false;
        }
        $stmt->close();   
        
        // verifica se tem variação e baixa o estoque da variação
        if($variacao=='S')
        {

            // busca quantidade atual do estoque
            $sql_code = "SELECT id,estoque FROM variacao_produto WHERE id = ".$id_variacao;
            $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
            $quantidade = $sql_query->num_rows;
            if($quantidade > 0){
                $ds_variacao = $sql_query->fetch_assoc();
                $estoque_atual = $ds_variacao['estoque'];
            }else{return false;}
            $estoque = $estoque_atual - $qtd_pedido;
            // altera o estoque do produto
            $sql = "UPDATE variacao_produto SET estoque=? WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {return false;}
            $stmt->bind_param("ss", $estoque, $id_variacao);
            // Executa a consulta de atualização
            if ($stmt->execute()) {
                // update com sucesso
            } else {
                echo "Erro na atualização de dados: " . $stmt->error; return false;
            }
            $stmt->close(); 

        }
    }
    return true;
}

if(isset($_POST['bt_status'])) {
    $sql_code = "SELECT * FROM venda WHERE id = ".$cod;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 0){$_error_ = true;}else{
        $venda = $sql_query->fetch_assoc();
        $id_venda = $venda['id'];
        $status = $venda['status'];
        $id_empresa = $venda['empresa_id'];
        $ativa_cupom = $venda['ativa_cupom'];
        $id_cupom = $venda['id_cupom'];
    }
    // verifica o status
    if( strtoupper($status) == strtoupper('finalized'))
    {
        echo('Pedido já foi finalizado');
        exit;
    }
    if( strtoupper($status) == strtoupper('awaiting_approval')){$statusAltCod = 'preparation';}
    if( strtoupper($status) == strtoupper('preparation')){$statusAltCod = 'delivery';} 
    if( strtoupper($status) == strtoupper('delivery')){$statusAltCod = 'finalized';} 

    // verifica se o status e finalized e da baixa no estoque
    if($statusAltCod == 'finalized'){
        if (baixa_estoque($mysqli,$id_venda)!=true) {echo("ERROR ao tentar dar baixa no estoque"); exit;}; 
        // verifica se a venda teve cupom e se tem influ
        if($ativa_cupom == 'S')
        {

                // busca o cupom na base de dados
            $sql_code = "SELECT * FROM cupom WHERE id = ".$id_cupom;
            $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
            $quantidade = $sql_query->num_rows;
            if($quantidade > 0)
            {
                $ds_cupom = $sql_query->fetch_assoc();
                $ativa_influ = $ds_cupom['ativa_influ'];
                $qtd_cupom = $ds_cupom['qtd_cupom'];
                $qtd_cupom = $qtd_cupom - 1;
                if($ativa_influ == 'S')
                {
                    // da baixa na quantidade do cupom
                    $sql = "UPDATE cupom SET qtd_cupom=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
                    $stmt = $mysqli->prepare($sql);
                    if (!$stmt) {
                        echo "Erro na preparação da consulta: " . $conn->error;
                        return -1;
                    }
                    $stmt->bind_param("ss",  $qtd_cupom, $id_cupom);
                    // Executa a consulta de atualização
                    if ($stmt->execute()) {
                        //echo "Dados atualizados com sucesso!";
                        sendWhatsappInflu($mysqli,$cod);
                    } else {
                        echo "Erro na atualização de dados: " . $stmt->error;
                    }
                    $stmt->close(); 
                } 

            }           


        };

    }    

    $sql = "UPDATE venda SET status=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo "Erro na preparação da consulta: " . $conn->error;
        return -1;
    }
    $stmt->bind_param("ss",  $statusAltCod, $id_venda);
    // Executa a consulta de atualização
    if ($stmt->execute()) {
        //echo "Dados atualizados com sucesso!";
        sendWhatsappUser($mysqli,$id_venda,$statusAltCod,$id_empresa);
        $_SUCCESS = true;
    } else {
        echo "Erro na atualização de dados: " . $stmt->error;
    }
    $stmt->close(); 

}

//
if($_error_ == false){
    $sql_code = "SELECT * FROM venda WHERE id = ".$cod;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 0){$_error_ = true;}else{
        $venda = $sql_query->fetch_assoc();
        $id_venda = $venda['id'];
        $id_usuario = $venda['usuario_id'];
        $id_endereco = $venda['endereco_id'];
        $status = $venda['status'];
    }
    // verifica o status aletar
    if( strtoupper($status) == strtoupper('awaiting_approval')){
        $statusAltCod = 'preparation';
        $statusAltDesc = 'Pedido em preparação';
    }
    if( strtoupper($status) == strtoupper('preparation')){
        $statusAltCod = 'delivery';
        $statusAltDesc = 'Pedido em entrega';
    } 
    if( strtoupper($status) == strtoupper('delivery')){
        $statusAltCod = 'finalized';
        $statusAltDesc = 'Pedido finalizado';
    }          
}


//busca usuario
if($_error_ == false){
    $sql_code = "SELECT * FROM usuario WHERE id = ".$id_usuario;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 0){$noSelectEndereco = true;}else{
        $usuario = $sql_query->fetch_assoc();
        $nome_usuario   = $usuario['nome'];
        $nun_whatsapp_usuario  = $usuario['numero_whatsapp'];
    }
}

//sendWhatsappEmpresa($mysqli,$id_venda);
//sendWhatsappUser($mysqli,$id_venda);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Pedido:<?php echo str_pad($id_venda, 8, '0', STR_PAD_LEFT) . ' - ' . $nome_usuario; ?></title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/main.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/alerts.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/buttons.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/checkbox.css'>
    <!--boxicon-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!--sweetalert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src='../assets/js/alerts.js'></script>
    <style>
        .roboto-light {
            font-family: "Roboto", sans-serif;
            font-weight: 500;
            font-style: normal;
            font-size: 18px;
        }
        .roboto-light-2 {
            font-family: "Roboto", sans-serif;
            font-weight: 400;
            font-style: normal;
            font-size: 14px;
            color: #7A8282;
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
    <div class="b-main-container-produtos b-main-centro-total">
        <div style="width: 600px;" class="display">

            <!-- alerta de erros -->
            <div style="display: <?php if($_error_ == false){ echo('none'); } ?>;" class="warning">
                <img src="../assets/img/alert.png" />
                <p><?php echo($_error_msg_); ?></p>
            </div>
            <br>
            <div style="width: 100%; height: 30px;" class="b-main-container-left">
                <label class="roboto-light">Alterar status pedido: #<?php echo str_pad($id_venda, 8, '0', STR_PAD_LEFT);  ?></label>
            </div>
            <div style="width: 100%; height: 30px;" class="b-main-container-left">
                <label class="roboto-light-2">Alterar para: <b><?php echo($statusAltDesc); ?></b></label>
            </div>            
            <br><br> 
            <form action="#" method="POST" enctype="multipart/form-data">    
                <div style="width: 100%; height: 50px; display: <?php if($_SUCCESS == true){ echo "none"; }; ?>;" class="b-main-container-left">
                    <button style="width: 220px;" name="bt_status" class="button-65" type="submit">Alterar status</button>
                </div>
            </form>
            <?php 
                    if($_SUCCESS == true){
                        echo "<script type='text/javascript'>
                            window.parent.location.href = './list.pedidos.php';
                        </script>";
                    }         
            ?>            
        </div>
    </div>

</body>
<script src='../assets/js/main.js'></script>
</html>