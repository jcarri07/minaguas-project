<?php
include '../php/Conexion.php';

$id = $_POST['id'];

$queryEmbalses = mysqli_query($conn, "SELECT * FROM embalses WHERE id_embalse = $id");
// $num = mysqli_fetch_row($queryEmbalses);
// echo mysqli_fetch_row($queryEmbalses)>0;

if ($queryEmbalses) {
    $row = mysqli_fetch_assoc($queryEmbalses);
    if ($row) {
        echo $row['nombre_embalse'];
    } else {
        echo "No se encontraron resultados.";
    }
} else {
    echo "Error en la consulta: " . mysqli_error($tuConexion);
}
