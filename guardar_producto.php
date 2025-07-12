<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

// Obtener los datos del formulario
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$talla = $_POST['talla'];
$color = $_POST['color'];
$cantidad = $_POST['cantidad'];
$ubicacion = $_POST['ubicacion'];
$costo = $_POST['costo'];
$fecha = $_POST['fecha_llegada'];

// Crear la consulta SQL para insertar los datos
$sql = "INSERT INTO productos (id, nombre, talla, color, cantidad, ubicacion, costo, fecha_llegada)
        VALUES ('$id', '$nombre', '$talla', '$color', $cantidad, '$ubicacion', $costo, '$fecha')";

// Ejecutar la consulta
if ($conn->query($sql) === TRUE) {
    echo "<h3>✅ Producto agregado correctamente.</h3>";
    echo "<a href='agregar.html'>← Agregar otro producto</a><br>";
    echo "<a href='index.html'>🏠 Ir al inicio</a>";
} else {
    echo "<h3>❌ Error al guardar: " . $conn->error . "</h3>";
    echo "<a href='agregar.html'>← Intentar de nuevo</a>";
}

// Cerrar la conexión
$conn->close();
?>
