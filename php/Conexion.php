<?php


function contiene_subcadena($cadena, $subcadena)
{
    return strpos($cadena, $subcadena) !== false;
}

$fullPath = getcwd();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "minagua_db";

// if (contiene_subcadena($fullPath, "C:")) {
//     $host = "localhost";
//     $user = "root";
//     $pass = "";
//     $dbname = "minagua_db";
// } else {
//     $host = "localhost";
//     $user = "id21716991_jcarri07";
//     $pass = "Negro0414*";
//     $dbname = "id21716991_minagua_db";
// }

// Crear conexión
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Verificar si hay errores en la conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Si la conexión es exitosa, muestra un mensaje por consola
// echo "Conexión exitosa a la base de datos project-manager";

// Cerrar conexión
function closeConection($conn)
{
    mysqli_close($conn);
}
