<?php
    require_once '../../Conexion.php';

    $anio = "";
    if(isset($_GET['anio']))
        $anio = $_GET['anio'];
    else {
        echo "Seleccione un año";
        exit;
    }

    require_once '../../../vendor/autoload.php';
    /*date_default_timezone_set("America/Caracas");
    setlocale(LC_TIME, "spanish");*/

    /*function pixelsToColumnWidth($pixels)
    {
        // Aproximación: 1 unidad de ancho de columna equivale a aproximadamente 7 píxeles
        return $pixels / 7;
    }*/

    function buscarPosicion($array, $valorABuscar, $columna) {
        //$columna = 'codigo'; // Columna en la que deseas buscar
        $posicion = array_search($valorABuscar, array_column($array, $columna));
        return $posicion !== false ? $posicion : -1;
    }

    $diasSemanaEspañol = [
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    ];

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Border;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    use PhpOffice\PhpSpreadsheet\Style\Color;
    use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

    $sql = "SELECT DISTINCT id_embalse, nombre_embalse, estado, municipio, parroquia, id_encargado
            FROM embalses em, estados e, municipios m, parroquias p
            WHERE em.id_estado = e.id_estado AND em.id_municipio = m.id_municipio AND em.id_parroquia = p.id_parroquia AND m.id_estado = e.id_estado AND p.id_municipio = m.id_municipio AND em.estatus = 'activo';";
    $query = mysqli_query($conn, $sql);

    $sql = "SELECT  tce.id AS 'id_tipo_extraccion', COUNT(tce.id) AS 'cant', nombre, cantidad_primaria, unidad
            FROM tipo_codigo_extraccion tce, codigo_extraccion ce
            WHERE tce.id = ce.id_tipo_codigo_extraccion AND 
            ce.estatus = 'activo' AND 
            tce.estatus = 'activo' AND 
            tce.id <> '6' AND 
            tce.id <> '7' AND
            ce.concepto <> 'Subtotal'
            GROUP BY tce.id
            ORDER BY codigo ASC;";
    $query_tipos_de_extraccion = mysqli_query($conn, $sql);

    $array_tipos_de_extraccion = array();
    while($row = mysqli_fetch_array($query_tipos_de_extraccion)){
        $array_aux = [];
        $array_aux['id_tipo_extraccion'] = $row['id_tipo_extraccion'];
        $array_aux['cant'] = $row['cant'];
        $array_aux['nombre'] = $row['nombre'];
        $array_aux['cantidad_primaria'] = $row['cantidad_primaria'];
        $array_aux['unidad'] = $row['unidad'];
        array_push($array_tipos_de_extraccion, $array_aux);
    }

    $sql = "SELECT tce.id AS 'id_tipo_extraccion', codigo, leyenda_sistema, concepto, uso, ce.id AS 'id_codigo_extraccion', IF(leyenda_sistema <> '', leyenda_sistema, concepto) AS 'name'
            FROM tipo_codigo_extraccion tce, codigo_extraccion ce
            WHERE tce.id = ce.id_tipo_codigo_extraccion AND 
                ce.estatus = 'activo' AND 
                tce.estatus = 'activo' AND 
                tce.id <> '6' AND 
                tce.id <> '7' AND
                ce.concepto <> 'Subtotal'
            ORDER BY codigo ASC;";
    $query_codigos = mysqli_query($conn, $sql);

    $array_codigos = array();
    while($row = mysqli_fetch_array($query_codigos)){
        $array_aux = [];
        $array_aux['id_tipo_extraccion'] = $row['id_tipo_extraccion'];
        $array_aux['codigo'] = $row['codigo'];
        $array_aux['leyenda_sistema'] = $row['leyenda_sistema'];
        $array_aux['concepto'] = $row['concepto'];
        $array_aux['name'] = $row['name'];
        $array_aux['id_codigo_extraccion'] = $row['id_codigo_extraccion'];
        $array_aux['columna'] = "";
        $array_aux['sumatoria'] = 0;
        $array_aux['cantidad'] = 0;

        if ($row['id_tipo_extraccion'] == '1' ||
            $row['id_tipo_extraccion'] == '2' ||
            $row['id_tipo_extraccion'] == '3' ||
            $row['id_tipo_extraccion'] == '4'
        ) {
            $array_aux['sumable'] = true;
        }
        else
            $array_aux['sumable'] = false;

        array_push($array_codigos, $array_aux);
    }

    if(mysqli_num_rows($query) > 0) {

        $spreadsheet = new Spreadsheet();

        $i = 0;
        while($row = mysqli_fetch_array($query)) {

            //Reseteando las cantidades y sumatorias a 0 para cada embalse
            foreach($array_codigos as &$codigo) {
                $codigo['sumatoria'] = 0;
                $codigo['cantidad'] = 0;
            }
            unset($codigo);




            if($i == 0) {
                $hoja = $spreadsheet->getActiveSheet();
                $hoja->setTitle(mb_strtoupper($row['nombre_embalse']));
            }
            else {
                $hoja = $spreadsheet->createSheet();
                $hoja->setTitle(mb_strtoupper($row['nombre_embalse']));
            }

            $i++;

            /*for ($i = 1; $i <= 100; $i++) {
                $hoja->setCellValue('A' . $i, 'Dato ' . $i);
            }*/

            $hoja->freezePane('D9');

            $hoja->getDefaultColumnDimension()->setWidth(12.56);
            $hoja->getColumnDimension('A')->setWidth(11.11);
            $hoja->getColumnDimension('B')->setWidth(13.89);
            $hoja->getColumnDimension('C')->setWidth(11.11);
            $hoja->getColumnDimension('D')->setWidth(12.89);
            $hoja->getColumnDimension('T')->setWidth(48.89);
            $hoja->getColumnDimension('U')->setWidth(16.89);
            $hoja->getColumnDimension('V')->setWidth(32.22);


            $hoja->mergeCells('A1:B1');
            $hoja->mergeCells('A2:B2');
            $hoja->mergeCells('A3:B3');
            $hoja->mergeCells('A4:B4');
            $hoja->mergeCells('A7:B7');
            /*$hoja->mergeCells('E7:H7');
            $hoja->mergeCells('I7:L7');
            $hoja->mergeCells('M7:P7');
            $hoja->mergeCells('Q7:S7');
            $hoja->mergeCells('T7:U7');
            $hoja->mergeCells('V7:W7');*/

            $hoja->mergeCells('C7:C8');
            $hoja->mergeCells('D7:D8');

            $hoja->getRowDimension(8)->setRowHeight(53.40);


            
            $color = '5B9BD5'; 
            $hoja->getStyle("A7")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
            $hoja->getStyle("A8")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
            $hoja->getStyle("B8")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
            $hoja->getStyle("C7")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);

            $color = 'DDEBF7';
            $hoja->getStyle("D7")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);

            /*$color = "2F75B5";
            $hoja->getStyle("E7")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);

            $color = "A9D08E";
            $hoja->getStyle("I7")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);

            $color = "92D050";
            $hoja->getStyle("M7")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);

            $color = "ED7D31";
            $hoja->getStyle("Q7")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);*/

            $tipo_extraccion = "";
            $cont = 0;
            $index_columna_inicio = Coordinate::columnIndexFromString("E");
            $columna_inicio = "E";
            $index_columna_final = "";
            $columna_final = "";
            $tipo_extraccion_actual = "";

            //Añadiendo los tipo de extracciones
            foreach($array_tipos_de_extraccion as $tipo_extraccion) {
                $index_columna_final = $index_columna_inicio + $tipo_extraccion['cant'] - 1;
                $columna_final = Coordinate::stringFromColumnIndex($index_columna_final);

                $hoja->mergeCells($columna_inicio . '7:' . $columna_final. '7');

                $color = "FFFFFF";
                if($tipo_extraccion['id_tipo_extraccion'] == '1')
                    $color = "2F75B5";
                if($tipo_extraccion['id_tipo_extraccion'] == '2')
                    $color = "A9D08E";
                if($tipo_extraccion['id_tipo_extraccion'] == '3')
                    $color = "92D050";
                if($tipo_extraccion['id_tipo_extraccion'] == '4')
                    $color = "ED7D31";

                $hoja->getStyle($columna_inicio . "7")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
                

                $string = $tipo_extraccion['nombre'];
                if($tipo_extraccion['cantidad_primaria'] != "" && $tipo_extraccion['cantidad_primaria'] != "0")
                    $string .= " (" . $tipo_extraccion['cantidad_primaria'] . " " . $tipo_extraccion['unidad'] . ")";

                $hoja->setCellValue($columna_inicio . "7", $string);

                $hoja->getStyle($columna_inicio . "7")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $hoja->getStyle($columna_inicio . "7")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);


                //Añadiendo codigos de las extracciones
                $index_column_aux = $index_columna_inicio;
                $column_aux = Coordinate::stringFromColumnIndex($index_column_aux);
                foreach($array_codigos as &$codigo) {
                    if($codigo['id_tipo_extraccion'] == $tipo_extraccion['id_tipo_extraccion']) {

                        $codigo['columna'] = $column_aux;

                        //Se ingresa mas abajo en conjunto con el promedio
                        //$hoja->setCellValue($column_aux . "8", $codigo['name'] . " (" . $codigo['codigo'] . ")");

                        $hoja->getStyle($column_aux . "8")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $hoja->getStyle($column_aux . "8")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                        if($codigo['id_tipo_extraccion'] != "5")
                            $hoja->getStyle($column_aux . "8")->getFont()->setSize(9);

                        $index_column_aux += 1;
                        $column_aux = Coordinate::stringFromColumnIndex($index_column_aux);
                    }
                }
                unset($codigo);


                //Obteniendo el siguente tipo de extraccion
                $index_columna_inicio = $index_columna_final + 1;
                $columna_inicio = Coordinate::stringFromColumnIndex($index_columna_inicio);

            }



            //La columna que dice "reportado por"
            $index_columna_final = $index_columna_inicio + 1;
            $columna_final = Coordinate::stringFromColumnIndex($index_columna_final);
            $COLUMNA_FINAL_REPORTE = $columna_final;
            $hoja->mergeCells($columna_inicio . '7:' . $columna_final. '7');
            $hoja->setCellValue($columna_inicio . "7", "Reportado por");

            $hoja->getStyle($columna_inicio . "7")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $hoja->getStyle($columna_inicio . "7")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            
            $hoja->setCellValue($columna_inicio . "8", "Nombre");
            $hoja->getStyle($columna_inicio . "8")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $hoja->getStyle($columna_inicio . "8")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $hoja->setCellValue($columna_final . "8", "Fecha");
            $hoja->getStyle($columna_final . "8")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $hoja->getStyle($columna_final . "8")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);


            //Bordes
            $celdaInicio = 'A7';
            $celdaFin = $columna_final . '8';

            // Aplicar bordes a las celdas del rango específico
            $hoja->getStyle($celdaInicio . ':' . $celdaFin)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ]);


            //Añadiendo membrete
            $hoja->setCellValue('A1', '% De información faltante hasta la fecha:');
            $hoja->setCellValue('A2', 'Información Faltante (días):');
            $hoja->setCellValue('A3', 'Días Transcurridos:');
            $hoja->setCellValue('A4', 'Información Faltante del Año:');
            $hoja->setCellValue('A5', 'Embalse:');
            $hoja->setCellValue('B5', mb_strtoupper($i));
            $hoja->setCellValue("A7", 'FECHA');

            $styleCell = $hoja->getStyle("A7");
            $styleCell->getFont()->setBold(true);
            $styleCell->getAlignment()->setWrapText(true);
            $styleCell->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $styleCell->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $styleCell->getFont()->setColor(new Color(Color::COLOR_WHITE));


            $hoja->setCellValue("A8", 'DIA');
            $styleCell = $hoja->getStyle("A8");
            $styleCell->getFont()->setBold(true);
            $styleCell->getAlignment()->setWrapText(true);
            $styleCell->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $styleCell->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $styleCell->getFont()->setColor(new Color(Color::COLOR_WHITE));

            $hoja->setCellValue("B8", 'FECHA');
            $styleCell = $hoja->getStyle("B8");
            $styleCell->getFont()->setBold(true);
            $styleCell->getAlignment()->setWrapText(true);
            $styleCell->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $styleCell->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $styleCell->getFont()->setColor(new Color(Color::COLOR_WHITE));

            $hoja->setCellValue("C7", 'Cota Actual (msnm)');
            $styleCell = $hoja->getStyle("C7");
            $styleCell->getFont()->setBold(true);
            $styleCell->getAlignment()->setWrapText(true);
            $styleCell->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $styleCell->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $styleCell->getFont()->setColor(new Color(Color::COLOR_WHITE));

            $hoja->setCellValue("D7", 'Dias de Reserva de Agua');
            $styleCell = $hoja->getStyle("D7");
            $styleCell->getFont()->setBold(true);
            $styleCell->getAlignment()->setWrapText(true);
            $styleCell->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $styleCell->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);



            //Filas de extracciones
            $sql = "SELECT de.id_registro AS 'id_registro', fecha, hora, cota_actual, 
                        (
                            SELECT GROUP_CONCAT(id_codigo_extraccion, '&', extraccion, '&', id_detalles_extraccion SEPARATOR ';')
                            FROM detalles_extraccion dex
                            WHERE de.id_registro = dex.id_registro
                        ) AS 'extraccion', 
                        (
                            SELECT CONCAT(P_Nombre, ' ', P_Apellido) 
                            FROM usuarios u 
                            WHERE u.id_usuario = de.id_encargado
                        ) AS 'encargado'
                    FROM datos_embalse de
                    WHERE id_embalse = '$row[id_embalse]' AND de.estatus = 'activo' AND YEAR(fecha) = '$anio'
                    GROUP BY de.id_registro
                    ORDER BY fecha DESC, id_registro DESC;";
            $query_extracciones = mysqli_query($conn, $sql);
            
            $array_extracciones = array();
            while($row = mysqli_fetch_array($query_extracciones)){
                $array_aux = [];
                $array_aux['id_registro'] = $row['id_registro'];
                $array_aux['fecha'] = $row['fecha'];
                $array_aux['hora'] = $row['hora'];
                $array_aux['cota_actual'] = $row['cota_actual'];
                $array_aux['extraccion'] = $row['extraccion'];
                $array_aux['encargado'] = $row['encargado'];
                array_push($array_extracciones, $array_aux);
            }

            $ultimoDiaDelAnio = date('Y-m-d', strtotime("$anio-12-31"));

            // Calcular la cantidad de días en el año sumando 1 al último día del año
            $numberOfDays = date('z', strtotime($ultimoDiaDelAnio)) + 1;

            $dia_actual = "$anio-01-01";
            $fila_actual = 9;
            $cant_registros = 0;
            //$columna_actual = "A";
            for($i = 0 ; $i < $numberOfDays ; $i++) {

                $hoja->setCellValue("A" . $fila_actual, $diasSemanaEspañol[date('l', strtotime($dia_actual))]);
                $hoja->getStyle("A" . $fila_actual)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $hoja->setCellValue("B" . $fila_actual, date("d/m/Y",strtotime($dia_actual)));
                $hoja->getStyle("B" . $fila_actual)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $hoja->getStyle("B", $fila_actual)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('DDEBF7');


                //$filasEncontradas = [];
                /*foreach ($array_extracciones as $extraccion) {
                    if ($extraccion['fecha'] === date("Y-m-d", strtotime($dia_actual))) {
                        //$filasEncontradas[] = $extraccion;

                        $hoja->setCellValue("C" . $fila_actual, $extraccion['cota_actual']);
                        $hoja->getStyle("C" . $fila_actual)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        echo $extraccion['fecha'] . " - " . date("Y-m-d", strtotime($dia_actual)) . "<br>";
                    }
                    
                }*/

                //Aplicando color a las celdas (tarda en generarse el excel)
                /*$index_aux = Coordinate::columnIndexFromString($COLUMNA_FINAL_REPORTE);
                $final_aux = Coordinate::stringFromColumnIndex($index_aux - 4);
                //echo 'C' , $fila_actual . ":" . $final_aux . $fila_actual; echo "<br>";
                $hoja->getStyle('C' . $fila_actual . ":" . $final_aux . $fila_actual)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF4D5'],
                    ],
                ]);*/


                //Añadiendo extracciones
                $index_row = buscarPosicion($array_extracciones, date("Y-m-d", strtotime($dia_actual)), 'fecha');
                if($index_row != -1) {
                    $cant_registros++;
                    $extraccion = $array_extracciones[$index_row];
                    $hoja->setCellValue("C" . $fila_actual, $extraccion['cota_actual']);
                    $hoja->getStyle("C" . $fila_actual)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $extraccion_aux = explode(";", $extraccion['extraccion']);
                    for($j = 0 ; $j < count($extraccion_aux) ; $j++) {
                        if($extraccion_aux[$j] !== "") {
                            $fila = explode("&", $extraccion_aux[$j]);

                            $index_extraccion = buscarPosicion($array_codigos, $fila[0], 'id_codigo_extraccion');
                            $columna_extraccion = $array_codigos[$index_extraccion]['columna'];

                            //AUmentado la cantidad de reportes y su suma
                            if( $array_codigos[$index_extraccion]['sumable'] == true &&
                                $fila[1] != "" && 
                                $fila[1] != 0 && 
                                $fila[1] != "0"
                            ) {
                                $array_codigos[$index_extraccion]['sumatoria'] += $fila[1];
                                $array_codigos[$index_extraccion]['cantidad']++;
                            }

                            $valor_extraccion = $fila[1];
                            if($array_codigos[$index_extraccion]['id_codigo_extraccion'] == "30") {
                                if(is_numeric($valor_extraccion)) {
                                    $valor_extraccion = $fila[1] . "%";
                                    if($fila[1] < 1) {
                                        $valor_extraccion = ($fila[1] * 100) . "%";
                                    }
                                }
                            }

                            $hoja->setCellValue($columna_extraccion . $fila_actual, $valor_extraccion);
                            $hoja->getStyle($columna_extraccion . $fila_actual)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            $hoja->getStyle($columna_extraccion . $fila_actual)->getNumberFormat()->setFormatCode('0.00');

                        }
                    }

                    //Añadiendo informacion a la celda de "reportado"
                    $index_aux = Coordinate::columnIndexFromString($COLUMNA_FINAL_REPORTE);
                    $final_aux = Coordinate::stringFromColumnIndex($index_aux - 1);
                    $hoja->setCellValue($final_aux . $fila_actual, $extraccion['encargado']);
                    $hoja->getStyle($final_aux . $fila_actual)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }


                

                /*foreach ($filasEncontradas as $extraccion) {
                    if($extraccion)
                }*/


                //Bordes en las celdas de extracciones (tarda en generarse el excel)
                $celdaInicio = 'A' . $fila_actual;
                $celdaFin = $COLUMNA_FINAL_REPORTE . $fila_actual;

                // Aplicar bordes a las celdas del rango específico
                /*$hoja->getStyle($celdaInicio . ':' . $celdaFin)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);*/

                $dia_actual = date("Y-m-d",strtotime($dia_actual."+ 1 days")); 
                $fila_actual++;

            }


            //Añadiendo valores de estadisticas a la cabecera de la hoja
            $dia_del_anio_aux = (date("Y") == $anio) ? (date('z') + 1) : $numberOfDays;
            $hoja->setCellValue('C1', number_format(100 - ($cant_registros * 100 / $dia_del_anio_aux), 2, ".", "") . "%"); // % de informacion faltante hasta la fecha
            $hoja->getStyle("C1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            //$hoja->setCellValue('D1', date('z') );
            $hoja->setCellValue('C2', $numberOfDays - $cant_registros); // Información Faltante (días)
            $hoja->getStyle("C2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $hoja->setCellValue('C3', $dia_del_anio_aux); // Días transcurridos
            $hoja->getStyle("C3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $hoja->setCellValue('C4', number_format(100 - ($cant_registros * 100 / $numberOfDays), 2, ".", "") . "%"); // % de informacion faltante del año 
            $hoja->getStyle("C4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


            //Añadiendo los promedios a los codigos
            //aca no se necesita la referencia con el "&", ya que no se asignara ningun valor al array
            foreach($array_codigos as $codigo) {
                if($codigo['sumable']) {
                    $divisor = $codigo['cantidad'] > 0 ? $codigo['cantidad'] : 1;
                    $promedio = number_format( ($codigo['sumatoria'] / $divisor), 2, ',', '');

                    $contenido_celda = $codigo['name'] . " (" . $codigo['codigo'] . ")" . "\n" . $promedio;
                    $hoja->setCellValue($codigo['columna'] . "8", $contenido_celda);

                    //Esto es para que se implemente el salto de linea en la celda
                    $hoja->getStyle($codigo['columna'] . "8")->getAlignment()->setWrapText(true);
                }
            }
        }


        //Generar el documento excel
        $writer = new Xlsx($spreadsheet);

        // Guardar el archivo en el servidor o enviarlo al navegador para descarga
        $nombreArchivo = "EXTRACCIONES $anio.xlsx";
        $writer->save($nombreArchivo);

        if (file_exists($nombreArchivo)) {
            // Configurar los encabezados HTTP para indicar que se va a descargar un archivo
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . basename($nombreArchivo) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($nombreArchivo));
            // Leer el archivo y enviar su contenido al navegador
            readfile($nombreArchivo);
            // Eliminar el archivo del servidor (opcional)
            unlink($nombreArchivo);
            exit;
        } else {
            die('El archivo no existe.');
        }

    }

    closeConection($conn);
?>