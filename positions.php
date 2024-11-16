<!-- $positions_ids = [
1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23,
24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42,
43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61,
62, 63, 64, 65, 66, 67
] -->

<?php
include '../minaguas-project/php/Conexion.php';


$positions = [
    1 => "br",
    2 => "l",
    3 => "bl",
    4 => "tr",
    5 => "b",
    6 => "tr",
    7 => "r",
    8 => "r",
    9 => "t",
    10 => "b",
    12 => "bl",
    15 => "bl",
    16 => "r",
    17 => "t",
    19 => "l",
    20 => "t",
    21 => "l",
    22 => "t",
    23 => "tr",
    24 => "l",
    25 => "r",
    26 => "tr",
    27 => "r",
    28 => "t",
    29 => "r",
    30 => "r",
    31 => "r",
    32 => "t",
    33 => "t",
    34 => "bl",
    35 => "bl",
    36 => "tl",
    37 => "l",
    38 => "br",
    39 => "b",
    40 => "t",
    41 => "b",
    42 => "b",
    43 => "t",
    44 => "t",
    45 => "bl",
    46 => "t",
    47 => "tr",
    48 => "r",
    49 => "r",
    50 => "t",
    51 => "l",
    52 => "r",
    53 => "b",
    54 => "t",
    55 => "tl",
    56 => "t",
    57 => "t",
    58 => "bl",
    60 => "b",
    61 => "b",
    62 => "t",
    63 => "b",
    64 => "b",
    65 => "r",
    66 => "br",
];

$encode_positions = json_encode($positions);

$marks = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'marks_posiciones'");
if (mysqli_num_rows($marks) > 0) {
    mysqli_query($conn, "UPDATE configuraciones SET configuracion = '$encode_positions' WHERE nombre_config = 'marks_posiciones'");
} else {
    mysqli_query($conn, "INSERT INTO configuraciones (nombre_config, configuracion) VALUES ('marks_posiciones', '$encode_positions')");
}

if ($marks) {

    echo "Todo excelente, los datos se actualizaron correctamente en la base de datos.";
} else {
    echo "Ocurrió un error al actualizar los datos: " . mysqli_error($conn);
}

?>