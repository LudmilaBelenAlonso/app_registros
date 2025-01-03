<?php
include 'config.php';
session_start();

// Filtros desde el formulario
$filtro_tipo_pago = isset($_GET['filtro_tipo_pago']) ? $_GET['filtro_tipo_pago'] : '';
$filtro_banco = isset($_GET['filtro_banco']) ? $_GET['filtro_banco'] : '';
$filtro_tarjeta = isset($_GET['filtro_tarjeta']) ? $_GET['filtro_tarjeta'] : '';
$filtro_cuotas = isset($_GET['filtro_cuotas']) ? $_GET['filtro_cuotas'] : '';
$filtro_mes = isset($_GET['filtro_mes']) ? $_GET['filtro_mes'] : '';
$filtro_anio = isset($_GET['filtro_anio']) ? $_GET['filtro_anio'] : '';

// Consulta para obtener todas las categorías del usuario
$sql_gastos = "SELECT transaccion_id, t.monto, t.fecha, t.descripcion, c.nombre AS categoria, 
                      b.nombre AS banco, t.tipo_pago, tc.nombre AS tarjeta, 
                      t.cuotas, t.cuota_actual, t.fecha_cierre
               FROM transacciones t
               JOIN categorias c ON t.categoria_id = c.categoria_id
               LEFT JOIN bancos b ON t.banco_id = b.banco_id
               LEFT JOIN tarjetas_credito tc ON t.tarjeta_id = tc.tarjeta_id
               WHERE t.usuario_id = 1";

// Aplicar filtros si están seleccionados
if (!empty($filtro_tipo_pago)) {
    $sql_gastos .= " AND t.tipo_pago = '$filtro_tipo_pago'";
}

if (!empty($filtro_banco)) {
    $sql_gastos .= " AND t.banco_id = '$filtro_banco'";
}

if (!empty($filtro_tarjeta)) {
    $sql_gastos .= " AND t.tarjeta_id = '$filtro_tarjeta'";
}

if (!empty($filtro_cuotas)) {
    $sql_gastos .= " AND t.cuotas = '$filtro_cuotas'";
}

if (!empty($filtro_mes)) {
    $sql_gastos .= " AND MONTH(t.fecha_registro) = '$filtro_mes'";
}

if (!empty($filtro_anio)) {
    $sql_gastos .= " AND YEAR(t.fecha_registro) = '$filtro_anio'";
}

$result_gastos = $conn->query($sql_gastos);

// Consulta para obtener los bancos del usuario
$sql_bancos = "SELECT * FROM bancos WHERE usuario_id = 1";
$result_bancos = $conn->query($sql_bancos);

// Consulta para obtener las tarjetas del usuario
$sql_tarjetas = "SELECT * FROM tarjetas_credito WHERE usuario_id = 1";
$result_tarjetas = $conn->query($sql_tarjetas);
?>

<section class="container">
    <h3>Gastos Registrados</h3>
    
        <!-- Formulario de filtros -->
        <form method="GET" action="ver_gastos.php">
            <label for="filtro_tipo_pago">Filtrar por Tipo de Pago:</label>
            <select name="filtro_tipo_pago" class="form-control">
                <option value="">Todos</option>
                <option value="debito" <?php if ($filtro_tipo_pago == 'debito') echo 'selected'; ?>>Débito</option>
                <option value="credito" <?php if ($filtro_tipo_pago == 'credito') echo 'selected'; ?>>Crédito</option>
            </select>

            <label for="filtro_banco">Filtrar por Banco:</label>
            <select name="filtro_banco" class="form-control">
                <option value="">Todos</option>
                <?php while ($row_banco = $result_bancos->fetch_assoc()): ?>
                    <option value="<?php echo $row_banco['banco_id']; ?>" 
                        <?php if ($filtro_banco == $row_banco['banco_id']) echo 'selected'; ?>>
                        <?php echo $row_banco['nombre']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="filtro_tarjeta">Filtrar por Tarjeta de Crédito:</label>
            <select name="filtro_tarjeta" class="form-control">
                <option value="">Todas</option>
                <?php while ($row_tarjeta = $result_tarjetas->fetch_assoc()): ?>
                    <option value="<?php echo $row_tarjeta['tarjeta_id']; ?>" 
                        <?php if ($filtro_tarjeta == $row_tarjeta['tarjeta_id']) echo 'selected'; ?>>
                        <?php echo $row_tarjeta['nombre']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="filtro_cuotas">Filtrar por Cantidad de Cuotas:</label>
            <input type="number" name="filtro_cuotas" min="1" value="<?php echo $filtro_cuotas; ?>" class="form-control">

            <label for="filtro_mes">Filtrar por Mes:</label>
            <select name="filtro_mes" class="form-control">
                <option value="">Todos</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php if ($filtro_mes == $i) echo 'selected'; ?>>
                        <?php echo date("F", mktime(0, 0, 0, $i, 10)); // Nombre del mes ?>
                    </option>
                <?php endfor; ?>
            </select>

            <label for="filtro_anio">Filtrar por Año:</label>
            <select name="filtro_anio" class="form-control">
                <option value="">Todos</option>
                <?php 
                $currentYear = date('Y');
                for ($i = $currentYear; $i >= $currentYear - 10; $i--): ?>
                    <option value="<?php echo $i; ?>" <?php if ($filtro_anio == $i) echo 'selected'; ?>>
                        <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>

            <button class="btn btn-secondary btn-sm" type="submit">Aplicar Filtros</button>
        </form>

        <!-- Tabla de gastos -->
        <table class="table" border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Banco</th>
                    <th>Tipo de Pago</th>
                    <th>Tarjeta</th>
                    <th>Cuotas</th>
                    <th>Cuota Actual</th>
                    <th>Fecha Cierre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result_gastos->num_rows > 0): 
                while ($gasto = $result_gastos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $gasto['transaccion_id']; ?></td>
                        <td><?php echo $gasto['monto']; ?></td>
                        <td><?php echo $gasto['fecha']; ?></td>
                        <td><?php echo $gasto['descripcion']; ?></td>
                        <td><?php echo $gasto['categoria']; ?></td>
                        <td><?php echo $gasto['banco']; ?></td>
                        <td><?php echo ucfirst($gasto['tipo_pago']); ?></td>
                        <td><?php echo $gasto['tarjeta']; ?></td>
                        <td><?php echo $gasto['cuotas']; ?></td>
                        <td><?php echo $gasto['cuota_actual']; ?></td>
                        <td><?php echo $gasto['fecha_cierre']; ?></td>
                        <td><form method="POST" action="duplicar_gasto.php">
                                <input type="hidden" name="transaccion_id" value="<?php echo $gasto['transaccion_id']; ?>">
                                <button class="btn btn-secondary btn-sm" type="submit" name="duplicar">Duplicar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="9">No se han registrado gastos.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    <a href="index.php">Volver al Inicio</a>
</section>
