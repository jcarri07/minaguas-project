<?php
//require_once '../../../vendor/PHPExcel/Classes/PHPExcel.php';

ini_set('memory_limit', '4G');
ini_set('max_execution_time', 400);

require_once '../../../vendor/autoload.php';
require_once '../../Conexion.php';

//ini_set('memory_limit', '1024M');

//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

function buscarPosicion($array, $valorABuscar, $columna) {
    //$columna = 'codigo'; // Columna en la que deseas buscar
    $posicion = array_search($valorABuscar, array_column($array, $columna));
    return $posicion !== false ? $posicion : -1;
}

if (isset($_POST['opc']) && $_POST['opc'] == "importar_data") {
    $hoja = $_POST['hoja'];
    $id_embalse = $_POST['id_embalse'];
    $nombre_archivo = $_POST['nombre_archivo'];
    $id_encargado = $_POST['id_usuario'];
    $fecha_importacion = date("Y-m-d");

    //Comprobando si ya se importo informacion de este archivo a este embalse
    $sql = "SELECT DISTINCT nombre_embalse, archivo_importacion, fecha_importacion
            FROM embalses e, datos_embalse de
            WHERE e.id_embalse = de.id_embalse AND archivo_importacion <> '' AND de.estatus = 'activo' AND de.id_embalse = '$id_embalse' AND de.archivo_importacion = '$nombre_archivo'
            ORDER BY fecha_importacion DESC;";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0) {
        echo "ya se importo";
        unlink("temp/" . $nombre_archivo);
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


    $spreadsheet = IOFactory::load("temp/" . $nombre_archivo);
    $spreadsheet->setActiveSheetIndexByName($hoja);

    $array_codigos_consulta = array();

    $columna_inicio = "E";
    $index_columna_inicio = Coordinate::columnIndexFromString($columna_inicio);
    $columna_final = null;

    $col =  $index_columna_inicio + 1;

    while($col){
        $letraColumna = Coordinate::stringFromColumnIndex($col);

        if ($spreadsheet->getActiveSheet()->getCell($letraColumna . "8")->getValue() === null) {
            //Se reduce el numero de la columna ya que cuando encuentra la columna vacia la ultima columna es la anterior a la vacia
            //Ademas las otras dos son el nombre y fecha de quien reporto (por eso de reduce en 3)
            $col = $col - 3;
            break;
        }
        $col++;
    }
    $columna_final = Coordinate::stringFromColumnIndex($col);
    $index_columna_final = Coordinate::columnIndexFromString($columna_final);

    for($i = $index_columna_inicio; $i <= $index_columna_final ; $i++){
        $letraColumna = Coordinate::stringFromColumnIndex($i);
        //$celda_codigo = $spreadsheet->getActiveSheet()->getCell('J8')->getValue();
        $celda_codigo = $spreadsheet->getActiveSheet()->getCell($letraColumna . '8')->getValue();

        if(strpos($celda_codigo, "=") !== false) { 
            $string = str_replace('=','', $celda_codigo);
            $string = str_replace(' ', '', $string);
            $array = explode('"', $string);


            $array_codigo = $array[1];
            $codigo = explode("(", $array_codigo);

        }
        else{
            $array_codigo = explode("(", $celda_codigo);

            $codigo = array();
            array_push($codigo, "");
            array_push($codigo, str_replace(')','', end($array_codigo)));
            array_push($codigo, "");
            
            for($j = 0 ; $j < count($array_codigo) - 1 ; $j++){
                $codigo[0] .= $array_codigo[$j];

                if($j + 1 < count($array_codigo) - 1)
                    $codigo[0] .= "(";
            }
        }

        if(isset($codigo[0]) && $codigo[0] != "" && isset($codigo[1]) && $codigo[1]){
            $array_aux = [];
            $array_aux['columna'] = $letraColumna;
            $array_aux['fila'] = '8';
            $array_aux['codigo'] = str_replace(')','', $codigo[1]);
            $array_aux['nombre_codigo'] = $codigo[0];
            array_push($array_codigos_consulta, $array_aux);
        }

    }


    /*echo '<pre>';
    print_r($array_codigos_consulta);
    echo '</pre>';*/

    $fecha = "";
    $fila = 9;
    while($fila){
    //for($i = $fila_inicial; $i < 400 ; $i++){

        $valorCelda = $spreadsheet->getActiveSheet()->getCell('B' . $fila)->getValue();
        if($valorCelda == "") {
            //Si el valor es blanco y ademas no hay fecha o ha recorrido mas de 10 filas y no ha encontrado nada se sale del ciclo
            //esto se hace para evitar que se haga un ciclo infitino
            if($fecha != "" || $fila >= 19){
                $fila--;
                break;
            }
        }
        
        //Con esto evito el error de que añadan una fila de mas al membrete del excel
        //y el $valorCelda no sea una fecha sino un string con el valor 'FECHA'
        /*if(strtoupper($valorCelda) == 'FECHA'){
            $fila++;
            continue;
        }*/

        try {

            //if ($valorCelda instanceof Date) {
            if (is_numeric($valorCelda)) {
                $fecha_obj = Date::excelToDateTimeObject($valorCelda);
                $fecha = $fecha_obj->format('Y-m-d');
            }
            else {
                $fila++;
                continue;
            }


        } catch (\Exception $e) {
            // Maneja valores no válidos y pasa a la siguiente fila
            //echo "Fila {$row->getRowIndex()}: Error procesando celda: {$valorCelda} - {$e->getMessage()}\n";
            //continue;

            $fila++;
            continue;
        }

        //echo "Celda B$fila: " . $fecha . "<br>";

        $cota_actual = $spreadsheet->getActiveSheet()->getCell('C' . $fila)->getValue();

        $sql = "INSERT INTO datos_embalse (id_embalse, fecha, hora, cota_actual, id_encargado, archivo_importacion, fecha_importacion, estatus) VALUES ('$id_embalse', '$fecha', '', '$cota_actual', '$id_encargado', '$nombre_archivo', '$fecha_importacion', 'activo');";
        $res = mysqli_query($conn, $sql);

        if($res == 1){
            //sleep(0.02);
            usleep(20000); // 0.03 segundos = 20000 microsegundos

            $sql = "SELECT id_registro FROM datos_embalse WHERE id_embalse = '$id_embalse' AND id_encargado = '$id_encargado' ORDER BY id_registro DESC LIMIT 1;";
            $query = mysqli_query($conn, $sql);
            $id_registro = mysqli_fetch_array($query)['id_registro'];

            //Ciclo para almacenar extracciones
            for($i = 0 ; $i < count($array_codigos_consulta) ; $i++) {
                $columna = $array_codigos_consulta[$i]['columna'];
                $valor_extraccion_aux = $spreadsheet->getActiveSheet()->getCell($columna . $fila)->getValue();
                if($valor_extraccion_aux != ""){

                    if($spreadsheet->getActiveSheet()->getCell($columna . $fila)->getDataType() === DataType::TYPE_FORMULA) {
                        $valor_extraccion_aux = $spreadsheet->getActiveSheet()->getCell($columna . $fila)->getCalculatedValue();

                        if(!is_numeric($valor_extraccion_aux))
                            $valor_extraccion_aux = 0;
                    }
                    
                    $index_codigo = buscarPosicion($array_codigos_sql, $array_codigos_consulta[$i]['codigo'], 'codigo');
                    $codigo_aux = $array_codigos_sql[$index_codigo]['id_codigo_bd'];
                    $sql = "INSERT INTO detalles_extraccion (id_codigo_extraccion, extraccion, id_registro, estatus) VALUES ('$codigo_aux', '$valor_extraccion_aux', '$id_registro', 'activo');";
                    mysqli_query($conn, $sql);
                }
            }
            
        }
        

        if($fecha >= date("Y-m-d")){
            break;
        }

        $fila++;
    }

    echo 'si';


    /*$excel = PHPExcel_IOFactory::load("temp/" . $nombre_archivo);

        $excel->setActiveSheetIndexByName($hoja);*/

    //echo $contenido_celda = $excel->getActiveSheet()->getCell('C9')->getValue();

    //leer y guardar archivos

    unlink("temp/" . $nombre_archivo);
} else {

    if ($_FILES['file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name'])) {

        //Ubic temporal
        $nombre_temporal = $_FILES['file']['tmp_name'];
        $name = $_FILES['file']['name'];
        $ubicacion = 'temp/' . $name;
        move_uploaded_file($nombre_temporal, $ubicacion);

        //$excel = PHPExcel_IOFactory::load($ubicacion);

echo "se cargo";
        //$spreadsheet = IOFactory::load($ubicacion);
        //$hojas = $spreadsheet->getSheetNames();

        //$excel = PHPExcel_IOFactory::load($ubicacion);

        // Hojas del archivo
        //$hojas = $excel->getSheetNames();

/*
?>
        <div class="table-responsive mb-3 mt-5">
            <table class="table align-items-center text-sm text-center table-sm" id="hojas-excel-table">
                <thead class="table-primary">
                    <tr>
                        <th scope="col" class="sort" data-sort="name">#</th>
                        <th scope="col" class="sort" data-sort="name">Nombre</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody class="list">
                    <?php
                    $i = 1;
                    foreach ($hojas as $hoja) {
                    ?>
                        <tr>
                            <th scope="row">
                                <?php echo $i++; ?>
                            </th>
                            <td scope="row">
                                <div class="media">
                                    <div class="media-body">
                                        <span class="name mb-0"><?php echo $hoja; ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button type="button" title="Importar data" class="btn btn-outline-secondary border-2 mb-0" style="padding: 0.3rem 0.6rem" onclick="importarData('<?php echo $hoja; ?>');">
                                    <i class="fa fa-upload"></i>
                                </button>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

<?php
*/
    } else {
        echo "<h3>Error al subir el archivo.</h3>";
    }

    // Delete file temporal
    //unlink($ubicacion);

}

closeConection($conn);



?>