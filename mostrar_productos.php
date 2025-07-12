<?php
include 'conexion.php';

$sql = "SELECT * FROM productos";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inventario de Productos</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <div class="container">
    <h2>📦 Inventario Actual</h2>
    <table class="inventory-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Producto</th>
          <th>Talla</th>
          <th>Color</th>
          <th>Cantidad</th>
          <th>Ubicación</th>
          <th>Costo</th>
          <th>Llegada</th>
          <th>Vendidos</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($resultado->num_rows > 0) {
          while ($fila = $resultado->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($fila["id"]) . "</td>
                    <td>" . htmlspecialchars($fila["nombre"]) . "</td>
                    <td>" . htmlspecialchars($fila["talla"]) . "</td>
                    <td>" . htmlspecialchars($fila["color"]) . "</td>
                    <td>" . htmlspecialchars($fila["cantidad"]) . "</td>
                    <td>" . htmlspecialchars($fila["ubicacion"]) . "</td>
                    <td>$" . htmlspecialchars(number_format($fila["costo"], 2)) . "</td>
                    <td>" . htmlspecialchars($fila["fecha_llegada"]) . "</td>
                    <td>" . htmlspecialchars($fila["vendidos"]) . "</td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='9'>No hay productos en el inventario.</td></tr>";
        }
        $conn->close();
        ?>
      </tbody>
    </table>
    <br />
    <a href="index.html">← Volver al inicio</a>
  </div>
</body>
</html>
