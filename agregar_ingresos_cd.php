<?php
include 'config.php';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria_id = $_POST['categoria_id'];
    $monto = isset($_POST['monto']) && is_numeric($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $banco_id = $_POST['banco_id'];
    $moneda_id = $_POST['moneda_id'];

    // Verificar si el monto es válido
    if ($monto <= 0) {
        echo "El monto debe ser un valor positivo.<br>";
        exit;
    }

    // Inserción de ingreso en la tabla ingresos_crypto_dolares
    $sql_ingreso = "INSERT INTO ingresos_crypto_dolares (usuario_id, categoria_id, banco_id, moneda_id, monto, fecha, descripcion)
                    VALUES ('1', '$categoria_id', '$banco_id', '$moneda_id', '$monto', '$fecha', '$descripcion')";

    if (mysqli_query($conn, $sql_ingreso)) {
        echo "Ingreso registrado correctamente.<br>";

        // Comprobar si ya existe un saldo para este banco y moneda
        $sql_check_saldo = "SELECT saldo_cd FROM saldos_crypto_dolares WHERE banco_id = '$banco_id' AND moneda_id = '$moneda_id'";
        $result_saldo = mysqli_query($conn, $sql_check_saldo);

        if ($result_saldo && mysqli_num_rows($result_saldo) > 0) {
            // Actualizar saldo sumando el nuevo monto
            $sql_update_saldo = "UPDATE saldos_crypto_dolares 
                                 SET saldo_cd = saldo_cd + $monto, fecha_registro = NOW() 
                                 WHERE banco_id = '$banco_id' AND moneda_id = '$moneda_id'";

            if (mysqli_query($conn, $sql_update_saldo)) {
                echo "Saldo actualizado correctamente.<br>";
            } else {
                echo "Error al actualizar el saldo: " . mysqli_error($conn) . "<br>";
            }
        } else {
            // Crear una nueva entrada en saldos_crypto_dolares si no existe
            $sql_insert_saldo = "INSERT INTO saldos_crypto_dolares (usuario_id, banco_id, moneda_id, saldo_cd, fecha_registro)
                                 VALUES ('1', '$banco_id', '$moneda_id', '$monto', NOW())";

            if (mysqli_query($conn, $sql_insert_saldo)) {
                echo "Saldo inicial registrado correctamente.<br>";
            } else {
                echo "Error al registrar el saldo inicial: " . mysqli_error($conn) . "<br>";
            }
        }
    } else {
        echo "Error al registrar el ingreso: " . mysqli_error($conn) . "<br>";
    }
}

// Consulta para obtener todas las categorías del usuario
$sql_categorias = "SELECT * FROM categorias WHERE usuario_id='1' AND tipo='ingreso'";
$result_categorias = $conn->query($sql_categorias);

// Consulta para obtener todos los bancos del usuario
$sql_bancos = "SELECT * FROM bancos WHERE usuario_id='1' AND crypto='1' OR dolar='1'";
$result_bancos = $conn->query($sql_bancos);

// Consulta para obtener todas las monedas disponibles
$sql_monedas = "SELECT * FROM monedas_crypto_dolares";
$result_monedas = $conn->query($sql_monedas);
?>

<section class="container">
    <h3>Agregar Ingresos en Crypto/Dólares</h3>

    <!-- Formulario para agregar un nuevo ingreso -->
    <form method="POST" action="index.php?s=agregar_ingresos_cd">
        <label for="categoria_id">Categoría:</label>
        <select name="categoria_id" required class="form-control">
            <?php while ($row = $result_categorias->fetch_assoc()): ?>
                <option value="<?php echo $row['categoria_id']; ?>"><?php echo $row['nombre']; ?></option>
            <?php endwhile; ?>
        </select>

        <label for="moneda_id">Moneda:</label>
        <select name="moneda_id" required class="form-control">
            <?php while ($row = $result_monedas->fetch_assoc()): ?>
                <option value="<?php echo $row['moneda_id']; ?>"><?php echo $row['descripcion']; ?></option>
            <?php endwhile; ?>
        </select>

        <label for="monto">Monto:</label>
        <input type="number" step="0.00000001" name="monto" placeholder="Monto" required class="form-control">

        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" required class="form-control">

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" placeholder="Descripción" class="form-control"></textarea>

        <label for="banco_id">Banco:</label>
        <select name="banco_id" required class="form-control">
            <?php while ($row = $result_bancos->fetch_assoc()): ?>
                <option value="<?php echo $row['banco_id']; ?>"><?php echo $row['nombre']; ?></option>
            <?php endwhile; ?>
        </select>

        

        <button class="btn btn-secondary btn-sm" type="submit">Registrar Ingreso</button>
    </form>
    <a href="index.php">Volver al Inicio</a>
</section>
