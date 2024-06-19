<?php
include('conex.php');
if(!isset($_COOKIE['authorization_id'])) {
    die("error x001 Você não pode acessar esta página porque não está logado.<p><a href=\"log-in.php\">Entrar</a></p>");
}
if(!isset($_COOKIE['authorization_type'])) {
    die("error x002 Você não pode acessar esta página porque não está logado.<p><a href=\"log-in.php\">Entrar</a></p>");
}
// verifica se tem o usuario na base de dados
if ($_COOKIE['authorization_type'] == 'company'){
    $sql_code = "SELECT * FROM empresa WHERE id = ".$_COOKIE['authorization_id'];
    $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
    $quantidade = $sql_query->num_rows;
    if($quantidade == 0) {
        die("error x006 Você não pode acessar esta página porque não está logado.<p><a href=\"log-in.php\">Entrar</a></p>");
    }
    // verifica se usuario esta ativo
    $usuario = $sql_query->fetch_assoc();
    if($usuario['status'] != 'active'){
        if ($usuario['status'] == 'pending_validate_whatsApp'){header("Location: ../register/validate_whatsapp.php"); exit;}
        die("error x007 Você não pode acessar esta página porque não está logado.<p><a href=\"log-in.php\">Entrar</a></p>");
    }    
}
?>