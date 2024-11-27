<?php
require '../conexion.php';

// Crear instancia de la clase Conexion
$conexion = new Conexion();
$db = $conexion->getConexion();

// Consulta SQL para obtener los libros con la información de autor y género
$sql = "
SELECT
    L.libro_id,
    L.titulo,
    L.isbn,
    L.fecha_publicacion,
    L.unidades,
    -- Concatenar los autores sin duplicados
    STUFF((
        SELECT DISTINCT ', ' + A.nombre
        FROM Libros_Autores LA
        JOIN Autores A ON LA.autor_id = A.autor_id
        WHERE LA.libro_id = L.libro_id
        FOR XML PATH('')
    ), 1, 2, '') AS autores,

    -- Concatenar los géneros sin duplicados
    STUFF((
        SELECT DISTINCT ', ' + G.nombre_genero
        FROM Generos_Libros GL
        JOIN Generos G ON GL.genero_id = G.genero_id
        WHERE GL.libro_id = L.libro_id
        FOR XML PATH('')
    ), 1, 2, '') AS generos

FROM
    Libros L
";

// Ejecutar la consulta
$stmt = $db->query($sql);

// Obtener todos los resultados
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver los resultados como JSON
echo json_encode($libros);
?>