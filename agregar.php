<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $talla = $_POST['talla'];
    $color = $_POST['color'];
    $cantidad = $_POST['cantidad'];
    $ubicacion = $_POST['ubicacion'];
    $costo = $_POST['costo'];
    $precio_proveedor = $_POST['precio_proveedor'];
    $fecha_llegada = $_POST['fecha_llegada'];

    $sql = "INSERT INTO productos (id, nombre, talla, color, cantidad, ubicacion, costo, precio_proveedor, fecha_llegada, vendidos)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssidds", $id, $nombre, $talla, $color, $cantidad, $ubicacion, $costo, $precio_proveedor, $fecha_llegada);

    if ($stmt->execute()) {
        header('Location: inventario.php?mensaje=producto_agregado');
        exit;
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Agregar Producto</title>
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      background-color: #e6f3ff;
      font-family: 'Inter', sans-serif;
      color: #222;
      line-height: 1.5;
      padding: 20px;
    }
    .container {
      max-width: 600px;
      margin: 50px auto;
      background: #fff;
      padding: 30px 40px;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(0, 122, 204, 0.15);
    }
    h2 {
      text-align: center;
      color: #005c99;
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 30px;
    }
    form div {
      margin-bottom: 20px;
    }
    label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
      color: #007acc;
    }
    input[type="text"],
    input[type="number"],
    input[type="date"] {
      width: 100%;
      padding: 10px 14px;
      font-size: 1rem;
      border: 2px solid #007acc;
      border-radius: 8px;
      transition: border-color 0.3s ease;
    }
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus {
      outline: none;
      border-color: #005c99;
    }
    button {
      display: block;
      width: 100%;
      padding: 14px 0;
      background-color: #007acc;
      color: white;
      font-weight: 700;
      font-size: 1.1rem;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 0 6px 12px rgba(0, 122, 204, 0.3);
      transition: background-color 0.3s ease, transform 0.2s ease;
    }
    button:hover {
      background-color: #005c99;
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0, 92, 153, 0.5);
    }
    a {
      display: inline-block;
      margin-top: 25px;
      color: #007acc;
      font-weight: 600;
      text-decoration: none;
      transition: color 0.3s ease;
      text-align: center;
      width: 100%;
    }
    a:hover {
      color: #005c99;
    }
    .error {
      background-color: #ffd6d6;
      border: 1px solid #cc0000;
      padding: 10px 15px;
      border-radius: 8px;
      color: #a30000;
      margin-bottom: 20px;
      font-weight: 700;
    }

    /* Responsivo para móviles */
    @media (max-width: 480px) {
      body {
        padding: 10px;
      }

      .container {
        padding: 20px 18px;
        margin: 20px auto;
      }

      h2 {
        font-size: 1.5rem;
      }

      label {
        font-size: 0.95rem;
      }

      input[type="text"],
      input[type="number"],
      input[type="date"] {
        font-size: 1rem;
        padding: 9px 12px;
      }

      button {
        padding: 12px 0;
        font-size: 1rem;
      }

      a {
        font-size: 0.95rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>➕ Agregar Producto</h2>
    <form method="POST" action="">
      <div>
        <label for="id">ID:</label>
        <input type="number" id="id" name="id" placeholder="ID" required />
      </div>
      <div>
        <label for="nombre">Nombre del Producto:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre del Producto" required />
      </div>
      <div>
        <label for="talla">Talla:</label>
        <input type="text" id="talla" name="talla" placeholder="Talla" required />
      </div>
      <div>
        <label for="color">Color:</label>
        <input type="text" id="color" name="color" placeholder="Color" required />
      </div>
      <div>
        <label for="cantidad">Cantidad:</label>
        <input type="number" id="cantidad" name="cantidad" placeholder="Cantidad" required min="1" />
      </div>
      <div>
        <label for="ubicacion">Ubicación:</label>
        <input type="text" id="ubicacion" name="ubicacion" placeholder="Ubicación" required />
      </div>
      <div>
        <label for="costo">Costo:</label>
        <input type="number" id="costo" name="costo" placeholder="Costo" required min="0" step="0.01" />
      </div>
      <div>
        <label for="precio_proveedor">Precio de Proveedor:</label>
        <input type="number" id="precio_proveedor" name="precio_proveedor" placeholder="Precio de Proveedor" required min="0" step="0.01" />
      </div>
      <div>
        <label for="fecha_llegada">Fecha de llegada:</label>
        <input type="date" id="fecha_llegada" name="fecha_llegada" required />
      </div>
      <button type="submit">Guardar</button>
    </form>
    <a href="index.php">← Volver al inicio</a>
  </div>
</body>
</html>
