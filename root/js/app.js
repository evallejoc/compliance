function login() {
    const user = $('#user').val();
    const pass = $('#pass').val();

    if (!user || !pass) {
        Swal.fire('Atención', 'Datos incompletos', 'warning');
        return;
    }

    $.ajax({
        url: 'php/auth.php?action=login',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ username: user, password: pass }),
        success: function(res) {
            sessionStorage.setItem('sessionToken', res.token);
            sessionStorage.setItem('userData', JSON.stringify(res.user));
            
            Swal.fire({
                icon: 'success',
                title: 'Acceso Concedido',
                showConfirmButton: false,
                timer: 1000
            }).then(() => {
                mostrarDashboard();
            });
        },
        error: function() {
            Swal.fire('Error', 'Credenciales incorrectas', 'error');
        }
    });
}

function mostrarDashboard() {
    const userData = JSON.parse(sessionStorage.getItem('userData'));
    if (userData) {
        $('#welcome-text').text(`Bienvenido: ${userData.nombre} | ${userData.empresa}`);
    }

    // Intercambio de secciones usando clases de Bootstrap
    $('#login-section').addClass('d-none');
    $('#dashboard').removeClass('d-none');
}

function irASistema(sistema) {
    if (sistema === 'reune') {
        window.location.href = 'reune/reclamaciones.php';
    } else {
        // Por ahora lo mandamos a una alerta hasta tener REDECO
        Swal.fire('Próximamente', 'Módulo REDECO en desarrollo', 'info');
    }
}

function logout() {
    sessionStorage.clear();
    location.reload();
}

$(document).ready(function() {
    if (sessionStorage.getItem('sessionToken')) {
        mostrarDashboard();
    }
});