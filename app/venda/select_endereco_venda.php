<?php 
include('../req/conex.php');
include('../req/protect.php');
if(!isset($_GET['id_venda'])){
    $_error_ = True;
    die("error não foi passado a variavel id_venda <p><a href=\"log-in.php\">documentação</a></p>");    
}else{$id = $_GET['id_venda'];}
$sql_code = "SELECT id,cep,bairro,rua,complemento,nome,numero FROM endereco WHERE usuario_id = ".$_COOKIE['authorization_id'];
$sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
$quantidade = $sql_query->num_rows;
$endereco = $sql_query->fetch_all();
if($quantidade == 0){
    $noEndereco = true;
    $id_endereco = 0;
}else{
    $noEndereco = false;
    $id_endereco = $endereco['id'];
}
$_SUCCESS = False;
// add endereco ao pedido
if(isset($_POST['bt_add_endereco'])) {
    if(!isset($_GET['id_endereco_selected'])){
        $_error_ = True;
        die("error não foi passado a variavel id_venda <p><a href=\"log-in.php\">documentação</a></p>");    
    }else{$id_endereco_selected = $_GET['id_endereco_selected'];}
    // prepara para atualizar o endereco na venda
    $sql = "UPDATE venda SET endereco_id=? WHERE id = ?"; // Você pode ajustar a condição WHERE conforme necessário
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo "Erro na preparação da consulta: " . $conn->error;
        return -1;
    }
    $stmt->bind_param("ss", $id_endereco_selected, $id);
    // Executa a consulta de atualização
    if ($stmt->execute()) {
        //echo "Dados atualizados com sucesso!";
        $_SUCCESS = True;
    } else {
        echo "Erro na atualização de dados: " . $stmt->error;
    }
    $stmt->close();    
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Selecionar endereço</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/main.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/buttons.css'>
    <script src='../assets/js/main.js'></script>
</head>
<body>
    <?php foreach($endereco as $row){?>
    <form action="select_endereco_venda.php?id_endereco_selected=<?php echo $row[0]; ?>&id_venda=<?php echo $id; ?>" method="POST">
    <div class="b-main-endereco-list-display">
        <p><?php echo $row[5]; // nome do endereco ?></p>
        <label><?php echo 'cep:'.$row[1].' - '.$row[3].' n°'.$row[6];?></label>
        <br>
        <label><?php echo $row[2]; // bairro ?></label>
        <br><br>
        <button class="bt-selecionar-endereco" name="bt_add_endereco" type="submit">Selecionar Endereco</button>
    </div>
    </form>
    <?php } ?>
    <div class="b-main-container-footer b-main-centro-total" style="height: 70px; padding-top: 10px;">
        <button style="width: 220px;" class="button-65" onclick="openEnderecoAdd('<?php echo($id_endereco); ?>','<?php echo($id); ?>')" name="btadd" type="submit">Adicionar novo endereço</button>
    </div>    
</body>
<?php 
    if($_SUCCESS == true){
        echo "<script type='text/javascript'>
        window.parent.location.href = 'bag.php';
        </script>";
    }         
?>
</html>