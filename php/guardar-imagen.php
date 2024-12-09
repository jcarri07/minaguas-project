<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$data = $_POST['imagen'];
$i = $_POST['nombre'];
$t = $_POST['numero'];

$aux = $data;
$nombre_archivo = "imagen-" . $i . "-" . $t . ".png"; // Nombre del archivo
$ruta_archivo = "../assets/img/temp/$nombre_archivo"; // Ruta donde guardar el archivo

// Decodificación de la imagen
$aux = explode(";", $aux)[1];
$aux = explode(",", $aux)[1];
$aux = str_replace('data:image/png;base64,', '', $aux);
$aux = str_replace(' ', '+', $aux);
$imagen_bin = base64_decode($aux);

// Guardar la imagen en el archivo
if (file_put_contents($ruta_archivo, $imagen_bin) == false) {
    echo "error en file_put_contents";
} else {
    echo "si";
}
