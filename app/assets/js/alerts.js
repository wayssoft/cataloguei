function publicar(){
    Swal.fire({
        html: `
            <br>
            <iframe src="login.php?type=company" class="janela-publicar" title="Crie ou acesse sua conta"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        title:'Acesse sua conta',
        width: 400
    });     
}
function produtoDetalhe(id,loja){
    Swal.fire({
        html: `
            <br>
            <iframe src="./venda/produto.php?id=`+id+`&loja=`+loja+`" class="janela-produto-detalhe" title="Crie ou acesse sua conta"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        width: 500
    });      
}
function addProduto(id){
    Swal.fire({
        html: `
            <br>
            <iframe src="add.produto.php?id=`+id+`" class="janela-produto-detalhe" title="Crie ou acesse sua conta"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        width: 500
    });      
}
function addProdutoGrupo(id_produto_grupo,id_produto){
    Swal.fire({
        html: `
            <br>
            <iframe src="add.produto.grupo.php?id_produto_grupo=`+id_produto_grupo+`" class="janela-produto-detalhe" title="Crie ou acesse sua conta"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        width: 500
    });      
}
function trocarEnd(id,loja){
    Swal.fire({
        html: `
            <br>
            <iframe src="select_endereco_venda.php?id_venda=`+id+`" class="janela-produto-detalhe" title="Crie ou acesse sua conta"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        width: 500
    });      
}
// na pagina de vendas esse alert abre o detalhe do pedido
function pedido_detalhe(id, token){
    Swal.fire({
        html: `
            <br>
            <iframe src="list.pedido.detalhe.php?cod=`+id+`&token=`+token+`" class="janela-produto-detalhe" title="Crie ou acesse sua conta"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        width: 500
    });      
}
// 
function logoEmpresa(){
    Swal.fire({
        html: `
            <br>
            <iframe src="logo.php" class="janela-logo" title="Crie ou acesse sua conta"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        width: 500
    });  
    $('.drop-down').toggleClass('drop-down--active');    
}
// troca status do pedido
function pedido_status(id){
    Swal.fire({
        html: `
            <br>
            <iframe src="action.pedido.status.php?cod=`+id+`" class="janela-pedido_status" title="Crie ou acesse sua conta"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        width: 500
    });      
}

// menu do produto
function produto_menu(id){
    Swal.fire({
        html: `
            <br>
            <iframe src="action.produto.menu.php?id=`+id+`" class="janela-produto-menu" title="Crie ou acesse sua conta"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        width: 500
    });      
}

// abrir janela de cadastro de usuario apartir do menu
function criar_usuario_menu(url){
    Swal.fire({
        html: `
            <br>
            <iframe src="`+url+`" class="janela-criar-usuario-menu" title="Criar conta de usuario"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        width: 500
    }); 
    $('.drop-down').toggleClass('drop-down--active');     
}

// abrir janela de log-in usuario a apartir do menu
function entrar_usuario_menu(url){
    Swal.fire({
        html: `
            <br>
            <iframe src="`+url+`" class="janela-publicar" title="Log-in usuario"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        width: 400
    }); 
    $('.drop-down').toggleClass('drop-down--active');     
}


// area destinada painel
// prefixo das funções para painel = painel ex: painel_config

// menu do produto
function painel_open_settings()
{
    Swal.fire({
        html: `
            <br>
            <iframe src="settings/empresa.php" class="janela-produto-menu" title="Crie ou acesse sua conta"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        width: 500
    }); 
    $('.drop-down').toggleClass('drop-down--active');      
}