<?php
include 'config.php';

// Mostrar errores detallados
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['duplicar'])) {
    echo "Botón de duplicar presionado.<br>";
    if (isset($_POST['transaccion_id'])) {
        $transaccion_id = mysqli_real_escape_string($conn, $_POST['transaccion_id']);
        echo "ID de transacción recibido: " . $transaccion_id . "<br>";
    } else {
        echo "ID de transacción no proporcionado.<br>";
    }
} else {
    echo "No se presionó el botón de duplicar.<br>";
}

if (isset($_POST['transaccion_id'])) {
    $transaccion_id = mysqli_real_escape_string($conn, $_POST['transaccion_id']);
    
    // Obtener la transacción original
    $sql = "SELECT * FROM transacciones WHERE transaccion_id = '$transaccion_id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $transaccion = mysqli_fetch_assoc($result);
        
        // Extraer los datos de la transacción original
        $usuario_id = $transaccion['usuario_id'];
        $categoria_id = $transaccion['categoria_id'];
        $banco_id = $transaccion['banco_id'];
        $monto = $transaccion['monto'];
        $fecha = $transaccion['fecha'];
        $descripcion = $transaccion['descripcion'];
        $tipo_pago = $transaccion['tipo_pago'];
        $tarjeta_id = $transaccion['tarjeta_id'];
        $cuota_actual = $transaccion['cuota_actual'];
        $cuotas = $transaccion['cuotas'];
        $fecha_cierre = $transaccion['fecha_cierre']; // Solo para crédito

        // Validar que la cuota actual no sea mayor o igual a la cantidad de cuotas
        if ($cuota_actual >= $cuotas) {
            echo "No se puede duplicar el registro. La cuota actual ya es igual a la cantidad de cuotas.";
            header("Location: ver_gastos.php?error=cuotas_excedidas"); // Redirigir a la página de ver gastos con mensaje de error
            exit();
        }

        // Incrementar la cuota actual y actualizar el mes de cierre si es crédito
        if ($tipo_pago == 'credito') {
            $cuota_actual++;
            $fecha_cierre = date('Y-m-d', strtotime("+1 month", strtotime($fecha_cierre)));
        }

        // Insertar el nuevo registro duplicado
        $sql_insert = "INSERT INTO transacciones (usuario_id, categoria_id, banco_id, monto, fecha, descripcion, tipo_pago, tarjeta_id, cuota_actual, cuotas, fecha_cierre)
                       VALUES ('$usuario_id', '$categoria_id', '$banco_id', '$monto', NOW(), '$descripcion', '$tipo_pago', '$tarjeta_id','$cuota_actual', '$cuotas', '$fecha_cierre')";

        if (mysqli_query($conn, $sql_insert)) {
            echo "Transacción duplicada correctamente.";
            header("Location: index.php?s=ver_gastos"); // Redirigir a la página de ver gastos
            exit();
        } else {
            echo "Error al duplicar la transacción: " . mysqli_error($conn);
        }
    } else {
        echo "Transacción no encontrada.";
    }
} else {
    echo "No se proporcionó el ID de transacción.";
}
?>