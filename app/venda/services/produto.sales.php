<?php 


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

function addProdutoVenda($mysqli, $ProdutoArray, $id_venda): bool {
    // recupera dados do array
    $id_produto  = strval($ProdutoArray["id_produto"]);
    $qtd         = $ProdutoArray["qtd"];
    $obs         = $ProdutoArray["obs"];
    $variacao    = $ProdutoArray["variacao"];
    $id_variacao = $ProdutoArray["id_variacao"];

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

    // verifica se tem variacao e pega o preco da variacao
    if($variacao == 'S')
    {

        // busca a variação
        $sql_code = "SELECT * FROM variacao_produto WHERE id = ".$id_variacao;
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);
        $quantidade = $sql_query->num_rows;
        if($quantidade == 1) { 
            $produto = $sql_query->fetch_assoc();
            $_preco = $produto['preco'];
        }else{$_preco = 0;};

    }
    // Prepara a consulta SQL para inserção dos dados
    $sql = "INSERT INTO venda_detalhe ( qtd,
                                        valor_un,
                                        valor_total,
                                        status,
                                        obs,
                                        venda_id,
                                        produto_id,
                                        variacao,
                                        id_variacao)  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        //echo "Erro na preparação da consulta: " . $conn->error;
        return false;
    }
    // Vincula os parâmetros à consulta preparada
    $status = 'added';
    $total = $_preco * $qtd;
    $stmt->bind_param("sssssssss", $qtd, $_preco, $total, $status, $obs, $id_venda, $id_produto,$variacao,$id_variacao);
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