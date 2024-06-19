<?php 
include('../req/conex.php');
$noShopCart = false;
$_error_ = false;
$_error_msg_ = '';
$_SUCCESS = false;
if(!isset($_GET['cod'])){
    $noShopCart = true;
}else{$cod = $_GET['cod'];}
if(!isset($_GET['token'])){
    $noShopCart = true;
}else{$token = $_GET['token'];}
//calcula o token
$token_aux = md5($cod."Todos temos luz e trevas dentro de nós."); // frase do filme harry potter
if($token != $token_aux){$noShopCart = true;};
//
if($noShopCart == false){
    $sql_code = "SELECT * FROM venda WHERE id = ".$cod;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 0){$noShopCart = true;}else{
        $venda = $sql_query->fetch_assoc();
        $id_venda = $venda['id'];
        $id_usuario = $venda['usuario_id'];
        $id_endereco = $venda['endereco_id'];
        $data_venda = $venda['data_venda'];
        $hora_venda = $venda['hora_venda'];
        $tipo_pagamento = $venda['tipo_pagamento'];
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

//busca usuario
if($noShopCart == false){
    $sql_code = "SELECT * FROM usuario WHERE id = ".$id_usuario;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 0){$noSelectEndereco = true;}else{
        $usuario = $sql_query->fetch_assoc();
        $nome_usuario   = $usuario['nome'];
        $nun_whatsapp_usuario  = $usuario['numero_whatsapp'];
    }
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
        $sql_code = "SELECT * FROM empresa WHERE id = ".$_id_empresa;
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
        $quantidade = $sql_query->num_rows;
        if($quantidade == 1) {
            $usuario = $sql_query->fetch_assoc();
            $num_whatsapp = $usuario['numero_whatsapp'];
            $cod = str_pad($value, 8, '0', STR_PAD_LEFT);    
            $msg = "Você tem um novo pedido no cataloguei.shop cod. pedido: *#".$cod."* click no link para mais informações www.cataloguei.shop/pedido/pedido.php";
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
</head>
<body>
    <div class="b-main-container-produtos b-main-centro-total">
        <div style="width: 600px;" class="display">

            <!-- alerta de erros -->
            <div style="display: <?php if($_error_ == false){ echo('none'); } ?>;" class="warning">
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

                <div style="margin-top: 5px;" class="checkbox-wrapper-4">
                    <input class="inp-cbx" id="morning" type="checkbox" disabled/>
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
                    <label>Obs: teste de obs</label>
                </div>
                </form>
                <?php }?>
            </div>
            <!-- Fim Itens do pedido -->

            

            <!-- Tipo de pagamento -->
            <form action="" method="POST">
            <p style="display: <?php if($noShopCart == true){ echo('none'); }; if($_SUCCESS == true){ echo('none'); } ?>;" class="b-main-bag-title">Tipo de pagamento</p>
            <div style="display: <?php if($noShopCart == true){ echo('none'); }; if($_SUCCESS == true){ echo('none'); } ?>;" class="b-main-bag-container-pagamento">
                <input <?php if($tipo_pagamento == 'DINHEIRO'){ echo "checked";}; ?> type="radio" id="DINHEIRO" name="tipo_pagamento" value="DINHEIRO" disabled>
                <label class="bt-radio" for="DINHEIRO">Dinheiro</label><br><br>

                <input <?php if($tipo_pagamento == 'PIX'){ echo "checked";}; ?> type="radio" id="PIX" name="tipo_pagamento" value="PIX" disabled>
                <label class="bt-radio" for="PIX">Pix</label><br><br>

                <input <?php if($tipo_pagamento == 'CARTAO_CREDITO'){ echo "checked";}; ?> type="radio" id="CARTAO_CREDITO" name="tipo_pagamento" value="CARTAO_CREDITO" disabled>                
                <label class="bt-radio" for="CARTAO_CREDITO">Cartão de credito</label><br><br>

                <input <?php if($tipo_pagamento == 'CARTAO_DEBITO'){ echo "checked";}; ?> type="radio" id="CARTAO_DEBITO" name="tipo_pagamento" value="CARTAO_DEBITO" disabled>
                <label class="bt-radio" for="CARTAO_DEBITO">Cartão de debito</label>
            </div>
            <!-- Fim Tipo de pagamento -->
            <div style="display: <?php if($noShopCart == true){ echo('none'); }; if($_SUCCESS == true){ echo('none'); } ?>;" class="b-main-bag-container-bt-finalizar">
                <button class="print-button" onclick="printInvoice()"><i class='bx bx-printer'></i>Imprimir Página</button>
                <button class="whatsapp-button" onclick="openChatWhatsapp('<?php echo $nun_whatsapp_usuario; ?>')"><i class='bx bxl-whatsapp' ></i>Abrir whatsapp</button>
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
    <!-- Área de texto para inserir a fatura -->
<textarea style="font-size: 12px; font-family: monospace; display: none;" id="invoiceText" wrap="hard">
<?php
// Ajuste estes valores conforme necessário para garantir o alinhamento
$description_length = 32;
$quantity_length = 5;
$total_length = 10;

echo str_pad("PEDIDO CATALOGUEI.SHOP", $description_length + $quantity_length + $total_length, " ") . "\n";
echo str_pad("COD:#".str_pad($id_venda, 8, '0', STR_PAD_LEFT), $description_length + $quantity_length + $total_length, " ") . "\n";

echo str_pad("****************************************************", $description_length + $quantity_length + $total_length, " ") . "\n";
echo str_pad("PRODUTOS", $description_length + $quantity_length + $total_length, " ") . "\n";
echo str_pad("****************************************************", $description_length + $quantity_length + $total_length, " ") . "\n";
$total_pedido = 0;
foreach($venda_detalhe as $row) {
    // soma o total do produtos
    $total_pedido = $total_pedido + $row[3];
    $description = $row[5]; // Descrição do item
    $quantity = "1x"; // Quantidade
    $total = "TOTALR$" . number_format($row[3], 2); // Total formatado

    echo str_pad("Item 1: " . $description, $description_length, " ") . " " .
         str_pad($quantity, $quantity_length, " ", STR_PAD_LEFT) . " " .
         str_pad($total, $total_length, " ", STR_PAD_LEFT) . "\n";
}

echo str_pad("****************************************************", $description_length + $quantity_length + $total_length, " ") . "\n";

echo str_pad("TOTAL PEDIDO R$:".number_format($total_pedido, 2), $description_length + $quantity_length + $total_length, " ") . "\n";
echo str_pad("TIPO PGT:".$tipo_pagamento, $description_length + $quantity_length + $total_length, " ") . "\n";

echo "\n";

echo str_pad("****************************************************", $description_length + $quantity_length + $total_length, " ") . "\n";
echo str_pad("ENDEREÇO  ENTREGA", $description_length + $quantity_length + $total_length, " ") . "\n";
echo str_pad("****************************************************", $description_length + $quantity_length + $total_length, " ") . "\n";

echo str_pad("CEP:".$cep , $description_length + $quantity_length + $total_length, " ") . "\n";
echo str_pad("BAIRRO:".$bairro, $description_length + $quantity_length + $total_length, " ") . "\n";
echo str_pad("RUA:".$rua, $description_length + $quantity_length + $total_length, " ") . "\n";
echo str_pad("COMPLEMENTO:".$complemento, $description_length + $quantity_length + $total_length, " ") . "\n";

echo "\n";

echo str_pad("****************************************************", $description_length + $quantity_length + $total_length, " ") . "\n";
echo str_pad("CLIENTE", $description_length + $quantity_length + $total_length, " ") . "\n";
echo str_pad("****************************************************", $description_length + $quantity_length + $total_length, " ") . "\n";

echo str_pad("NOME:".$nome_usuario, $description_length + $quantity_length + $total_length, " ") . "\n";
echo str_pad("TELEFONE:".$nun_whatsapp_usuario, $description_length + $quantity_length + $total_length, " ") . "\n";

?>
</textarea>
</body>
<script src='../assets/js/main.js'></script>
<script>
        // Função para imprimir a fatura
        function printInvoice() {
            // Captura o conteúdo da área de texto
            var invoiceContent = document.getElementById('invoiceText').value;

            // Cria uma nova janela para imprimir
            var printWindow = window.open('', '', 'height=600,width=800');

            // Escreve o conteúdo da fatura na nova janela
            printWindow.document.write('<html><head><title>Fatura</title>');
            printWindow.document.write('<style>body { font-family: monospace; font-size: 12px; margin: 0px; white-space: pre-wrap; }</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(invoiceContent.replace(/\n/g, '<br>'));
            printWindow.document.write('</body></html>');

            // Fecha o documento e abre a caixa de diálogo de impressão
            printWindow.document.close();
            printWindow.print();
        }
</script>
<?php 
if(isset($_GET['produto'])){
    echo "<script type='text/javascript'>
    produtoDetalhe('".$_GET['produto']."');
    </script>";    
}
?>
</html>