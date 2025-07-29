<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}
?>

<?php
include 'db.php';
$condicionBusqueda = "";
if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
    $busqueda = $conexion->real_escape_string($_GET['busqueda']);
    $condicionBusqueda = "WHERE productos.nombre LIKE '%$busqueda%' OR productos.marca LIKE '%$busqueda%' OR productos.tipo LIKE '%$busqueda%'";
}

$sql = "SELECT productos.*, categorias.nombre AS categoria 
        FROM productos 
        LEFT JOIN categorias ON productos.id_categoria = categorias.id
        $condicionBusqueda";


$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - Alimentos para Mascotas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="text-end mb-3">
        <a href="logout.php" class="btn btn-outline-danger">🔓 Cerrar sesión</a>
    </div>

    <h1 class="mb-4 text-center">🐶 Montañitas - Alimentos para Mascotas 🐱</h1>



    <div class="text-end mb-3">
        <a href="agregar.php" class="btn btn-success">➕ Agregar Producto</a>
    </div>

  <form method="GET" class="mb-4 d-flex justify-content-between">
    <div class="d-flex flex-grow-1 me-2">
        <input type="text" name="busqueda" class="form-control me-2" placeholder="Buscar producto..." value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>">
        <button type="submit" class="btn btn-primary me-2">Buscar</button>
        <?php if (isset($_GET['busqueda']) && $_GET['busqueda'] !== ''): ?>
            <a href="index.php" class="btn btn-outline-secondary">Limpiar</a>
        <?php endif; ?>
    </div>
</form>



    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Marca</th>
                <th>Imagen</th>
                <th>Tipo</th>
                <th>Precio</th>
                <th>Categoría</th>  
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
<?php if ($resultado->num_rows > 0): ?>
    <?php while($fila = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= $fila['id'] ?></td>
            <td><?= $fila['nombre'] ?></td>
            <td><?= $fila['marca'] ?></td>
            <td><?= $fila['tipo'] ?></td>
            <td>$<?= $fila['precio'] ?></td>
            <td><?= $fila['stock'] ?></td>
            <td><?= $fila['categoria'] ?></td>
            <td>
                <?php if ($fila['imagen']): ?>
                    <img src="uploads/<?= $fila['imagen'] ?>" alt="Imagen" width="80">
                <?php else: ?>
                    Sin imagen
                <?php endif; ?>
            </td>
            <td>
                <a href="editar.php?id=<?= $fila['id'] ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                <a href="eliminar.php?id=<?= $fila['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que querés eliminar este producto?')">🗑️ Eliminar</a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="9" class="text-center text-muted">🔍 No se encontraron productos que coincidan con la búsqueda.</td>
    </tr>
<?php endif; ?>

        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
