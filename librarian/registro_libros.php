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

require '../conexion.php';

// Crear instancia de la clase Conexion
$conexion = new Conexion();
$db = $conexion->getConexion();

// Consulta para obtener los libros con la cantidad de préstamos (VistaCantidadPrestamos)
$sql = "SELECT * FROM VistaCantidadPrestamos ORDER BY cantidad_prestamos DESC";

// Preparar la consulta
$stmt = $db->prepare($sql);

// Ejecutar la consulta
$stmt->execute();

// Obtener los resultados
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro De Usuarios - Libros Más Prestados</title>
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
        .table {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
            padding: 15px;
        }
        .table thead {
            background-color: #c51436;
            color: white;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }
        .table-bordered {
            border: 1px solid #ddd;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #ddd;
        }
        .table-hover tbody tr:hover {
            background-color: #ff1744;
            color: white;
        }
        .table-container {
            padding: 30px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
        <h1 class="mb-4">Libros Más Prestados</h1>

        <!-- Contenedor de la tabla -->
        <div class="table-container">
            <!-- Tabla que muestra los libros con mayor cantidad de préstamos -->
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ISBN</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Cantidad de Préstamos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Mostrar los resultados de la consulta en la tabla
                    foreach ($libros as $index => $libro) {
                        echo "<tr>";
                        echo "<td>" . ($index + 1) . "</td>";
                        echo "<td>" . htmlspecialchars($libro['isbn']) . "</td>";
                        echo "<td>" . htmlspecialchars($libro['titulo']) . "</td>";
                        echo "<td>" . htmlspecialchars($libro['autores']) . "</td>";
                        echo "<td>" . htmlspecialchars($libro['cantidad_prestamos']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
