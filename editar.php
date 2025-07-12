<?php
session_start();

// Validar sesión y rol admin
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Control de inactividad 5 minutos (300 segundos)
if (isset($_SESSION['ultimo_acceso']) && time() - $_SESSION['ultimo_acceso'] > 300) {
    session_unset();
    session_destroy();
    header("Location: login.php?mensaje=inactivo");
    exit;
}
$_SESSION['ultimo_acceso'] = time();

include 'conexion.php';

$mensaje = '';
$producto = null;

// Si se recibe el ID para cargar el producto
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
    } else {
        $mensaje = "Producto no encontrado con ID $id.";
    }
    $stmt->close();
}

// Si se envía el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nombre = $_POST['nombre'];
    $talla = $_POST['talla'];
    $color = $_POST['color'];
    $cantidad = intval($_POST['cantidad']);
    $ubicacion = $_POST['ubicacion'];
    $costo = floatval($_POST['costo']);
    $fecha_llegada = $_POST['fecha_llegada'];
    $vendidos = intval($_POST['vendidos']);
    $precio_proveedor = floatval($_POST['precio_proveedor']);

    $stmt = $conn->prepare("UPDATE productos SET nombre=?, talla=?, color=?, cantidad=?, ubicacion=?, costo=?, fecha_llegada=?, vendidos=?, precio_proveedor=? WHERE id=?");
    $stmt->bind_param("sssisdiddi", $nombre, $talla, $color, $cantidad, $ubicacion, $costo, $fecha_llegada, $vendidos, $precio_proveedor, $id);

    if ($stmt->execute()) {
        $mensaje = "Producto actualizado correctamente.";
        // Recargar datos para mostrar los valores actualizados
        $stmt->close();
        $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $producto = $resultado->fetch_assoc();
    } else {
        $mensaje = "Error al actualizar producto: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Producto - Uniformes Mari</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
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
      max-width: 500px;
      margin: 40px auto;
      background: #fff;
      padding: 30px 35px;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(0, 122, 204, 0.15);
    }
    h2 {
      color: #005c99;
      font-weight: 700;
      font-size: 2rem;
      text-align: center;
      margin-bottom: 25px;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 18px;
    }
    label {
      font-weight: 600;
      font-size: 1rem;
      text-align: left;
    }
    input[type="text"],
    input[type="number"],
    input[type="date"] {
      padding: 12px 15px;
      font-size: 1rem;
      border-radius: 8px;
      border: 1.8px solid #007acc;
      transition: border-color 0.3s ease;
      width: 100%;
    }
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus {
      border-color: #005c99;
      outline: none;
    }
    button {
      background-color: #007acc;
      color: white;
      padding: 14px 0;
      font-size: 1.1rem;
      font-weight: 700;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 0 6px 12px rgba(0, 122, 204, 0.3);
      transition: background-color 0.3s ease, transform 0.2s ease;
      margin-top: 10px;
    }
    button:hover {
      background-color: #005c99;
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(0, 92, 153, 0.5);
    }
    .mensaje {
      margin-top: 20px;
      font-weight: 600;
      font-size: 1rem;
      color: #007acc;
      text-align: center;
    }
    .buscar-form {
      margin-bottom: 30px;
      display: flex;
      gap: 10px;
      justify-content: center;
      flex-wrap: wrap;
    }
    .buscar-form input[type="number"] {
      width: 100px;
      border-radius: 8px;
      padding: 10px 12px;
      border: 1.8px solid #007acc;
      transition: border-color 0.3s ease;
      font-size: 1rem;
      flex-grow: 1;
      min-width: 140px;
    }
    .buscar-form input[type="number"]:focus {
      border-color: #005c99;
      outline: none;
    }
    .buscar-form button {
      background-color: #007acc;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 10px 18px;
      font-weight: 700;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
      flex-grow: 1;
      min-width: 100px;
    }
    .buscar-form button:hover {
      background-color: #005c99;
      transform: translateY(-2px);
    }
    a.volver {
      display: inline-block;
      margin-top: 25px;
      color: #007acc;
      font-weight: 600;
      text-decoration: none;
      text-align: center;
      width: 100%;
      transition: color 0.3s ease;
    }
    a.volver:hover {
      color: #005c99;
      text-decoration: underline;
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
      .buscar-form {
        flex-direction: column;
      }
      .buscar-form input[type="number"],
      .buscar-form button {
        width: 100%;
        min-width: unset;
        flex-grow: unset;
      }
      a.volver {
        font-size: 0.95rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>✏️ Editar Producto</h2>

    <form method="GET" class="buscar-form" action="">
      <input type="number" name="id" placeholder="Ingresa ID del producto" required />
      <button type="submit">Buscar</button>
    </form>

    <?php if ($producto): ?>
      <form method="POST" action="">
        <input type="hidden" name="id" value="<?= htmlspecialchars($producto['id']) ?>" />

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required />

        <label for="talla">Talla:</label>
        <input type="text" name="talla" id="talla" value="<?= htmlspecialchars($producto['talla']) ?>" required />

        <label for="color">Color:</label>
        <input type="text" name="color" id="color" value="<?= htmlspecialchars($producto['color']) ?>" required />

        <label for="cantidad">Cantidad:</label>
        <input type="number" name="cantidad" id="cantidad" min="0" value="<?= htmlspecialchars($producto['cantidad']) ?>" required />

        <label for="ubicacion">Ubicación:</label>
        <input type="text" name="ubicacion" id="ubicacion" value="<?= htmlspecialchars($producto['ubicacion']) ?>" required />

        <label for="costo">Costo:</label>
        <input type="number" step="0.01" min="0" name="costo" id="costo" value="<?= htmlspecialchars($producto['costo']) ?>" required />

        <label for="precio_proveedor">Precio Proveedor:</label>
        <input type="number" step="0.01" min="0" name="precio_proveedor" id="precio_proveedor" value="<?= htmlspecialchars($producto['precio_proveedor'] ?? '') ?>" required />

        <label for="fecha_llegada">Fecha de Llegada:</label>
        <input type="date" name="fecha_llegada" id="fecha_llegada" value="<?= htmlspecialchars($producto['fecha_llegada']) ?>" required />

        <label for="vendidos">Vendidos:</label>
        <input type="number" name="vendidos" id="vendidos" min="0" value="<?= htmlspecialchars($producto['vendidos']) ?>" required />

        <button type="submit">Actualizar Producto</button>
      </form>
    <?php elseif(isset($_GET['id'])): ?>
      <p class="mensaje">Producto no encontrado.</p>
    <?php endif; ?>

    <?php if ($mensaje): ?>
      <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <a href="index.php" class="volver">← Volver al Panel Principal</a>
  </div>
</body>
</html>
