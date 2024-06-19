function publicar(){
    Swal.fire({
        html: `
            <br>
            <iframe src="app/login.php?type=company" class="janela-publicar" title="Crie ou acesse sua conta"></iframe>
        `,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        showConfirmButton: false,
        title:'Acesse sua conta',
        width: 400
    });     
}