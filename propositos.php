<?php

include './php/Conexion.php';

$embalses = mysqli_query($conn, "SELECT id_embalse, proposito, uso_actual FROM embalses");

if (mysqli_num_rows($embalses) > 0) {
    while ($row = mysqli_fetch_array($embalses)) {

        $id = $row['id_embalse'];
        $proposito = $row['proposito'];
        $uso = $row['uso_actual'];

        $proposito_nuevo = str_replace(" - ", ",", $proposito);
        $proposito_nuevo = str_replace(" , ", ",", $proposito);
        $uso_nuevo = str_replace("-", ",", $uso);
        $uso_nuevo = str_replace(" , ", ",", $uso);
        var_dump($proposito, $uso);
        mysqli_query($conn, "UPDATE embalses SET proposito = '$proposito_nuevo', uso_actual = '$uso_nuevo' WHERE id_embalse = '$id'");
    }
}
