<?php
    require_once '../../batimetria.php';
    require_once '../../Conexion.php';

    $anio = $_POST['anio'];
    $id = $_POST['id'];
    $valor = $_POST['valor'];

    $embalse = new Batimetria($id, $conn);

    closeConection($conn);

    $array = array();
    array_push($array, $embalse->getByCota($anio, $valor));
    array_push($array, $embalse->cotaMinima());
    array_push($array, $embalse->cotaMaxima());

    echo json_encode($array);
?>