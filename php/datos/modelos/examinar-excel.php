<?php
//require_once '../../../vendor/PHPExcel/Classes/PHPExcel.php';

require_once '../../../vendor/autoload.php';

//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


if (isset($_POST['opc']) && $_POST['opc'] == "importar_data") {
    $hoja = $_POST['hoja'];
    $id_embalse = $_POST['id_embalse'];
    $nombre_archivo = $_POST['nombre_archivo'];

    $spreadsheet = IOFactory::load("temp/" . $nombre_archivo);
    $spreadsheet->setActiveSheetIndexByName($hoja);



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

        echo "columna: " . $letraColumna;
        echo ", numero: " . str_replace(')','', $codigo[1]);
        echo ", nombre codigo: " . $codigo[0];
        echo "<br>";
    }



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


        $spreadsheet = IOFactory::load($ubicacion);
        $hojas = $spreadsheet->getSheetNames();

        //$excel = PHPExcel_IOFactory::load($ubicacion);

        // Hojas del archivo
        //$hojas = $excel->getSheetNames();


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

    } else {
        echo "<h3>Error al subir el archivo.</h3>";
    }

    // Delete file temporal
    //unlink($ubicacion);

}





?>