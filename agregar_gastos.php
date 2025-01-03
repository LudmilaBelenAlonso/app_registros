<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';

// Verificar conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria_id = $_POST['categoria_id'];
    $monto = floatval($_POST['monto']);
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $tipo_pago = $_POST['tipo_pago'];
    $banco_id = null;
    $tarjeta_id = null;
    $cuotas = isset($_POST['cuotas']) ? intval($_POST['cuotas']) : null;
    $cuota_actual = isset($_POST['cuota_actual']) ? intval($_POST['cuota_actual']) : null;
    $fecha_cierre = null;

    // Manejar valores según el tipo de pago
    if ($tipo_pago === 'debito') {
        $banco_id = !empty($_POST['banco_id_debito']) ? intval($_POST['banco_id_debito']) : null;
    } elseif ($tipo_pago === 'credito') {
        $banco_id = !empty($_POST['banco_id_credito']) ? intval($_POST['banco_id_credito']) : null;
        $tarjeta_id = !empty($_POST['tarjeta_id']) ? intval($_POST['tarjeta_id']) : null;
        $fecha_cierre = !empty($_POST['fecha_cierre']) ? $_POST['fecha_cierre'] : null;
    }

    // Actualizar el saldo si es tipo de pago débito
    if ($tipo_pago === 'debito' && $banco_id !== null) {
        $sql_ver_saldo = "SELECT saldo FROM saldos_actuales WHERE banco_id = $banco_id";
        $resultado_saldo = mysqli_query($conn, $sql_ver_saldo);

        if ($resultado_saldo && mysqli_num_rows($resultado_saldo) > 0) {
            $fila_saldo = mysqli_fetch_assoc($resultado_saldo);
            $saldo_anterior = floatval($fila_saldo['saldo']);

            // Actualizar el saldo restando el monto
            $sql_actualizar_saldo = "UPDATE saldos_actuales 
                                     SET saldo = $saldo_anterior - $monto
                                     WHERE banco_id = $banco_id";

            if (!mysqli_query($conn, $sql_actualizar_saldo)) {
                echo "Error al actualizar el saldo: " . mysqli_error($conn);
            }
        } else {
            echo "No se encontró un saldo para el banco_id: $banco_id<br>";
        }
    }

    // Inserción de gasto en la tabla transacciones
    $sql = "INSERT INTO transacciones (usuario_id, categoria_id, banco_id, tarjeta_id, monto, fecha, descripcion, tipo_pago, cuotas, cuota_actual, fecha_cierre, timestamp)
            VALUES (
                1, 
                '$categoria_id', 
                " . ($banco_id !== null ? "'$banco_id'" : "NULL") . ",
                " . ($tarjeta_id !== null ? "'$tarjeta_id'" : "NULL") . ",
                '$monto', 
                '$fecha', 
                '$descripcion', 
                '$tipo_pago', 
                " . ($cuotas !== null ? "'$cuotas'" : "NULL") . ",
                " . ($cuota_actual !== null ? "'$cuota_actual'" : "NULL") . ",
                " . ($fecha_cierre !== null ? "'$fecha_cierre'" : "NULL") . ",
                NOW()
            )";

    if (!mysqli_query($conn, $sql)) {
        echo "Error al registrar gasto: " . mysqli_error($conn);
    } else {
        echo "Gasto registrado correctamente.";
    }
}

// Consulta para obtener todas las categorías del usuario
$sql_categorias = "SELECT * FROM categorias WHERE usuario_id='1' AND tipo='gasto'";
$result_categorias = $conn->query($sql_categorias);
if (!$result_categorias) {
    die("Error en la consulta de categorías: " . $conn->error);
}

// Consulta para obtener todos los bancos (débito)
$sql_bancos = "SELECT * FROM bancos WHERE usuario_id='1' AND deb_cred = 0";
$result_bancos = $conn->query($sql_bancos);

// Consulta para obtener todos los bancos (crédito)
$sql_tarjetas_bancos = "SELECT * FROM bancos WHERE usuario_id='1' AND deb_cred = 1";
$result_tarjetas_bancos = $conn->query($sql_tarjetas_bancos);

// Consulta para obtener todas las tarjetas de crédito
$sql_tarjetas = "SELECT * FROM tarjetas_credito WHERE usuario_id='1'";
$result_tarjetas = $conn->query($sql_tarjetas);
?>

<section class="container">
    <h3>Agregar Gastos</h3>

    <form method="post" action="index.php?s=agregar_gastos">
        <label for="categoria_id">Categoría:</label>
        <select name="categoria_id" required class="form-control">
            <?php while ($row = $result_categorias->fetch_assoc()): ?>
                <option value="<?php echo $row['categoria_id']; ?>"><?php echo $row['nombre']; ?></option>
            <?php endwhile; ?>
        </select>

        <label for="monto">Monto:</label>
        <input type="number" name="monto" step="0.01" required class="form-control">

        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" required class="form-control">

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" placeholder="Descripción" class="form-control"></textarea>
        
        <label for="tipo_pago">Tipo de Pago:</label>
        <select name="tipo_pago" id="tipo_pago" required onchange="toggleBancoTarjeta()" class="form-control">
            <option value="debito">Débito</option>
            <option value="credito">Crédito</option>
        </select>

        <div id="banco_debito" style="display:block;">
            <label for="banco_id_debito">Banco (Débito):</label>
            <select name="banco_id_debito" class="form-control">
                <?php while ($row = $result_bancos->fetch_assoc()): ?>
                    <option value="<?php echo $row['banco_id']; ?>"><?php echo $row['nombre']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div id="banco_credito" style="display:none;">
            <label for="banco_id_credito">Banco (Crédito):</label>
            <select name="banco_id_credito" class="form-control">
                <?php while ($row = $result_tarjetas_bancos->fetch_assoc()): ?>
                    <option value="<?php echo $row['banco_id']; ?>"><?php echo $row['nombre']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="tarjeta_id">Tarjeta de Crédito:</label>
            <select name="tarjeta_id" class="form-control">
                <?php while ($row = $result_tarjetas->fetch_assoc()): ?>
                    <option value="<?php echo $row['tarjeta_id']; ?>"><?php echo $row['nombre']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="cuotas">Cantidad de Cuotas:</label>
            <input type="number" name="cuotas" min="1" class="form-control">

            <label for="cuota_actual">Cuota Actual:</label>
            <input type="number" name="cuota_actual" min="1" class="form-control">
        
            <label for="fecha_cierre">Fecha de Cierre (Crédito):</label>
            <input type="date" name="fecha_cierre" class="form-control">
        </div>

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
