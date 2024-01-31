<?php
session_start();

$data = $_POST['imagen'];
$i = $_POST['nombre'];
$t = $_POST['numero'];    



    
    $aux = $data;
    $nombre_archivo = "imagen-" . $i . "-".$t.".png"; //nombre del archivo
    $ruta_archivo = "../assets/img/temp/$nombre_archivo"; //ruta donde guardar el archivo
    $aux = explode(";", $aux)[1];
    $aux = explode(",", $aux)[1];
    $aux = str_replace('data:image/png;base64,', '', $aux);
    $aux = str_replace(' ', '+', $aux);
    $imagen_bin = base64_decode($aux);
    file_put_contents($ruta_archivo, $imagen_bin);

?>
