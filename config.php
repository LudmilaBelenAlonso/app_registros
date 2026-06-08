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

// Función para verificar si el usuario logueado es Administrador (usuario_id = 1)
if (!function_exists('es_admin')) {
    function es_admin() {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1;
    }
}
?>
