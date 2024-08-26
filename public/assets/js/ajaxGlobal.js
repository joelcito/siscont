// Configuración global de AJAX
$.ajaxSetup({
    error: function(jqXHR, textStatus, errorThrown) {
        if (jqXHR.status === 419) {

            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: "Su sesion a terminado vuelva a ingresar!",
                showConfirmButton: true,
                confirmButtonText: 'OK',
                timerProgressBar: true,
                allowOutsideClick: false, // No cerrar al hacer clic fuera
                allowEscapeKey: false,    // No cerrar con la tecla "Esc"
                preConfirm: () => {
                    location.reload();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Acción realizada después de hacer clic en OK');
                }
            });

            // alert('Tu sesión ha expirado. Por favor, vuelve a cargar la página.');
            // Opcional: Recargar la página
        } else {
            // Manejar otros errores
            console.error('Error:', textStatus, errorThrown);
        }
    }
});
