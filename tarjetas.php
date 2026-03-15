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

function mostrarMensaje($mensaje, $tipo = 'success') {
    $clase = $tipo === 'success' ? 'success' : 'error';
    echo "<div class='$clase'>" . htmlspecialchars($mensaje) . "</div>";
}

function obtenerTarjetas($conn, $usuario_id) {
    $stmt = $conn->prepare("SELECT * FROM tarjetas_credito WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tarjetas = [];
    while ($row = $result->fetch_assoc()) {
        $tarjetas[] = $row;
    }
    $stmt->close();
    return $tarjetas;
}

$usuario_id = 1; // Reemplazar por $_SESSION['user_id'] en producción
$tarjetas = obtenerTarjetas($conn, $usuario_id);
?>

<section class="container mt-4 mb-5">
    <h2 class="mb-4">Tarjetas de Crédito</h2>

    <div class="table-responsive shadow">
        <table class="table table-striped table-hover table-dark text-center mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Límite</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tarjetas as $row): ?>
                    <tr>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['limite']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        <a href="index.php" class="text-info">Volver al Inicio</a>
    </div>
</section>
