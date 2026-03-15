<?php
include 'config.php';  // Incluir la configuración de la base de datos
//session_start();

/*Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}*/

/* Manejar la adición de nuevas tarjetas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar_tarjeta'])) {
    $nombre = $_POST['nombre'];
    $limite = $_POST['limite'];
    $usuario_id = $_SESSION['user_id'];

    $sql = "INSERT INTO tarjetas_credito (usuario_id, nombre, limite) VALUES ('$usuario_id', '$nombre', '$limite')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Tarjeta de crédito agregada correctamente.";
    } else {
        echo "Error al agregar tarjeta: " . $conn->error;
    }
}*/

/* Manejar la eliminación de tarjetas
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM tarjetas_credito WHERE id='$id' AND usuario_id='{$_SESSION['user_id']}'";
    
    if ($conn->query($sql) === TRUE) {
        echo "Tarjeta de crédito eliminada correctamente.";
    } else {
        echo "Error al eliminar tarjeta: " . $conn->error;
    }
}*/

// Obtener todas las tarjetas de crédito del usuario
$sql = "SELECT * FROM categorias WHERE usuario_id='1'";
$result = $conn->query($sql);
?>

<section class="container mt-4 mb-5">
    <h3 class="mb-4">Categorías</h3>
    
    <div class="table-responsive shadow">
        <table class="table table-striped table-hover table-dark text-center mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['tipo']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <a href="index.php" class="text-info">Volver al Inicio</a>
    </div>
</section>

