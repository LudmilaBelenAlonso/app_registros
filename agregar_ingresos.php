<?php
include 'config.php';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria_id = $_POST['categoria_id'];
    $monto = isset($_POST['monto']) && is_numeric($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $banco_id = $_POST['banco_id'];

    // Verificar si el monto es válido
    if ($monto <= 0) {
        echo "El monto debe ser un valor positivo.<br>";
        exit;
    }

    // Inserción de ingreso en la tabla ingresos
    $sql_ingreso = "INSERT INTO ingresos (usuario_id, categoria_id, banco_id, monto, fecha, descripcion)
                    VALUES ('1', '$categoria_id', '$banco_id', '$monto', '$fecha', '$descripcion')";

    if (mysqli_query($conn, $sql_ingreso)) {
        echo "Ingreso registrado correctamente.<br>";

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
    } else {
        echo "Error al registrar el ingreso: " . mysqli_error($conn) . "<br>";
    }
}

// Consulta para obtener todas las categorías del usuario
$sql_categorias = "SELECT * FROM categorias WHERE usuario_id='1' AND tipo='ingreso'";
$result_categorias = $conn->query($sql_categorias);

// Consulta para obtener todos los bancos del usuario
$sql_bancos = "SELECT * FROM bancos WHERE usuario_id='1'";
$result_bancos = $conn->query($sql_bancos);
?>

<section class="container">
    <h3>Agregar Ingresos</h3>

    <!-- Formulario para agregar un nuevo ingreso -->
    <form method="POST" action="index.php?s=agregar_ingresos">
        <label for="categoria_id">Categoría:</label>
        <select name="categoria_id" required class="form-control">
            <?php while ($row = $result_categorias->fetch_assoc()): ?>
                <option value="<?php echo $row['categoria_id']; ?>"><?php echo $row['nombre']; ?></option>
            <?php endwhile; ?>
        </select>

        <label for="monto">Monto:</label>
        <input type="number" step="0.01" name="monto" placeholder="Monto" required class="form-control">

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
