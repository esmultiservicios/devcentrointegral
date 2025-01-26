<?php
include "../funtions.php";

// Crear conexión
$conn = connect_mysqli();

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT productos_id, nombre FROM productos WHERE estado = 1 AND categoria_producto_id = 1";
$result = $conn->query($sql);

$productos = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Eliminar espacios a la izquierda y derecha del campo 'nombre'
        $row['nombre'] = trim($row['nombre']);
        $productos[] = $row;
    }
}

$conn->close();

echo json_encode($productos);