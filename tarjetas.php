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

<section class="container">
    <h2>Tarjetas de Crédito</h2>

    <!-- Formulario para agregar tarjeta de crédito -->
    <!--
    <form method="post" action="tarjetas.php">
        <input type="text" name="nombre" placeholder="Nombre de la tarjeta" required>
        <input type="number" step="0.01" name="limite" placeholder="Límite de crédito" required>
        <button type="submit" name="agregar_tarjeta">Agregar Tarjeta</button>
    </form>
    -->

    <table class="table" border="1">
        <tr>
            <th>Nombre</th>
            <th>Límite</th>
            <!--th>Acciones</th-->
        </tr>
        <?php foreach ($tarjetas as $row): ?>
            <tr>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['limite']; ?></td>
                <!--td>
                    <a href="tarjetas.php?eliminar=<?php echo $row['id']; ?>">Eliminar</a>
                </td-->
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="index.php">Volver al Inicio</a>
</section>
