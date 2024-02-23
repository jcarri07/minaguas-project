

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'Conexion.php';

if (isset($_GET['id'])) {
    $embalseId = $_GET['id'];

    echo '<script>';
    echo 'console.log("ID del embalse:", ' . json_encode($embalseId) . ');';
    echo '</script>';

    $sql = "SELECT MONTH(fecha) as mes, COUNT(*) as cantidad FROM datos_embalse WHERE id_embalse = '$embalseId' GROUP BY MONTH(fecha)";
    $result = $conn->query($sql);

    if ($result === false) {
        die('Error en la consulta: ' . $conn->error);
    }

    $datos = array();

    while ($row = $result->fetch_assoc()) {
        $mes = $row["mes"];
        $cantidad = $row["cantidad"];

        // Almacena los datos en un formato adecuado para el gráfico
        $datos[$mes] = $cantidad;
    }

    echo json_encode($datos);
}

$conn->close();
?>

