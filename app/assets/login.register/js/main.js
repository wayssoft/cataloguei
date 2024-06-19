$(function() {

    $('.btn-link[aria-expanded="true"]').closest('.accordion-item').addClass('active');
    $('.collapse').on('show.bs.collapse', function () {
	    $(this).closest('.accordion-item').addClass('active');
	});

    $('.collapse').on('hidden.bs.collapse', function () {
	    $(this).closest('.accordion-item').removeClass('active');
	});

    

});

function alert_error(status,msg){
	if(status == 'True'){
		Swal.fire(
			{
				title: msg,
				icon: 'warning',
				confirmButtonColor: '#007bff'
			}
		);
	}
}


function alert_success(status,msg){
	if(status == 'True'){
		Swal.fire(
			{
				title: msg,
				icon: 'success',
				confirmButtonColor: '#007bff'
			}
		);
	}
}

function open_novo_cadastro(){
	window.location.href = "cadastrar.php";
}