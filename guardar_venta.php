<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fecha = $_POST['fecha_venta'];
    $costo = $_POST['costo'];
    $talla = $_POST['talla'];
    $id_producto = $_POST['id_producto'];

    // 1. Insertar venta en tabla ventas (asumiendo que existe)
    $sql_venta = "INSERT INTO ventas (id_producto, cantidad_vendida, fecha, hora, costo, talla)
                  VALUES (?, 1, ?, NOW(), ?, ?)";

    $stmt = $conn->prepare($sql_venta);
    $stmt->bind_param("isss", $id_producto, $fecha, $costo, $talla);

    if ($stmt->execute()) {
        // 2. Actualizar inventario: restar 1 a cantidad donde id = $id_producto
        $sql_inventario = "UPDATE productos SET cantidad = cantidad - 1, vendidos = vendidos + 1 WHERE id = ? AND cantidad > 0";
        $stmt2 = $conn->prepare($sql_inventario);
        $stmt2->bind_param("i", $id_producto);
        if ($stmt2->execute()) {
            echo "<h3>✅ Venta registrada y stock actualizado correctamente.</h3>";
            echo "<a href='ventas.php'>Registrar otra venta</a><br>";
            echo "<a href='index.php'>Volver al inicio</a>";
        } else {
            echo "<h3>❌ Venta registrada pero error al actualizar inventario: " . $conn->error . "</h3>";
            echo "<a href='ventas.php'>Intentar de nuevo</a>";
        }
        $stmt2->close();
    } else {
        echo "<h3>❌ Error al registrar la venta: " . $conn->error . "</h3>";
        echo "<a href='ventas.php'>Intentar de nuevo</a>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ventas.php");
    exit();
}
?>
