<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Consulta segura para evitar SQL Injection
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ? AND password = ?");
    $stmt->bind_param("ss", $usuario, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['ultimo_acceso'] = time();

        // Después de validar usuario y contraseña:

if ($user['rol'] === 'admin') {
    header("Location: index.php");
} else if ($user['rol'] === 'trabajador') {
    header("Location: trabajador.php");
}
exit;


       
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Login - Uniformes Mari</title>
  
  <!-- Font Awesome CDN para íconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');
    body {
      font-family: 'Inter', sans-serif;
      background-color: #87CEEB; /* azul celeste */
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-container {
      background: #fff;
      padding: 35px 45px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      width: 380px;
      text-align: center;
      position: relative;
      box-sizing: border-box;
      max-width: 95vw; /* que no desborde en móviles */
    }
    .login-container h1 {
      font-size: 32px;
      color: #21618c;
      font-weight: 700;
      margin-bottom: 8px;
    }
    .login-container h2 {
      font-size: 22px;
      color: #2874a6;
      margin-bottom: 25px;
      font-weight: 600;
    }
    .input-group {
      position: relative;
      margin-bottom: 20px;
      text-align: left;
    }
    .input-group i {
      position: absolute;
      top: 12px;
      left: 12px;
      color: #2980b9;
      font-size: 18px;
    }
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px 12px 12px 38px;
      font-size: 16px;
      border: 1.8px solid #2980b9;
      border-radius: 7px;
      transition: border-color 0.3s ease;
      box-sizing: border-box;
    }
    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #1b4f72;
      outline: none;
      box-shadow: 0 0 6px #1b4f72;
    }
    button {
      width: 100%;
      padding: 14px;
      font-size: 18px;
      background-color: #2874a6;
      border: none;
      border-radius: 8px;
      color: white;
      font-weight: 700;
      cursor: pointer;
      transition: background-color 0.3s ease;
      box-shadow: 0 4px 8px rgba(40, 116, 166, 0.6);
    }
    button:hover {
      background-color: #1b4f72;
      box-shadow: 0 6px 15px rgba(27, 79, 114, 0.8);
    }
    .error {
      color: #e74c3c;
      margin-bottom: 20px;
      font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 480px) {
      .login-container {
        padding: 25px 30px;
        width: 90vw;
      }
      .login-container h1 {
        font-size: 26px;
      }
      .login-container h2 {
        font-size: 18px;
      }
      input[type="text"],
      input[type="password"] {
        font-size: 14px;
        padding: 10px 10px 10px 36px;
      }
      button {
        font-size: 16px;
        padding: 12px;
      }
      .input-group i {
        font-size: 16px;
        top: 10px;
        left: 10px;
      }
    }

    @media (max-width: 320px) {
      .login-container h1 {
        font-size: 22px;
      }
      .login-container h2 {
        font-size: 16px;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h1>Uniformes Mari</h1>
    <h2>Iniciar Sesión</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST" autocomplete="off">
      <div class="input-group">
        <i class="fa fa-user"></i>
        <input type="text" name="usuario" placeholder="Usuario" required autofocus />
      </div>
      <div class="input-group">
        <i class="fa fa-lock"></i>
        <input type="password" name="password" placeholder="Contraseña" required />
      </div>
      <button type="submit">Ingresar</button>
    </form>
  </div>
</body>
</html>
