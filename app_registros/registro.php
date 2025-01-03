<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$password')";
    if (mysqli_query($conn, $sql)) {
        echo "Usuario registrado correctamente";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>


<form method="post" action="registro.php">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <!--input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Contraseña" required-->
    <button type="submit">Registrar</button>
</form>

