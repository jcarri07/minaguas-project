<?php
//require_once '../../../vendor/PHPExcel/Classes/PHPExcel.php';

require_once '../../../vendor/autoload.php';

//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


if (isset($_POST['opc']) && $_POST['opc'] == "importar_data") {
    $hoja = $_POST['hoja'];
    $id_embalse = $_POST['id_embalse'];
    $nombre_archivo = $_POST['nombre_archivo'];

    $spreadsheet = IOFactory::load("temp/" . $nombre_archivo);
    $spreadsheet->setActiveSheetIndexByName($hoja);

    echo $contenido_celda = $spreadsheet->getActiveSheet()->getCell('C9')->getValue();

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

        $excel = PHPExcel_IOFactory::load($ubicacion);


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