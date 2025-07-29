<?php
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// 🔍 1. Buscar la imagen asociada al producto
$sqlImg = "SELECT imagen FROM productos WHERE id = $id";
$resultado = $conexion->query($sqlImg);

if ($resultado->num_rows == 1) {
    $producto = $resultado->fetch_assoc();

    // 🗑️ 2. Si hay imagen, eliminarla del servidor
    if ($producto['imagen'] && file_exists("uploads/" . $producto['imagen'])) {
        unlink("uploads/" . $producto['imagen']);
    }

    // 🧹 3. Eliminar el producto de la base
    $sqlDelete = "DELETE FROM productos WHERE id = $id";
    if ($conexion->query($sqlDelete) === TRUE) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error al eliminar el producto: " . $conexion->error;
    }
} else {
    echo "Producto no encontrado.";
}
?>
