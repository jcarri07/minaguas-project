<?php
//require_once '../../../vendor/PHPExcel/Classes/PHPExcel.php';

ini_set('memory_limit', '4G');
ini_set('max_execution_time', 400);


require_once '../../Conexion.php';


function buscarPosicion($array, $valorABuscar, $columna) {
    //$columna = 'codigo'; // Columna en la que deseas buscar
    $posicion = array_search($valorABuscar, array_column($array, $columna));
    return $posicion !== false ? $posicion : -1;
}

if (isset($_POST['opc']) && $_POST['opc'] == "importar_data") {
    $id_embalse = $_POST['id_embalse'];
    $nombre_archivo = $_POST['nombre_archivo'];
    $id_encargado = $_POST['id_usuario'];
    $fecha_importacion = date("Y-m-d");

    $array_codigos_consulta = json_decode($_POST['array_codigos_consulta']);
    $fullData = json_decode($_POST['fullData']);

    //Comprobando si ya se importo informacion de este archivo a este embalse
    $sql = "SELECT DISTINCT nombre_embalse, archivo_importacion, fecha_importacion
            FROM embalses e, datos_embalse de
            WHERE e.id_embalse = de.id_embalse AND archivo_importacion <> '' AND de.estatus = 'activo' AND de.id_embalse = '$id_embalse' AND de.archivo_importacion = '$nombre_archivo'
            ORDER BY fecha_importacion DESC;";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0) {
        echo "ya se importo";
        exit;
    }

    $sql = "SELECT nombre, cantidad_primaria, unidad, codigo, leyenda_sistema, concepto, uso, ce.id AS 'id_codigo_extraccion', IF(leyenda_sistema <> '', leyenda_sistema, concepto) AS 'name'
            FROM tipo_codigo_extraccion tce, codigo_extraccion ce
            WHERE tce.id = ce.id_tipo_codigo_extraccion AND 
                ce.estatus = 'activo' AND 
                tce.estatus = 'activo' /*AND 
                tce.id <> '6' AND 
                tce.id <> '7' AND
                ce.concepto <> 'Subtotal'*/
            ORDER BY codigo ASC;";
    $query_codigos = mysqli_query($conn, $sql);
    $array_codigos_sql = array();

    while($row = mysqli_fetch_array($query_codigos)){
        $array_aux = [];
        $array_aux['id_codigo_bd'] = $row['id_codigo_extraccion'];
        $array_aux['codigo'] = $row['codigo'];
        array_push($array_codigos_sql, $array_aux);
    }



    //print_r($array_codigos_consulta[0]);
    //for($i = 0; $i < count($fullData) ; $i++){
    foreach($fullData as $key => $data){
        $fecha = $data->fecha;
        $cota_actual = $data->cota_actual != '' ? $data->cota_actual : 0;
        
        //if($key == 0) {
            $sql = "INSERT INTO datos_embalse (id_embalse, fecha, hora, cota_actual, id_encargado, archivo_importacion, fecha_importacion, estatus) 
                    VALUES ('$id_embalse', '$fecha', '00:00:00', '$cota_actual', '$id_encargado', '$nombre_archivo', '$fecha_importacion', 'activo');";
            $res = mysqli_query($conn, $sql);

            if($res == 1){
                usleep(20000);

                $sql = "SELECT id_registro FROM datos_embalse WHERE id_embalse = '$id_embalse' AND id_encargado = '$id_encargado' ORDER BY id_registro DESC LIMIT 1;";
                $query = mysqli_query($conn, $sql);
                $id_registro = mysqli_fetch_array($query)['id_registro'];

                //for($i = 0 ; $i < count($data->extracciones) ; $i++) {
                foreach($data->extracciones as $extraccion){
                    $valor_extraccion_aux = $extraccion->valor;

                    if($valor_extraccion_aux != ""){
                        $index_codigo = buscarPosicion($array_codigos_sql, $extraccion->codigo, 'codigo');
                        $codigo_aux = $array_codigos_sql[$index_codigo]['id_codigo_bd'];
                        $sql = "INSERT INTO detalles_extraccion (id_codigo_extraccion, extraccion, id_registro, estatus) VALUES ('$codigo_aux', '$valor_extraccion_aux', '$id_registro', 'activo');";
                        mysqli_query($conn, $sql);

                        //echo "Codigo: " . $extraccion->codigo . "\n Valor: " . $valor_extraccion_aux . "\n Fecha: " . $fecha . "\n Cota: " . $cota_actual;
                    }
                }

                unset($extraccion);
            }
        //}

    }
    unset($data);

    echo 'si';


} 

closeConection($conn);


?>