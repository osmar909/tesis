<?php
include 'conexion.php';

if (!isset($_GET['producto'])) {
    echo json_encode([]);
    exit;
}

$producto = $_GET['producto'];
$stmt = $conn->prepare("SELECT DISTINCT talla FROM productos WHERE nombre = ?");
$stmt->bind_param("s", $producto);
$stmt->execute();
$result = $stmt->get_result();

$tallas = [];
while ($row = $result->fetch_assoc()) {
    $tallas[] = $row['talla'];
}
echo json_encode($tallas);
