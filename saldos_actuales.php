<?php
include 'config.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicializar filtros (mes y año)
$mes = isset($_POST['mes']) ? $_POST['mes'] : date('m');
$anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');

// Consulta inicial para obtener los saldos actuales
$sql_saldos = "
    SELECT b.nombre AS banco, sa.saldo AS saldo, fecha_registro
    FROM saldos_actuales sa
    JOIN bancos b ON sa.banco_id = b.banco_id
    WHERE sa.usuario_id = 1";
$result_saldos = mysqli_query($conn, $sql_saldos);
if (!$result_saldos) {
    die("Error en la consulta de saldos: " . mysqli_error($conn));
}

?>

<section class="container">
    <h3>Saldos Actualizados</h3>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Banco</th>
                <th>Saldo Actual</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_saldos && mysqli_num_rows($result_saldos) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_saldos)): ?>
                    <tr>
                        <td><?php echo $row['banco']; ?></td>
                        <td><?php echo number_format($row['saldo'], 2); ?></td>
                        <td><?php echo $row['fecha_registro']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">No se encontraron saldos actuales.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="index.php">Volver al Inicio</a>
</section>
