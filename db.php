<?php
$conexion = new mysqli("localhost", "root", "", "alimentos_mascotas");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
