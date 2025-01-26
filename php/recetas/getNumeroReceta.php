<?php
session_start();
include "../funtions.php";

// Usar operador ternario para verificar si receta_id está presente en POST
$receta_id = isset($_POST['receta_id']) ? $_POST['receta_id'] : null;

// Si no está presente, devolver un error
if ($receta_id === null) {
    echo json_encode(array('error' => 'Faltan parámetros'));
    exit;
}

// Conexión a la base de datos
$mysqli = connect_mysqli(); 

// Usar sentencias preparadas para evitar inyecciones SQL
$consulta = "SELECT LPAD(receta_id, 6, '0') AS receta_numero 
             FROM recetas 
             WHERE receta_id = ?";

if ($stmt = $mysqli->prepare($consulta)) {
    $stmt->bind_param("i", $receta_id); // 'i' indica que el parámetro es un entero
    $stmt->execute();
    $result = $stmt->get_result();

    // Comprobar si la consulta devuelve resultados
    if ($result->num_rows > 0) {
        $consulta2 = $result->fetch_assoc();
        $datos = array(
            0 => $consulta2['receta_numero']
        );
        echo json_encode($datos);
    } else {
        echo json_encode(array('error' => 'Receta no encontrada'));
    }

    // Cerrar la sentencia
    $stmt->close();
} else {
    echo json_encode(array('error' => 'Error en la consulta SQL'));
}

// Cerrar la conexión
$mysqli->close();