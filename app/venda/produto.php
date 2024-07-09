<?php 
include('../req/conex.php');
include('services/produto.sales.php');

# include na nova versão do codigo em class
include('../model/settings.php');
include('../model/utilities.php');

$_error_    = FALSE;
$_error_msg = '';
#verifica se tem a variavel na url loja
if(!isset($_GET['id']))
{
    $_error_ = TRUE;
    die("error não foi passado a variavel id do produto<p><a href=\"log-in.php\">documentação</a></p>");    

}else{$id = $_GET['id'];}


if(!isset($_GET['loja']))
{
    $_error_ = TRUE;
    die("error não foi passado a variavel loja<p><a href=\"log-in.php\">documentação</a></p>");    

}else{$loja = $_GET['loja'];}

#busca o id da empresa
$id_empresa = retornaIdEmpresa($mysqli,$loja);
$settings = new Settings($id_empresa);
#|
#|  Dados produtos 
#|_______________________________________________________________________________________
#|

// busca produto principal
$sql_code = "SELECT * FROM produto WHERE id = ".$id;
$sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
$quantidade = $sql_query->num_rows;
if($quantidade == 1) 
{    
    $produto            = $sql_query->fetch_assoc();
    $id_produto         = $produto['id'];
    $nome               = $produto['nome'];
    $desc               = $produto['descricao'];
    $cod_barras         = $produto['codigo_barras'];
    $preco              = $produto['preco'];
    $preco_normal       = $produto['preco'];
    $estoque            = $produto['estoque'];
    $path_img           = $produto['path_imagem'];
    $id_empresa_produto = $produto['id_empresa'];
    $promocao_produto  = $produto['promocao'];

    $has_promocao = false;
    $diferenca_percentual = 0;
    if($promocao_produto == 'S'){
        $has_promocao = true;
        $preco = $produto['preco_promocional'];
        $diferenca = $preco - $produto['preco'];
        $diferenca_percentual = ($diferenca / $produto['preco']) * 100;
    }
} else {
    $_error_ = TRUE;
    die("error não foi encontrado a pagina<p><a href=\"log-in.php\">documentação</a></p>");
}

// busca variação do produto
$sql_code = "SELECT id, descricao, estoque, preco, status 
             FROM variacao_produto 
             WHERE id_produto = ".$id_produto."
             AND status = 'A'
             AND estoque > 0
             ORDER BY estoque";
$sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
$variacao = $sql_query->fetch_all(MYSQLI_ASSOC);
$TotRecordsVariacao = $sql_query->num_rows;

#|
#|  Fim dos dados de produto
#|_______________________________________________________________________________________
#|




#|
#|  Inicio da ação de adicionar o produto a sacola
#|_______________________________________________________________________________________
#|
if(isset($_POST['btadd'])) 
{

    // verifica se tem quantidade
    $qtd_pedido = $mysqli->real_escape_string($_POST['qtd_pedido']);
    // verifica se o usuarios esta logado
    if(!isset($_COOKIE['authorization_id'])) {
        // redireciona para pagina de log-in
        $link = 'd.php?produto='.$id.'&loja='.$loja;
        header("Location: ../login.php?type=user&redirect=". urlencode($link));
        exit; 
    }
    if(!isset($_COOKIE['authorization_type'])) {
        // redireciona para pagina de log-in
        $link = 'd.php?produto='.$id.'&loja='.$loja;
        header("Location: ../login.php?type=user&redirect=". urlencode($link));
        exit; 
    }
    // verifica se o tipo de usuario logado e user
    if($_COOKIE['authorization_type'] != 'user'){
        $link = 'd.php?produto='.$id.'&loja='.$loja;
        header("Location: ../login.php?type=user&redirect=". urlencode($link));
        exit;        
    }

    // Verifica se tem e foi informado a variação do produto
    if($TotRecordsVariacao > 0)
    {
        if (isset($_POST['produto_variacao'])) 
        {
            $id_produto_variacao = $_POST['produto_variacao'];
            
        } else {
            $_error_msg = 'Nenhuma opção foi selecionada.';
            $_error_ = TRUE;
        }
    } else {
        $id_produto_variacao=0;
    }


    $horarios = $settings->getHorario(obterDiaDaSemana());

    if($horarios[0]['horario_ativo'] == TRUE)
    {
        $result_horarios = verificarHorarioLoja($horarios);
        if($result_horarios['status'] == 'fechado')
        {
            $_error_msg = 'A loja está fechada e não pode receber pedidos';
            $_error_ = TRUE;           
        }
    }  


    if ($_error_ == FALSE){

        // verifica se tem venda em andamento
        $sql_code = "SELECT * FROM venda WHERE usuario_id = ".$_COOKIE['authorization_id']." and status='pending'";
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
        $quantidade = $sql_query->num_rows;
        if($quantidade == 0) {    
            // cria o cabecario da venda
            // Prepara a consulta SQL para inserção dos dados
            $sql = "INSERT INTO venda (data_venda, hora_venda, total, status,usuario_id,empresa_id,endereco_id)  VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "Erro na preparação da consulta: " . $conn->error;
                return -1;
            }
            // Vincula os parâmetros à consulta preparada
            $status = 'pending';
            $id_endereco = 1;
            $total = 0;
            $currentDate = date("Y-m-d");
            $currentTime = date("H:i:s");
            $id_user = $_COOKIE['authorization_id'];
            if(intval($id_empresa) == 0){
                $_error_ = TRUE;
                die("error não foi encontrado o id da empresa<p><a href=\"log-in.php\">documentação</a></p>");   
            }
            $stmt->bind_param("sssssss", $currentDate, $currentTime, $total, $status, $id_user, $id_empresa, $id_endereco);
            // Executa a consulta
            if ($stmt->execute()) {
                $id_venda = $mysqli->insert_id; // Obtém o ID do registro inserido
                            // verifica se tem variação do produto
                if($TotRecordsVariacao > 0)
                {
                    $has_variacao = 'S'; 
                }  else {
                    $has_variacao = 'N';
                }   
                $Produto = array(
                    "id_produto" => $id_produto,
                    "qtd"        => $qtd_pedido,
                    "obs"        => "",
                    "variacao"   => $has_variacao,
                    "id_variacao"=> $id_produto_variacao
                );
                if (addProdutoVenda($mysqli,$Produto,$id_venda)){
                    header("Location: ../assets/pages/success_product_bag.html?loja=".$loja);
                }else{
                    echo "Erro na inserção da venda detalhe: "; 
                }
            } else {
                echo "Erro na inserção de dados: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $venda = $sql_query->fetch_assoc();
            $id_venda = $venda['id'];
            $id_empresa_venda = $venda['empresa_id'];
            // verifica se a venda e da mesma loja 
            if(intval($id_empresa_produto) != intval($id_empresa_venda)){
                //echo "Você já tem um pedido de outra empresa em adamento "; 
                $_error_msg = 'Você já tem um pedido de outra empresa em adamento';
                $_error_ = TRUE;
            }else{
                // verifica se tem variação do produto
                if($TotRecordsVariacao > 0)
                {
                    $has_variacao = 'S'; 
                }  else {
                    $has_variacao = 'N';
                }   
                $Produto = array(
                    "id_produto" => $id_produto,
                    "qtd"        => $qtd_pedido,
                    "obs"        => "",
                    "variacao"   => $has_variacao,
                    "id_variacao"=> $id_produto_variacao
                );
                if (addProdutoVenda($mysqli,$Produto,$id_venda)){
                    header("Location: ../assets/pages/success_product_bag.html?loja=".$loja);
                }else{
                    echo "Erro na inserção da venda detalhe: "; 
                }
            }

        }

    }

}
#|
#|  FIM da ação de adicionar o produto a sacola
#|_______________________________________________________________________________________
#|

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Produto</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/main.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/buttons.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/alerts.css'>
    <!--boxicon-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>    
</head>
<body>

    <!-- alerta -->
    <div style="display: <?php if($_error_ == FALSE){ echo('none'); } ?>; margin-top: -0px;" class="warning-no-margin">
        <img src="../assets/img/alert.png" />
        <p><?php echo($_error_msg); ?></p>
    </div>

    <div class="b-main-p-prodto-display-img b-main-centro-total"><img src="../painel/<?php echo($path_img); ?>"/></div>
    <div class="b-main-p-prodto-display-title"><p><?php echo($nome); ?></p></div> 
    
    <?php 

        // verifica se mostra o preço promocional
        $displayPrecoPromocao = '';
        $displayPreco         = '';
        $displayVariacao      = '';

        if($has_promocao == false)
        {
            $displayPrecoPromocao = 'none'; 
        }

        // Verifica se tem variação de preco se tiver 
        // não mostra o preço promocional
        if($TotRecordsVariacao > 0)
        {
            $displayPrecoPromocao = 'none'; 
            $displayPreco         = 'none';
            $estoque              = 1; // Inportante quando o produto tem variação seta o estoque como 1

        } else {
            $displayVariacao      = 'none';
        }
        
        

    ?>
    
    <div style="display: <?php echo($displayPrecoPromocao);  ?>;" class="b-main-p-prodto-display-promo">
        <label>R$ <?php echo number_format($preco_normal,2,",","."); ?></label>
        <div class="taxa-promo">
            <i class='bx bx-down-arrow-alt'></i>
            <p><?php echo round($diferenca_percentual); ?>%</p>
        </div>
    </div>
    <div style="display: <?php echo($displayPreco);  ?>;" class="b-main-p-prodto-display-preco"><p>R$ <?php echo number_format($preco,2,",","."); ?></p></div>


    <div class="b-main-p-prodto-display-desc"><label><?php echo($desc); ?></label></div>


    <form action="#" method="POST">

        <!--
            Verifica se tem variação de preço
        -->
        <div style="display: <?php echo($displayVariacao);  ?>;" class="b-main-p-prodto-display-variacao">
            <p>Selecione a variação</p><br>
            <?php foreach($variacao as $row){?>
            <div style="width: 100%; height: 25px;">    
                <input type="radio" id="variacao_<?php echo $row['id']; ?>" name="produto_variacao" value="<?php echo $row['id']; ?>">
                <label for="variacao_<?php echo $row['id']; ?>"><?php echo $row['descricao']; ?></label>
                <b>   R$ <?php echo number_format($row['preco'],2,",","."); ?></b>
            </div>
            <?php } ?>
        </div>

        <!--
            Fim da variação de preço
        --> 

        <div style="width: 100%; height: 75px;"></div>
        <div class="b-main-container-footer" style="height: 70px; padding-top: 10px;">
                <div class="b-main-container-qtd-produto-venda">
                    <button class="bt-menos" type="button"><i class='bx bx-minus'></i></button>
                    <input type="text" class="input-qtd" name="qtd_pedido" value="1"/>
                    <button class="bt-mais" type="button"><i class='bx bx-plus' ></i></button>
                </div>
                <button style="width: 180px; position: relative; float: right;" class="button-65" name="btadd" type="submit">Adicionar a sacola</button>
        </div>

    </form>
</body>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnMais = document.querySelector('.bt-mais');
            const btnMenos = document.querySelector('.bt-menos');
            const inputQtd = document.querySelector('.input-qtd');
            const maxStock = <?php echo $estoque; ?>; // Replace with actual stock quantity

            btnMais.addEventListener('click', () => {
                let currentValue = parseInt(inputQtd.value);
                if (currentValue < maxStock) {
                    inputQtd.value = currentValue + 1;
                }
            });

            btnMenos.addEventListener('click', () => {
                let currentValue = parseInt(inputQtd.value);
                if (currentValue > 1) {
                    inputQtd.value = currentValue - 1;
                }
            });
        });
    </script>
</html>