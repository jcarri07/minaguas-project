<?php
require_once "../Conexion.php";
if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
$user = $_POST["usuario"];
$contra = $_POST["pass"];

$res = mysqli_query($conn, "SELECT * 
                           FROM usuarios 
                           WHERE N_Usuario='$user' AND Contrasena='$contra';");
$num_r = mysqli_num_rows($res);


if ($num_r >= 1) {
    $obj = mysqli_fetch_object($res);
    $_SESSION["Id_usuario"] = $obj->id_usuario;
    $_SESSION["P_Nombre"] = $obj->P_Nombre;
    $_SESSION["S_Nombre"] = $obj->S_Nombres;
    $_SESSION["P_Apellido"] = $obj->P_Apellido;
    $_SESSION["S_Apellido"] = $obj->S_Apellido;
    $_SESSION["cedula"] = $obj->Cedula;
    $_SESSION["usuario"] = $obj->N_Usuario;
    $_SESSION["tipo"] = $obj->Tipo;
    
    echo "si";
} else {
    echo "no";
};
closeConection($conn);