<?php 
if(!isset($_GET['id'])){
    $_error_ = True;
    die("error não foi passado a variavel id do produto.<p><a href=\"log-in.php\">documentação</a></p>");    
}else{$id_produto = $_GET['id'];}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Menu de produto</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/main.css'>
    <!--boxicon-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
        .container-menu{
            width: 100%; height: 50px;
            border-bottom: 1px solid #F3F3F3;
        }
        .container-menu .desc-menu{
            position: relative; float: left;
            height: 100%; width: calc(100% - 50px);
            font-family: "Roboto", sans-serif;
            font-weight: 500;
            font-size: 16px;
            padding-top: 15px;
        }
        .container-menu .container-bt{
            position: relative; float: left;
            height: 100%; width: 50px;
        }
        .container-menu .container-bt i{
            font-size: 26px;
        }
    </style>
</head>
<body>
    <div class="container-menu">
        <div class="desc-menu">
            <p>Aplicar promoção no produto</p>
        </div>
        <div class="container-bt b-main-centro-total">
            <a href="produto_promocao.php?id=<?php echo $id_produto; ?>"><i class='bx bx-chevron-right'></i></a>
        </div>
    </div>
</body>
</html>