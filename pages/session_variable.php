<?php
// Inicia o reanuda una sesión
session_start();

// Verifica si se ha enviado un valor desde JavaScript
if(isset($_POST['valor'])) {
    // Obtiene el valor enviado
    $valor = $_POST['valor'];

    // Guarda el valor en la variable de sesión
    $_SESSION['id_embalse'] = $valor;

    // Envía una respuesta de éxito al cliente
    echo "1";
} else {
    // Envía una respuesta de error al cliente
    echo "0";
}
?>