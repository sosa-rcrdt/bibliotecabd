// Esperamos a que el DOM se cargue completamente
document.addEventListener('DOMContentLoaded', function() {
    // Obtener el contenedor donde se mostrarán los préstamos
    const contenedorPrestamos = document.getElementById('contenedor-prestamos');

    // Obtener el correo del usuario desde el formulario de login o desde la sesión (esto dependerá de la implementación)
    const usuarioEmail = sessionStorage.getItem('usuarioEmail'); // Asumimos que guardamos el correo al loguearse

    // Comprobar si el correo está disponible
    if (!usuarioEmail) {
        contenedorPrestamos.innerHTML = '<p>No estás logueado. Por favor, ingresa para ver tus préstamos.</p>';
        return;
    }

    // Hacer una solicitud fetch para obtener los préstamos del usuario desde el backend (PHP)
    fetch('obtener_prestamos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email: usuarioEmail }) // Enviamos el correo como parámetro
    })
    .then(response => response.json())
    .then(data => {
        if (data.length > 0) {
            // Si el usuario tiene préstamos, los mostramos
            data.forEach(prestamo => {
                const prestamoDiv = document.createElement('div');
                prestamoDiv.classList.add('libro');
                prestamoDiv.innerHTML = `
                    <h3>${prestamo.titulo}</h3>
                    <p><strong>Autor:</strong> ${prestamo.autor}</p>
                    <p><strong>Fecha de Préstamo:</strong> ${prestamo.fecha_prestamo}</p>
                    <p><strong>Fecha de Devolución:</strong> ${prestamo.fecha_devolucion}</p>
                    <p><strong>Estado:</strong> ${prestamo.estado}</p>
                `;
                contenedorPrestamos.appendChild(prestamoDiv);
            });
        } else {
            contenedorPrestamos.innerHTML = '<p>No tienes préstamos activos.</p>';
        }
    })
    .catch(error => {
        contenedorPrestamos.innerHTML = '<p>Error al obtener los préstamos. Intenta más tarde.</p>';
        console.error('Error al obtener los préstamos:', error);
    });
});
