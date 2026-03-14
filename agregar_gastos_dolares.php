<?php
<<<<<<< HEAD
include 'config.php';

// Manejo de errores
function mostrarError($mensaje) {
    echo "<div class='error'>" . htmlspecialchars($mensaje) . "</div>";
}

=======
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
>>>>>>> 46e3041cf4e7ef0ce460df0df749844a78b8172c
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria_id = $_POST['categoria_id'];
    $monto = floatval($_POST['monto']);
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $tipo_pago = $_POST['tipo_pago'];
<<<<<<< HEAD
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

// Consulta para obtener categorías, bancos, tarjetas y monedas
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

// Formulario HTML (simplificado)
?>
<section class="container">
    <h3>Agregar Gastos Dólares</h3>
    <form method="post" action="agregar_gastos_dolares.php">
        <label for="categoria_id">Categoría:</label>
        <select name="categoria_id" required>
            <?php foreach ($categorias as $row): ?>
                <option value="<?php echo $row['categoria_id']; ?>"><?php echo $row['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
        <label for="monto">Monto:</label>
        <input type="number" name="monto" step="0.01" required>
        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" required>
        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion"></textarea>
        <label for="tipo_pago">Tipo de Pago:</label>
        <select name="tipo_pago" required>
            <option value="debito">Débito</option>
            <option value="credito">Crédito</option>
        </select>
        <label for="banco_id">Banco:</label>
        <select name="banco_id">
            <?php foreach ($bancos as $row): ?>
                <option value="<?php echo $row['banco_id']; ?>"><?php echo $row['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
        <label for="tarjeta_id">Tarjeta de Crédito:</label>
        <select name="tarjeta_id">
            <?php foreach ($tarjetas as $row): ?>
                <option value="<?php echo $row['tarjeta_id']; ?>"><?php echo $row['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
        <label for="moneda_id">Moneda:</label>
        <select name="moneda_id">
            <?php foreach ($monedas as $row): ?>
                <option value="<?php echo $row['moneda_id']; ?>"><?php echo $row['descripcion']; ?></option>
            <?php endforeach; ?>
        </select>
        <label for="cuotas">Cantidad de Cuotas:</label>
        <input type="number" name="cuotas" min="1">
        <label for="cuota_actual">Cuota Actual:</label>
        <input type="number" name="cuota_actual" min="1">
        <label for="fecha_cierre">Fecha de Cierre:</label>
        <input type="date" name="fecha_cierre">
        <button type="submit">Registrar Gasto</button>
    </form>
    <a href="index.php">Volver al Inicio</a>
</section>
=======
    $banco_id = null;
    $tarjeta_id = null;
    $cuotas = isset($_POST['cuotas']) ? intval($_POST['cuotas']) : null;
    $cuota_actual = isset($_POST['cuota_actual']) ? intval($_POST['cuota_actual']) : null;
    $fecha_cierre = null;
    $moneda_id = $_POST['moneda_id'];

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
        $sql_ver_saldo = "SELECT saldo_cd FROM saldos_crypto_dolares WHERE banco_id = $banco_id AND moneda_id = $moneda_id";
        $resultado_saldo = mysqli_query($conn, $sql_ver_saldo);

        if ($resultado_saldo && mysqli_num_rows($resultado_saldo) > 0) {
            $fila_saldo = mysqli_fetch_assoc($resultado_saldo);
            $saldo_anterior = floatval($fila_saldo['saldo_cd']);

            // Actualizar el saldo restando el monto
            $sql_actualizar_saldo = "UPDATE saldos_crypto_dolares 
                                     SET saldo_cd = $saldo_anterior - $monto
                                     WHERE banco_id = $banco_id AND moneda_id = $moneda_id";

            if (!mysqli_query($conn, $sql_actualizar_saldo)) {
                echo "Error al actualizar el saldo: " . mysqli_error($conn);
            }
        } else {
            echo "No se encontró un saldo para el banco_id: $banco_id<br>";
        }
    }

    // Inserción de gasto en la tabla transacciones
    $sql = "INSERT INTO transacciones_dolares (usuario_id, categoria_id, banco_id, tarjeta_id, moneda_id, monto, fecha, descripcion, tipo_pago, cuotas, cuota_actual, fecha_cierre, timestamp)
            VALUES (
                1, 
                '$categoria_id', 
                " . ($banco_id !== null ? "'$banco_id'" : "NULL") . ",
                " . ($tarjeta_id !== null ? "'$tarjeta_id'" : "NULL") . ",
                " . ($moneda_id !== null ? "'$moneda_id'" : "NULL") . ",
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

// Consulta para obtener todas las monedas disponibles
$sql_monedas = "SELECT * FROM monedas_crypto_dolares";
$result_monedas = $conn->query($sql_monedas);

// Consulta para obtener todos los bancos (débito)
$sql_bancos = "SELECT * FROM bancos WHERE usuario_id='1' AND debito = 1";
$result_bancos = $conn->query($sql_bancos);

// Consulta para obtener todos los bancos (crédito)
$sql_tarjetas_bancos = "SELECT * FROM bancos WHERE usuario_id='1' AND credito = 1";
$result_tarjetas_bancos = $conn->query($sql_tarjetas_bancos);

// Consulta para obtener todas las tarjetas de crédito
$sql_tarjetas = "SELECT * FROM tarjetas_credito WHERE usuario_id='1'";
$result_tarjetas = $conn->query($sql_tarjetas);
?>

<section class="container">
    <h3>Agregar Gastos</h3>

    <form method="post" action="index.php?s=agregar_gastos_dolares">
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
        
        <label for="moneda_id">Moneda:</label>
        <select name="moneda_id" required class="form-control">
            <?php while ($row = $result_monedas->fetch_assoc()): ?>
                <option value="<?php echo $row['moneda_id']; ?>"><?php echo $row['descripcion']; ?></option>
            <?php endwhile; ?>
        </select>

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
>>>>>>> 46e3041cf4e7ef0ce460df0df749844a78b8172c
