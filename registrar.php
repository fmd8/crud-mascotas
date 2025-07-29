<?php
include 'db.php';

$mensaje = "";
$errores = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $clave = $_POST['clave'];
    $clave2 = $_POST['clave2'];

    // Validaciones
    if (strlen($usuario) < 3) {
        $errores[] = "El usuario debe tener al menos 3 caracteres.";
    }

    if ($clave !== $clave2) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (strlen($clave) < 4) {
        $errores[] = "La contraseña debe tener al menos 4 caracteres.";
    }

    // Verificamos si el usuario ya existe
    $check = $conexion->query("SELECT * FROM usuarios WHERE usuario = '$usuario'");
    if ($check->num_rows > 0) {
        $errores[] = "Ese nombre de usuario ya está en uso.";
    }

    // Si no hay errores, lo creamos
    if (empty($errores)) {
        $claveHasheada = password_hash($clave, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (usuario, clave) VALUES ('$usuario', '$claveHasheada')";
        if ($conexion->query($sql) === TRUE) {
            header("Location: login.php?registro=1");
            exit;
        } else {
            $errores[] = "Error al crear el usuario: " . $conexion->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card p-4 shadow-sm">
        <h2 class="mb-4">📝 Registrar nuevo usuario</h2>

        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errores as $e): ?>
                        <li><?= $e ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nombre de usuario</label>
                <input type="text" name="usuario" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="clave" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Repetir contraseña</label>
                <input type="password" name="clave2" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Crear cuenta</button>
            <a href="login.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
</body>
</html>
