<?php
session_start();

// Validar sesión y rol admin
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Control de inactividad 60 segundos
if (isset($_SESSION['ultimo_acceso']) && time() - $_SESSION['ultimo_acceso'] > 60) {
    session_unset();
    session_destroy();
    header("Location: login.php?mensaje=inactivo");
    exit;
}
$_SESSION['ultimo_acceso'] = time();

include 'conexion.php';

// Filtros para fechas
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

$ventas = [];

if ($fecha_inicio && $fecha_fin) {
    $sql = "SELECT * FROM ventas WHERE fecha BETWEEN ? AND ? ORDER BY fecha DESC";
    if ($stmt = $conn->prepare($sql)) {
        $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
        $fecha_fin_completa = $fecha_fin . ' 23:59:59';

        $stmt->bind_param("ss", $fecha_inicio_completa, $fecha_fin_completa);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($fila = $result->fetch_assoc()) {
            $ventas[] = $fila;
        }
        $stmt->close();
    }
} else {
    $sql = "SELECT * FROM ventas ORDER BY fecha DESC";
    $result = $conn->query($sql);
    if ($result) {
        while ($fila = $result->fetch_assoc()) {
            $ventas[] = $fila;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Reporte de Ventas - Uniformes Mari</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      background-color: #e6f3ff;
      font-family: 'Inter', sans-serif;
      color: #222;
      line-height: 1.5;
    }
    .container {
      max-width: 1000px;
      margin: 40px auto;
      background: #fff;
      padding: 30px 40px;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(0, 122, 204, 0.15);
      overflow-x: auto;
    }
    h2 {
      color: #005c99;
      font-weight: 700;
      font-size: 2rem;
      text-align: center;
      margin-bottom: 25px;
    }
    form.filtros {
      margin-bottom: 30px;
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
    }
    input[type="date"] {
      padding: 10px 14px;
      font-size: 1rem;
      border-radius: 8px;
      border: 1.8px solid #007acc;
      transition: border-color 0.3s ease;
      min-width: 180px;
    }
    input[type="date"]:focus {
      border-color: #005c99;
      outline: none;
    }
    button {
      background-color: #007acc;
      color: white;
      padding: 12px 28px;
      font-weight: 700;
      font-size: 1rem;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
      box-shadow: 0 6px 12px rgba(0, 122, 204, 0.3);
    }
    button:hover {
      background-color: #005c99;
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0, 92, 153, 0.5);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
      margin-bottom: 40px;
      min-width: 700px; /* para que haya scroll horizontal si es necesario */
    }
    thead {
      background-color: #007acc;
      color: #fff;
    }
    th, td {
      padding: 14px 12px;
      text-align: center;
      font-size: 0.95rem;
      white-space: nowrap;
    }
    tbody tr:nth-child(even) {
      background-color: #f9fbff;
    }
    tbody tr:hover {
      background-color: #d9eaff;
      cursor: default;
      transition: background-color 0.3s ease;
    }
    .btn-volver {
      display: inline-block;
      padding: 12px 30px;
      background-color: #cc0000;
      color: white;
      font-weight: 700;
      border-radius: 10px;
      text-decoration: none;
      transition: background-color 0.3s ease;
      box-shadow: 0 5px 12px rgba(204, 0, 0, 0.6);
      user-select: none;
    }
    .btn-volver:hover {
      background-color: #a30000;
    }
    @media (max-width: 768px) {
      .container {
        padding: 20px 15px;
      }
      table, th, td {
        font-size: 0.85rem;
      }
      form.filtros {
        flex-direction: column;
        align-items: center;
      }
      input[type="date"], button {
        width: 90%;
        max-width: 350px;
        margin-bottom: 10px;
      }
      table {
        min-width: unset;
      }
      th, td {
        white-space: normal;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>🛒 Reporte de Ventas</h2>

    <form method="GET" class="filtros">
      <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" required />
      <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>" required />
      <button type="submit">Filtrar</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>ID Venta</th><th>Producto</th><th>Talla</th><th>Color</th><th>Cantidad</th>
          <th>Pagado</th><th>Cambio</th><th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php if(count($ventas) > 0): ?>
          <?php foreach($ventas as $v): ?>
            <tr>
              <td><?= htmlspecialchars($v['id']) ?></td>
              <td><?= htmlspecialchars($v['producto']) ?></td>
              <td><?= htmlspecialchars($v['talla']) ?></td>
              <td><?= htmlspecialchars($v['color']) ?></td>
              <td><?= htmlspecialchars($v['cantidad']) ?></td>
              <td>$<?= number_format($v['pagado'], 2) ?></td>
              <td>$<?= number_format($v['cambio'], 2) ?></td>
              <td><?= htmlspecialchars($v['fecha']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8">No hay ventas registradas para estas fechas.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <a href="index.php" class="btn-volver">← Volver al Panel Principal</a>
  </div>
</body>
</html>
