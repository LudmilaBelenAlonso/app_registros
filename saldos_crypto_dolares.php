<?php
include 'config.php';

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
            <?php if (count($saldos) > 0): ?>
                <?php foreach ($saldos as $row): ?>
                    <tr>
                        <td><?php echo $row['banco']; ?></td>
                        <td><?php echo $row['moneda']; ?></td>
                        <td><?php echo number_format($row['saldo'], 7); ?></td>
                        <td><?php echo $row['fecha_registro']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No se encontraron saldos actuales.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="index.php">Volver al Inicio</a>
</section>
