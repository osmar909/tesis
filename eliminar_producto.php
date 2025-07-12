<?php
include 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM productos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<h3>✅ Producto eliminado correctamente.</h3>";
    } else {
        echo "<h3 class='error'>❌ Error al eliminar: " . $conn->error . "</h3>";
    }
} else {
    echo "<h3 class='error'>⚠️ ID de producto no especificado.</h3>";
}

echo "<br><a href='eliminar.php' class='btn'>← Volver a Eliminar</a>";
echo " <a href='index.php' class='btn'>🏠 Ir al Inicio</a>";

$conn->close();
?>
