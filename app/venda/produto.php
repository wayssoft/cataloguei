<?php 
include('../req/conex.php');
#verifica se tem a variavel na url loja
if(!isset($_GET['id'])){
    $_error_ = True;
    die("error não foi passado a variavel id do produto<p><a href=\"log-in.php\">documentação</a></p>");    
}else{$id = $_GET['id'];}
if(!isset($_GET['loja'])){
    $_error_ = True;
    die("error não foi passado a variavel loja<p><a href=\"log-in.php\">documentação</a></p>");    
}else{$loja = $_GET['loja'];}
$sql_code = "SELECT * FROM produto WHERE id = ".$id;
$sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
$quantidade = $sql_query->num_rows;
if($quantidade == 1) {    
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
    $_error_ = True;
    die("error não foi encontrado a pagina<p><a href=\"log-in.php\">documentação</a></p>");
}

function retornaIdEmpresa($mysqli,$value): int {
    $sql_code = "SELECT * FROM empresa WHERE dominio = '".$value."'";
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) { 
        $empresa = $sql_query->fetch_assoc();
        $_id = $empresa['id'];
    }else{$_id = 0;};
    return $_id;
} 

function atualizaTotalVenda($mysqli,$id_venda,$valor_add): bool {
    // busca o valor atual da venda
    $sql_code = "SELECT * FROM venda WHERE id = ".$id_venda;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) { 
        $venda = $sql_query->fetch_assoc();
        $_total = $venda['total'];
    }else{$_total = 0;};
    $_total = $_total + $valor_add;
    $sql = "UPDATE venda SET total = ? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        //echo "Erro na preparação da consulta: " . $conn->error;
        return false;
    }
    $stmt->bind_param("ss", $_total, $id_venda);
    // Executa a consulta de atualização
    if ($stmt->execute()) {
        //echo "Dados atualizados com sucesso!";
        return true;
    } else {
        //echo "Erro na atualização de dados: " . $stmt->error;
        return false;
    }
    $stmt->close();
} 

function addProdutoVenda($mysqli,$id_produto, $qtd, $obs, $id_venda): bool {
    // busca o produto
    $sql_code = "SELECT * FROM produto WHERE id = ".$id_produto;
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 1) { 
        $produto = $sql_query->fetch_assoc();
        $_preco = $produto['preco'];
        $_promocao = $produto['promocao'];
        // verifica se tem promoção
        if($_promocao == 'S'){$_preco = $produto['preco_promocional'];};
    }else{$_preco = 0;};

    // Prepara a consulta SQL para inserção dos dados
    $sql = "INSERT INTO venda_detalhe (qtd, valor_un, valor_total, status,obs,venda_id,produto_id)  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        //echo "Erro na preparação da consulta: " . $conn->error;
        return false;
    }
    // Vincula os parâmetros à consulta preparada
    $status = 'added';
    $total = $_preco * $qtd;
    $stmt->bind_param("sssssss", $qtd, $_preco, $total, $status, $obs, $id_venda, $id_produto);
    // Executa a consulta
    if ($stmt->execute()) {
        $id_venda_detalhe = $mysqli->insert_id; // Obtém o ID do registro inserido
        // atualiza o total venda
        if(atualizaTotalVenda($mysqli,$id_venda,$_preco)){
            return true;
        }        
    } else {
        echo "Erro na inserção de dados: " . $stmt->error;
    }
    $stmt->close();
}

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
    <!--boxicon-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>    
</head>
<body>
    <div class="b-main-p-prodto-display-img b-main-centro-total"><img src="../painel/<?php echo($path_img); ?>"/></div>
    <div class="b-main-p-prodto-display-title"><p><?php echo($nome); ?></p></div>
    <div style="display: <?php if($has_promocao == false){echo("none");}; ?>;" class="b-main-p-prodto-display-promo">
        <label>R$ <?php echo number_format($preco_normal,2,",","."); ?></label>
        <div class="taxa-promo">
            <i class='bx bx-down-arrow-alt'></i>
            <p><?php echo round($diferenca_percentual); ?>%</p>
        </div>
    </div>
    <div class="b-main-p-prodto-display-preco"><p>R$ <?php echo number_format($preco,2,",","."); ?></p></div>
    <div class="b-main-p-prodto-display-desc"><label><?php echo($desc); ?></label></div>
    <div class="b-main-container-footer" style="height: 70px; padding-top: 10px;">
        <form action="" method="POST">
            <div class="b-main-container-qtd-produto-venda">
                <button class="bt-menos" type="button"><i class='bx bx-minus'></i></button>
                <input type="text" class="input-qtd" name="qtd_pedido" value="1"/>
                <button class="bt-mais" type="button"><i class='bx bx-plus' ></i></button>
            </div>
            <button style="width: 180px; position: relative; float: right;" class="button-65" name="btadd" type="submit">Adicionar a sacola</button>
        </form>
    </div>
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
<?php 
if(isset($_POST['btadd'])) {
    // verifica se tem quantidade
    $qtd_pedido = $mysqli->real_escape_string($_POST['qtd_pedido']);
    // verifica se o usuarios esta logado
    if(!isset($_COOKIE['authorization_id'])) {
        // redireciona para pagina de log-in
        $link = 'd.php?produto='.$id.'&loja='.$loja;
        header("Location: ../login.php?type=user&redirect=". urlencode($link));
    }
    if(!isset($_COOKIE['authorization_type'])) {
        // redireciona para pagina de log-in
        $link = 'd.php?produto='.$id.'&loja='.$loja;
        header("Location: ../login.php?type=user&redirect=". urlencode($link));
    }
    // verifica se o tipo de usuario logado e user
    if($_COOKIE['authorization_type'] != 'user'){
        $link = 'd.php?produto='.$id.'&loja='.$loja;
        header("Location: ../login.php?type=user&redirect=". urlencode($link));
        exit;
    }

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
        $id_empresa = retornaIdEmpresa($mysqli,$loja);
        if(intval($id_empresa) == 0){
            $_error_ = True;
            die("error não foi encontrado o id da empresa<p><a href=\"log-in.php\">documentação</a></p>");   
        }
        $stmt->bind_param("sssssss", $currentDate, $currentTime, $total, $status, $id_user, $id_empresa, $id_endereco);
        // Executa a consulta
        if ($stmt->execute()) {
            $id_venda = $mysqli->insert_id; // Obtém o ID do registro inserido
            if (addProdutoVenda($mysqli,$id_produto,$qtd_pedido,'obs',$id_venda)){
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
            echo "Você já tem um pedido de outra empresa em adamento "; 
        }else{
            if (addProdutoVenda($mysqli,$id_produto,$qtd_pedido,'obs',$id_venda)){
                header("Location: ../assets/pages/success_product_bag.html?loja=".$loja);
            }else{
                echo "Erro na inserção da venda detalhe: "; 
            }
        }

    }
}
?>