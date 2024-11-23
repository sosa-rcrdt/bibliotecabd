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
    A.nombre AS autor,
    STRING_AGG(G.nombre_genero, ', ') AS generos
FROM
    Libros L
JOIN
    Libros_Autores LA ON L.libro_id = LA.libro_id
JOIN
    Autores A ON LA.autor_id = A.autor_id
JOIN
    Generos_Libros GL ON L.libro_id = GL.libro_id
JOIN
    Generos G ON GL.genero_id = G.genero_id
WHERE
    L.disponible = 1  -- Solo mostrar los libros disponibles
GROUP BY
    L.libro_id, L.titulo, L.isbn, L.fecha_publicacion, L.unidades, A.nombre";  // Agrupar por todos los campos no agregados

// Ejecutar la consulta
$stmt = $db->query($sql);

// Obtener todos los resultados
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver los resultados como JSON
echo json_encode($libros);
?>