<?php 
    require_once '../../model/settings.php';
    //verifica se o tipo de usuario e empresa

    if($_COOKIE['authorization_type'] != 'company')
    {
        die("error x011 o tipo de usuario não e compativel para acessar essa pagina.<p><a href=\"log-in.php\">Entrar</a></p>");
    }  

    // verifica se tem o id da empresa no cookie
    if(!isset($_COOKIE['authorization_id']))
    {
        die("error x012 não foi encontrado o id da empresa.<p><a href=\"log-in.php\">Entrar</a></p>");
    }      

    $settings = new Settings($_COOKIE['authorization_id']);

    // aplica alterações
    if(isset($_POST['btSave']))
    {
        // verifica checkbox 
        if (isset($_POST['ckNoUpdateImgProduto'])) 
        {
            $settings->set__no_update_imagem_produto('S');
        }else{
            $settings->set__no_update_imagem_produto('N');
        }

        $TaxaEntregaGeral = floatval(str_replace(',', '.', $_POST['TaxaEntregaGeral']));

        $settings->set__taxa_entrega_geral($TaxaEntregaGeral);
        // atualiza os dados na base de dados
        if($settings->update())
        {
           $_success_update = TRUE; 
        }

    }else{
        $_success_update = FALSE;
    }

    // recupera os dados
    $no_update_imagem_produto = $settings->get__no_update_imagem_produto();
    $taxa_entrega_geral       = $settings->get__taxa_entrega_geral();

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
    <link rel='stylesheet' type='text/css' media='screen' href='../../assets/css/main.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../../assets/css/buttons.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../../assets/css/checkbox.css'>
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
        <div class="container-label"><label>Atualização de produtos</label><div> 
        
        <div class="container-input">      
            <div style="margin-top: 5px;" class="checkbox-wrapper-4">
                <input class="inp-cbx" id="ckNoUpdateImgProduto" name="ckNoUpdateImgProduto" type="checkbox" <?php if($no_update_imagem_produto == 'S'){echo('checked');} ?>/>
                <label class="cbx" for="ckNoUpdateImgProduto"><span>
                <svg width="12px" height="10px">
                    <use xlink:href="#check-4"></use>
                </svg></span><span>Não atualizar as imagens pelo cataloguei desktop</span></label>
                <svg class="inline-svg">
                    <symbol id="check-4" viewbox="0 0 12 10">
                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                    </symbol>
                </svg>
            </div>             
        </div> 

        <div class="container-label"><label>Taxa frete geral</label><div> 
        <div class="container-input "> 
            <input style="width: 120px;" class="text-input" type="text" name="TaxaEntregaGeral" id="TaxaEntregaGeral" value="<?php echo($taxa_entrega_geral); ?>" placeholder="ex: 10" required>
        </div> 
                                
        <br><br>        
        <div style="width: 100%; height: 50px;" class="b-main-container-left b-main-centro-total">
            <button style="width: 220px;" class="button-65" type="submit" name="btSave">Salvar produto</button>
        </div>
    </form>
    <?php 
        // verifica se foi atualizado com sucesso os dados e mostra uma mensagem para o usuario
        if (($_success_update == TRUE))
        {
            echo "<script type='text/javascript'>
                window.location.href = '../assets/pages/success.html';
            </script>";
        }         
    ?>
</body>
</html>