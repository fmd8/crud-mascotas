<?php
include 'db.php';

$usuario = "admin";
$clave_plana = "1234";

$check = $conexion->query("SELECT * FROM usuarios WHERE usuario = '$usuario'");

if ($check->num_rows > 0) {
    echo "⚠️ El usuario '$usuario' ya existe. No se creó nuevamente.";
    exit;
}

$clave = password_hash($clave_plana, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (usuario, clave) VALUES ('$usuario', '$clave')";

if ($conexion->query($sql) === TRUE) {
    echo "✅ Usuario creado correctamente: $usuario / $clave_plana";
} else {
    echo "❌ Error al crear usuario: " . $conexion->error;
}
?>