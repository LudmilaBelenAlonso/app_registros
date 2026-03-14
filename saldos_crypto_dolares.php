<?php
include 'config.php';
<<<<<<< HEAD

function obtenerSaldos($conn, $usuario_id) {
    $stmt = $conn->prepare("SELECT b.nombre AS banco, mcd.descripcion as moneda, saldo_cd AS saldo, fecha_registro FROM saldos_crypto_dolares scd JOIN bancos b ON scd.banco_id = b.banco_id JOIN monedas_crypto_dolares mcd ON scd.moneda_id=mcd.moneda_id WHERE scd.usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $saldos = [];
    while ($row = $result->fetch_assoc()) {
        $saldos[] = $row;
    }
    $stmt->close();
    return $saldos;
}

$usuario_id = 1;
$saldos = obtenerSaldos($conn, $usuario_id);
?>
<section class="container">
    <h3>Saldos Crypto y Dólares</h3>
=======
//session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicializar filtros (mes y año)
$mes = isset($_POST['mes']) ? $_POST['mes'] : date('m');
$anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');

// Consulta inicial para obtener los saldos actuales
$sql_saldos = "
    SELECT b.nombre AS banco, mcd.descripcion as moneda , saldo_cd AS saldo, fecha_registro
    FROM saldos_crypto_dolares scd
    JOIN bancos b ON scd.banco_id = b.banco_id
    JOIN monedas_crypto_dolares mcd ON scd.moneda_id=mcd.moneda_id
    WHERE scd.usuario_id = 1";
$result_saldos = mysqli_query($conn, $sql_saldos);
if (!$result_saldos) {
    die("Error en la consulta de saldos: " . mysqli_error($conn));
}

?>

<section class="container">
    <h3>Saldos Actualizados</h3>
    
>>>>>>> 46e3041cf4e7ef0ce460df0df749844a78b8172c
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Banco</th>
                <th>Tipo Moneda</th>
                <th>Saldo Actual</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
<<<<<<< HEAD
            <?php if (count($saldos) > 0): ?>
                <?php foreach ($saldos as $row): ?>
=======
            <?php if ($result_saldos && mysqli_num_rows($result_saldos) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_saldos)): ?>
>>>>>>> 46e3041cf4e7ef0ce460df0df749844a78b8172c
                    <tr>
                        <td><?php echo $row['banco']; ?></td>
                        <td><?php echo $row['moneda']; ?></td>
                        <td><?php echo number_format($row['saldo'], 7); ?></td>
                        <td><?php echo $row['fecha_registro']; ?></td>
                    </tr>
<<<<<<< HEAD
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No se encontraron saldos actuales.</td>
=======
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">No se encontraron saldos actuales.</td>
>>>>>>> 46e3041cf4e7ef0ce460df0df749844a78b8172c
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<<<<<<< HEAD
=======

>>>>>>> 46e3041cf4e7ef0ce460df0df749844a78b8172c
    <a href="index.php">Volver al Inicio</a>
</section>
