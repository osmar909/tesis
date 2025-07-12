<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'trabajador') {
    header("Location: login.php");
    exit;
}

// Control de inactividad 300 segundos (5 minutos)
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
  <title>Panel del Trabajador - Uniformes Mari</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    /* Reset básico */
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      background-color: #e6f3ff;
      font-family: 'Inter', sans-serif;
      color: #222;
      line-height: 1.5;
    }
    .container {
      max-width: 1100px;
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
      margin-bottom: 30px;
    }
    /* Botón principal para registrar venta */
    .btn-primary {
      display: block;
      width: max-content;
      margin: 0 auto 40px auto;
      padding: 14px 36px;
      background-color: #007acc;
      color: #fff;
      font-weight: 700;
      font-size: 1.1rem;
      border-radius: 10px;
      text-decoration: none;
      box-shadow: 0 6px 12px rgba(0, 122, 204, 0.3);
      transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .btn-primary:hover {
      background-color: #005c99;
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(0, 92, 153, 0.5);
    }
    /* Barra de búsqueda */
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
    /* Tabla */
    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
      margin-bottom: 40px;
      min-width: 700px; /* scroll horizontal si es necesario */
    }
    thead {
      background-color: #007acc;
      color: #fff;
    }
    th, td {
      padding: 16px 12px;
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
    /* Botón cerrar sesión */
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
    }
    .btn-logout:hover {
      background-color: #a30000;
      transform: translateY(-2px);
      box-shadow: 0 7px 15px rgba(163, 0, 0, 0.8);
    }
    /* Responsive */
    @media (max-width: 768px) {
      .container {
        padding: 20px 15px;
      }
      th, td {
        font-size: 0.85rem;
        padding: 12px 8px;
        white-space: normal;
      }
      .btn-primary, .btn-logout {
        padding: 12px 28px;
        font-size: 1rem;
      }
      table {
        min-width: unset;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>👷 Bienvenido, Trabajador</h2>

    <a href="ventas.php" class="btn-primary">🛒 Registrar Venta</a>

    <input type="text" id="buscar" placeholder="Buscar producto..." aria-label="Buscar producto" />

    <h3>📦 Inventario Actual</h3>
    <table id="tabla-inventario">
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
                    <td>{$fila['id']}</td>
                    <td>{$fila['nombre']}</td>
                    <td>{$fila['talla']}</td>
                    <td>{$fila['color']}</td>
                    <td>{$fila['cantidad']}</td>
                    <td>{$fila['ubicacion']}</td>
                    <td>$" . number_format($fila['costo'], 2) . "</td>
                    <td>{$fila['fecha_llegada']}</td>
                    <td>{$fila['vendidos']}</td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='9'>No hay productos registrados.</td></tr>";
        }
        ?>
      </tbody>
    </table>

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
