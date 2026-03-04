<!-- jQuery (necesario para algunas funcionalidades) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- SweetAlert2 para alertas modernas -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Notificaciones Toast -->
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Función global para mostrar notificaciones
    window.showToast = function(type, message) {
        Toast.fire({
            icon: type,
            title: message
        });
    };

    // Manejar mensajes de sesión
    @if (Session::get('message'))
        @if (Session::get('icon') == 'success')
            showToast('success', "{{ Session::get('message') }}");
        @elseif (Session::get('icon') == 'error')
            showToast('error', "{{ Session::get('message') }}");
        @endif
    @endif
</script>

