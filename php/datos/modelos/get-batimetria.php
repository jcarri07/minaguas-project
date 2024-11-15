<?php
    require_once '../../batimetria.php';
    require_once '../../Conexion.php';

    $anio = $_POST['anio'];
    $id = $_POST['id'];
    $valor = $_POST['valor'];


    if(!is_numeric($valor)) {
        $valor = str_replace('.', '', $valor);
        if(!is_numeric($valor)) {
            $valor = str_replace(',', '.', $valor);
        }
    }


    $embalse = new Batimetria($id, $conn);

    closeConection($conn);

    $array = array();

    $array_area_volumen = $embalse->getByCota($anio, $valor);
    $array_area_volumen[0] = number_format($array_area_volumen[0], 2, ',', '.');
    $array_area_volumen[1] = number_format($array_area_volumen[1], 2, ',', '.');

    array_push($array, $array_area_volumen);
    array_push($array, $embalse->cotaMinima());
    array_push($array, $embalse->cotaMaxima());
    array_push($array, $valor);

    echo json_encode($array);
?>