<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['email'])) {
    // Si no está logueado, redirigir al login
    header("Location: index.html");
    exit;
}

// Obtener los datos del usuario desde la sesión
$nombre_usuario = $_SESSION['nombre'];  // Primer nombre guardado en la sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca - Libros Disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            padding-left: 15px;
            background: linear-gradient(90deg, #6a11cb, #882beb);
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        h1 {
            color: #343a40;
        }
        .navbar-divider {
            height: 40px;
            width: 2px;
            background-color: rgba(255, 255, 255, 0.7);
            margin-left: 15px;
            margin-right: 15px;
        }
        .card {
            border: 1px solid #6a11cb;
            transition: transform 0.2s ease;
            cursor: pointer;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card-title {
            color: #6a11cb;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand font-weight-bold">Cliente: <?php echo $nombre_usuario;?></a>
            <div class="navbar-divider"></div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="ver_prestamos.php">Consultar Préstamos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="libros_mas.php">Libros En Existencia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.html">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Libros En Existencia</h1>
        <!-- Contenedor donde se mostrarán los libros -->
        <div id="contenedor-libros" class="row">
            <!-- Los libros se cargarán aquí por JS -->
        </div>
    </div>

    <!-- Agregar el script JavaScript para cargar los libros -->
    <script>
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
                                        <strong>Autor:</strong> ${libro.autores}<br>
                                        <strong>Géneros:</strong> ${libro.generos}<br>
                                        <strong>Fecha de Publicación:</strong> ${new Date(libro.fecha_publicacion).toLocaleDateString()}<br>
                                        ${libro.unidades == 0 ? "<strong>No hay unidades disponibles</strong>" : `<strong>Unidades Disponibles:</strong> ${libro.unidades}`}
                                    </p>
                                </div>
                            </div>
                        `;

                        contenedorLibros.appendChild(divLibro);
                    });
                })
                .catch(error => console.error('Error al obtener los libros:', error));
        });
    </script>

    <script src="mostrar_libros.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>