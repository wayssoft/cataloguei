<?php 
require_once '../req/conex.php';

$_SUCCESS = false;
$_error_ = False;
$_error_msg_ = '';

# zera as variaveis
$produto_grupo='';

#verifica se tem a variavel na url id do grupo
if(!isset($_GET['id_produto_grupo'])){
    $_error_ = True;
    die("error não foi passado a variavel id do grupo.<p><a href=\"log-in.php\">documentação</a></p>");    
}else{$id_produto_grupo = $_GET['id_produto_grupo'];}

# verifica se tem o ID da empresa
if(!isset($_COOKIE['authorization_id'])){
    $_error_ = True;
    die("error não foi passado a variavel id da empresa.<p><a href=\"log-in.php\">documentação</a></p>");
}else{$id_empresa  = $_COOKIE['authorization_id'];}

# se for para editar busca o produto
if(intval($id_produto_grupo) > 0){
    $sql_code = "SELECT * FROM produto_categoria WHERE id=".$id_produto_grupo;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) {
        $produto_grupo = $sql_query->fetch_assoc();
        $grupo_descricao       = $produto_grupo['descricao'];
        $path_imagem_atual     = $produto_grupo['icon'];        
    } else {
        $_error_ = True;
        $_error_msg_ = 'Falha ao logar! E-mail ou senha incorretos';
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    
    
            //verifica se vai inserir imagem
        if(intval($id_produto_grupo) == 0){$insertImg = True;};
        if(intval($id_produto_grupo) > 0){
            if ($_FILES['arquivo']['error'] == 4) {$insertImg = False;}
            else{$insertImg = True;}
        };
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
                    $_error_ = True;
                    }

                }

                // Caminho para a imagem original
                $imagem_original = $_UP['pasta'] . $nome_final;


                // Novas dimensões desejadas
                $nova_largura = 80; // Largura desejada em pixels
                $nova_altura = 70; // Altura desejada em pixels

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
                $corte_x = ($nova_largura - 80) / 2;
                $corte_y = ($nova_altura - 70) / 2;

                // Criando uma nova imagem para o corte com fundo transparente
                $nova_imagem_cortada = imagecreatetruecolor(80, 70);
                imagefill($nova_imagem_cortada, 0, 0, $cor_transparente);

                // Cortando a imagem no centro
                imagecopy($nova_imagem_cortada, $nova_imagem, 0, 0, $corte_x, $corte_y, 80, 70);

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
    
            $grupo_descricao = $mysqli->real_escape_string($_POST['descricao']);;
            $path_imagem = $_UP['pasta'] . $nome_img_crop;  

            // verifica se vai ser um novo produto ou editar um produto
            if((intval($id_produto_grupo) == 0) 
            and ($_error_ != True))
            {
                // Prepara a consulta SQL para inserção dos dados
                $sql = "INSERT INTO produto_categoria (id_empresa, icon, descricao)  VALUES (?, ?, ?)";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                echo "Erro na preparação da consulta: " . $conn->error;
                return -1;
                }
                // Vincula os parâmetros à consulta preparada
                $stmt->bind_param("sss", $id_empresa, $path_imagem, $grupo_descricao);
                // Executa a consulta
                if ($stmt->execute()) {
                    $id_produto_return = $mysqli->insert_id; // Obtém o ID do registro inserido
                    $_SUCCESS = True;
                } else {
                    echo "Erro na inserção de dados: " . $stmt->error;
                }
                $stmt->close();
            }  


            if((intval($id_produto_grupo) > 0) 
            and ($_error_ != True))
            {

                if($insertImg != True)
                {
                    $path_imagem = $path_imagem_atual;
                }else{
                    // remove a imagem antiga 
                    if(file_exists( $path_imagem_atual )){
                        unlink($path_imagem_atual);
                    }                    
                }

                $sql = "UPDATE produto_categoria SET icon=?,descricao=? WHERE id = ?";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    echo "Erro na preparação da consulta: " . $conn->error;
                    return -1;
                }
                $stmt->bind_param("sss", $path_imagem, $grupo_descricao, $id_produto_grupo);
                // Executa a consulta de atualização
                if ($stmt->execute()) {
                    //echo "Dados atualizados com sucesso!";
                    $_SUCCESS = True;
                } else {
                    echo "Erro na atualização de dados: " . $stmt->error;
                }
                $stmt->close();  

                            
            }

}
if($_error_ == True){$show_alert = 'True';}else{$show_alert = 'False';}
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
        <div style="width: 100%; height: 30px;" class="b-main-container-left">
            <label class="roboto-light">Grupo de produtos</label>
        </div>
        <br><br> 
        <div class="container-label"><label>Dimensão da imagem deve ser de 80x70 na extensão JPG</label><div>   
        <div class="container-label"><label>Icon do grupo</label><div> 
        <div style="height: 30px;" class="container-input">      
            <input type="file" accept="image/jpeg,image/jpg" name="arquivo" <?php if(intval($id_produto_grupo) == 0){echo('required');}; ?>/>
        </div>                  
        <div class="container-label"><label>Descrição do grupo</label><div> 
        <div class="container-input "> 
            <input style="width: 100%" class="text-input" type="text" name="descricao" id="descricao" value="<?php echo $grupo_descricao; ?>" placeholder="Bebidas" required>
        </div>                           
        <br><br>        
        <div class="b-main-container-footer b-main-centro-total" style="height: 70px; padding-top: 10px;">
            <button style="width: 220px;" class="button-65" type="submit">Salvar grupo</button>
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
                window.location.href = '../assets/pages/success.html';
            </script>";
        }         
    ?>
</body>
    <script src='../assets/js/main.js'></script>
</html>