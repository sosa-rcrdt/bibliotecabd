<?php
require '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_autor = $_POST['nombre_autor'];
    $fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $nacionalidad = !empty($_POST['nacionalidad']) ? $_POST['nacionalidad'] : null;

    try {
        // Crear instancia de la conexión
        $conexion = new Conexion();
        $db = $conexion->getConexion();

        // Preparar consulta SQL para insertar un nuevo autor
        $query = "INSERT INTO Autores (nombre, fecha_nacimiento, nacionalidad) VALUES (:nombre, :fecha_nacimiento, :nacionalidad)";
        $stmt = $db->prepare($query);

        $stmt->bindParam(':nombre', $nombre_autor, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento, PDO::PARAM_NULL | PDO::PARAM_STR);
        $stmt->bindParam(':nacionalidad', $nacionalidad, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // Establecer mensaje de éxito en la sesión
            session_start(); // Asegúrate de que la sesión esté iniciada
            $_SESSION['success_message'] = 'El autor fue agregado exitosamente.';
            header("Location: añadir_libro.php");
            exit;
        } else {
            echo "Error al guardar el autor.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
