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
    WHERE estado = 'activo'
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
            padding-left: 15px;
            background: linear-gradient(90deg, #dc143c, #b22222); /* Colores escarlata */
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        h1 {
            color: #b22222; /* Título en escarlata */
            text-align: center;
            margin-top: 2rem;
        }
        .card {
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-title {
            color: #dc143c;
            font-weight: bold;
        }
        .btn-primary {
            background: linear-gradient(90deg, #dc143c, #b22222);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #b22222, #dc143c);
        }
        .navbar-divider {
            height: 40px;
            width: 2px;
            background-color: rgba(255, 255, 255, 0.7);
            margin-left: 15px;
            margin-right: 15px;
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
                        <a class="nav-link" href="../index.php">Página Principal</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Préstamos Activos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-3">
        <h1>Préstamos Activos</h1>

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
