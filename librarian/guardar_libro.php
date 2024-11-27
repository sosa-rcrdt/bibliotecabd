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
// Verificar si se enviaron todos los campos requeridos
if (
    isset($_POST['titulo'], $_POST['isbn'], $_POST['fecha_publicacion'], $_POST['autores'], $_POST['generos'], $_POST['unidades'])
    && is_array($_POST['autores']) && is_array($_POST['generos'])
) {
    // Recibir los datos del formulario
    $titulo = trim($_POST['titulo']);
    $isbn = trim($_POST['isbn']);
    $fecha_publicacion = $_POST['fecha_publicacion'];
    $autores = $_POST['autores']; // Array de IDs de autores
    $generos = $_POST['generos']; // Array de IDs de géneros
    $unidades = intval($_POST['unidades']);
    try {
        // Iniciar una transacción
        $db->beginTransaction();
        // Insertar el libro en la tabla Libros
        $sql_libro = "
            INSERT INTO Libros (titulo, isbn, fecha_publicacion, unidades, disponible)
            VALUES (:titulo, :isbn, :fecha_publicacion, :unidades, 1)
        ";
        $stmt_libro = $db->prepare($sql_libro);
        $stmt_libro->execute([
            ':titulo' => $titulo,
            ':isbn' => $isbn,
            ':fecha_publicacion' => $fecha_publicacion,
            ':unidades' => $unidades
        ]);
        // Obtener el ID del libro recién insertado
        $libro_id = $db->lastInsertId();
        // Insertar las relaciones entre el libro y los autores en Libros_Autores
        $sql_autores = "INSERT INTO Libros_Autores (libro_id, autor_id) VALUES (:libro_id, :autor_id)";
        $stmt_autores = $db->prepare($sql_autores);
        foreach ($autores as $autor_id) {
            $stmt_autores->execute([
                ':libro_id' => $libro_id,
                ':autor_id' => $autor_id
            ]);
        }
        // Insertar las relaciones entre el libro y los géneros en Generos_Libros
        $sql_generos = "INSERT INTO Generos_Libros (libro_id, genero_id) VALUES (:libro_id, :genero_id)";
        $stmt_generos = $db->prepare($sql_generos);
        foreach ($generos as $genero_id) {
            $stmt_generos->execute([
                ':libro_id' => $libro_id,
                ':genero_id' => $genero_id
            ]);
        }
        // Confirmar la transacción
        $db->commit();
        // Redirigir al registro de libros con un mensaje de éxito
        header("Location: registro_libros.php?mensaje=Libro+guardado+con+éxito");
        exit;
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $db->rollBack();
        // Mostrar un mensaje de error
        echo "Error al guardar el libro: " . $e->getMessage();
    }
} else {
    // Redirigir de vuelta al formulario si faltan datos
    header("Location: añadir_libro.php?error=Faltan+datos+obligatorios");
    exit;
}
?>