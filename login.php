<?php
session_start();
include 'db.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows == 1) {
        $fila = $resultado->fetch_assoc();
        if (password_verify($clave, $fila['clave'])) {
            $_SESSION['usuario'] = $usuario;
            header("Location: index.php");
            exit;
        } else {
            $mensaje = "❌ Clave incorrecta";
        }
    } else {
        $mensaje = "❌ Usuario no encontrado";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card p-4 shadow-sm">
        <h2 class="mb-4">🔐 Iniciar sesión</h2>
<?php
if (isset($_GET['registro']) && $_GET['registro'] == 1) {
    echo '<div class="alert alert-success">✅ Usuario registrado con éxito. Ahora podés iniciar sesión.</div>';
}
if ($mensaje) {
    echo '<div class="alert alert-danger">' . $mensaje . '</div>';
}
?>


        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="usuario" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Clave</label>
                <input type="password" name="clave" class="form-control" required>
            </div>
            <div class="mt-3 text-center">
    <p>¿No tenés cuenta? <a href="registrar.php">Registrate acá</a></p>
</div>
            <button type="submit" class="btn btn-primary">Ingresar</button>
            
        </form>
    </div>
</div>
</body>
</html>
