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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    // Preparar y ejecutar la consulta para eliminar
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $mensaje = "Producto con ID $id eliminado correctamente.";
    } else {
        $mensaje = "Error al eliminar producto: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Eliminar Producto - Uniformes Mari</title>
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
      max-width: 480px;
      margin: 50px auto;
      background: #fff;
      padding: 30px 35px;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(0, 122, 204, 0.15);
      text-align: center;
    }
    h2 {
      color: #cc0000;
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 25px;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 20px;
      align-items: center;
    }
    label {
      font-weight: 600;
      font-size: 1rem;
      text-align: center;
      width: 100%;
    }
    input[type="number"] {
      padding: 12px 15px;
      font-size: 1rem;
      border-radius: 8px;
      border: 1.8px solid #007acc;
      transition: border-color 0.3s ease;
      width: 100%;
      max-width: 300px;
    }
    input[type="number"]:focus {
      border-color: #005c99;
      outline: none;
    }
    button {
      background-color: #cc0000;
      color: white;
      padding: 14px 0;
      font-size: 1.1rem;
      font-weight: 700;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 0 6px 12px rgba(204, 0, 0, 0.4);
      transition: background-color 0.3s ease, transform 0.2s ease;
      width: 100%;
      max-width: 320px;
    }
    button:hover {
      background-color: #a30000;
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(163, 0, 0, 0.6);
    }
    .mensaje {
      margin-top: 25px;
      font-weight: 600;
      font-size: 1rem;
      color: #007acc;
    }
    a.volver {
      display: inline-block;
      margin-top: 30px;
      color: #007acc;
      font-weight: 600;
      text-decoration: none;
      transition: color 0.3s ease;
      font-size: 1rem;
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
        margin: 30px auto;
        padding: 20px 18px;
      }
      h2 {
        font-size: 1.6rem;
      }
      label {
        font-size: 0.95rem;
      }
      input[type="number"] {
        font-size: 1rem;
        padding: 10px 12px;
        max-width: 100%;
      }
      button {
        font-size: 1rem;
        padding: 12px 0;
        max-width: 100%;
      }
      a.volver {
        font-size: 0.95rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>❌ Eliminar Producto</h2>
    <form method="POST" action="">
      <label for="id">Ingresa el ID del producto a eliminar:</label>
      <input type="number" name="id" id="id" min="1" placeholder="ID del producto" required />
      <button type="submit">Eliminar</button>
    </form>

    <?php if ($mensaje): ?>
      <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <a href="index.php" class="volver">← Volver al Panel Principal</a>
  </div>
</body>
</html>
