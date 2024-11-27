<?php
session_start();
require '../conexion.php'; // Incluye el archivo de conexión

// Verificar si el usuario está logueado
if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit;
}

// Crear instancia de la clase Conexion
$conexion = new Conexion();
$db = $conexion->getConexion();

// Obtener la lista de usuarios
$usuarios_query = "SELECT usuario_id, nombre FROM Usuarios";
$stmt_usuarios = $db->query($usuarios_query);
$usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);

// Obtener la lista de libros disponibles (basado en la columna 'disponible' que es BIT)
$libros_query = "SELECT libro_id, titulo FROM Libros WHERE disponible = 1 AND unidades > 0";
$stmt_libros = $db->query($libros_query);
$libros = $stmt_libros->fetchAll(PDO::FETCH_ASSOC);

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar los datos enviados
    // Obtener los datos del formulario
    $usuario_id = $_POST['usuario_id'];
    $libro_id = $_POST['libro_id'];
    $fecha_prestamo = $_POST['fecha_prestamo'];
    $fecha_devolucion = $_POST['fecha_devolucion'];

    // Verificar si la fecha de devolución está vacía
    if (empty($fecha_devolucion)) {
        echo '<div class="alert alert-danger" role="alert">La fecha de devolución no puede estar vacía.</div>';
    } else {
        // Convertir las fechas a formato 'Y-m-d' si es necesario (asegurando que sean fechas válidas)
        $fecha_prestamo = date('Y-m-d', strtotime($fecha_prestamo));
        $fecha_devolucion = date('Y-m-d', strtotime($fecha_devolucion));

        // Registrar el préstamo en la base de datos
        $insert_query = "INSERT INTO Prestamos (usuario_id, libro_id, fecha_prestamo, fecha_devolucion, estado) VALUES (?, ?, ?, ?, 'Activo')";
        $stmt_insert = $db->prepare($insert_query);

        if ($stmt_insert->execute([$usuario_id, $libro_id, $fecha_prestamo, $fecha_devolucion])) {
            // Actualizar la cantidad de unidades disponibles en la tabla Libros (opcional)
            echo '<div class="alert alert-success" role="alert">¡Préstamo registrado con éxito!</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Error al registrar el préstamo.</div>';
        }
    }
}

$nombre_usuario = $_SESSION['nombre'];  // Primer nombre guardado en la sesión

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Préstamo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Cargar Font Awesome para los iconos -->
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
        .btn-primary {
            background-color: #a80038;
            border-color: #a80038;
        }
        .btn-primary:hover {
            background-color: #ff1744;
            border-color: #ff1744;
        }
        .select-icon {
            font-size: 1.2rem;
            margin-left: 10px;
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
                    <a class="nav-link" href="mostrar_libros.php">Libros En Existencia</a>
                </li>
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
    <h1 class="mb-4">Registrar Nuevo Préstamo</h1>
    <form action="agregar_prestamo.php" method="POST">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <div class="input-group">
                <select class="form-control" id="usuario" name="usuario_id" required>
                    <option value="" disabled selected>Selecciona un usuario</option>
                    <?php
                    foreach ($usuarios as $usuario) {
                        echo '<option value="' . $usuario['usuario_id'] . '">' . htmlspecialchars($usuario['nombre']) . '</option>';
                    }
                    ?>
                </select>
                <span class="input-group-text select-icon"><i class="fa fa-chevron-down"></i></span>
            </div>
        </div>
        <div class="mb-3">
            <label for="libro" class="form-label">Libro</label>
            <div class="input-group">
                <select class="form-control" id="libro" name="libro_id" required>
                    <option value="" disabled selected>Selecciona un libro</option>
                    <?php
                    foreach ($libros as $libro) {
                        echo '<option value="' . $libro['libro_id'] . '">' . htmlspecialchars($libro['titulo']) . '</option>';
                    }
                    ?>
                </select>
                <span class="input-group-text select-icon"><i class="fa fa-chevron-down"></i></span>
            </div>
        </div>
        <div class="mb-3">
            <label for="fecha_prestamo" class="form-label">Fecha de Préstamo</label>
            <input type="date" class="form-control" id="fecha_prestamo" name="fecha_prestamo" required>
        </div>
        <div class="mb-3">
            <label for="fecha_devolucion" class="form-label">Fecha de Devolución</label>
            <input type="date" class="form-control" id="fecha_devolucion" name="fecha_devolucion" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Préstamo</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
