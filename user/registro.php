<?php
session_start();

require '../conexion.php';

// Obtener la conexión a la base de datos
$conexion = new Conexion();
$db = $conexion->getConexion();

// Recibir los datos del formulario
$nombre = $_POST['nombre'];
$direccion = $_POST['direccion'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT); // Encriptar la contraseña

try {
    // Verificar si el correo ya existe
    $sql_check = "SELECT COUNT(*) AS total FROM Usuarios WHERE email = :email";
    $stmt = $db->prepare($sql_check);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado['total'] > 0) {
        echo "
            <script>
                alert('El correo electrónico ya está registrado. Por favor, utiliza otro.');
                window.location.href = 'registro.html';
            </script>
        ";
        exit;
    }

    // Insertar el nuevo usuario
    $sql_insert = "
        INSERT INTO Usuarios (nombre, direccion, telefono, email, contrasena_hash)
        VALUES (:nombre, :direccion, :telefono, :email, :contrasena)
    ";
    $stmt = $db->prepare($sql_insert);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':contrasena', $contrasena, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // Redirigir automáticamente al login si el registro es exitoso
        header("Location: buscar_prestamos.html");
        exit;
    } else {
        echo "
            <script>
                alert('Error en el registro. Intenta nuevamente.');
                window.location.href = 'registro.html';
            </script>
        ";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
