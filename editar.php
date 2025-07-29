<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}
?>

<?php
include 'db.php';

$categorias = $conexion->query("SELECT * FROM categorias");


if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$sql = "SELECT * FROM productos WHERE id = $id";
$resultado = $conexion->query($sql);

if ($resultado->num_rows == 0) {
    echo "Producto no encontrado.";
    exit;
}

$producto = $resultado->fetch_assoc();

$errores = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $marca = trim($_POST['marca']);
    $tipo = trim($_POST['tipo']);
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];
$nombreImagen = $producto['imagen']; // conservar si no hay nueva

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    // Si ya había imagen, la eliminamos
    if ($producto['imagen'] && file_exists("uploads/" . $producto['imagen'])) {
        unlink("uploads/" . $producto['imagen']);
    }

    // Guardamos la nueva imagen
    $nombreOriginal = $_FILES['imagen']['name'];
    $temp = $_FILES['imagen']['tmp_name'];
    $nombreImagen = uniqid() . "_" . basename($nombreOriginal);
    move_uploaded_file($temp, "uploads/" . $nombreImagen);
}



    if (empty($nombre) || strlen($nombre) < 2) {
        $errores[] = "El nombre es obligatorio y debe tener al menos 2 caracteres.";
    }

    if (empty($marca) || strlen($marca) < 2) {
        $errores[] = "La marca es obligatoria y debe tener al menos 2 caracteres.";
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

    if (empty($errores)) {
        $sql = "UPDATE productos 
        SET nombre='$nombre', marca='$marca', tipo='$tipo', precio='$precio', stock='$stock', id_categoria='$id_categoria', imagen='$nombreImagen'
        WHERE id = $id";



        if ($conexion->query($sql) === TRUE) {
            header("Location: index.php");
            exit;
        } else {
            $errores[] = "Error al actualizar en la base: " . $conexion->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">✏️ Editar producto</h2>

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
            <input type="text" name="nombre" value="<?= $producto['nombre'] ?>" class="form-control" required minlength="2" maxlength="100">
        </div>

        <div class="mb-3">
            <label class="form-label">Marca</label>
            <input type="text" name="marca" value="<?= $producto['marca'] ?>" class="form-control" required minlength="2" maxlength="100">
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo</label>
            <input type="text" name="tipo" value="<?= $producto['tipo'] ?>" class="form-control" required minlength="2" maxlength="100">
        </div>

        <div class="mb-3">
    <label class="form-label">Categoría</label>
    <select name="id_categoria" class="form-select" required>
        <option value="">Seleccionar...</option>
        <?php while($cat = $categorias->fetch_assoc()): ?>
            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $producto['id_categoria'] ? 'selected' : '' ?>>
                <?= $cat['nombre'] ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>


        <div class="mb-3">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" class="form-control" required minlength="2" maxlength="100">
        </div>

        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" value="<?= $producto['stock'] ?>" class="form-control" required minlength="2" maxlength="100">
        </div>

        <div class="mb-3">
    <label class="form-label">Imagen actual:</label><br>
    <?php if ($producto['imagen']): ?>
        <img src="uploads/<?= $producto['imagen'] ?>" width="100">
    <?php else: ?>
        <p>Sin imagen</p>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label class="form-label">Subir nueva imagen (opcional):</label>
    <input type="file" name="imagen" class="form-control" accept="image/*">
</div>



        <div class="d-flex justify-content-between">
            <a href="index.php" class="btn btn-secondary">⬅️ Volver</a>
            <button type="submit" class="btn btn-primary">Actualizar Producto</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>