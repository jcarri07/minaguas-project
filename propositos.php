<?php

// include './php/Conexion.php';

// $embalses = mysqli_query($conn, "SELECT id_embalse, proposito, uso_actual FROM embalses");

// if (mysqli_num_rows($embalses) > 0) {
//     while ($row = mysqli_fetch_array($embalses)) {

//         $id = $row['id_embalse'];
//         $proposito = $row['proposito'];
//         $uso = $row['uso_actual'];

//         $proposito_nuevo = str_replace(" - ", ",", $proposito);
//         $proposito_nuevo = str_replace(" , ", ",", $proposito);
//         $uso_nuevo = str_replace("-", ",", $uso);
//         $uso_nuevo = str_replace(" , ", ",", $uso);
//         var_dump("reemplazo: " . str_replace(" - ", ",", $proposito));
//         mysqli_query($conn, "UPDATE embalses SET proposito = '$proposito_nuevo', uso_actual = '$uso_nuevo' WHERE id_embalse = '$id'");
//     }
// }

include './php/Conexion.php';

$embalses = mysqli_query($conn, "SELECT id_embalse, proposito, uso_actual FROM embalses");

if (mysqli_num_rows($embalses) > 0) {
    while ($row = mysqli_fetch_array($embalses)) {
        $id = $row['id_embalse'];
        $proposito = $row['proposito'];
        $uso = $row['uso_actual'];

        var_dump("Original: ", $proposito, $uso);

        // Reemplazar valores
        $proposito_nuevo = str_replace(" - ", ",", $proposito);
        $proposito_nuevo = str_replace(" , ", ",", $proposito_nuevo);
        $uso_nuevo = str_replace("-", ",", $uso);
        $uso_nuevo = str_replace(" , ", ",", $uso_nuevo);

        var_dump("Reemplazado: ", $proposito_nuevo, $uso_nuevo);

        // Escapar valores
        $proposito_nuevo = mysqli_real_escape_string($conn, $proposito_nuevo);
        $uso_nuevo = mysqli_real_escape_string($conn, $uso_nuevo);

        // Actualizar en la base de datos
        $query = "UPDATE embalses SET proposito = '$proposito_nuevo', uso_actual = '$uso_nuevo' WHERE id_embalse = '$id'";
        if (!mysqli_query($conn, $query)) {
            echo "Error en la consulta: " . mysqli_error($conn);
        } else {
            echo "Registro actualizado correctamente.";
        }
    }
} else {
    echo "No se encontraron registros.";
}
