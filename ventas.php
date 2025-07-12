<?php
session_start();
if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] !== 'trabajador' && $_SESSION['rol'] !== 'admin')) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['ultimo_acceso']) && time() - $_SESSION['ultimo_acceso'] > 300) {
    session_unset();
    session_destroy();
    header("Location: login.php?mensaje=inactivo");
    exit;
}
$_SESSION['ultimo_acceso'] = time();

include 'conexion.php';

$mensaje = '';
$error = '';
$resumen = [];
$fecha = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto'])) {
    $productos = $_POST['producto'];
    $tallas = $_POST['talla'];
    $colores = $_POST['color'];
    $cantidades = $_POST['cantidad'];
    $precios = $_POST['precio'];
    $pagado = floatval($_POST['pagado']);
    $total = 0;
    $ventas_realizadas = [];

    foreach ($productos as $i => $producto) {
        $talla = $tallas[$i];
        $color = $colores[$i];
        $cantidad = intval($cantidades[$i]);
        $precio = floatval($precios[$i]);
        $subtotal = $precio * $cantidad;
        $total += $subtotal;

        $stmt = $conn->prepare("SELECT cantidad FROM productos WHERE nombre = ? AND talla = ? AND color = ?");
        $stmt->bind_param("sss", $producto, $talla, $color);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $error = "Producto no encontrado: $producto $talla $color";
            break;
        }
        $prod = $result->fetch_assoc();
        if ($cantidad > $prod['cantidad']) {
            $error = "Inventario insuficiente para $producto ($talla/$color). Disponible: " . $prod['cantidad'];
            break;
        }

        $ventas_realizadas[] = [
            'producto' => $producto,
            'talla' => $talla,
            'color' => $color,
            'cantidad' => $cantidad,
            'precio' => $precio,
            'subtotal' => $subtotal
        ];
    }

    if (!$error) {
        $cambio = $pagado - $total;
        if ($cambio < 0) {
            $error = "El monto pagado es insuficiente. Total: $" . number_format($total, 2);
        } else {
            $fecha = date("Y-m-d H:i:s");
            foreach ($ventas_realizadas as $v) {
                $stmt = $conn->prepare("INSERT INTO ventas (producto, talla, color, cantidad, pagado, cambio, fecha) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssidds", $v['producto'], $v['talla'], $v['color'], $v['cantidad'], $pagado, $cambio, $fecha);
                $stmt->execute();

                $stmt2 = $conn->prepare("UPDATE productos SET cantidad = cantidad - ?, vendidos = vendidos + ? WHERE nombre = ? AND talla = ? AND color = ?");
                $stmt2->bind_param("iisss", $v['cantidad'], $v['cantidad'], $v['producto'], $v['talla'], $v['color']);
                $stmt2->execute();
            }
            $mensaje = "Venta registrada exitosamente. Cambio: $" . number_format($cambio, 2);
            $resumen = $ventas_realizadas;
        }
    }
}

$alertas_bajo_stock = [];
$sql_stock = "SELECT nombre, talla, color, cantidad FROM productos WHERE cantidad < 3";
$res_stock = $conn->query($sql_stock);
while ($fila = $res_stock->fetch_assoc()) {
    $alertas_bajo_stock[] = "{$fila['nombre']} (Talla: {$fila['talla']}, Color: {$fila['color']}) - Quedan {$fila['cantidad']}";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Venta Múltiple</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background: #e6f3ff; padding: 20px; }
    .container { max-width: 900px; margin: auto; background: #fff; padding: 30px; border-radius: 14px; box-shadow: 0 10px 30px rgba(0, 122, 204, 0.15); }
    h2 { text-align: center; color: #005c99; }
    .row { display: flex; gap: 10px; margin-bottom: 10px; }
    .row input, .row select { padding: 8px; border: 1.5px solid #007acc; border-radius: 8px; flex: 1; }
    button { background: #007acc; color: #fff; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
    button:hover { background: #005c99; }
    .message { padding: 10px; font-weight: bold; border-radius: 8px; margin: 10px 0; }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #007acc; padding: 10px; text-align: center; }
    th { background-color: #f0f8ff; color: #005c99; }
    .total-section { margin-top: 20px; font-size: 1.1rem; }
    .total-section label { font-weight: bold; }
    .btn-add { margin-bottom: 15px; background-color: #00b894; }
    .alerta-flotante { position: fixed; bottom: 30px; right: 30px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 15px 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 9999; animation: deslizarArriba 0.5s ease-out; }
    .alerta-flotante ul { margin: 10px 0 0; padding-left: 18px; }
    .alerta-flotante .cerrar { float: right; font-weight: bold; cursor: pointer; font-size: 18px; margin-left: 10px; }
    @keyframes deslizarArriba { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .btn-regresar { background-color: #6c757d; color: white; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background-color 0.3s ease; margin-top: 20px; display: block; margin-left: auto; margin-right: auto; }
    .btn-regresar:hover { background-color: #495057; }
    .pago-container { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; font-weight: 600; color: #007acc; }
    .pago-container label { min-width: 130px; }
    #pagado { padding: 8px 12px; border: 1.5px solid #007acc; border-radius: 8px; width: 140px; font-weight: 600; font-size: 1rem; color: #003366; }
    #pagado:focus { outline: none; border-color: #005c99; box-shadow: 0 0 5px rgba(0, 92, 153, 0.7); }
  </style>
</head>
<body>
  <div class="container">
    <h2>🛒 Venta Múltiple de Productos</h2>

    <?php if ($mensaje): ?>
      <div class="message success"><?= htmlspecialchars($mensaje) ?></div>
    <?php elseif ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" oninput="actualizarTotales()">
      <div id="productos">
        <div class="row">
          <select name="producto[]" onchange="cargarPrecio(this)" required>
            <option value="">Producto</option>
            <?php
            $sql = "SELECT DISTINCT nombre FROM productos ORDER BY nombre";
            $res = $conn->query($sql);
            while ($row = $res->fetch_assoc()) {
              echo "<option value='" . htmlspecialchars($row['nombre']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
            }
            ?>
          </select>
          <input type="text" name="talla[]" placeholder="Talla" required oninput="cargarPrecio(this)">
          <input type="text" name="color[]" placeholder="Color" required oninput="cargarPrecio(this)">
          <input type="number" name="precio[]" placeholder="Precio Unitario" step="0.01" required readonly>
          <input type="number" name="cantidad[]" placeholder="Cantidad" min="1" required>
        </div>
      </div>

      <button type="button" class="btn-add" onclick="agregarFila()">➕ Agregar otro producto</button>

      <div class="total-section">
        <label>Total: $</label><span id="total">0.00</span><br><br>
        <div class="pago-container">
          <label for="pagado">Monto pagado: $</label>
          <input type="number" name="pagado" id="pagado" step="0.01" min="0" required oninput="actualizarTotales()">
        </div>
        <label>Cambio a entregar: $</label><span id="cambio">0.00</span>
      </div>

      <br>
      <button type="submit">Registrar Venta</button>
    </form>

    <?php if ($mensaje && $resumen): ?>
      <h3 style="text-align:center; color:#007acc;">Resumen de Productos Vendidos</h3>
      <p style="text-align:center; color:#333;">🕒 Fecha y hora de la venta: <strong><?= htmlspecialchars($fecha) ?></strong></p>
      <table>
        <thead>
          <tr><th>Producto</th><th>Talla</th><th>Color</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
          <?php $total = 0; foreach ($resumen as $r): $total += $r['subtotal']; ?>
          <tr>
            <td><?= htmlspecialchars($r['producto']) ?></td>
            <td><?= htmlspecialchars($r['talla']) ?></td>
            <td><?= htmlspecialchars($r['color']) ?></td>
            <td>$<?= number_format($r['precio'], 2) ?></td>
            <td><?= $r['cantidad'] ?></td>
            <td>$<?= number_format($r['subtotal'], 2) ?></td>
          </tr>
          <?php endforeach; ?>
          <tr><td colspan="5" style="text-align:right;">Total:</td><td>$<?= number_format($total, 2) ?></td></tr>
          <tr><td colspan="5" style="text-align:right;">Pagado:</td><td>$<?= number_format($pagado, 2) ?></td></tr>
          <tr><td colspan="5" style="text-align:right;">Cambio:</td><td>$<?= number_format($pagado - $total, 2) ?></td></tr>
        </tbody>
      </table>
    <?php endif; ?>

    <button class="btn-regresar" onclick="redirigirPorRol()">⬅️ Regresar al inicio</button>
  </div>

  <script>
    function agregarFila() {
      const contenedor = document.getElementById('productos');
      const fila = contenedor.firstElementChild.cloneNode(true);
      fila.querySelectorAll('input').forEach(input => input.value = '');
      fila.querySelector('select').selectedIndex = 0;
      contenedor.appendChild(fila);
    }

    function actualizarTotales() {
      const precios = document.querySelectorAll('input[name="precio[]"]');
      const cantidades = document.querySelectorAll('input[name="cantidad[]"]');
      let total = 0;
      for (let i = 0; i < precios.length; i++) {
        const precio = parseFloat(precios[i].value) || 0;
        const cantidad = parseInt(cantidades[i].value) || 0;
        total += precio * cantidad;
      }
      document.getElementById('total').textContent = total.toFixed(2);
      const pagado = parseFloat(document.getElementById('pagado').value) || 0;
      const cambio = pagado - total;
      document.getElementById('cambio').textContent = cambio >= 0 ? cambio.toFixed(2) : '0.00';
    }

    function cargarPrecio(element) {
      const row = element.closest('.row');
      const producto = row.querySelector('select[name="producto[]"]').value;
      const talla = row.querySelector('input[name="talla[]"]').value.trim();
      const color = row.querySelector('input[name="color[]"]').value.trim();
      const precioInput = row.querySelector('input[name="precio[]"]');

      if (producto && talla && color) {
        fetch(`obtener_precio.php?producto=${encodeURIComponent(producto)}&talla=${encodeURIComponent(talla)}&color=${encodeURIComponent(color)}`)
          .then(res => res.json())
          .then(data => {
            precioInput.value = data.precio !== undefined ? parseFloat(data.precio).toFixed(2) : '';
            actualizarTotales();
          })
          .catch(() => {
            precioInput.value = '';
            actualizarTotales();
          });
      } else {
        precioInput.value = '';
        actualizarTotales();
      }
    }

    function redirigirPorRol() {
      const rol = "<?= $_SESSION['rol'] ?>";
      if (rol === 'admin') {
        window.location.href = 'index.php';
      } else if (rol === 'trabajador') {
        window.location.href = 'trabajador.php';
      } else {
        window.location.href = 'login.php';
      }
    }
  </script>

  <?php if (!empty($alertas_bajo_stock)): ?>
  <div class="alerta-flotante" id="alertaStock">
    <span class="cerrar" onclick="document.getElementById('alertaStock').style.display='none';">&times;</span>
    <strong>⚠ Bajo inventario:</strong>
    <ul>
      <?php foreach ($alertas_bajo_stock as $a): ?>
      <li><?= htmlspecialchars($a) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <script>
    setTimeout(() => {
      const alerta = document.getElementById('alertaStock');
      if (alerta) alerta.style.display = 'none';
    }, 10000);
  </script>
  <?php endif; ?>
</body>
</html>
