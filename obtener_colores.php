<?php
include 'conexion.php';

if (!isset($_GET['producto']) || !isset($_GET['talla'])) {
    echo json_encode([]);
    exit;
}

$producto = $_GET['producto'];
$talla = $_GET['talla'];

$stmt = $conn->prepare("SELECT DISTINCT color FROM productos WHERE nombre = ? AND talla = ?");
$stmt->bind_param("ss", $producto, $talla);
$stmt->execute();
$result = $stmt->get_result();

$colores = [];
while ($row = $result->fetch_assoc()) {
    $colores[] = $row['color'];
}
echo json_encode($colores);
