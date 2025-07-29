
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}
?>

<?php
include 'db.php';

$errores = [];

// Traer las categorías desde la base
$categorias = $conexion->query("SELECT * FROM categorias");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $marca = trim($_POST['marca']);
    $tipo = trim($_POST['tipo']);
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];

    // Validaciones
    if (empty($nombre) || strlen($nombre) < 2) {
        $errores[] = "El nombre es obligatorio y debe tener al menos 2 caracteres.";
    }

    if (empty($marca) || strlen($marca) < 2) {
        $errores[] = "La marca es obligatoria.";
    }

    if (empty($tipo) || strlen($tipo) < 2) {
        $errores[] = "El tipo es obligatorio.";
    }

    if (!is_numeric($precio) || $precio <= 0) {
        $errores[] = "El precio debe ser un número mayor a 0.";
    }

    if (!filter_var($stock, FILTER_VALIDATE_INT) || $stock < 0) {
        $errores[] = "El stock debe ser un número entero positivo.";
    }

    if (!filter_var($id_categoria, FILTER_VALIDATE_INT)) {
        $errores[] = "Seleccioná una categoría válida.";
    }
    $nombreImagen = null;

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $nombreOriginal = $_FILES['imagen']['name'];
    $temp = $_FILES['imagen']['tmp_name'];
    $nombreImagen = uniqid() . "_" . basename($nombreOriginal);
    move_uploaded_file($temp, "uploads/" . $nombreImagen);
}


        if (empty($errores)) {
        $sql = "INSERT INTO productos (nombre, marca, tipo, precio, stock, id_categoria, imagen)
                VALUES ('$nombre', '$marca', '$tipo', '$precio', '$stock', '$id_categoria', '$nombreImagen')";

        if ($conexion->query($sql) === TRUE) {
            header("Location: index.php");
            exit;
        } else {
            $errores[] = "Error al guardar en la base: " . $conexion->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">➕ Agregar nuevo producto</h2>
<?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errores as $e): ?>
                <li><?= $e ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required minlength="2" maxlength="100">
        </div>

        <div class="mb-3">
            <label class="form-label">Marca</label>
            <input type="text" name="marca" class="form-control" required minlength="2" maxlength="100">
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo (ej: perro, gato)</label>
            <input type="text" name="tipo" class="form-control" required minlength="2" maxlength="50">

        </div>

        <div class="mb-3">
            <label class="form-label">Precio</label>
            <input type="number" name="precio" class="form-control" required min="1" step="0.01">
        </div>
        <div class="mb-3">
    <label class="form-label">Imagen del producto</label>
    <input type="file" name="imagen" class="form-control" accept="image/*">
</div>


        <div class="mb-3">
             <label class="form-label">Categoría</label>
             <select name="id_categoria" class="form-select" required>
                <option value="">Seleccionar...</option>
                    <?php while($cat = $categorias->fetch_assoc()): ?>
                   <option value="<?= $cat['id'] ?>">
                     <?= $cat['nombre'] ?>
                 </option>
                         <?php endwhile; ?>
             </select>
        </div>


        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" required min="0" step="1">
        </div>

        <div class="d-flex justify-content-between">
            <a href="index.php" class="btn btn-secondary">⬅️ Volver</a>
            <button type="submit" class="btn btn-primary">Guardar Producto</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
