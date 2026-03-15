<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria_id = $_POST['categoria_id'];
    $monto = floatval($_POST['monto']);
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $moneda_id = isset($_POST['moneda_id']) ? intval($_POST['moneda_id']) : null;

    $stmt = $conn->prepare("INSERT INTO ingresos_crypto_dolares (usuario_id, categoria_id, banco_id, moneda_id, monto, fecha, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $usuario_id = 1; // Reemplazar por $_SESSION['user_id'] en producción
    $stmt->bind_param("iiiidss", $usuario_id, $categoria_id, $banco_id, $moneda_id, $monto, $fecha, $descripcion);
    if (!$stmt->execute()) {
        echo "Error al registrar ingreso: " . $stmt->error;
    } else {
        echo "Ingreso registrado correctamente.";
    }
    $stmt->close();
}

function obtenerCategorias($conn, $usuario_id) {
    $stmt = $conn->prepare("SELECT * FROM categorias WHERE usuario_id = ? AND tipo = 'ingreso'");
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
$monedas = obtenerMonedas($conn);

?>
<section class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card bg-dark text-white shadow">
                <div class="card-header border-bottom border-secondary">
                    <h3 class="mb-0">Agregar Ingreso Crypto/USD</h3>
                </div>
                <div class="card-body">
                    <form method="post" action="agregar_ingresos_cd.php">
                        <div class="form-group">
                            <label for="categoria_id">Categoría:</label>
                            <select name="categoria_id" required class="form-control">
                                <?php foreach ($categorias as $row): ?>
                                    <option value="<?php echo $row['categoria_id']; ?>"><?php echo $row['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="monto">Monto:</label>
                            <input type="number" name="monto" step="0.01" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="fecha">Fecha:</label>
                            <input type="date" name="fecha" required class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion">Descripción:</label>
                            <textarea name="descripcion" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="banco_id">Banco/Billetera:</label>
                            <select name="banco_id" class="form-control">
                                <?php foreach ($bancos as $row): ?>
                                    <option value="<?php echo $row['banco_id']; ?>"><?php echo $row['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="moneda_id">Moneda:</label>
                            <select name="moneda_id" class="form-control">
                                <?php foreach ($monedas as $row): ?>
                                    <option value="<?php echo $row['moneda_id']; ?>"><?php echo $row['descripcion']; ?></option>
                                <?php endforeach; ?>
                            </select>
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
