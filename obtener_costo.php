<?php
include 'conexion.php';

if (!isset($_GET['producto']) || !isset($_GET['talla']) || !isset($_GET['color'])) {
    echo json_encode(['costo' => null]);
    exit;
}

$producto = $_GET['producto'];
$talla = $_GET['talla'];
$color = $_GET['color'];

$stmt = $conn->prepare("SELECT costo FROM productos WHERE nombre = ? AND talla = ? AND color = ?");
$stmt->bind_param("sss", $producto, $talla, $color);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['costo' => (float)$row['costo']]);
} else {
    echo json_encode(['costo' => null]);
}
