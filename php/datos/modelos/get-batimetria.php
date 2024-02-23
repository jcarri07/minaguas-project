<?php
    require_once '../../batimetria.php';
    require_once '../../Conexion.php';

    $anio = $_POST['anio'];
    $id = $_POST['id'];
    $valor = $_POST['valor'];

    $embalse = new Batimetria($id, $conn);

    closeConection($conn);

    echo json_encode($embalse->getByCota($anio, $valor));
?>