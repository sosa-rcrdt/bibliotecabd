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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            padding: 10px 15px;
            background: linear-gradient(90deg, #a80038, #ff1744);
        }
        .navbar-brand, .nav-link {
            color: #ffffff !important;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .navbar-divider {
            height: 40px;
            width: 2px;
            background-color: rgba(255, 255, 255, 0.7);
            margin: 0 15px;
        }
        h1 {
            color: #a80038;
            text-align: center;
        }
        .card {
            border: 1px solid #a80038;
            transition: transform 0.2s ease;
            cursor: pointer;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(168, 0, 56, 0.3);
        }
        .card-title {
            color: #a80038;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand">Cliente: <?php echo $nombre_usuario; ?></a>
            <div class="navbar-divider"></div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="registro_usuarios.php">Registro De Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="registro_libros.php">Registro De Libros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="añadir_libro.php">Añadir Libro</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="agregar_prestamo.php">Añadir Prestamo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cambiar_estado.php">Registro De Prestamos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.html">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">📖 Libros Disponibles</h1>
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
                        divLibro.className = 'col-md-4 mb-4';
                        divLibro.innerHTML = `
                            <div class="card libro h-100">
                                <div class="card-body">
                                    <h5 class="card-title">${libro.titulo}</h5>
                                    <p class="card-text">
                                        <strong>ISBN:</strong> ${libro.isbn}<br>
                                        <strong>Autor:</strong> ${libro.autores}<br>
                                        <strong>Géneros:</strong> ${libro.generos}<br>
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
