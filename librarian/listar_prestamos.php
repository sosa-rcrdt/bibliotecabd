<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit;
}

require '../conexion.php';
$conexion = new Conexion();
$db = $conexion->getConexion();

$usuario_id = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : 0;

// Verificar si el usuario existe
$sql_usuario = "SELECT nombre FROM Usuarios WHERE usuario_id = :usuario_id";
$stmt_usuario = $db->prepare($sql_usuario);
$stmt_usuario->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_usuario->execute();
$usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Obtener préstamos activos
$sql_prestamos = "
    SELECT prestamo_id, titulo, fecha_prestamo, fecha_devolucion
    FROM Prestamos
    JOIN Libros ON Prestamos.libro_id = Libros.libro_id
    WHERE usuario_id = :usuario_id AND devuelto = 0
";
$stmt_prestamos = $db->prepare($sql_prestamos);
$stmt_prestamos->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_prestamos->execute();
$prestamos = $stmt_prestamos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos Activos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Préstamos Activos de <?php echo htmlspecialchars($usuario['nombre']); ?></h1>
    <form action="procesar_devoluciones.php" method="POST">
        <input type="hidden" name="usuario_id" value="<?php echo $usuario_id; ?>">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Fecha de Préstamo</th>
                    <th>Fecha de Devolución</th>
                    <th>Devolver</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestamos as $prestamo) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($prestamo['prestamo_id']); ?></td>
                        <td><?php echo htmlspecialchars($prestamo['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($prestamo['fecha_prestamo']); ?></td>
                        <td><?php echo htmlspecialchars($prestamo['fecha_devolucion']); ?></td>
                        <td>
                            <input type="checkbox" name="prestamos[]" value="<?php echo $prestamo['prestamo_id']; ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Procesar Devoluciones</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
