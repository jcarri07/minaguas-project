<?php
    require_once '../../Conexion.php';
    date_default_timezone_set("America/Caracas");
    setlocale(LC_TIME, "spanish");

    $id_embalse = $_POST['id_embalse'];
    $anio = $_POST['anio'];
    $mes = $_POST['mes'];

    $meses = array(
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    );

    function buscarPosicion($array, $valorABuscar, $columna) {
        //$columna = 'codigo'; // Columna en la que deseas buscar
        $posicion = array_search($valorABuscar, array_column($array, $columna));
        return $posicion !== false ? $posicion : -1;
    }

    //Solo se guardan los codigos que se pueden sumar
    $sql = "SELECT nombre, cantidad_primaria, unidad, codigo, leyenda_sistema, concepto, uso, ce.id AS 'id_codigo_extraccion', IF(leyenda_sistema <> '', leyenda_sistema, concepto) AS 'name'
            FROM tipo_codigo_extraccion tce, codigo_extraccion ce
            WHERE tce.id = ce.id_tipo_codigo_extraccion AND 
                ce.estatus = 'activo' AND 
                tce.estatus = 'activo' AND
                (tce.id = '1' OR tce.id = '2' OR tce.id = '3' OR tce.id = '4')
            ORDER BY codigo ASC;";
    $query_codigos = mysqli_query($conn, $sql);
    $array_codigos_sql = array();

    while($row = mysqli_fetch_array($query_codigos)){
        $array_aux = [];
        $array_aux['id_codigo_bd'] = $row['id_codigo_extraccion'];
        $array_aux['codigo'] = $row['codigo'];
        array_push($array_codigos_sql, $array_aux);
    }

    //Guardando los codigos de abertura y caudal
    $sql = "SELECT id AS 'id_codigo_extraccion', codigo, concepto
            FROM codigo_extraccion ce
            WHERE ce.estatus = 'activo' AND 
                (codigo = '29' OR codigo = '30')
            ORDER BY codigo ASC;";
    $query = mysqli_query($conn, $sql);
    $array_codigos_no_suma = array();

    while($row = mysqli_fetch_array($query)){
        $array_aux = [];
        $array_aux['id_codigo_extraccion'] = $row['id_codigo_extraccion'];
        $array_aux['codigo'] = $row['codigo'];
        $array_aux['concepto'] = $row['concepto'];
        array_push($array_codigos_no_suma, $array_aux);
    }

    $sql = "SELECT DISTINCT YEAR(fecha) AS 'anio'
            FROM datos_embalse
            WHERE id_embalse = '$id_embalse' AND estatus = 'activo'
            ORDER BY anio DESC;";
    $query_anios = mysqli_query($conn, $sql);

    if($anio == '') {
        $anio = mysqli_fetch_array($query_anios)['anio'];
        $query_anios = mysqli_query($conn, $sql);
    }

    /*if($mes != "" && $anio == ''){
        $anio = mysqli_fetch_array($query_anios)['anio'];
        $query_anios = mysqli_query($conn, $sql);
    }*/

    $add_where = "";
    if($anio != '')
        $add_where .= " AND YEAR(fecha) = '$anio' ";
    if($mes != '')
        $add_where .= " AND MONTH(fecha) = '$mes' ";

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
            WHERE id_embalse = '$id_embalse' AND de.estatus = 'activo' $add_where
            GROUP BY de.id_registro
            ORDER BY fecha DESC, id_registro DESC;";
    $query = mysqli_query($conn, $sql);
?>

<?php
            if(mysqli_num_rows($query) > 0){
?>
                <div class="row">
                    <div class="col">
                        <label>Año</label>
                        <div class="input-group mb-3">
                            <select class="form-select" name="anio" id="anio">
                                <!--<option value=''>Todos</option>-->
<?php
                            $i = 0;
                            while($row = mysqli_fetch_array($query_anios)){
                                /*$selected = "";
                                if($i == 0 && $anio == "")
                                    $selected = "selected";
                                $i++;*/
?>
                                <option value='<?php echo $row['anio'];?>' <?php echo ($anio == $row['anio']) ? 'selected' : '';?>><?php echo $row['anio'];?></option>
<?php
                            }
?>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <label>Mes</label>
                        <select class="form-select" name="mes" id="mes">
                            <option value=''>Todos</option>
                            <option value='01'>Enero</option>
                            <option value='02'>Febrero</option>
                            <option value='03'>Marzo</option>
                            <option value='04'>Abril</option>
                            <option value='05'>Mayo</option>
                            <option value='06'>Junio</option>
                            <option value='07'>Julio</option>
                            <option value='08'>Agosto</option>
                            <option value='09'>Septiembre</option>
                            <option value='10'>Octubre</option>
                            <option value='11'>Noviembre</option>
                            <option value='12'>Diciembre</option>
                        </select>
                    </div>
                </div>
<?php
                if($anio != ""){
?>
                <div class="row">
                    <div class="col text-center">
                        <button class="btn btn-primary px-3" data-bs-dismiss="modal" onclick="openModalParametrosAnio('<?php echo $id_embalse;?>', $('#body-details #anio').val(), $('#body-details #mes').val());">
                            <i class="fas fa-info-circle" title="Detalles" aria-hidden="true"></i>
<?php
                            $mes_espaniol = ucfirst($meses[date('n', strtotime("$anio-$mes-01"))]);
?>
                            <span>Detalles y Parámetros de Reportes (<?php echo $mes == "" ? "Año $anio" : "$mes_espaniol, $anio";?>)</span>
                        </button>
                    </div>
                </div>
<?php
                }
?>


                <div class="table-responsive">
                    <div class="mb-3">
                        <table class="table align-items-center text-sm text-center table-sm text-xs text-dark" id="table-history">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">#</th>
                                    <th scope="col" class="sort" data-sort="name">Fecha y Hora (00)</th>
                                    <th scope="col" class="sort" data-sort="budget">Cota (01)</th>
                                    <th scope="col" class="sort" data-sort="budget">Extraccion (1000 <span style="text-transform: lowercase;">m</span><sup>3</sup>) (23)</th>
                                    <th scope="col" class="sort" data-sort="budget">Abertura (cm,%) (29)</th>
                                    <th scope="col" class="sort" data-sort="budget">Caudal (<span style="text-transform: lowercase;">m<sup>3</sup>/s)</span> (30)</th>
                                    <th scope="col" class="sort" data-sort="budget">Cargado por</th>
                                    <th scope="col" style="min-width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody class="list">
                        

<?php
                $i = 0;
                while($row = mysqli_fetch_array($query)){
                    $i++;
                    //$fecha = strftime("%d/%b/%Y", strtotime($row['fecha']));
                    $fecha = date('d', strtotime($row['fecha'])) . "/" . substr($meses[date('n', strtotime($row['fecha']))],0,3) . "./" . date('Y', strtotime($row['fecha']));
                    $hora = date("g:i a", strtotime($row['hora']));

                    $abertura = "";
                    $caudal = "";

                    $extraccion = 0;
                    if(is_string($row['extraccion'])) {
                        $extraccion_array = explode(";", $row['extraccion']);
                        for($j = 0 ; $j < count($extraccion_array) ; $j++) {
                            if($extraccion_array[$j] !== "") {
                                $fila = explode("&", $extraccion_array[$j]);

                                if(buscarPosicion($array_codigos_sql, $fila[0], 'id_codigo_bd') !== -1 && is_numeric($fila[1]))
                                    $extraccion += $fila[1];

                                $index = buscarPosicion($array_codigos_no_suma, $fila[0], 'id_codigo_extraccion');
                                if($index !== -1) {
                                    if($array_codigos_no_suma[$index]['codigo'] == "29") {
                                        if(is_numeric($fila[1])) {
                                            $abertura = ( ($fila[1] < 1) ? ($fila[1] * 100) : $fila[1]);
                                            $abertura = number_format($abertura, 1, ",", "");
                                        }
                                        else
                                            $abertura = $fila[1];
                                    }
                                    if($array_codigos_no_suma[$index]['codigo'] == "30") {
                                        $caudal = $fila[1];
                                    }
                                }
                            }
                        }
                    }
?>


                                <tr>
                                    <th>
                                        <?php echo $i;?>
                                    </th>
                                    <th scope="row">
                                        <div class="media">
                                            <div class="media-body">
                                                <span class="name mb-0"><?php echo $fecha . " " . $hora;?></span>
                                            </div>
                                        </div>
                                    </th>
                                    <td>
                                        <?php echo number_format($row['cota_actual'], 3, '.' , '');?>
                                    </td>
                                    <td> 
                                        <?php echo number_format($extraccion, 3, ".", ""); ?>
                                    </td>
                                    <td> 
                                        <?php echo $abertura; ?>
                                    </td>
                                    <td> 
                                        <?php echo is_numeric($caudal) ? number_format($extraccion, 3, ".", "") : $caudal; ?>
                                    </td>
                                    <td>
                                        <?php echo ($row['encargado'] != "" && $row['encargado'] != NULL) ? $row['encargado'] : "-";?>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btn-sm px-3 mb-0" href="javascript:;" data-bs-dismiss="modal" onclick="openModalDetalles('<?php echo $row['id_registro'];?>', '<?php echo $row['fecha'];?>', '<?php echo $row['hora'];?>', '<?php echo $row['cota_actual'];?>', '<?php echo $row['extraccion'];?>');">
                                            <i class="fas fa-list" title="Detalles" aria-hidden="true"></i>
                                        </a>
                                        <a class="btn btn-danger btn-sm px-3 mb-0" href="javascript:;" onclick="openModalAction('<?php echo $row['id_registro'];?>', 'delete');">
                                            <i class="fas fa-trash" title="Eliminar" aria-hidden="true"></i>
                                        </a>
<?php
                    
?>
                                    </td>
                                </tr>
<?php
                }
?>
                            </tbody>
                        </table>
                    </div>
                </div>
<?php
            }
            else{
?>
                <h2 class="mb-1 text-dark font-weight-bold text-center mt-4">No hay información</h2>
<?php                  
            }
?>

<script>
    $("#mes").val("<?php echo $mes;?>");

    $("#anio, #mes").off("change");
    //function sinDecimales(){}

    $("#anio, #mes").change(function(){
        openModalHistory($("#id_embalse_aux").text(), $("#nombre_embalse_aux").text(), $("#anio").val(), $("#mes").val());
    })
</script>