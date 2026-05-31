<?php
include 'config.php';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria_id = $_POST['categoria_id'];
    $monto = isset($_POST['monto']) && is_numeric($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $tipo_pago = isset($_POST['tipo_pago']) ? $_POST['tipo_pago'] : 'debito';
    $banco_id = null;
    $tarjeta_id = null;

    if ($tipo_pago === 'debito') {
        $banco_id = !empty($_POST['banco_id_debito']) ? intval($_POST['banco_id_debito']) : null;
    } elseif ($tipo_pago === 'credito') {
        $banco_id = !empty($_POST['banco_id_credito']) ? intval($_POST['banco_id_credito']) : null;
        $tarjeta_id = !empty($_POST['tarjeta_id']) ? intval($_POST['tarjeta_id']) : null;
    }

    // Verificar si el monto es válido
    if ($monto <= 0) {
        echo "El monto debe ser un valor positivo.<br>";
        exit;
    }

    // Inserción de ingreso en la tabla ingresos
    $sql_ingreso = "INSERT INTO ingresos (usuario_id, categoria_id, banco_id, tipo_pago, tarjeta_id, monto, fecha, descripcion)
                    VALUES ('1', '$categoria_id', " . ($banco_id !== null ? "'$banco_id'" : "NULL") . ", '$tipo_pago', " . ($tarjeta_id !== null ? "'$tarjeta_id'" : "NULL") . ", '$monto', '$fecha', '$descripcion')";

    if (mysqli_query($conn, $sql_ingreso)) {
        echo "Ingreso registrado correctamente.<br>";

        // Solo actualizar el saldo si el tipo de pago es débito y hay un banco seleccionado
        if ($tipo_pago === 'debito' && $banco_id !== null) {
            // Comprobar si ya existe un saldo para este banco
            $sql_check_saldo = "SELECT saldo FROM saldos_actuales WHERE banco_id = '$banco_id'";
            $result_saldo = mysqli_query($conn, $sql_check_saldo);

            if ($result_saldo && mysqli_num_rows($result_saldo) > 0) {
                // Actualizar saldo sumando el nuevo monto
                $sql_update_saldo = "UPDATE saldos_actuales 
                                     SET saldo = saldo + $monto, fecha_registro = NOW() 
                                     WHERE banco_id = '$banco_id'";

                if (mysqli_query($conn, $sql_update_saldo)) {
                    echo "Saldo actualizado correctamente.<br>";
                } else {
                    echo "Error al actualizar el saldo: " . mysqli_error($conn) . "<br>";
                }
            } else {
                // Crear una nueva entrada en saldos_actuales si no existe
                $sql_insert_saldo = "INSERT INTO saldos_actuales (usuario_id, banco_id, saldo, fecha_registro)
                                     VALUES ('1', '$banco_id', '$monto', NOW())";

                if (mysqli_query($conn, $sql_insert_saldo)) {
                    echo "Saldo inicial registrado correctamente.<br>";
                } else {
                    echo "Error al registrar el saldo inicial: " . mysqli_error($conn) . "<br>";
                }
            }
        }
    } else {
        echo "Error al registrar el ingreso: " . mysqli_error($conn) . "<br>";
    }
}

// Consulta para obtener todas las categorías del usuario
$sql_categorias = "SELECT * FROM categorias WHERE usuario_id='1' AND tipo='ingreso'";
$result_categorias = $conn->query($sql_categorias);

// Consulta para obtener todos los bancos (débito)
$sql_bancos = "SELECT * FROM bancos WHERE usuario_id='1' AND (debito='1' OR banco_id='8')";
$result_bancos = $conn->query($sql_bancos);

// Consulta para obtener todos los bancos (crédito)
$sql_tarjetas_bancos = "SELECT * FROM bancos WHERE usuario_id='1' AND credito = 1";
$result_tarjetas_bancos = $conn->query($sql_tarjetas_bancos);

// Consulta para obtener todas las tarjetas de crédito
$sql_tarjetas = "SELECT * FROM tarjetas_credito WHERE usuario_id='1'";
$result_tarjetas = $conn->query($sql_tarjetas);
?>

<section class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card bg-dark text-white shadow">
                <div class="card-header border-bottom border-secondary">
                    <h3 class="mb-0">Agregar Ingresos</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?s=agregar_ingresos">
                        <div class="form-group">
                            <label for="categoria_id">Categoría:</label>
                            <select name="categoria_id" required class="form-control">
                                <?php while ($row = $result_categorias->fetch_assoc()): ?>
                                    <option value="<?php echo $row['categoria_id']; ?>"><?php echo $row['nombre']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="monto">Monto:</label>
                            <input type="number" step="0.01" name="monto" placeholder="Monto" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="fecha">Fecha:</label>
                            <input type="date" name="fecha" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción:</label>
                            <textarea name="descripcion" placeholder="Descripción" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="tipo_pago">Tipo de Pago:</label>
                            <select name="tipo_pago" id="tipo_pago" required onchange="toggleBancoTarjeta()" class="form-control">
                                <option value="debito">Débito</option>
                                <option value="credito">Crédito</option>
                            </select>
                        </div>

                        <div id="banco_debito" class="form-group" style="display:block;">
                            <label for="banco_id_debito">Banco (Débito):</label>
                            <select name="banco_id_debito" class="form-control">
                                <?php while ($row = $result_bancos->fetch_assoc()): ?>
                                    <option value="<?php echo $row['banco_id']; ?>"><?php echo $row['nombre']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div id="banco_credito" style="display:none;">
                            <div class="form-group">
                                <label for="banco_id_credito">Banco (Crédito):</label>
                                <select name="banco_id_credito" class="form-control">
                                    <?php while ($row = $result_tarjetas_bancos->fetch_assoc()): ?>
                                        <option value="<?php echo $row['banco_id']; ?>"><?php echo $row['nombre']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="tarjeta_id">Tarjeta de Crédito:</label>
                                <select name="tarjeta_id" class="form-control">
                                    <?php while ($row = $result_tarjetas->fetch_assoc()): ?>
                                        <option value="<?php echo $row['tarjeta_id']; ?>"><?php echo $row['nombre']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <button class="btn btn-secondary btn-block mt-4" type="submit">Registrar Ingreso</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="index.php" class="text-info">Volver al Inicio</a>
                </div>
            </div>
        </div>
    </div>
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
