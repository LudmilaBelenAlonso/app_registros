<?php
// Configuración de la base de datos
$servername = "localhost";  // Cambia esto si tu servidor no está en localhost
$username = "root";  // Nombre de usuario de MySQL, por defecto es 'root'
$password = "";  // Contraseña de MySQL, por defecto es vacía en XAMPP
$database = "economia_hogar";  // Nombre de la base de datos que creaste

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
