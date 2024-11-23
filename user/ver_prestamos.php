<?php
session_start();

require '../conexion.php';

// Obtener el correo del usuario logueado
$email = $_SESSION['email'];
$nombre_usuario = $_SESSION['nombre'];

// Crear instancia de la clase Conexion
$conexion = new Conexion();
$db = $conexion->getConexion();

// Consulta para obtener los reportes del usuario logueado (usando SELECT TOP)
$sql = "
    SELECT TOP (1000)
        R.reporte_id,
        R.usuario_id,
        R.libro_id,
        U.nombre AS usuario_nombre,
        L.titulo AS libro_titulo,
        R.fecha_prestamo,
        R.fecha_devolucion,
        R.estado,
        A.nombre AS autor
    FROM
        Reportes R
    JOIN
        Usuarios U ON R.usuario_id = U.usuario_id
    JOIN
        Libros L ON R.libro_id = L.libro_id
    JOIN
        Libros_Autores LA ON L.libro_id = LA.libro_id
    JOIN
        Autores A ON LA.autor_id = A.autor_id
    WHERE
        U.email = :email AND R.estado = 'activo'";

// Preparar la consulta
$stmt = $db->prepare($sql);
$stmt->bindParam(':email', $email, PDO::PARAM_STR);

// Ejecutar la consulta
$stmt->execute();

// Obtener los resultados
$prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reportes</title>
    <!-- Incluir Bootstrap para estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        h1 {
            color: #343a40;
            text-align: center;
            margin-top: 2rem;
        }
        .card {
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-title {
            color: #6a11cb;
            font-weight: bold;
        }
        .btn-primary {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #2575fc, #6a11cb);
        }
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            padding-left: 15px;
            background: linear-gradient(90deg, #6a11cb, #2575fc);
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
                        <a class="nav-link" href="mostrar_libros.php">Mostrar Libros</a>
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
        <h1>Mis Prestamos</h1>

        <!-- Sección de los reportes -->
        <div id="contenedor-reportes" class="mt-4">
            <?php
            if (empty($prestamos)) {
                echo "<p class='text-center text-muted'>No tienes reportes activos registrados.</p>";
            } else {
                foreach ($prestamos as $prestamo) {
                    // Verificar si la fecha de devolución está vacía o es NULL
                    $fechaDevolucion = empty($prestamo['fecha_devolucion']) ? "Sin Devolver" : htmlspecialchars($prestamo['fecha_devolucion']);
                    echo "
                        <div class='card'>
                            <div class='card-body'>
                                <h5 class='card-title'>📖 " . htmlspecialchars($prestamo['libro_titulo']) . "</h5>
                                <p class='card-text'>
                                    <strong>Autor:</strong> " . htmlspecialchars($prestamo['autor']) . "<br>
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
