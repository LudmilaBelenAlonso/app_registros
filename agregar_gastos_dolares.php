<?php
include 'config.php';

// Manejo de errores
function mostrarError($mensaje) {
    echo "<div class='error'>" . htmlspecialchars($mensaje) . "</div>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria_id = $_POST['categoria_id'];
    $monto = floatval($_POST['monto']);
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $tipo_pago = $_POST['tipo_pago'];
    $banco_id = isset($_POST['banco_id']) ? intval($_POST['banco_id']) : null;
    $tarjeta_id = isset($_POST['tarjeta_id']) ? intval($_POST['tarjeta_id']) : null;
    $cuotas = isset($_POST['cuotas']) ? intval($_POST['cuotas']) : null;
    $cuota_actual = isset($_POST['cuota_actual']) ? intval($_POST['cuota_actual']) : null;
    $fecha_cierre = isset($_POST['fecha_cierre']) ? $_POST['fecha_cierre'] : null;
    $moneda_id = isset($_POST['moneda_id']) ? intval($_POST['moneda_id']) : null;

    $stmt = $conn->prepare("INSERT INTO transacciones_dolares (usuario_id, categoria_id, banco_id, tarjeta_id, moneda_id, monto, fecha, descripcion, tipo_pago, cuotas, cuota_actual, fecha_cierre, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $usuario_id = 1; // Reemplazar por $_SESSION['user_id'] en producción
    $stmt->bind_param("iiiidsssiiis", $usuario_id, $categoria_id, $banco_id, $tarjeta_id, $moneda_id, $monto, $fecha, $descripcion, $tipo_pago, $cuotas, $cuota_actual, $fecha_cierre);
    if (!$stmt->execute()) {
        mostrarError("Error al registrar gasto: " . $stmt->error);
    } else {
        echo "Gasto registrado correctamente.";
    }
    $stmt->close();
}

function obtenerCategorias($conn, $usuario_id) {
    $stmt = $conn->prepare("SELECT * FROM categorias WHERE usuario_id = ? AND tipo = 'gasto'");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $categorias = [];
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
    $stmt->close();
    return $categorias;
}
function obtenerBancos($conn, $usuario_id) {
    $stmt = $conn->prepare("SELECT * FROM bancos WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bancos = [];
    while ($row = $result->fetch_assoc()) {
        $bancos[] = $row;
    }
    $stmt->close();
    return $bancos;
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
function obtenerMonedas($conn) {
    $result = $conn->query("SELECT * FROM monedas_crypto_dolares");
    $monedas = [];
    while ($row = $result->fetch_assoc()) {
        $monedas[] = $row;
    }
    return $monedas;
}

$usuario_id = 1;
$categorias = obtenerCategorias($conn, $usuario_id);
$bancos = obtenerBancos($conn, $usuario_id);
$tarjetas = obtenerTarjetas($conn, $usuario_id);
$monedas = obtenerMonedas($conn);

?>
<section class="container">
    <h3>Agregar Gastos Dólares</h3>
    <form method="post" action="index.php?s=agregar_gastos_dolares">
        <label for="categoria_id">Categoría:</label>
        <select name="categoria_id" required class="form-control">
            <?php foreach ($categorias as $row): ?>
                <option value="<?php echo $row['categoria_id']; ?>"><?php echo $row['nombre']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="monto">Monto:</label>
        <input type="number" name="monto" step="0.01" required class="form-control">

        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" required class="form-control">

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" class="form-control"></textarea>

        <label for="tipo_pago">Tipo de Pago:</label>
        <select name="tipo_pago" id="tipo_pago" required onchange="toggleBancoTarjeta()" class="form-control">
            <option value="debito">Débito</option>
            <option value="credito">Crédito</option>
        </select>

        <div id="banco_debito" style="display:block;">
            <label for="banco_id_debito">Banco (Débito):</label>
            <select name="banco_id_debito" class="form-control">
                <?php foreach ($bancos as $row): ?>
                    <option value="<?php echo $row['banco_id']; ?>"><?php echo $row['nombre']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="banco_credito" style="display:none;">
            <label for="banco_id_credito">Banco (Crédito):</label>
            <select name="banco_id_credito" class="form-control">
                <?php foreach ($bancos as $row): ?>
                    <option value="<?php echo $row['banco_id']; ?>"><?php echo $row['nombre']; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="tarjeta_id">Tarjeta de Crédito:</label>
            <select name="tarjeta_id" class="form-control">
                <?php foreach ($tarjetas as $row): ?>
                    <option value="<?php echo $row['tarjeta_id']; ?>"><?php echo $row['nombre']; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="cuotas">Cantidad de Cuotas:</label>
            <input type="number" name="cuotas" min="1" class="form-control">

            <label for="cuota_actual">Cuota Actual:</label>
            <input type="number" name="cuota_actual" min="1" class="form-control">

            <label for="fecha_cierre">Fecha de Cierre (Crédito):</label>
            <input type="date" name="fecha_cierre" class="form-control">
        </div>

        <label for="moneda_id">Moneda:</label>
        <select name="moneda_id" class="form-control">
            <?php foreach ($monedas as $row): ?>
                <option value="<?php echo $row['moneda_id']; ?>"><?php echo $row['descripcion']; ?></option>
            <?php endforeach; ?>
        </select>

        <button class="btn btn-secondary btn-sm" type="submit">Registrar Gasto</button>
    </form>
    <a href="index.php">Volver al Inicio</a>
</section>

<script>
function toggleBancoTarjeta() {
    var tipoPago = document.getElementById("tipo_pago").value;
    var bancoDebitoDiv = document.getElementById("banco_debito");
    var tarjetaCreditoDiv = document.getElementById("banco_credito");

    if (tipoPago === "credito") {
        bancoDebitoDiv.style.display = "none";
        tarjetaCreditoDiv.style.display = "block";
    } else {
        bancoDebitoDiv.style.display = "block";
        tarjetaCreditoDiv.style.display = "none";
    }
}
</script>
