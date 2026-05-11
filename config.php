<?php
// Configuración de la base de datos
$servername = "127.0.0.1";  // Usamos IP para forzar TCP en vez de socket
$username = "devroot";  // Nombre de usuario de MySQL
$password = "123";  // Contraseña de MySQL
$database = "economia_hogar";  // Nombre de la base de datos
$puerto = "3306";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $database, $puerto);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
