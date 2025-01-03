<?php
session_start();

// Obtener la sección desde el parámetro GET, con un valor predeterminado
$seccion = isset($_GET['s']) ? $_GET['s'] : 'home';

// Sanitización del parámetro para evitar inyecciones
$seccion = preg_replace('/[^a-zA-Z0-9_]/', '', $seccion);

// Construir la ruta del archivo basado en la sección
$archivo_seccion = $seccion . '.php';

// Si el archivo no existe, redirigir a una página de error
if (!file_exists($archivo_seccion)) {
    $archivo_seccion = '404.php'; // Archivo para sección no encontrada
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Economía Hogareña</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header id="main-nav" class="sticky-top shadow">
        <div>
            <nav id="navbarNav">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link" href="index.php?s=agregar_ingresos">Agregar Ingresos</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?s=agregar_gastos">Agregar Gastos</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?s=saldos_actuales">Saldos actuales</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?s=ver_gastos">Ver Gastos</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?s=categorias">Categorías</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?s=tarjetas">Tarjetas de Crédito</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main id="main-content">
        <h2>Economia hogareña</h2>
        <?php
            // Incluir el archivo de la sección seleccionada
            include $archivo_seccion;
        ?>
    </main>
    <footer id="main-footer" class="bg-dark text-white py-3">
        <div class="container text-center">
            <p>© 2024 Aplicación de Economía Hogareña</p>
        </div>
    </footer>
</body>
</html>
