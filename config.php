<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "devroot";
$password = "123";
$database = "economia_hogar";
$puerto = "3306";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $database, $puerto);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
