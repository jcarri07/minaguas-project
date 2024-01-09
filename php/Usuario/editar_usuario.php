<?php

use LDAP\Result;

require_once "../Conexion.php";
//$user = $_POST["usuario"];
$ident = $_POST["ident"];

switch ($ident) {
    case 'editar':
        editar($conn);
        break;
    case 'borrar':
        borrar($conn);
        break;
    case 'recuperar':
        recuperar($conn);
        break;

    default:
        # code...
        break;
}


function editar($conn)
{
    $nombre = $_POST["nombre"];
    $contra = $_POST["pass"];
    $apellido = $_POST["apellido"];
    $cedula = $_POST["cedula"];
    $cedula2 = $_POST["cedula2"];
    $telefono = $_POST["telefono"];
    $email = $_POST["email"];
    $num = 0;

    $apellido2 = "";
    $nombre2 = "";

    $nombres = explode(" ", $nombre);
    if (count($nombres) > 1) {
        $nombre1 = $nombres[0];
        $nombre2 = $nombres[1];
    } else {
        $nombre1 = $nombre;
    };

    $apellidos = explode(" ", $apellido);
    if (count($apellidos) > 1) {
        $apellido1 = $apellidos[0];
        $apellido2 = $apellidos[1];
    } else {
        $apellido1 = $apellido;
    };

    $res = mysqli_query($conn, "SELECT * FROM usuarios WHERE Cedula = '$cedula2';");
    $result = mysqli_fetch_object($res);

    if ($cedula == $cedula2) {
        if ($email == $result->Correo) {

            $num = 1;
        } else {
            $res = mysqli_query($conn, "SELECT * FROM usuarios WHERE Correo = '$email';");
            $num_r = mysqli_num_rows($res);
            if ($num_r >= 1) {
                echo 'existe_usuario';
                return;
            } else {
                $num = 1;
            }
        }
    } else {

        $res = mysqli_query($conn, "SELECT * FROM usuarios WHERE Cedula = '$cedula';");
        $num_r = mysqli_num_rows($res);
        if ($num_r >= 1) {
            echo 'existe_cedula';
            return;
        } else {
            $num = 1;

            if ($email == $result["Correo"]) {

                $num = 1;
            } else {
                $res = mysqli_query($conn, "SELECT Cedula FROM usuarios WHERE Correo = '$email';");
                $num_r = mysqli_num_rows($res);
                if ($num_r >= 1) {
                    echo 'existe_usuario';
                    return;
                } else {
                    $num = 1;
                }
            }
        }
    }



    if ($num) {



        $res = mysqli_query($conn, "UPDATE usuarios SET `Contrasena`='$contra',`P_Nombre`='$nombre1',`S_Nombre`='$nombre2',`P_Apellido`='$apellido1',`S_Apellido`='$apellido2',`Cedula`='$cedula',`Correo`='$email',`Telefono`='$telefono',`Tipo`='User' WHERE Cedula = '$cedula2';");

        if ($res) {
            echo "si";
        } else {
            echo "no";
        };
    }
}
function borrar($conn)
{
    $id = $_POST['id'];

    $res = mysqli_query($conn, "UPDATE usuarios SET estatus= 'inactivo' WHERE Id_Usuario = '$id';");

    if ($res) {

        echo 'borrado';
    } else {

        echo 'usuario no existe';
    }
}
function recuperar($conn)
{
    $id = $_POST['id'];

    $res = mysqli_query($conn, "UPDATE usuarios SET estatus= 'activo' WHERE Id_Usuario = '$id';");

    if ($res) {

        echo 'recuperado';
    } else {

        echo 'usuario no existe';
    }
}
closeConection($conn);
