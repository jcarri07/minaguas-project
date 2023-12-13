<?php
require_once "../Conexion.php";
if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
$user = $_POST["email"];
$contra = $_POST["pass"];

$res = mysqli_query($conn, "SELECT * 
                           FROM usuarios 
                           WHERE Correo='$user' AND Contrasena='$contra';");
$num_r = mysqli_num_rows($res);


if ($num_r >= 1) {
    $obj = mysqli_fetch_object($res);
    $_SESSION["Id_usuario"] = $obj->Id_usuario;
    $_SESSION["P_Nombre"] = $obj->P_Nombre;
    $_SESSION["S_Nombre"] = $obj->S_Nombre;
    $_SESSION["P_Apellido"] = $obj->P_Apellido;
    $_SESSION["S_Apellido"] = $obj->S_Apellido;
    $_SESSION["Cedula"] = $obj->Cedula;
    $_SESSION["Tipo"] = $obj->Tipo;
    
    echo "si";
} else {
    echo "no";
};
closeConection($conn);