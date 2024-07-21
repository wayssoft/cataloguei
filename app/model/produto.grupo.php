<?php
include('conex.php');

class ProdutoGrupo
{
    private string $descricao;
    private string $icon;
    private string $id_empresa;
    private int $id;
    private $mysqli; // Atributo para armazenar a conexão com o banco de dados

    //atributos para erros
    private string $error_msg;    

    function __construct(int $id_produto_grupo)
    {
        // seta as variaveis de erro
        $this->error_msg='';
        
        // iniciar class de coenxão com banco de dados
        $this->mysqli = new ConexaoBD();


        // Construir a consulta SQL com segurança
        $sql_code = "SELECT * FROM produto_categoria WHERE id=" . intval($id_produto_grupo);
        $sql_query = $this->mysqli->query($sql_code) or die("Falha na execução do código SQL: "  /*. $this->mysqli->error*/);

        
        // Verificar o número de linhas retornadas
        $quantidade = $sql_query->num_rows;


        if($quantidade == 1) 
        {
            $produto_grupo   = $sql_query->fetch_assoc();

            $this->descricao                     = $produto_grupo['descricao']; 
            $this->icon                          = $produto_grupo['icon'];

        } else {
            die("error xP001 Não foi encontrado os dados do produto.<p><a href=\"log-in.php\">Entrar</a></p>");
        }
    }

    function fetch_list(int $id_empresa)
    {
        // seta as variaveis de erro
        $this->error_msg='';
        
        // iniciar class de coenxão com banco de dados
        $this->mysqli = new ConexaoBD();


        // Construir a consulta SQL com segurança
        $sql_code = "SELECT * FROM produto_categoria WHERE id_empresa=" . intval($id_empresa);
        $sql_query = $this->mysqli->query($sql_code) or die("Falha na execução do código SQL: "  /*. $this->mysqli->error*/); 
        
        return $sql_query->fetch_assoc();
    }

}

?>