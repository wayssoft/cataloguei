<?php 
include('conex.php');

class Settings
{
    private string $no_update_imagem_produto;
    private float  $taxa_entrega_geral;
    private int    $id_config_empresa;
    private string $zapi_ativado;
    private string $zapi_client_token;
    private string $zapi_token;
    private string $zapi_instances;
    private int    $id_empresa;
    private $mysqli; // Atributo para armazenar a conexão com o banco de dados

    //atributos para erros
    private string $error_msg;

    function __construct(int $id_empresa) 
    {
        // seta as variaveis de erro
        $this->error_msg='';
        
        // iniciar class de coenxão com banco de dados
        $this->mysqli = new ConexaoBD();


        // Construir a consulta SQL com segurança
        $sql_code = "SELECT * FROM empresa_config WHERE id_empresa=" . intval($id_empresa);
        $sql_query = $this->mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $this->mysqli->error);

        
       // Verificar o número de linhas retornadas
       $quantidade = $sql_query->num_rows;


        if($quantidade == 1) 
        {
            $empresa_config   = $sql_query->fetch_assoc();

            $this->no_update_imagem_produto = $empresa_config['no_update_imagem_produto']; 
            $this->taxa_entrega_geral       = $empresa_config['taxa_entrega_geral'];
            $this->id_config_empresa        = $empresa_config['id'];
            $this->zapi_ativado             = $empresa_config['zapi_ativado'];
            $this->zapi_client_token        = $empresa_config['zapi_client_token'];
            $this->zapi_token               = $empresa_config['zapi_token'];
            $this->zapi_instances           = $empresa_config['zapi_instances'];

            $this->id_empresa               = $id_empresa;
        } else {
            die("error xS001 Não foi encontrado os dados da configurações.<p><a href=\"log-in.php\">Entrar</a></p>");
        }
        

    }
    
    public function set__no_update_imagem_produto(string $value): void 
    {
        $this->no_update_imagem_produto = $value;
    }

    public function set__taxa_entrega_geral(float $value): void 
    {
        $this->taxa_entrega_geral = $value;
    }    

    public function get__no_update_imagem_produto(): string 
    {
        return $this->no_update_imagem_produto;
    }   
    
    public function get__taxa_entrega_geral(): string 
    {
        return $this->taxa_entrega_geral;
    }  
    
    
    public function getZapi_ativado()
    {
        return $this->zapi_ativado;
    }

    public function setZapi_ativado($zapi_ativado): void
    {
        $this->zapi_ativado = $zapi_ativado;
    }


    public function getZapi_client_token()
    {
        return $this->zapi_client_token;
    }


    public function setZapi_client_token($zapi_client_token): void
    {
        $this->zapi_client_token = $zapi_client_token;
    }


    public function getZapi_token()
    {
        return $this->zapi_token;
    }

    public function setZapi_token($zapi_token): void
    {
        $this->zapi_token = $zapi_token;
    }


    public function getZapi_instances()
    {
        return $this->zapi_instances;
    }

    public function setZapi_instances($zapi_instances): void
    {
        $this->zapi_instances = $zapi_instances;
    }

    public function getHorario(string $semana)
    {
        // Construir a consulta SQL com segurança
        $sql_code = "SELECT * FROM empresa_config_horarios WHERE semana = '".$semana."' AND id_empresa=" . intval($this->id_empresa);
        $sql_query = $this->mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $this->mysqli->error);

        
        // Verificar o número de linhas retornadas
        $quantidade = $sql_query->num_rows;
        
        $horarios=array();

        if($quantidade > 0)
        {
            $DsHorarios = $sql_query->fetch_all(MYSQLI_ASSOC);
            foreach($DsHorarios as $row)
            {
                $horarios[]=array(
                    "abertura" => $row['hora_abertura'],
                    "fechamento" => $row['hora_fechamento'],
                    "horario_ativo" => TRUE
                );
            }
        }else{
            $horarios[]=array(
                "abertura" => "00:00:00",
                "fechamento" => "00:00:00",
                "horario_ativo" => FALSE
            );
        }

        return $horarios;
    }

    
    public function update(): bool
    {

        $sql = "UPDATE empresa_config SET 
                                        no_update_imagem_produto=?,
                                        taxa_entrega_geral=?, 
                                        zapi_ativado=?,
                                        zapi_client_token=?,
                                        zapi_token=?,
                                        zapi_instances=?
                                        WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) { 
            $this->error_msg = "Erro na preparação da consulta: " . $stmt->error;
            return FALSE;
        }
        $stmt->bind_param("sssssss",  
                            $this->mysqli->real_escape_string($this->no_update_imagem_produto),
                            $this->mysqli->real_escape_string($this->taxa_entrega_geral),
                            $this->mysqli->real_escape_string($this->zapi_ativado),
                            $this->mysqli->real_escape_string($this->zapi_client_token),
                            $this->mysqli->real_escape_string($this->zapi_token),
                            $this->mysqli->real_escape_string($this->zapi_instances),
                            $this->id_config_empresa);
        // Executa a consulta de atualização
        if ($stmt->execute()) {
            return TRUE;
        } else {
            $this->error_msg = "Erro na atualização de dados: " . $stmt->error;
            return FALSE;            
        }

    }

}




