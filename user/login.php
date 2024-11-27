<?php
session_start();
require '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener las credenciales del formulario
    $email = $_POST['email'];
    $password = $_POST['contrasena'];

    // Validar que los campos no estén vacíos
    if (empty($email) || empty($password)) {
        echo "Correo o contraseña no proporcionados.";
        exit;
    }

    // Crear instancia de la clase Conexion
    $conexion = new Conexion();
    $db = $conexion->getConexion();

    // Consulta para verificar el correo y obtener el hash de la contraseña, y obtener el nombre
    $sql = "SELECT usuario_id, email, contrasena_hash, nombre FROM Usuarios WHERE email = :email";

    // Preparar la consulta
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el usuario
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe y la contraseña es correcta
    if ($user && password_verify($password, $user['contrasena_hash'])) {
        // Si las credenciales son correctas, iniciar la sesión
        $_SESSION['usuario_id'] = $user['usuario_id'];
        $_SESSION['email'] = $user['email'];

        // Obtener el primer nombre (en caso de que tenga más de uno)
        $nombre = $user['nombre'];
        $primer_nombre = explode(' ', $nombre)[0]; // Solo el primer nombre

        // Guardar el primer nombre en la sesión
        $_SESSION['nombre'] = $primer_nombre;

        // Redirigir a la página de mostrar libros
        header("Location: mostrar_libros.php");
        exit;
    } else {
        // Si las credenciales no son correctas
        echo "<div style='text-align:center; margin-top:20%; font-family:Arial; color:red;'>
            <h1>Acceso Denegado</h1>
            <p>Los datos ingresados no coinciden con los registros. Verifique que su correo electrónico y contraseña sean correctos.</p>
            <form action='index.php'>
                <button type='submit' style='
                    background-color: red;
                    color: white;
                    font-size: 16px;
                    padding: 12px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                '>Volver a Intentarlo</button>
            </form>
        </div>";
        exit;
    }
}
?>
