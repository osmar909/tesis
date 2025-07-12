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
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel Admin - Uniformes Mari</title>
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
      padding: 15px;
    }
    .container {
      max-width: 1200px;
      margin: 40px auto;
      background: #fff;
      padding: 30px 40px;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(0, 122, 204, 0.15);
      overflow-x: auto;
    }
    h2 {
      text-align: center;
      color: #005c99;
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 25px;
    }
    .acciones {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 18px;
      margin-bottom: 35px;
    }
    .btn-accion {
      background-color: #007acc;
      color: white;
      padding: 14px 28px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 1rem;
      text-decoration: none;
      box-shadow: 0 6px 14px rgba(0, 122, 204, 0.3);
      transition: background-color 0.3s ease, transform 0.2s ease;
      user-select: none;
      white-space: nowrap;
    }
    .btn-accion:hover {
      background-color: #005c99;
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(0, 92, 153, 0.5);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
      margin-bottom: 40px;
      min-width: 800px;
    }
    thead {
      background-color: #007acc;
      color: #fff;
    }
    th, td {
      padding: 16px 12px;
      text-align: center;
      font-size: 0.95rem;
      word-wrap: break-word;
    }
    tbody tr:nth-child(even) {
      background-color: #f9fbff;
    }
    tbody tr:hover {
      background-color: #d9eaff;
      cursor: default;
      transition: background-color 0.3s ease;
    }
    .btn-logout {
      display: block;
      width: max-content;
      margin: 0 auto;
      padding: 14px 32px;
      background-color: #cc0000;
      color: #fff;
      font-weight: 700;
      font-size: 1rem;
      border-radius: 10px;
      text-decoration: none;
      box-shadow: 0 5px 12px rgba(204, 0, 0, 0.6);
      transition: background-color 0.3s ease, transform 0.2s ease;
      user-select: none;
    }
    .btn-logout:hover {
      background-color: #a30000;
      transform: translateY(-2px);
      box-shadow: 0 7px 15px rgba(163, 0, 0, 0.8);
    }
    #buscar {
      width: 100%;
      padding: 10px 15px;
      margin-bottom: 20px;
      border-radius: 8px;
      border: 1.8px solid #007acc;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }
    #buscar:focus {
      border-color: #005c99;
      outline: none;
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .container {
        padding: 20px 20px;
      }
    }
    @media (max-width: 768px) {
      th, td {
        font-size: 0.85rem;
        padding: 12px 6px;
      }
      .btn-accion, .btn-logout {
        padding: 12px 20px;
        font-size: 0.95rem;
      }
      .acciones {
        gap: 12px;
      }
      table {
        min-width: 700px;
      }
    }
    @media (max-width: 480px) {
      .container {
        padding: 15px 10px;
      }
      h2 {
        font-size: 1.5rem;
      }
      .btn-accion {
        padding: 10px 16px;
        font-size: 0.9rem;
      }
      table {
        min-width: 600px;
        font-size: 0.85rem;
      }
      #buscar {
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>👑 Hola Lola - Uniformes Mari</h2>

    <section class="acciones">
      <a href="agregar.php" class="btn-accion">➕ Agregar Producto</a>
      <a href="editar.php" class="btn-accion">✏️ Editar Producto</a>
      <a href="eliminar.php" class="btn-accion">❌ Eliminar Producto</a>
      <a href="reportes.php" class="btn-accion">📄 Generar Reportes</a>
      <a href="ventas.php" class="btn-accion">🛒 Ventas de Productos</a>
      <a href="inventario.php" class="btn-accion">📋 Ver Inventario</a>
    </section>

    <input type="text" id="buscar" placeholder="Buscar producto..." aria-label="Buscar producto" />

    <div style="overflow-x:auto;">
      <table id="tabla-inventario" aria-label="Tabla de inventario">
        <thead>
          <tr>
            <th>ID</th><th>Nombre</th><th>Talla</th><th>Color</th>
            <th>Cantidad</th><th>Ubicación</th><th>Costo</th>
            <th>Fecha de Llegada</th><th>Vendidos</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT * FROM productos";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($fila = $result->fetch_assoc()) {
              echo "<tr>
                      <td>" . htmlspecialchars($fila['id']) . "</td>
                      <td>" . htmlspecialchars($fila['nombre']) . "</td>
                      <td>" . htmlspecialchars($fila['talla']) . "</td>
                      <td>" . htmlspecialchars($fila['color']) . "</td>
                      <td>" . htmlspecialchars($fila['cantidad']) . "</td>
                      <td>" . htmlspecialchars($fila['ubicacion']) . "</td>
                      <td>$" . number_format($fila['costo'], 2) . "</td>
                      <td>" . htmlspecialchars($fila['fecha_llegada']) . "</td>
                      <td>" . htmlspecialchars($fila['vendidos']) . "</td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='9'>No hay productos registrados.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <a href="cerrar_sesion.php" class="btn-logout">🔒 Cerrar sesión</a>
  </div>

<script>
  const inputBuscar = document.getElementById('buscar');
  const tabla = document.getElementById('tabla-inventario');
  const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

  inputBuscar.addEventListener('keyup', () => {
    const filtro = inputBuscar.value.toLowerCase();

    for (let fila of filas) {
      const textoFila = fila.textContent.toLowerCase();
      fila.style.display = textoFila.includes(filtro) ? '' : 'none';
    }
  });
</script>
</body>
</html>

