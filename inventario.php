<?php
include 'conexion.php';

$sql = "SELECT * FROM productos";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inventario de Productos</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #e6f3ff;
      margin: 0;
      padding: 20px;
      color: #222;
    }
    .container {
      max-width: 1200px;
      margin: auto;
      background: #fff;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 122, 204, 0.15);
    }
    h2 {
      text-align: center;
      color: #005c99;
      font-weight: 700;
      margin-bottom: 25px;
      font-size: 2rem;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      overflow-x: auto;
      min-width: 900px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 12px 10px;
      text-align: center;
      font-size: 0.95rem;
      word-wrap: break-word;
    }
    th {
      background-color: #007acc;
      color: white;
      font-weight: 600;
    }
    tbody tr:nth-child(even) {
      background-color: #f9fbff;
    }
    tbody tr:hover {
      background-color: #d9eaff;
      transition: background-color 0.3s ease;
    }
    a {
      color: #007acc;
      text-decoration: none;
      font-weight: 600;
      user-select: none;
    }
    a:hover {
      text-decoration: underline;
    }
    .volver {
      display: inline-block;
      margin-top: 25px;
      font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
      body, .container {
        padding: 15px 10px;
      }
      table {
        min-width: 600px;
        font-size: 0.85rem;
      }
      th, td {
        padding: 10px 6px;
      }
      h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>📦 Inventario de Productos</h2>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Talla</th>
          <th>Color</th>
          <th>Cantidad</th>
          <th>Ubicación</th>
          <th>Costo</th>
          <th>Precio Proveedor</th>
          <th>Fecha de llegada</th>
          <th>Vendidos</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($resultado->num_rows > 0) {
          while ($fila = $resultado->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($fila['id']) . "</td>
                    <td>" . htmlspecialchars($fila['nombre']) . "</td>
                    <td>" . htmlspecialchars($fila['talla']) . "</td>
                    <td>" . htmlspecialchars($fila['color']) . "</td>
                    <td>" . htmlspecialchars($fila['cantidad']) . "</td>
                    <td>" . htmlspecialchars($fila['ubicacion']) . "</td>
                    <td>$" . number_format($fila['costo'], 2) . "</td>
                    <td>$" . number_format($fila['precio_proveedor'], 2) . "</td>
                    <td>" . htmlspecialchars($fila['fecha_llegada']) . "</td>
                    <td>" . htmlspecialchars($fila['vendidos']) . "</td>
                    <td>
                      <a href='editar.php?id=" . urlencode($fila['id']) . "'>✏️ Editar</a> |
                      <a href='eliminar.php?id=" . urlencode($fila['id']) . "' onclick=\"return confirm('¿Estás seguro de eliminar este producto?');\">❌ Eliminar</a>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='11'>No hay productos en el inventario.</td></tr>";
        }
        $conn->close();
        ?>
      </tbody>
    </table>

    <a href="index.php" class="volver">← Volver al inicio</a>
  </div>
</body>
</html>
