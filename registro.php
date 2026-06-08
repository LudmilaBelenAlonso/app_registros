<?php
session_start();
include 'config.php';

// Si ya está logueado, ir a index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql_check = "SELECT usuario_id FROM usuarios WHERE email='$email'";
    $result_check = mysqli_query($conn, $sql_check);
    if (mysqli_num_rows($result_check) > 0) {
        $mensaje = "El email ya está registrado.";
        $tipo_mensaje = "danger";
    } else {
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$password')";
        if (mysqli_query($conn, $sql)) {
            $mensaje = "Usuario registrado correctamente. Puedes iniciar sesión.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Error al registrar el usuario: " . mysqli_error($conn);
            $tipo_mensaje = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Economía Hogareña</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card bg-secondary shadow">
                    <div class="card-header text-center">
                        <h3>Crear Cuenta</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
                        <?php endif; ?>
                        <form method="post" action="registro.php">
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" name="nombre" id="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Contraseña</label>
                                <input type="password" class="form-control" name="password" id="password" required>
                            </div>
                            <button type="submit" class="btn btn-dark btn-block mt-4">Registrar</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <small>¿Ya tienes cuenta? <a href="login.php" class="text-light font-weight-bold">Inicia sesión aquí</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
