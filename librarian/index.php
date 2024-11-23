<?php
session_start();
require '../conexion.php';

// Crear instancia de la clase Conexion
$conexion = new Conexion();
$db = $conexion->getConexion();

$error = "";

// Verificar si se envió el formulario
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
    $sql = "SELECT bibliotecario_id, email, contrasena_hash, nombre FROM Bibliotecario WHERE email = :email";

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
            $_SESSION['bibliotecario_id'] = $user['bibliotecario_id'];
            $_SESSION['email'] = $user['email'];

     // Obtener el primer nombre (en caso de que tenga más de uno)
    $nombre = $user['nombre'];
     $primer_nombre = explode(' ', $nombre)[0]; // Solo el primer nombre

     // Guardar el primer nombre en la sesión
    $_SESSION['nombre'] = $primer_nombre;

     // Redirigir a la página de mostrar libros
    header("Location: mostrar_prestamos.php");
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión | Biblioteca</title>
    <!-- Incluir Bootstrap para estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir Font Awesome para íconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 70vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding-top: 130px;
        }
        .card {
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
        h1 {
            color: #343a40;
            text-align: center;
            margin-bottom: 2rem;
        }
        .btn-primary {
            background: linear-gradient(90deg, #dc143c, #b22222);;
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #b22222, #dc143c);
        }
        .register-link {
            color: #dc143c;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link:hover {
            text-decoration: underline;
        }
        label {
            font-weight: bold;
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background-color: #ffffff;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: #343a40;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }
        .back-button:hover {
            background-color: #dc143c;
            color: #ffffff;
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(37, 117, 252, 0.3);
        }
    </style>
</head>
<body>

    <!-- Flecha de Regresar -->
    <a href="javascript:history.back()" class="back-button">
        <i class="fas fa-arrow-left"></i>
    </a>

    <!-- Contenido principal -->
    <div class="container">
        <div class="card">
            <h1>Iniciar Sesión</h1>
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <!-- Formulario de Inicio de Sesión -->
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Ingrese su usuario" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="Ingrese su contraseña" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary w-100 mb-3">Iniciar Sesión</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Incluir JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
