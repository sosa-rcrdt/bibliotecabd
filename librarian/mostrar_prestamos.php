<?php
session_start();
require '../conexion.php';

// Crear instancia de la clase Conexion
$conexion = new Conexion();
$db = $conexion->getConexion();

// Consulta para obtener los préstamos activos desde la tabla Reportes
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

// Preparar la consulta
$stmt = $db->prepare($sql);

// Ejecutar la consulta
$stmt->execute();

// Obtener los resultados
$prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nombre_usuario = $_SESSION['nombre'];  // Primer nombre guardado en la sesión
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
                        <a class="nav-link" href="mostrar_libros.php">Libros Disponibles</a>
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
                        <a class="nav-link" href="../index.html">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-3">
        <h1>Registro De Prestamos</h1>

        <!-- Sección de los préstamos -->
        <div id="contenedor-prestamos" class="mt-4">
            <?php
            if (empty($prestamos)) {
                echo "<p class='text-center text-muted'>No hay préstamos activos en este momento.</p>";
            } else {
                foreach ($prestamos as $prestamo) {
                    // Verificar si la fecha de devolución está vacía o es NULL
                    $fechaDevolucion = empty($prestamo['fecha_devolucion']) ? "Sin Devolver" : htmlspecialchars($prestamo['fecha_devolucion']);
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
                            </div>
                        </div>
                    ";
                }
            }
            ?>
        </div>
    </div>

    <!-- Incluir el archivo JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
