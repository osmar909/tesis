<?php
include 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Buscar los datos actuales
    $sql = "SELECT * FROM productos WHERE id = $id";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows === 1) {
        $producto = $resultado->fetch_assoc();
    } else {
        echo "<h3>❌ Producto no encontrado.</h3>";
        exit;
    }
} else {
    echo "<h3>⚠️ ID de producto no especificado.</h3>";
    exit;
}

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $talla = $_POST['talla'];
    $color = $_POST['color'];
    $cantidad = $_POST['cantidad'];
    $ubicacion = $_POST['ubicacion'];
    $costo = $_POST['costo'];
    $fecha_llegada = $_POST['fecha_llegada'];
    $vendidos = $_POST['vendidos'];

    $sql = "UPDATE productos SET 
            nombre='$nombre',
            talla='$talla',
            color='$color',
            cantidad=$cantidad,
            ubicacion='$ubicacion',
            costo=$costo,
            fecha_llegada='$fecha_llegada',
            vendidos=$vendidos
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<h3>✅ Producto actualizado correctamente.</h3>";
        echo "<a href='index.php'>🏠 Volver al Inicio</a>";
        exit;
    } else {
        echo "<h3>❌ Error al actualizar: " . $conn->error . "</h3>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Producto</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="container">
    <h2>✏️ Editar Producto</h2>
    <form method="POST">
      <label>Nombre:</label><br>
      <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required><br><br>

      <label>Talla:</label><br>
      <input type="text" name="talla" value="<?= htmlspecialchars($producto['talla']) ?>" required><br><br>

      <label>Color:</label><br>
      <input type="text" name="color" value="<?= htmlspecialchars($producto['color']) ?>" required><br><br>

      <label>Cantidad:</label><br>
      <input type="number" name="cantidad" value="<?= $producto['cantidad'] ?>" required><br><br>

      <label>Ubicación:</label><br>
      <input type="text" name="ubicacion" value="<?= htmlspecialchars($producto['ubicacion']) ?>" required><br><br>

      <label>Costo:</label><br>
      <input type="number" step="0.01" name="costo" value="<?= $producto['costo'] ?>" required><br><br>

      <label>Fecha de llegada:</label><br>
      <input type="date" name="fecha_llegada" value="<?= $producto['fecha_llegada'] ?>" required><br><br>

      <label>Vendidos:</label><br>
      <input type="number" name="vendidos" value="<?= $producto['vendidos'] ?>" required><br><br>

      <button type="submit">Guardar Cambios</button>
    </form>

    <br>
    <a href="index.php">← Volver al inicio</a>
  </div>
</body>
</html>
