<?php
include('../../req/conex.php');
class Produto
{
    function list($id)
    {
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
    }
}