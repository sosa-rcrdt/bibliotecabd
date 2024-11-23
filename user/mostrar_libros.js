document.addEventListener('DOMContentLoaded', () => {
    // Realizar la petición AJAX para obtener los libros disponibles
    fetch('buscar_libros.php')
        .then(response => response.json()) // Convertir la respuesta JSON a un objeto JavaScript
        .then(libros => {
            const contenedorLibros = document.getElementById('contenedor-libros');

            // Limpiar el contenedor antes de mostrar los libros
            contenedorLibros.innerHTML = '';

            // Mostrar cada libro en el contenedor
            libros.forEach(libro => {
                const divLibro = document.createElement('div');
                divLibro.className = 'col-md-4 mb-4'; // Div para la columna con Bootstrap
                divLibro.innerHTML = `
                    <div class="card libro h-100">
                        <div class="card-body">
                            <h5 class="card-title">${libro.titulo}</h5>
                            <p class="card-text">
                                <strong>ISBN:</strong> ${libro.isbn}<br>
                                <strong>Autor:</strong> ${libro.autor}<br>
                                <strong>Géneros:</strong> ${libro.generos} <!-- Mostrar los géneros concatenados --><br>
                                <strong>Fecha de Publicación:</strong> ${new Date(libro.fecha_publicacion).toLocaleDateString()}<br>
                                <strong>Unidades Disponibles:</strong> ${libro.unidades}
                            </p>
                        </div>
                    </div>
                `;

                contenedorLibros.appendChild(divLibro);
            });
        })
        .catch(error => console.error('Error al obtener los libros:', error));
});
