<?php
    require_once '../../Conexion.php';
    date_default_timezone_set("America/Caracas");
    setlocale(LC_TIME, "spanish");

    $id_embalse = $_POST['id_embalse'];
    $nombre_embalse = $_POST['nombre_embalse'];
    $anio = $_POST['anio'];
    $mes = $_POST['mes'];

    $dias_del_anio = array();

    $dia = strtotime($anio . '-01-01');

    while (date('Y', $dia) == $anio) {
        $dias_del_anio[] = date('Y-m-d', $dia);
        
        $dia = strtotime('+1 day', $dia);
    }

    //echo count($dias_del_anio);

    

    //Solo se guardan los codigos que se pueden sumar
    /*$sql = "SELECT nombre, cantidad_primaria, unidad, codigo, leyenda_sistema, concepto, uso, ce.id AS 'id_codigo_extraccion', IF(leyenda_sistema <> '', leyenda_sistema, concepto) AS 'name'
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

    $sql = "SELECT DISTINCT YEAR(fecha) AS 'anio'
            FROM datos_embalse
            WHERE id_embalse = '$id_embalse' AND estatus = 'activo'
            ORDER BY anio DESC;";
    $query_anios = mysqli_query($conn, $sql);*/

    /*if($mes != "" && $anio == ''){
        $anio = mysqli_fetch_array($query_anios)['anio'];
        $query_anios = mysqli_query($conn, $sql);
    }*/

    /*$add_where = "";
    if($anio != '')
        $add_where .= " AND YEAR(fecha) = '$anio' ";
    if($mes != '')
        $add_where .= " AND MONTH(fecha) = '$mes' ";

    /*$sql = "SELECT de.id_registro AS 'id_registro', fecha, hora, cota_actual, 
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
            ORDER BY fecha DESC, id_registro DESC;";*/

    function buscarPosicion($array, $valorABuscar, $columna) {
        //$columna = 'codigo'; // Columna en la que deseas buscar
        $posicion = array_search($valorABuscar, array_column($array, $columna));
        return $posicion !== false ? $posicion : -1;
    }

    $add_where = "";
    if($mes != '')
        $add_where .= " AND MONTH(dem.fecha) = '$mes' ";

    $sql = "SELECT 
                COUNT(DISTINCT fecha) AS total_registros/*,
                (DATEDIFF('$anio-12-31', '$anio-01-01') + 1) AS total_dias_del_anio,
                ((DATEDIFF('$anio-12-31', '$anio-01-01') + 1) - COUNT(DISTINCT fecha)) AS dias_faltantes,
                (COUNT(DISTINCT fecha) / (DATEDIFF('$anio-12-31', '$anio-01-01') + 1)) * 100 AS porcentaje_informacion_faltante*/
            FROM 
                datos_embalse de
            WHERE id_embalse = '$id_embalse' AND de.estatus = 'activo' AND YEAR(fecha) = $anio;";
    $query = mysqli_query($conn, $sql);
    $porcentaje_faltante = mysqli_fetch_array($query);

    $sql = "SELECT 
                fecha
            FROM 
                datos_embalse de
            WHERE id_embalse = '$id_embalse' AND de.estatus = 'activo' AND YEAR(fecha) = $anio
            ORDER BY fecha DESC;";
    $query = mysqli_query($conn, $sql);
    $ultimo_reporte = mysqli_fetch_array($query)['fecha'];


    $sql = "SELECT codigo, 
                    leyenda_sistema, 
                    ROUND(SUM(CAST(extraccion AS DOUBLE)), 5) AS 'suma', 
                    COUNT(extraccion) AS 'cantidad', 
                    SUM(CASE WHEN extraccion > 0 THEN 1 ELSE 0 END) AS 'cant_mayor_0'
            FROM detalles_extraccion de, codigo_extraccion ce, tipo_codigo_extraccion tce, datos_embalse dem
            WHERE de.id_codigo_extraccion = ce.id AND ce.id_tipo_codigo_extraccion = tce.id AND dem.id_registro = de.id_registro
                AND (tce.id = '1' OR tce.id = '2' OR tce.id = '3' OR tce.id = '4') 
                AND id_embalse = '$id_embalse' 
                AND dem.estatus = 'activo' 
                AND YEAR(dem.fecha) = '$anio' $add_where
            GROUP BY ce.id;";
    $query = mysqli_query($conn, $sql);
    $array_sumas = array();

    while($row = mysqli_fetch_array($query)){
        $array_aux = [];
        //$array_aux['id_codigo_bd'] = $row['id_codigo_extraccion'];
        $array_aux['codigo'] = $row['codigo'];
        $array_aux['suma'] = $row['suma'];
        $array_aux['cantidad'] = $row['cantidad'];
        $array_aux['cant_mayor_0'] = $row['cant_mayor_0'];
        array_push($array_sumas, $array_aux);
    }


    //Solo se guardan los codigos que se pueden sumar
    $sql = "SELECT nombre, cantidad_primaria, unidad, codigo, leyenda_sistema, concepto, uso, ce.id AS 'id_codigo_extraccion', tce.id AS 'id_tipo_codigo_extraccion', IF(leyenda_sistema <> '', leyenda_sistema, concepto) AS 'name'
            FROM tipo_codigo_extraccion tce, codigo_extraccion ce
            WHERE tce.id = ce.id_tipo_codigo_extraccion AND 
                ce.estatus = 'activo' AND 
                tce.estatus = 'activo' AND
                (tce.id = '1' OR tce.id = '2' OR tce.id = '3' OR tce.id = '4')
            ORDER BY codigo ASC;";
    $query = mysqli_query($conn, $sql);



    $total_registros = $porcentaje_faltante['total_registros'];
    $total_dias_anio = 1;
    $dia = strtotime($anio . '-01-01');
    while (date('Y', $dia) == $anio) {
        //$dias_del_anio[] = date('Y-m-d', $dia);
        $total_dias_anio++;
        $dia = strtotime('+1 day', $dia);

        if(date("Y-m-d") == date("Y-m-d", $dia))
            break;

        if(date($anio . "-12-31") == date("Y-m-d", $dia))
            $total_dias_anio--;
    }
?>

<?php
            //if(mysqli_num_rows($query) > 0){
?>
                <div class="row justify-content-center">
                    <div class="col-sm-10 punteado">
                        <h4 class="text-center">Relación del <?php echo $anio;?></h4>
                        <table class="table align-items-center text-sm text-center table-sm">
                            <tbody class="list">
                                <tr>
                                    <th>% de Información faltante en el año <?php echo ($anio == date("Y")) ? " hasta la fecha" : "";?></th>
                                    <td><?php echo number_format(100 - ($total_registros * 100 / $total_dias_anio), 2, ".", "");?>%</td>
                                </tr>
                                <tr>
                                    <th>Información Faltante (días) <?php echo ($anio == date("Y")) ? " en el año" : "";?></th>
                                    <td><?php echo $total_dias_anio - $total_registros;?></td>
                                </tr>
                                <tr>
                                    <th>Días transcurridos en el año</th>
                                    <td><?php echo $total_dias_anio;?></td>
                                </tr>
                                <tr>
                                    <th>Fecha del Último Reporte</th>
                                    <td><?php echo strftime("%d/%b/%Y", strtotime($ultimo_reporte));?></td>
                                </tr>
                                <tr>
                                    <th>Fecha Inicial</th>
                                    <td><?php echo "01/01/" . $anio;?></td>
                                </tr>
                                <tr>
                                    <th>Fecha Final</th>
                                    <td><?php echo "31/12/" . $anio;?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row justify-content-center mt-4">
                    <div class="col-sm-12">
<?php
                        $mes_espaniol = ucfirst(strftime("%B", strtotime("$anio-$mes-01")));
?>
                        <h4 class="text-center">Sumatorias y Promedios <?php echo $mes == "" ? "Año $anio" : "$mes_espaniol, $anio";?></h4>
                        <table class="table align-items-center text-sm text-center table-sm table-punteada">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">#</th>
                                    <th scope="col" class="sort" data-sort="name"></th>
                                    <th scope="col" class="sort" data-sort="name">Código</th>
                                    <th scope="col" class="sort" data-sort="budget">Leyenda</th>
                                    <th scope="col" class="sort" data-sort="budget">Concepto</th>
                                    <th scope="col" class="sort" data-sort="budget">Sumatoria (1000 <span style="text-transform: lowercase;">m</span><sup>3</sup>)</th>
                                    <th scope="col" class="sort" data-sort="budget">Promedio (1000 <span style="text-transform: lowercase;">m</span><sup>3</sup>)</th>
                                </tr>
                            </thead>
                            <tbody class="list">
<?php
                            $i = 1;
                            $sum_cat = 0;
                            $sum_total = 0;
                            while($row = mysqli_fetch_array($query)) {
                                $es_total = false;
                                $negrita = "";
                                $celda_subtotal_top_bottom = "";
                                $celda_subtotal_left = "";
                                $celda_subtotal_right = "";
                                $cancel_border_right = "";
                                
                                if($row['codigo'] == "08" || $row['codigo'] == "13" || $row['codigo'] == "18" || $row['codigo'] == "22")
                                    $es_total = true;

                                if($es_total) {
                                    $negrita = "font-weight: bold;";
                                    $celda_subtotal_top_bottom = "cell-subtotal-top-bottom";
                                    $celda_subtotal_left = "cell-subtotal-left";
                                    $celda_subtotal_right = "cell-subtotal-right";
                                    $cancel_border_right = "border-right: none;";
                                }
?>
                                <tr style="<?php echo $cancel_border_right;?>">
                                    <th><?php echo $i++;?></th>
                                    <td class="code_<?php echo $row['id_tipo_codigo_extraccion']?>" title="<?php echo $row['nombre']?>"></td>
                                    <td><?php echo $row['codigo'];?></td>
                                    <td><?php echo $row['leyenda_sistema'];?></td>
                                    <td style="<?php echo $negrita;?>" class="<?php echo $celda_subtotal_top_bottom . " " . $celda_subtotal_left;?>"><?php echo $row['concepto'];?></td>
                                    <td style="<?php echo $negrita;?>" class="<?php echo $celda_subtotal_top_bottom;?>">
<?php
                                        $index = buscarPosicion($array_sumas, $row['codigo'], 'codigo');
                                        if($index !== -1) {
                                            echo number_format($array_sumas[$index]['suma'], 3, ",", "");
                                            $sum_cat += $array_sumas[$index]['suma'];
                                            $sum_total += $array_sumas[$index]['suma'];
                                        }
                                        else
                                            echo "";

                                        if($es_total) {
                                            echo number_format($sum_cat, 3, ",", "");
                                            $sum_cat = 0;
                                        }
                                        
?>
                                    </td>
                                    <td style="<?php echo $negrita;?>"  class="<?php echo $celda_subtotal_top_bottom . " " . $celda_subtotal_right;?>">
<?php 
                                        if($index !== -1)
                                            echo number_format( ($array_sumas[$index]['suma'] / $array_sumas[$index]['cant_mayor_0']) , 3, ",", "");
                                        else
                                            echo "";

                                        if($es_total) 
                                            echo "-";
?>  
                                    </td>

                                </tr>
<?php
                                if($es_total) {
?>
                                    <tr>
                                        <th style="padding: 20px 0;"></th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
<?php
                                }
                            }
?>
                            </tbody>
                        </table>


                        <div class="punteado">
                            <table class="table align-items-center text-center text-dark" style="margin-bottom: 0px; font-size: 1.2rem;">
                                <tbody class="list">
                                    <tr>
                                        <th style="width: 50%;">Total Descargas (23)</th>
                                        <th style="width: 50%; color: #1B569D;"><?php echo number_format($sum_total, 2, ".", "");?></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
<?php
            /*}
            else{
?>
                <h2 class="mb-1 text-dark font-weight-bold text-center mt-4">No hay información</h2>
<?php                  
            }*/
?>

                <div class="text-center">
                    <button type="button" class="btn btn-secondary mt-4 mb-0 btn-edit" data-bs-dismiss="modal" onclick="openModalHistory('<?php echo $id_embalse;?>', '<?php echo $nombre_embalse;?>', $('#body-details #anio').val(), $('#body-details #mes').val())">Atrás</button>
                </div>

<script>

    function buscarPosicion(array, valorABuscar, columna) {
        for (var i = 0; i < array.length; i++) {
            if (array[i][columna] === valorABuscar) {
                return i;
            }
        }
        return -1;
    }

    $(document).ready(function() {
        var item = "";
        var cant = 1;
        var array = [];
        $("td[class^=code_]").each(function(index) {
            var index_aux = buscarPosicion(array, $(this).attr("class"), "class");
            if(index_aux !== -1) {
                array[index_aux]["cant"]++;
            }
            else {
                var aux = {"class": $(this).attr("class"), "nombre": $(this).attr("title"), "cant": 1};
                array.push(aux);
            }
        });

        for(var i = 0 ; i < array.length ; i++) {
            $("td[class=" + array[i]['class'] + "]").each(function(index) {
                if(index == 0) {
                    $(this).text(array[i]['nombre']);
                    $(this).attr("rowspan", array[i]['cant']);
                }
                else{
                    $(this).remove();
                }
            });
        }
    });
</script>