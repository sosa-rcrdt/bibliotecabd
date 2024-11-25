<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit;
}

// Incluir la conexión
require '../conexion.php';

// Crear instancia de la clase Conexion
$conexion = new Conexion();
$db = $conexion->getConexion();

// Obtener el ID del préstamo a actualizar
$prestamo_id = $_POST['prestamo_id'];

// Actualizar el estado del préstamo a "Devuelto"
$update_query = "UPDATE Prestamos SET estado = 'Devuelto' WHERE prestamo_id = :prestamo_id";
$stmt_update = $db->prepare($update_query);
$stmt_update->bindParam(':prestamo_id', $prestamo_id);

if ($stmt_update->execute()) {
    $_SESSION['success_message'] = "Préstamo marcado como devuelto correctamente.";
} else {
    $_SESSION['success_message'] = "Hubo un error al marcar el préstamo como devuelto.";
}

header("Location: mostrar_prestamos.php");
exit;
?>
