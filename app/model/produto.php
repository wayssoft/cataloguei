<?php
include('conex.php');

class Produto
{
    private string $nome;
    private string $descricao;
    private string $codigo_barras;
    private float  $preco;
    private string $path_img;
    private float  $estoque;
    private int    $id_empresa;
    private string $promocao;
    private float  $preco_promocao;
    private string $identificado;
    private int    $id;
    private $mysqli; // Atributo para armazenar a conexão com o banco de dados

    //atributos para erros
    private string $error_msg;

    function __construct(int $id_produto)
    {
        // seta as variaveis de erro
        $this->error_msg='';
        
        // iniciar class de coenxão com banco de dados
        $this->mysqli = new ConexaoBD();


        // Construir a consulta SQL com segurança
        $sql_code = "SELECT * FROM produto WHERE id=" . intval($id_produto);
        $sql_query = $this->mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $this->mysqli->error);

        
       // Verificar o número de linhas retornadas
       $quantidade = $sql_query->num_rows;


       if($quantidade == 1) 
       {
           $produto   = $sql_query->fetch_assoc();

           $this->nome                     = $produto['nome']; 
           $this->descricao                = $produto['descricao'];
           $this->codigo_barras            = $produto['codigo_barras'];
           $this->preco                    = $produto['preco'];
           $this->path_img                 = $produto['path_imagem'];
           $this->estoque                  = $produto['estoque'];
           $this->id_empresa               = $produto['id_empresa'];
           $this->promocao                 = $produto['promocao'];
           $this->preco_promocao           = $produto['preco_promocional'];
           $this->identificado             = $produto['identificador'];

       } else {
           die("error xP001 Não foi encontrado os dados do produto.<p><a href=\"log-in.php\">Entrar</a></p>");
       }
    }


    public function getCodigo_barras()
    {
        return $this->codigo_barras;
    }

    public function setCodigo_barras($codigo_barras): void
    {
        $this->codigo_barras = $codigo_barras;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome): void
    {
        $this->nome = $nome;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }

    public function getPreco()
    {
        return $this->preco;
    }


    public function setPreco($preco): void
    {
        $this->preco = $preco;
    }


    public function getPath_img()
    {
        return $this->path_img;
    }


    public function setPath_img($path_img): void
    {
        $this->path_img = $path_img;
    }

    public function getEstoque()
    {
        return $this->estoque;
    }

    public function setEstoque($estoque): void
    {
        $this->estoque = $estoque;
    }


    public function getId_empresa()
    {
        return $this->id_empresa;
    }


    public function setId_empresa($id_empresa): void
    {
        $this->id_empresa = $id_empresa;
    }

    public function getPromocao()
    {
        return $this->promocao;
    }

    public function setPromocao($promocao): void
    {
        $this->promocao = $promocao;

    }

    public function getPreco_promocao()
    {
        return $this->preco_promocao;
    }

    public function setPreco_promocao($preco_promocao): void
    {
        $this->preco_promocao = $preco_promocao;
    }

    public function getIdentificado()
    {
        return $this->identificado;
    }


    public function setIdentificado($identificado): void
    {
        $this->identificado = $identificado;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }


    function update(): bool
    {

        $sql = "UPDATE produto SET 
                                    codigo_barras=?,
                                    nome=?,
                                    descricao=?,
                                    preco=?,
                                    path_imagem=?,
                                    estoque=?,
                                    id_empresa=?,
                                    promocao=?,
                                    preco_promocional=?
                                    identificador=? WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) { 
            $this->error_msg = "Erro na preparação da consulta: " . $stmt->error;
            return FALSE;
        }
        $stmt->bind_param("ssssssssss",  
                            $this->mysqli->real_escape_string($this->codigo_barras),
                            $this->mysqli->real_escape_string($this->nome),
                            $this->mysqli->real_escape_string($this->descricao),
                            $this->mysqli->real_escape_string($this->preco),
                            $this->mysqli->real_escape_string($this->path_img),
                            $this->mysqli->real_escape_string($this->estoque),
                            $this->mysqli->real_escape_string($this->id_empresa),
                            $this->mysqli->real_escape_string($this->promocao),
                            $this->mysqli->real_escape_string($this->preco_promocao),
                            $this->mysqli->real_escape_string($this->identificado),
                            $this->id);
        // Executa a consulta de atualização
        if ($stmt->execute()) {
            return TRUE;
        } else {
            $this->error_msg = "Erro na atualização de dados: " . $stmt->error;
            return FALSE;            
        }

    }


}



?>