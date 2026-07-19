

document.addEventListener('DOMContentLoaded', function () {

    const patrones = {
        cedula: /^[0-9]{1,2}-[0-9]{3,4}-[0-9]{1,6}$/,
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        soloNumeros: /^[0-9]+(\.[0-9]{1,2})?$/
    };

    function mostrarError(campoDiv, mensaje) {
        campoDiv.classList.add('con-error');
        let ayuda = campoDiv.querySelector('.ayuda-error');
        if (!ayuda) {
            ayuda = document.createElement('div');
            ayuda.className = 'ayuda-error';
            campoDiv.appendChild(ayuda);
        }
        ayuda.textContent = mensaje;
    }

    function limpiarError(campoDiv) {
        campoDiv.classList.remove('con-error');
    }

    function validarCampo(input) {
        const campoDiv = input.closest('.campo');
        if (!campoDiv) return true;

        limpiarError(campoDiv);

        if (input.hasAttribute('required') && !input.value.trim()) {
            mostrarError(campoDiv, 'Este campo es obligatorio.');
            return false;
        }

        if (input.dataset.tipo === 'cedula' && input.value.trim() && !patrones.cedula.test(input.value.trim())) {
            mostrarError(campoDiv, 'Formato esperado: 8-123-4567.');
            return false;
        }

        if (input.type === 'email' && input.value.trim() && !patrones.email.test(input.value.trim())) {
            mostrarError(campoDiv, 'Ingrese un correo válido.');
            return false;
        }

        if (input.dataset.tipo === 'monto' && input.value.trim() && !patrones.soloNumeros.test(input.value.trim())) {
            mostrarError(campoDiv, 'Ingrese un monto válido (ej. 120.00).');
            return false;
        }

        return true;
    }

    document.querySelectorAll('.formulario input, .formulario select').forEach(function (input) {
        input.addEventListener('blur', function () { validarCampo(input); });
    });


    document.querySelectorAll('form.validar').forEach(function (form) {
        form.addEventListener('submit', function (evento) {
            let valido = true;
            form.querySelectorAll('input, select').forEach(function (input) {
                if (!validarCampo(input)) {
                    valido = false;
                }
            });
            if (!valido) {
                evento.preventDefault();
                const primerError = form.querySelector('.con-error input, .con-error select');
                if (primerError) primerError.focus();
            }
        });
    });

    document.querySelectorAll('form.formulario-correo').forEach(function (form) {
        form.addEventListener('submit', function (evento) {
            const destinatario = form.querySelector('input[type="email"]');
            const valorDestino = destinatario ? destinatario.value.trim() : '';
            const confirmar = window.confirm('¿Enviar este reporte al correo ' + valorDestino + '?');
            if (!confirmar) {
                evento.preventDefault();
            }
        });
    });

});
