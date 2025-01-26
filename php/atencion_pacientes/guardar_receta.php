<?php
session_start(); 
include "../funtions.php";

// Obtener los datos del formulario
$pacientes_id = $_POST['receta_pacientes_id'];
$colaboradorId = $_POST['receta_colaboradorId'];
$servicioId = $_POST['receta_servicioId'];
$empresa_id = $_SESSION['empresa_id'];
$estado = 1;

$productos = $_POST['producto'];

$cantidad = $_POST['cantidad'];
$descripciones = $_POST['descripcion'];
$fecha = date('Y-m-d H:i:s'); // Obtener la fecha y hora actual

// Verificar que los datos no estén vacíos
if (!empty($pacientes_id) && !empty($productos) && !empty($descripciones)) {
    // Conexión a la base de datos
    $mysqli = connect_mysqli();

    // Verificar la conexión
    if ($mysqli->connect_error) {
        echo json_encode(["status" => "error", "message" => "Conexión fallida: " . $mysqli->connect_error]);
        exit();
    }

    // Iniciar una transacción
    $mysqli->begin_transaction();

    try {
        // Insertar en la tabla recetas
        $stmt_receta = $mysqli->prepare("INSERT INTO recetas (pacientes_id, colaborador_id , servicio_id , empresa_id, fecha, estado) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_receta->bind_param("iiiis", $pacientes_id, $colaboradorId, $servicioId, $empresa_id, $fecha, $estado);
        $stmt_receta->execute();
        $receta_id = $stmt_receta->insert_id; // Obtener el ID de la receta insertada

        // Preparar la consulta de inserción para receta_detalles
        $stmt_detalle = $mysqli->prepare("INSERT INTO receta_detalles (receta_id, productos_id, cantidad, descripcion) VALUES (?, ?, ?, ?)");

        // Iterar sobre los productos, cantidades y descripciones para insertarlos en la base de datos
        for ($i = 0; $i < count($productos); $i++) {
            $producto_id = $productos[$i];  // Cambié de $productos_id a $producto_id

            $cantidad_producto = $cantidad[$i];  // Cambié de $cantidad a $cantidad_producto
            $descripcion_producto = $descripciones[$i];  // Cambié de $descripcion a $descripcion_producto

            // Vincular los parámetros y ejecutar la consulta
            $stmt_detalle->bind_param("iiis", $receta_id, $producto_id, $cantidad_producto, $descripcion_producto);
            $stmt_detalle->execute();
        }

        // Confirmar la transacción
        $mysqli->commit();

        // Cerrar las declaraciones y la conexión
        $stmt_receta->close();
        $stmt_detalle->close();
        $mysqli->close();

        echo json_encode([
            "status" => "success",
            "message" => "Receta guardada exitosamente",
            "receta_id" => $receta_id // Enviar el ID de la receta
        ]);
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $mysqli->rollback();
        echo json_encode([
            "status" => "error", 
            "message" => "Error al guardar la receta: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Error: Los datos de la receta están incompletos"
    ]);
}