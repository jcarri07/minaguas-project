<?php
// Forzar mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Prueba básica
echo "TEST 1: Esto debería verse<br>";

// Prueba de función PHP
print_r("TEST 2: Esto también debería verse<br>");

// Prueba de error deliberado
echo $variable_no_definida; // Esto debería generar un warning
?>