<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
    } else {
        echo "Email o contraseña incorrectos";
    }
}
?>

<form method="post" action="login.php">
    <input type="email" name="email" placeholder="Email" required>
    <!--input type="password" name="password" placeholder="Contraseña" required-->
    <button type="submit">Iniciar Sesión</button>
</form>
