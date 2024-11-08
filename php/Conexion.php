<?php


function contiene_subcadena($cadena, $subcadena)
{
    return strpos($cadena, $subcadena) !== false;
}

$fullPath = getcwd();

// $host = "localhost";
// $user = "root";
// $pass = "root";
// $dbname = "minagua_db";

if (contiene_subcadena($fullPath, "C:")) {
    $host = "localhost";
    $user = "root";
    $pass = "root";
    $dbname = "minagua_db";
} else {
    $host = "localhost";
    $user = "sbtebpav_minaguas_user";
    $pass = "Minaguasroot#";
    $dbname = "sbtebpav_minagua_db";
}


// Crear conexión
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Verificar si hay errores en la conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

$conn->set_charset("utf8");

// Si la conexión es exitosa, muestra un mensaje por consola
// echo "Conexión exitosa a la base de datos project-manager";

// Cerrar conexión
function closeConection($conn)
{
    mysqli_close($conn);
}
