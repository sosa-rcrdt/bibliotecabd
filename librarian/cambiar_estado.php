<?php
session_start();
require '../conexion.php';

// Crear instancia de la clase Conexion
$conexion = new Conexion();
$db = $conexion->getConexion();

// Consulta para obtener los usuarios registrados (o puedes usar usuarios de una tabla específica si es necesario)
$sql = "SELECT usuario_id, nombre FROM Usuarios";
$stmt = $db->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicializar variables
$usuario_id = isset($_POST['usuario_id']) ? $_POST['usuario_id'] : null;

// Consulta para obtener los préstamos activos (filtrados por usuario si es seleccionado)
$sql = "
    SELECT TOP (1000)
        reporte_id,
        usuario_id,
        libro_id,
        usuario_nombre,
        libro_titulo,
        fecha_prestamo,
        fecha_devolucion,
        estado
    FROM Reportes
";

// Si se ha seleccionado un usuario, agregar la condición para filtrar por el usuario
if ($usuario_id) {
    $sql .= " WHERE usuario_id = :usuario_id";
}

// Preparar la consulta de préstamos
$stmt = $db->prepare($sql);

// Si se ha seleccionado un usuario, vincular el parámetro en la consulta
if ($usuario_id) {
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
}

// Ejecutar la consulta
$stmt->execute();

// Obtener los resultados
$prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nombre_usuario = $_SESSION['nombre'];  // Primer nombre guardado en la sesión

// Verificar si se ha presionado el botón "Marcar como Devuelto"
if (isset($_POST['marcar_devuelto'])) {
    // Obtener el ID del préstamo desde el formulario
    $reporte_id = $_POST['reporte_id'];

    // Actualizar el estado del préstamo a 'Devuelto'
    $sql = "UPDATE Reportes SET estado = 'Devuelto' WHERE reporte_id = :reporte_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':reporte_id', $reporte_id, PDO::PARAM_INT);

    // Ejecutar la actualización
    if ($stmt->execute()) {
        echo "<p class='alert alert-success text-center'>El préstamo ha sido marcado como devuelto.</p>";
        
        // Redirigir a la misma página para evitar el reenvío del formulario al actualizar la página
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<p class='alert alert-danger text-center'>Hubo un error al actualizar el estado del préstamo.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos Activos</title>
    <!-- Incluir Bootstrap para estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
            margin-bottom: 1.5rem;
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

        /* Cambiar el color de los botones */
        .btn-primary, .btn-success {
            background-color: #a80038;
            border-color: #a80038;
        }
        .btn-primary:hover, .btn-success:hover {
            background-color: #ff1744;
            border-color: #ff1744;
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
                        <a class="nav-link" href="agregar_prestamo.php">Añadir Prestamo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.html">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-3">
        <h1>Registro De Prestamos</h1>

        <!-- Formulario para seleccionar usuario -->
        <form method="POST" action="" class="mb-4">
            <div class="row align-items-center mt-4">
                <div class="col-md-6">
                    <select name="usuario_id" id="usuario_id" class="form-select" required>
                        <option value="">Selecciona un Usuario</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?php echo $usuario['usuario_id']; ?>"><?php echo $usuario['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">Consultar Préstamos</button>
                </div>
            </div>
        </form>

        <!-- Sección de los préstamos -->
        <div id="contenedor-prestamos" class="mt-4">
            <?php
            if (empty($prestamos)) {
                echo "<p class='text-center text-muted'>No hay préstamos activos para el usuario seleccionado.</p>";
            } else {
                foreach ($prestamos as $prestamo) {
                    // Verificar si la fecha de devolución está vacía o es NULL
                    $fechaDevolucion = empty($prestamo['fecha_devolucion']) ? "Sin Devolver" : htmlspecialchars($prestamo['fecha_devolucion']);
                    
                    // Solo mostrar el botón si el estado es 'Activo' o 'Retrasado'
                    if ($prestamo['estado'] == 'Activo' || $prestamo['estado'] == 'Retrasado') {
                        echo "
                            <div class='card'>
                                <div class='card-body'>
                                    <h5 class='card-title'>📖 " . htmlspecialchars($prestamo['libro_titulo']) . "</h5>
                                    <p class='card-text'>
                                        <strong>Usuario:</strong> " . htmlspecialchars($prestamo['usuario_nombre']) . "<br>
                                        <strong>Fecha de Préstamo:</strong> " . htmlspecialchars($prestamo['fecha_prestamo']) . "<br>
                                        <strong>Fecha de Devolución:</strong> " . $fechaDevolucion . "<br>
                                        <strong>Estado:</strong> " . htmlspecialchars($prestamo['estado']) . "
                                    </p>
                                    <form method='POST' action='' class='d-inline'>
                                        <input type='hidden' name='reporte_id' value='" . $prestamo['reporte_id'] . "'>
                                        <button type='submit' class='btn btn-success' name='marcar_devuelto'>Marcar como Devuelto</button>
                                    </form>
                                </div>
                            </div>
                        ";
                    }
                }
            }
            ?>
        </div>
    </div>

    <!-- Incluir Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
