<?php
include 'conexion.php';

$producto = $_GET['producto'] ?? '';
$talla = $_GET['talla'] ?? '';
$color = $_GET['color'] ?? '';

if ($producto && $talla && $color) {
    $stmt = $conn->prepare("SELECT costo FROM productos WHERE nombre = ? AND talla = ? AND color = ?");
    $stmt->bind_param("sss", $producto, $talla, $color);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['precio' => $row['costo']]);
    } else {
        echo json_encode(['error' => 'No encontrado']);
    }
} else {
    echo json_encode(['error' => 'Parámetros incompletos']);
}
?>
