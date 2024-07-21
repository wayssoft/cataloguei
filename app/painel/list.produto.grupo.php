<?php 
include('../req/protect.php');
//verifica se o tipo de usuario e empresa
if($_COOKIE['authorization_type'] != 'company'){
  die("error x011 o tipo de usuario não e compativel para acessar essa pagina.<p><a href=\"log-in.php\">Entrar</a></p>");
}
// verifica se tem o id da empresa
if(!isset($_COOKIE['authorization_id'])){
  die("error x011 o tipo de usuario não e compativel para acessar essa pagina.<p><a href=\"log-in.php\">Entrar</a></p>");
}

// busca dados da empresa
$sql_code = "SELECT * FROM empresa WHERE id=".$_COOKIE['authorization_id'];
$sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
$quantidade = $sql_query->num_rows;
if($quantidade == 1) {
    $empresa   = $sql_query->fetch_assoc();
    $path_logo = $empresa['path_logo']; 
    $dominio = $empresa['dominio']; 
    if(!file_exists( $path_logo )){
      $path_logo = '../assets/img/img.perfil.jpg';
    } 
} else {
    die("error x011 o tipo de usuario não e compativel para acessar essa pagina.<p><a href=\"log-in.php\">Entrar</a></p>");
}


// Definindo o número de registros por página
$registros_por_pagina = 50;

// Obtendo o número da página atual
$pagina_atual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Consulta para obter o total de registros
$sql_total = "SELECT COUNT(*) AS total FROM produto_categoria WHERE id_empresa = ".$_COOKIE['authorization_id'];
$result_total = $mysqli->query($sql_total) or die("Falha na execução do código SQL: " . $mysqli->error);
$total_registros = $result_total->fetch_assoc()['total'];

// Consulta para obter os registros com limite e offset
$sql_code = "SELECT id, descricao, icon 
             FROM produto_categoria 
             WHERE id_empresa = ".$_COOKIE['authorization_id']."
             LIMIT $registros_por_pagina OFFSET $offset";
$sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
$clientes = $sql_query->fetch_all(MYSQLI_ASSOC);

// Calculando o número total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);
//verifica esta navegando do mobile
$isMobile = true;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Vendas</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/main.css?v=1.2'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/alerts.css?v=1.1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/buttons.css?v=1.1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/table.css?v=1.1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/menu.css?v=1.1'>
    <!--boxicon-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!--sweetalert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!--table -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/rwd-table/dist/css/rwd-table.css?v=1.1'>
    <script src='../assets/rwd-table/dist/js/rwd-table.js'></script>
</head>
<body>
    <div class="b-main-container-topo b-main-shadow-topo">
        <div class="logo b-main-centro-total"><img src="../assets/img/cataloguei.shop.logo.png" /></div>
        <div class="item-menu b-main-centro-total b-main-active-mobile-menu-item"><a href="list.pedidos.php">Pedidos</a></div>
        <div class="item-menu b-main-centro-total b-main-active-mobile-menu-item"><a href="list.produto.php">Produtos</a></div>
        <div class="item-menu b-main-centro-total b-main-active-mobile-menu-item"><a href="list.clientes.php" >Clientes</a></div>
        <div class="link-catalago">
            <p>Link do catalago</p><input type="text" class="link" value="https://app.cataloguei.shop/d?loja=<?php echo($dominio); ?>" disabled />
            <div class="container-bt b-main-centro-total"><i class='bx bx-copy'></i></div>
            <div class="container-bt b-main-centro-total"><i onclick="openExternalLink('app.cataloguei.shop/d?loja=<?php echo($dominio); ?>')" class='bx bx-link-external' ></i></div>
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
                            <li onclick="logoEmpresa()" data-name="profile" class="drop-down__item">
                                Logo da empresa
                            </li>
                            <li  style="color: #932be9;" data-name="profile" class="drop-down__item">
                                Grupos de produtos
                            </li>                            
                            <li data-name="dashboard" class="drop-down__item">
                                Suporte
                            </li>
                            <li data-name="activity" class="drop-down__item">
                                Configurações
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fim menu-->
        <div class="img-user b-main-centro-total"><img src="<?php echo($path_logo); ?>" /></div>
        <div class="cart b-main-centro-total"><i onclick="addProdutoGrupo('0')" class='bx bx-plus'></i></div>
    </div>
    <div style="width: 100%; height: 60px;"></div>
    <div class="b-main-container-produtos b-main-centro-total">
        <div class="display display-modular">
         

        <table summary="" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th data-priority="1" style="width: 80px;">Icon</th>
                        <th class="b-main-active-mobile-col-table" data-priority="2">Descrição</th>
                        <th data-priority="3" style="width: 80px;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($clientes as $row){?>
                      <tr>
                        <td><?php echo $row['icon']; ?></td>
                        <td class="b-main-active-mobile-col-table"><?php echo $row['descricao']; ?></td>
                        <td style="width: 100px;">
                          <div style="width: 40px; position: relative; float: right;" class="b-main-container-right b-main-centro-total">
                            <div class="buttons-bt-generic-2-table b-main-centro-total"><a href="#" onclick="addProdutoGrupo('<?php echo $row['id']; ?>')"><i class='bx bx-edit'></i></a></div>
                          </div>
                        </td>
                      </tr>
                      <?php }; ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="6" class="text-center">
                        <!-- paginação dos dados -->
                        <div class="pagination b-main-centro-total">
                            <?php if ($pagina_atual > 1): ?>
                                <a class="pagination-previous" href="?pagina=<?php echo $pagina_atual - 1; ?>">Anterior</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <a class="pagination-number" href="?pagina=<?php echo $i; ?>" <?php if ($pagina_atual == $i) echo 'style="font-weight: bold;"'; ?>><?php echo $i; ?></a>
                            <?php endfor; ?>

                            <?php if ($pagina_atual < $total_paginas): ?>
                                <a class="pagination-next" href="?pagina=<?php echo $pagina_atual + 1; ?>">Próxima</a>
                            <?php endif; ?>
                        </div>
                        <!-- Fim da paginação -->
                        </td>                        
                      </tr>
                    </tfoot>
                  </table>


        </div>
    </div>
    <div style="width: 100%; height: 60px;"></div><!-- Separa a grid do menu -->
    <div style="height: 60px;" class="b-main-container-footer b-main-active-mobile-footer-bar">
      <div class="b-main-item-menu-footer-mobile b-main-centro-total"><i onclick="openPageMenuMobile('vendas.php')" class='bx bx-cart disabled'></i></div>
      <div class="b-main-item-menu-footer-mobile b-main-centro-total"><i onclick="openPageMenuMobile('produtos.php')" class='bx bx-package disabled' ></i></div>
      <div class="b-main-item-menu-footer-mobile b-main-centro-total"><i class='bx bx-user active' ></i></div>
    </div>    
</body>
<script src='../assets/js/alerts.js'></script>
<script src='../assets/js/main.js?v=1.0'></script>
</html>