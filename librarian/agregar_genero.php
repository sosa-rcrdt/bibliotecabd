<?php
require '../conexion.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario
    $nombre_genero = isset($_POST['nombreGenero']) ? $_POST['nombreGenero'] : null;
    // Verificar si el nombre del género está presente
    if (!$nombre_genero) {
        echo json_encode(['success' => false, 'error' => 'El nombre del género es obligatorio.']);
        exit;
    }
    try {
        // Crear instancia de la conexión
        $conexion = new Conexion();
        $db = $conexion->getConexion();
        // Preparar la consulta SQL para insertar el nuevo género
        $query = "INSERT INTO Generos (nombre_genero) VALUES (:nombre_genero)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':nombre_genero', $nombre_genero);
        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Establecer mensaje de éxito en la sesión
            session_start(); // Asegúrate de que la sesión esté iniciada
            $_SESSION['success_message'] = 'El género fue agregado exitosamente.';
            header("Location: añadir_libro.php"); // Redirigir a la página de destino
            exit;
        } else {
            echo "Error al guardar el género.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>