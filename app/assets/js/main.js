function openPageRegister(type,redirect){
    var url = encodeURIComponent(redirect)
    if(type == 'company'){window.parent.location.href = './register/empresa.php';}
    if(type == 'user'){window.location.href = '../register/user.php?redirect='+url;}
}
function openBagShop(loja){
    window.parent.location.href = './venda/bag.php?loja='+loja;
}
function openEnderecoAdd(id_endereco, id_venda){
    window.location.href = 'add_endereco_usuario.php?id='+id_endereco+'&id_venda='+id_venda;
}
function close_search(loja){
    window.location.href = 'd.php?loja='+loja;
}
$(document).ready(function(){
    $('#dropDown').click(function(){
      $('.drop-down').toggleClass('drop-down--active');
    });
});


async function submit_data_cancelar_pedido(id_pedido) {
    const action = 'CANCELAR_PEDIDO';
    //const id_pedido = document.getElementById('password').value;
    if (action && id_pedido) {
        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=${encodeURIComponent(action)}&id_pedido=${encodeURIComponent(id_pedido)}`
            });

            if (response.ok) {
                const result = await response.json(); // Analisa a resposta JSON
                console.log(result.result);
                if (result.result == 'success') {
                    alert('Sucesso: ' + result.message);
                    window.location.reload(false);
                } else {
                    alert('Erro: ' + result.message);
                }
            } else {
                alert('Erro ao enviar os dados.');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao enviar os dados.');
        }
    } else {
        alert('Por favor, preencha ambos os campos.');
    }
}

function cancela_pedido_bag(id_pedido){
	Swal.fire({
		title: "Você deseja cancelar seu pedido?",
		showDenyButton: false,
		showCancelButton: true,
		confirmButtonText: "Sim cancelar pedido"
	}).then((result) => {
		/* Read more about isConfirmed, isDenied below */
		if (result.isConfirmed) {
		    submit_data_cancelar_pedido(id_pedido);
		}
	});	
}

function openChatWhatsapp(numero){
    var url = "https://wa.me/55"+numero;
    var win = window.open(url, '_blank');
    win.focus();    
}

function openExternalLink(url_){
    var url = 'https://'+url_
    var win = window.open(url, '_blank');
    win.focus();    
}

function openPageMenuMobile(page){
    window.location.href = page;
}