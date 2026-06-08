<?php
include 'config.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $password_confirmar = $_POST['password_confirmar'];

    // Validar contraseñas nuevas
    if ($password_nueva !== $password_confirmar) {
        $mensaje = "Las contraseñas nuevas no coinciden.";
        $tipo_mensaje = "danger";
    } else {
        // Obtener usuario de la base de datos (prepared statement)
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE usuario_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password_actual, $user['password'])) {
            // Contraseña actual correcta, actualizar a la nueva
            $nueva_hash = password_hash($password_nueva, PASSWORD_BCRYPT);
            $stmt_update = $conn->prepare("UPDATE usuarios SET password = ? WHERE usuario_id = ?");
            $stmt_update->bind_param("si", $nueva_hash, $user_id);
            if ($stmt_update->execute()) {
                $mensaje = "Contraseña actualizada correctamente.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al actualizar contraseña: " . $conn->error;
                $tipo_mensaje = "danger";
            }
            $stmt_update->close();
        } else {
            $mensaje = "La contraseña actual es incorrecta.";
            $tipo_mensaje = "danger";
        }
    }
}
?>

<section class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card bg-dark text-white shadow">
                <div class="card-header border-bottom border-secondary text-center">
                    <h3 class="mb-0">Cambiar Contraseña</h3>
                </div>
                <div class="card-body">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?php echo $tipo_mensaje; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
                    <?php endif; ?>
                    <form method="post" action="index.php?s=cambiar_password">
                        <div class="form-group">
                            <label for="password_actual">Contraseña Actual:</label>
                            <input type="password" class="form-control" name="password_actual" id="password_actual" required>
                        </div>
                        <div class="form-group">
                            <label for="password_nueva">Nueva Contraseña:</label>
                            <input type="password" class="form-control" name="password_nueva" id="password_nueva" required>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmar">Confirmar Nueva Contraseña:</label>
                            <input type="password" class="form-control" name="password_confirmar" id="password_confirmar" required>
                        </div>
                        <button type="submit" class="btn btn-secondary btn-block mt-4">Actualizar Contraseña</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="index.php" class="text-info">Volver al Inicio</a>
                </div>
            </div>
        </div>
    </div>
</section>
