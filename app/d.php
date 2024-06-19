<?php 
include('req/conex.php');
#verifica se tem a variavel na url loja
$_error_ == False;
if(!isset($_GET['loja'])){
    $_error_ = True;
    die("error não foi passado a variavel loja<p><a href=\"log-in.php\">documentação</a></p>");    
}else{$loja = $_GET['loja'];}
$sql_code = "SELECT * FROM empresa WHERE upper(dominio) = '".strtoupper($loja)."'";
$sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
$quantidade = $sql_query->num_rows;
if($quantidade == 1) {    
    $empresa = $sql_query->fetch_assoc();
    $id_empresa = $empresa['id'];
    $path_logo  = $empresa['path_logo'];
    $nome_loja = $empresa['nome'];
    $url_loja  = 'https://app.cataloguei.shop/d?loja='.$empresa['dominio'];
    if($path_logo != ''){
        $path_logo = "painel/".$path_logo;
        if(!file_exists( $path_logo )){ $hasLogo = false; }else{ $hasLogo = true; }
    }else{$hasLogo = false;};
} else {
    $_error_ = True;
    die("error não foi encontrado a pagina<p><a href=\"log-in.php\">documentação</a></p>");
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

// verifica os produtos
if ($_error_ == False){
    
    // Definindo o número de registros por página
    $registros_por_pagina = 10;

    // Obtendo o número da página atual
    $pagina_atual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
    $offset = ($pagina_atual - 1) * $registros_por_pagina;

    // Consulta para obter o total de registros
    $sql_total = "SELECT COUNT(*) AS total FROM produto WHERE estoque > 0 AND id_empresa = ".$id_empresa;
    $result_total = $mysqli->query($sql_total) or die("Falha na execução do código SQL: " . $mysqli->error);
    $total_registros = $result_total->fetch_assoc()['total'];

    if(isset($_POST['search'])){
        $search = $_POST['search'];
        $search = strtoupper($search);
        $sql_code = "SELECT id,path_imagem,codigo_barras,nome,descricao,preco,estoque,promocao,preco_promocional FROM produto WHERE id_empresa = ".$id_empresa." AND estoque > 0 AND upper(nome) LIKE '%".$search."%'";
        $sql_code = $sql_code . " LIMIT $registros_por_pagina OFFSET $offset";
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
        $quantidade = $sql_query->num_rows;
        $produtos = $sql_query->fetch_all();
    }else{
        $sql_code = "SELECT id,path_imagem,codigo_barras,nome,descricao,preco,estoque,promocao,preco_promocional FROM produto WHERE estoque > 0 AND id_empresa = ".$id_empresa;
        $sql_code = $sql_code . " LIMIT $registros_por_pagina OFFSET $offset";
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
        $quantidade = $sql_query->num_rows;
        $produtos = $sql_query->fetch_all();
        $search ='';
    }  
    // Calculando o número total de páginas
    $total_paginas = ceil($total_registros / $registros_por_pagina);    
    
}
//Verifica se ja tem uma venda em andamento
if(isset($_COOKIE['authorization_id'])){
    $sql_code = "SELECT * FROM venda WHERE usuario_id = ".$_COOKIE['authorization_id']." and status='pending'";
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade > 0) {    
        $venda = $sql_query->fetch_assoc();
        $id_venda = $venda['id'];
        // filtra os itens
        $sql_code = "SELECT * FROM venda_detalhe WHERE venda_id=".$id_venda." and status = 'added'";
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
        $qtd_itens_venda = $sql_query->num_rows; 
    }else{
        $qtd_itens_venda = 0;
    }
}else{
    $qtd_itens_venda = 0;
}

// Links para pop-menu
$link = 'd.php?loja='.$loja;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title><?php echo $nome_loja; ?></title>
    <!-- preview -->
    <meta property="og:title" content="<?php echo $nome_loja; ?>" />
    <meta property="og:type" content="Catálogo de produtos" />
    <meta property="og:url" content="<?php echo  $url_loja; ?>" />
    <meta property="og:image" content="https://app.cataloguei.shop/<?php echo $path_logo; ?>" />
    <meta property="og:image:width" content="150" />
    <meta property="og:image:height" content="150" />
    <meta property="og:locale" content="pt_BR" />
    <!-- FIM preview -->
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='assets/css/main.css?v=1.0'>
    <link rel='stylesheet' type='text/css' media='screen' href='assets/css/alerts.css?v=1.0'>
    <link rel='stylesheet' type='text/css' media='screen' href='assets/css/buttons.css?v=1.0'>
    <link rel='stylesheet' type='text/css' media='screen' href='assets/css/menu.css?v=1.0'>
    <!--boxicon-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!--sweetalert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src='assets/js/alerts.js'></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <!-- -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>
<body>
    <div class="b-main-container-topo b-main-shadow-topo-home">
        <div style="display: <?php if($hasLogo==true){echo("none");}; ?>;" class="logo b-main-centro-total"><img src="assets/img/cataloguei.shop.logo.png" /></div>
        <div style="display: <?php if($hasLogo==false){echo("none");}; ?>;" class="logo b-main-centro-total"><img class="has-logo" src="<?php echo($path_logo) ?>" /></div>
        <div class="item-menu b-main-centro-total"><a href="">Inicio</a></div>
        <div class="item-menu b-main-centro-total"><a href="">Explorar</a></div>
        <div class="item-menu b-main-centro-total"><a href="#" onclick="publicar()">Publicar</a></div>
        <div class="search b-main-centro-total">
            <div class="display">
                <div class="icon b-main-centro-total"><i class='bx bx-search'></i></div>
                <form action="" method="POST">
                    <input class="text-input-search" type="text" name="search" value="<?php echo($search); ?>" placeholder="ex: Tubinhos Morango Fini" required>
                </form>
                <div onclick="close_search('<?php echo $loja; ?>')" style="display: <?php if(intval(Strlen($search) == 0)){ echo('none'); } ?>;" class="close-search b-main-centro-total"><i class='bx bx-x'></i></div>
            </div>
        </div> 
        <!-- menu-->  
        <div class="settings  b-main-centro-total">
            <div class="table_center">
                <div class="drop-down">
                    <div id="dropDown" class="drop-down__button">
                        <span class="drop-down__name"><i class='bx bxs-down-arrow'></i></span>
                    </div>
                    <div class="drop-down__menu-box">
                        <ul class="drop-down__menu">
                            <li onclick="entrar_usuario_menu('<?php echo('login.php?type=user&redirect='. urlencode($link)); ?>')" data-name="entrar" class="drop-down__item">
                                Entrar
                            </li>
                            <li onclick="criar_usuario_menu('<?php echo('register/user.php?redirect='. urlencode($link)); ?>')" data-name="criar_conta" class="drop-down__item">
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
        <div style="display: <?php if($login_user == true){echo("none");}; ?>;" class="img-user b-main-centro-total"><img src="assets/img/img.perfil.jpg" /></div>
        <div style="display: <?php if($login_user == false){echo("none");}; ?>;" class="img-user b-main-centro-total"><div class="avatar-user b-main-centro-total"><p><?php echo $primeira_letra_nome_usuario; ?></p></div></div>
        <div class="cart b-main-centro-total"><i onclick="openBagShop('<?php echo $loja; ?>')" class='bx bx-shopping-bag'><div style="display: <?php if(intval($qtd_itens_venda == 0)){ echo('none'); } ?>;" class="notification-cart-bag b-main-centro-total"><?php echo($qtd_itens_venda); ?></div></i></div>
    </div>
    <!-- Barras pesquisa mobile -->
    <div class="b-main-search-mobile b-main-shadow-topo">
        <div class="search b-main-centro-total">
            <div class="display">
                <div class="icon b-main-centro-total"><i class='bx bx-search'></i></div>
                <form action="" method="POST">
                    <input class="text-input-search" type="text" name="search" value="<?php echo($search); ?>" placeholder="ex: Tubinhos Morango Fini" required>
                </form>
                <div onclick="close_search('<?php echo $loja; ?>')" style="display: <?php if(intval(Strlen($search) == 0)){ echo('none'); } ?>;" class="close-search b-main-centro-total"><i class='bx bx-x'></i></div>
            </div>
        </div>         
    </div>
    <!-- FIM Barras pesquisa mobile -->
    <div class="b-main-separador"></div>
    <div class="b-main-container-produtos b-main-centro-total">
        <div class="display">
            <?php foreach($produtos as $row){
                $path_img_produto = $row[1];
                $path_img_produto = "painel/".$path_img_produto;
                if(!file_exists( $path_img_produto )){$path_img_produto="assets/img/produto-sem-imagem.png";}
                // calcula se tem preço promocional
                $has_promocao = false;
                $diferenca_percentual = 0;
                if($row[7] == 'S'){
                    $has_promocao = true;
                    $preco = $row[8];
                    $diferenca = $row[8] - $row[5];
                    $diferenca_percentual = ($diferenca / $row[5]) * 100;
                }else{$preco = $row[5];};    
            ?>
            <div class="b-main-produto b-main-shadow-produtos">
                <div class="-c-img b-main-centro-total"><img src="<?php echo $path_img_produto; ?>"/></div>
                <div class="-c-desc"><p><?php echo $row[3]; ?></p></div>
                <div class="-c-avaliacao">
                    <div class="estrelas">
                        <input type="radio" id="cm_star-empty-<?php echo $row[0]; ?>" name="fb" value="" checked/>
                        <label><i class="fa fa-2"></i></label>
                        <label><i class="fa fa-2"></i></label>
                        <label><i class="fa fa-2"></i></label>
                        <label><i class="fa fa-2"></i></label>
                        <label><i class="fa fa-2"></i></label>

                    </div>                    
                </div>
                <div class="-c-promo">
                    <label style="display: <?php if($has_promocao == false){echo("none");}; ?>;"><?php echo "R$ " . number_format($row[5],2,",","."); ?></label>
                    <div style="display: <?php if($has_promocao == false){echo("none");}; ?>;" class="taxa-desconto">
                        <i class='bx bx-down-arrow-alt'></i>
                        <p><?php echo round($diferenca_percentual); ?>%</p>
                    </div>
                </div>
                <div class="-c-preco"><p>R$ <?php echo number_format($preco,2,",","."); ?></p></div>
                <div class="-c-bt-add-cart">
                    <div class="bt-add-produto-cart b-main-centro-total"><a href="#" onclick="produtoDetalhe('<?php echo $row[0]; ?>','<?php echo($loja); ?>')"><i class='bx bx-plus'></i><label>Adicionar</label></a></div>
                </div>
            </div>
            <?php } ?>

            <div class="c-paginacao">
                <div class="total-produtos b-main-centro-total"><i class='bx bx-package' ></i><p><?php echo $total_registros; ?></p></div>
                <div class="pag">
                        <!-- paginação dos dados -->

                            <?php if ($pagina_atual > 1): ?>
                                <div class="pagination-previous b-main-centro-total"><a href="?loja=<?php echo $loja ?>&pagina=<?php echo $pagina_atual - 1; ?>"><i class='bx bx-chevron-left' ></i></a></div>
                            <?php endif; ?>
                            
                            <div style="display: <?php if($total_registros <= 10){echo "none";} ?>;" class="pagination-number b-main-centro-total">
                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <a href="?loja=<?php echo $loja ?>&pagina=<?php echo $i; ?>" <?php if ($pagina_atual == $i) echo 'style="font-weight: bold;"'; ?>><?php echo $i; ?></a>
                            <?php endfor; ?>
                            </div>

                            <?php if ($pagina_atual < $total_paginas): ?>
                                <div  class="pagination-next b-main-centro-total"><a href="?loja=<?php echo $loja ?>&pagina=<?php echo $pagina_atual + 1; ?>"><i class='bx bx-chevron-right'></i></a></div>
                            <?php endif; ?>

                        <!-- Fim da paginação -->                    
                </div>
            </div> 

        </div>        
    </div> 
    <!-- mostra totalbar -->
    <div class="b-main-total-bar">
        <div class="container-total">
            <p>Total: R$ 120,20</p>                    
        </div>
    </div>  
</body>
<script src='assets/js/main.js'></script>
<?php 
if(isset($_GET['produto'])){
    echo "<script type='text/javascript'>
    produtoDetalhe('".$_GET['produto']."','".$loja."');
    </script>";    
}
?>
</html>