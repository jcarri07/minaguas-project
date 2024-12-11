<?php
    ini_set('memory_limit', '4G');
    ini_set('max_execution_time', 400);
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

    function convertirValor($valor) {
        $valor = str_replace(',', '.', $valor);

        if (is_numeric($valor)) {
            return floatval($valor);
        }
        
        return null;
    }

    $array_total = array();
    

    $diasSemanaEspañol = [
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    ];

    $sql = "SELECT DISTINCT id_embalse, nombre_embalse, estado, municipio, parroquia, id_encargado
            FROM embalses em, estados e, municipios m, parroquias p
            WHERE em.id_estado = e.id_estado AND em.id_municipio = m.id_municipio AND em.id_parroquia = p.id_parroquia AND m.id_estado = e.id_estado AND p.id_municipio = m.id_municipio AND em.estatus = 'activo'
            /*AND em.id_embalse = '32'*/
            /*limit 25*/;";
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
            ORDER BY id_tipo_extraccion ASC;";
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


    $array_extracciones_all = array();
    $array_embalses_all = array();
    if(mysqli_num_rows($query) > 0) {
        while($row_embalse = mysqli_fetch_array($query)) {
            //while($row = mysqli_fetch_array($query_tipos_de_extraccion)){
            /*$array_aux = [];
            $array_aux['id_embalse'] = $row_embalse['id_embalse'];
            $array_aux['nombre_embalse'] = $row_embalse['nombre_embalse'];
            $array_aux['estado'] = $row_embalse['estado'];
            $array_aux['municipio'] = $row_embalse['municipio'];
            $array_aux['parroquia'] = $row_embalse['parroquia'];
            $array_aux['id_encargado'] = $row_embalse['id_encargado'];
            array_push($array_embalses_all, $array_aux);*/
            //}

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
                WHERE id_embalse = '$row_embalse[id_embalse]' AND de.estatus = 'activo' AND YEAR(fecha) = '$anio'
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

            $array_aux_2 = [];
            $array_aux_2['id_embalse'] = $row_embalse['id_embalse'];
            $array_aux_2['nombre_embalse'] = $row_embalse['nombre_embalse'];
            $array_aux_2['estado'] = $row_embalse['estado'];
            $array_aux_2['municipio'] = $row_embalse['municipio'];
            $array_aux_2['parroquia'] = $row_embalse['parroquia'];
            $array_aux_2['id_encargado'] = $row_embalse['id_encargado'];
            $array_aux_2['extracciones'] = $array_extracciones;
            //array_push($array_embalses_all, $array_aux);
            $array_embalses_all[$row_embalse['id_embalse']] = $array_aux_2;

            //$array_embalses_all[$row_embalse['id_embalse']] = $array_extracciones;
        }
    }

    $array_total['tipos_extraccion'] = $array_tipos_de_extraccion;
    $array_total['array_codigos'] = $array_codigos;
    $array_total['array_embalses_all'] = $array_embalses_all;

    closeConection($conn);

    echo json_encode(['array_total' => $array_total]);
?>