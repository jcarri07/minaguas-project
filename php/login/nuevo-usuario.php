<?php
require_once "../Conexion.php";
//$user = $_POST["usuario"];
$nombre = $_POST["nombre"];
$contra = $_POST["pass"];
$apellido = $_POST["apellido"];
$cedula = $_POST["cedula"];
$telefono = $_POST["telefono"];
$email = $_POST["email"];

$nombres = explode(" ", $nombre);
$nombre1 = $nombres[0];
$nombre2 = $nombres[1];
$apellidos = explode(" ", $apellido);
$apellido1 = $apellidos[0];
$apellido2 = $apellidos[1];

$res = mysqli_query($conn, "SELECT Cedula FROM usuarios WHERE Cedula = '$cedula';");
$num_r = mysqli_num_rows($res);
if ($num_r >= 1) {
    echo 'existe_cedula';
    return;
} else {
    $res = mysqli_query($conn, "SELECT N_Usuario FROM usuarios WHERE N_Usuario = '$user';");
    $num_r = mysqli_num_rows($res);
    if ($num_r >= 1) {
        echo 'existe_usuario';
        return;
    } else {

        $res = mysqli_query($conn, "INSERT INTO usuarios (P_Nombre,S_Nombre, P_Apellido,S_Apellido, Cedula,Telefono, Contrasena,Tipo,Correo) VALUES ('$nombre1','$nombre2', '$apellido1','$apellido2','$cedula','$telefono','$contra','User','$email');");

        if ($res) {
            echo "si";
        } else {
            echo "no";
        };
    }
    closeConection($conn);
}
