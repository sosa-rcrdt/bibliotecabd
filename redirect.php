<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el tipo de usuario seleccionado
    $user_type = $_POST['user_type'];

    // Redirigir según el tipo de usuario
    if ($user_type === 'librarian') {
        header("Location: librarian/index.php");
        exit;
    } elseif ($user_type === 'user') {
        header("Location: user/index.html");
        exit;
    } else {
        echo "Tipo de usuario no válido.";
    }
} else {
    echo "Acceso no permitido.";
}
?>
