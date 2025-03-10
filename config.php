<?php
// Configuración de la base de datos
$servername = "localhost";  // Cambia esto si tu servidor no está en localhost
$username = "devroot";  // Nombre de usuario de MySQL
$password = "123";  // Contraseña de MySQL
$database = "economia_hogar";  // Nombre de la base de datos que creaste
$puerto = "8080";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $database, $puerto);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
