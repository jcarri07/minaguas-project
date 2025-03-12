<?php

include './php/Conexion.php';

// Nombre del campo que quieres añadir
$nuevo_campo = "cota_min_dis";

// Verificar si el campo ya existe en la tabla
$sql_check = "SHOW COLUMNS FROM embalses LIKE '$nuevo_campo'";
$result = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result) == 0) {
    // El campo no existe, procedemos a añadirlo
    $sql_alter = "ALTER TABLE embalses ADD $nuevo_campo VARCHAR(255) DEFAULT ''";
    if (mysqli_query($conn, $sql_alter)) {
        echo "El campo $nuevo_campo ha sido añadido correctamente.";
    } else {
        echo "Error al añadir el campo: " . mysqli_error($conn);
    }
} else {
    echo "El campo $nuevo_campo ya existe en la tabla.";
}

mysqli_close($conn);
