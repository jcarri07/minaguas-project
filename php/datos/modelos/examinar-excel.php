<?php
    require_once '../../../vendor/PHPExcel/Classes/PHPExcel.php';

    if ($_FILES['file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name'])) {

        //Ubic temporal
        $nombre_temporal = $_FILES['file']['tmp_name'];
        $nombre_archivo = $_FILES['file']['name'];
        $ubicacion_guardado = 'temp/' . $nombre_archivo;
        move_uploaded_file($nombre_temporal, $ubicacion_guardado);
    
        $excel = PHPExcel_IOFactory::load($ubicacion_guardado);
    
        // Hojas del archivo
        $hojas = $excel->getSheetNames();
    
        // Listar las hojas
        echo "Hojas en el archivo $nombre_archivo:\n";
        foreach ($hojas as $hoja) {
            echo "- $hoja\n";
        }
    
        // Delete archivo temporal
        unlink($ubicacion_guardado);
    } else {
        echo "Error al subir el archivo.";
    }





?>