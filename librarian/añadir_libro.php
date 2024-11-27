<?php
session_start();
// Verificar si el usuario está logueado
if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit;
}
// Obtener los datos del usuario desde la sesión
$nombre_usuario = $_SESSION['nombre'];
// Incluir la conexión
require '../conexion.php';
// Crear instancia de la clase Conexion
$conexion = new Conexion();
$db = $conexion->getConexion();
// Obtener la lista de autores
$autores_query = "SELECT autor_id, nombre FROM Autores";
$stmt_autores = $db->query($autores_query);
$autores = $stmt_autores->fetchAll(PDO::FETCH_ASSOC);
// Obtener la lista de géneros
$generos_query = "SELECT genero_id, nombre_genero FROM Generos";
$stmt_generos = $db->query($generos_query);
$generos = $stmt_generos->fetchAll(PDO::FETCH_ASSOC);
// Verificar si hay un mensaje de éxito y mostrarlo
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success" role="alert">';
    echo $_SESSION['success_message'];
    echo '</div>';
    // Limpiar la variable de sesión para que el mensaje no se repita
    unset($_SESSION['success_message']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca - Añadir Libro</title>
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
        .form-control:focus {
            border-color: #a80038;
            box-shadow: 0 0 5px rgba(168, 0, 56, 0.5);
        }
        .btn-primary {
            background-color: #a80038;
            border-color: #a80038;
        }
        .btn-primary:hover {
            background-color: #ff1744;
            border-color: #ff1744;
        }
        /* Estilos para cambiar solo las letras y símbolos a escarlata */
        .btn-escarlata {
            color: #a80038; /* Color escarlata para el texto y el icono */
            border-color: transparent; /* Mantener el borde transparente */
        }
        .btn-escarlata:hover {
            color: #ff1744; /* Rojo más intenso al pasar el mouse */
        }
    </style>
</head>
<body>
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
                    <a class="nav-link" href="mostrar_prestamos.php">Registro De Prestamos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mostrar_libros.php">Libros Disponibles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="registro_usuarios.php">Registro De Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="registro_libros.php">Registro De Libros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.html">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">
    <h1 class="mb-4">Añadir Nuevo Libro</h1>
    <form action="guardar_libro.php" method="POST">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título del Libro</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
        </div>
        <div class="mb-3">
            <label for="isbn" class="form-label">ISBN</label>
            <input type="text" class="form-control" id="isbn" name="isbn" maxlength="13" required>
        </div>
        <div class="mb-3">
            <label for="fecha_publicacion" class="form-label">Fecha de Publicación</label>
            <input type="date" class="form-control" id="fecha_publicacion" name="fecha_publicacion" required>
        </div>
        <div class="mb-3">
            <label for="autores" class="form-label">Autor(es)</label>
            <select multiple class="form-control" id="autores" name="autores[]" required>
                <?php
                foreach ($autores as $autor) {
                    echo '<option value="' . htmlspecialchars($autor['autor_id']) . '">' . htmlspecialchars($autor['nombre']) . '</option>';
                }
                ?>
            </select>
            <small class="text-muted">Mantén presionada la tecla Ctrl (o Cmd) para seleccionar múltiples autores.</small>
            <button type="button" class="btn btn-link btn-escarlata" data-bs-toggle="modal" data-bs-target="#modalAutor">+Añadir Autor</button>
        </div>
        <div class="mb-3">
            <label for="generos" class="form-label">Género(s)</label>
            <select multiple class="form-control" id="generos" name="generos[]" required>
                <?php
                foreach ($generos as $genero) {
                    echo '<option value="' . htmlspecialchars($genero['genero_id']) . '">' . htmlspecialchars($genero['nombre_genero']) . '</option>';
                }
                ?>
            </select>
            <small class="text-muted">Mantén presionada la tecla Ctrl (o Cmd) para seleccionar múltiples géneros.</small>
            <button type="button" class="btn btn-link btn-escarlata" data-bs-toggle="modal" data-bs-target="#modalGenero">+Añadir Género</button>
        </div>
        <div class="mb-3">
            <label for="unidades" class="form-label">Unidades Disponibles</label>
            <input type="number" class="form-control" id="unidades" name="unidades" min="1" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Libro</button>
    </form>
    <!-- Modal para añadir nuevos autores -->
    <div class="modal fade" id="modalAutor" tabindex="-1" aria-labelledby="modalAutorLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAutorLabel">Añadir Nuevo Autor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="guardar_autor.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre_autor" class="form-label">Nombre del Autor</label>
                            <input type="text" class="form-control" id="nombre_autor" name="nombre_autor" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                        </div>
                        <div class="mb-3">
                            <label for="nacionalidad" class="form-label">Nacionalidad</label>
                            <input type="text" class="form-control" id="nacionalidad" name="nacionalidad">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Autor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal para añadir nuevos géneros -->
    <div class="modal fade" id="modalGenero" tabindex="-1" aria-labelledby="modalGeneroLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalGeneroLabel">Añadir Nuevo Género</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="agregar_genero.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreGenero" class="form-label">Nombre del Género</label>
                            <input type="text" class="form-control" id="nombreGenero" name="nombreGenero" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Género</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>