<?php 
require_once '../req/conex.php';
require_once '../req/protect.php';

$_SUCCESS = false;
$_error_ = false;
$_error_msg_ = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {    
    if($_error_ == false){
        $insertImg = True;
        if($insertImg == True){
            // Pasta onde o arquivo vai ser salvo
            $_UP['pasta'] = 'uploads/';
                    
            // Tamanho máximo do arquivo (em Bytes)
            $_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb

            // Array com as extensões permitidas
            $_UP['extensoes'] = array('jpg', 'png', 'gif', 'PNG', 'JPG', 'GIF');

            // Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
            $_UP['renomeia'] = true;

            // Array com os tipos de erros de upload do PHP
            $_UP['erros'][0] = 'Não houve erro';
            $_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
            $_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
            $_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
            $_UP['erros'][4] = 'Não foi feito o upload do arquivo';

            // Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
            if ($_FILES['arquivo']['error'] != 0) {
            die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['arquivo']['error']]);
            exit; // Para a execução do script
            }

            // Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar

            // Faz a verificação da extensão do arquivo
            $extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
            if (array_search($extensao, $_UP['extensoes']) === false) {
            echo "Por favor, envie arquivos com as seguintes extensões: jpg, png ou gif";
            }

            // Faz a verificação do tamanho do arquivo
            else if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
            echo "O arquivo enviado é muito grande, envie arquivos de até 2Mb.";
            }

            // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
            else {
            // Primeiro verifica se deve trocar o nome do arquivo
            if ($_UP['renomeia'] == true) {
                // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
                $nome_final = time().'.jpg';
            } else {
                // Mantém o nome original do arquivo
                $nome_final = $_FILES['arquivo']['name'];
            }

            // Depois verifica se é possível mover o arquivo para a pasta escolhida
            if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
                // Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
                //echo "Upload efetuado com sucesso!";
                //echo '<br /><a href="' . $_UP['pasta'] . $nome_final . '">Clique aqui para acessar o arquivo</a>';
                } else {
                // Não foi possível fazer o upload, provavelmente a pasta está incorreta
                echo "Não foi possível enviar o arquivo, tente novamente";
                $_error_ = true;
                }

            }

            // Caminho para a imagem original
            $imagem_original = $_UP['pasta'] . $nome_final;

            // Novas dimensões desejadas
            $nova_largura = 150; // Largura desejada em pixels
            $nova_altura = 150; // Altura desejada em pixels

            // Criando uma imagem em branco com as novas dimensões
            $nova_imagem = imagecreatetruecolor($nova_largura, $nova_altura);
            $cor_transparente = imagecolorallocatealpha($nova_imagem, 0, 0, 0, 127); // Cor transparente
            imagefill($nova_imagem, 0, 0, $cor_transparente);
            imagesavealpha($nova_imagem, true); // Salvar transparência

            // Carregando a imagem original
            $imagem_original = imagecreatefromjpeg($imagem_original);

            // Redimensionando a imagem original para a nova imagem
            imagecopyresampled($nova_imagem, $imagem_original, 0, 0, 0, 0, $nova_largura, $nova_altura, imagesx($imagem_original), imagesy($imagem_original));

            // Calculando as coordenadas para o corte no centro da imagem
            $corte_x = ($nova_largura - 150) / 2;
            $corte_y = ($nova_altura - 150) / 2;

            // Criando uma nova imagem para o corte com fundo transparente
            $nova_imagem_cortada = imagecreatetruecolor(150, 150);
            imagefill($nova_imagem_cortada, 0, 0, $cor_transparente);

            // Cortando a imagem no centro
            imagecopy($nova_imagem_cortada, $nova_imagem, 0, 0, $corte_x, $corte_y, 150, 150);

            // Salvar a nova imagem cortada como PNG (substituir a imagem original se desejar)
            $nome_img_crop = time();
            $nome_img_crop = md5($nome_img_crop);
            $nome_img_crop = $nome_img_crop.'.png';
            imagepng($nova_imagem_cortada, $_UP['pasta'] . $nome_img_crop);

            // Liberar memória
            imagedestroy($nova_imagem);
            imagedestroy($nova_imagem_cortada);
            imagedestroy($imagem_original);

            // delata o arquivo antigo
            if(file_exists( $_UP['pasta'] . $nome_final )){
                unlink($_UP['pasta'] . $nome_final);
            }
        }    
        
        // grava o registro na base de dados
        if ($_error_ == false){    
            $id_empresa = $_COOKIE['authorization_id'];  
            $path_imagem = $_UP['pasta'] . $nome_img_crop;  
            $sql = "UPDATE empresa SET path_logo=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "Erro na preparação da consulta: " . $conn->error;
                return -1;
            }
            $stmt->bind_param("ss",  $path_imagem, $id_empresa);
            // Executa a consulta de atualização
            if ($stmt->execute()) {
                //echo "Dados atualizados com sucesso!";
                $_SUCCESS = true;
            } else {
                echo "Erro na atualização de dados: " . $stmt->error;
            }
            $stmt->close();      
        }


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
    <form action="logo.php" method="POST" enctype="multipart/form-data"> 
        <div style="width: 100%; height: 30px;" class="b-main-container-left">
            <label class="roboto-light-2">⚠️ O formato da imagem dever ser quadrada de no máximo 300x300pixel</label>
        </div>
        <br><br>
        <div class="container-label"><label>Logo da empresa</label><div> 
        <div style="height: 30px;" class="container-input">      
            <input type="file" name="arquivo" accept="image/jpeg,image/jpg" required/>
        </div>      
        <br><br>     
        <br><br>   
        <div style="width: 100%; height: 50px; display: <?php if($_SUCCESS == true){ echo "none"; }; ?>;" class="b-main-container-left">
            <button style="width: 220px;" class="button-65" type="submit">Salvar logo</button>
        </div>
    </form>
    <br><br>
    <?php 
        if($_SUCCESS == true){
            echo('<label class="roboto-light">✅ Sua logo foi salva com sucesso</label>');
            echo('
                <div style="width: 100%; height: 30px;" class="b-main-container-left"> 
                <button style="width: 220px;" class="button-65" type="button" onclick="finish()">OK</button>
                </div>
            ');
        }
        if($_error_ == true){
            echo('<label class="roboto-light">⚠️ '.$_error_msg_.'</label>');
        }              
    ?>
</body>
<script>
    function finish(){
        window.parent.location.href = 'vendas.php';
    }
</script>
<script src='../assets/js/main.js'></script>
</html>