<?php
    require_once '../../Conexion.php';
    date_default_timezone_set("America/Caracas");
    setlocale(LC_TIME, "spanish");

    $anio = $_POST['anio'];
    $mes = $_POST['mes'];
    $detalles_mes_morosos = $_POST['detalles_mes_morosos'];
    $id_embalse = $_POST['id_embalse'];

    $nombre_embalse = "";

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


    $add_select = "";
    $add_where = "";
    $add_group_by = "";
    $add_order_by = "";
    if($anio != "") {
        $add_select .= ", YEAR(d.fecha) AS anio";
        $add_where .= " AND YEAR(d.fecha) = '$anio' ";
        $add_group_by .= ", YEAR(d.fecha)";
        $add_order_by .= ", anio";
    }
    if($mes != "") {
        $add_select .= ", MONTH(d.fecha) AS mes";
        $add_where .= " AND MONTH(d.fecha) = '$mes' ";
        $add_group_by .= ", MONTH(d.fecha)";
        $add_order_by .= ", mes";
    }
    /*if($dia != "") {
        $add_select .= ", DAY(d.fecha) AS dia";
        $add_where .= " AND DAY(d.fecha) = '$dia' ";
        $add_group_by .= ", DAY(d.fecha)";
        $add_order_by .= ", dia";
    }*/
    if($detalles_mes_morosos == "si") {
        $add_select .= ", DAY(d.fecha) AS dia";
        if($mes != "") 
            $add_where .= " AND e.id_embalse = '$id_embalse' ";
        $add_group_by .= ", DAY(d.fecha)";
        $add_order_by .= ", dia";
    }

    $anio_inicio = date("Y");
    $fecha_limite = "";
    if($anio != "") {
        $fecha_inicio = "$anio";
        if($anio != date("Y"))
            $fecha_limite = "$anio-12-31";
        else
            $fecha_limite = "CURDATE()";
    }

    $sql = "WITH RECURSIVE dates AS (
                SELECT DATE('$anio_inicio-01-01') AS fecha
                UNION ALL
                SELECT DATE_ADD(fecha, INTERVAL 1 DAY)
                FROM dates
                WHERE fecha < $fecha_limite
            )
            SELECT e.id_embalse AS id_embalse, e.nombre_embalse AS 'nombre_embalse', COUNT(d.fecha) AS 'reportes_faltantes',
                (
                    SELECT IF(COUNT(u.id_usuario) > 0, CONCAT(P_Nombre, ' ', P_Apellido), '')
                    FROM usuarios u 
                    WHERE u.id_usuario = de.id_encargado
                ) AS 'encargado'
                $add_select
            FROM embalses e
            JOIN dates d
            LEFT JOIN datos_embalse de ON e.id_embalse = de.id_embalse AND d.fecha = de.fecha
            WHERE de.id_registro IS NULL $add_where
            GROUP BY e.id_embalse $add_group_by
            ORDER BY e.id_embalse $add_order_by;";
    $query = mysqli_query($conn, $sql);

    

    if($detalles_mes_morosos == "si") {
        $events = array();
        while($row = $query->fetch_assoc()) {
            //$events[] = $row;

            $array_aux = [];
            $array_aux['id'] = "";
            $array_aux['start'] = $row['anio'] . "-" . ($row['mes'] < 10 ? ("0" . $row['mes']) : $row['mes']) . "-" . ($row['dia'] < 10 ? ("0" . $row['dia']) : $row['dia']);
            $array_aux['end'] = $row['anio'] . "-" . ($row['mes'] < 10 ? ("0" . $row['mes']) : $row['mes']) . "-" . ($row['dia'] < 10 ? ("0" . $row['dia']) : $row['dia']);
            $array_aux['backgroundColor'] = "#ff3c3c";
            $array_aux['borderColor'] = "#ff3c3c";
            array_push($events, $array_aux);
        }


        //Agregando en verde los dias del mes que si tienen reportes
        $dias_del_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);

        for($i = 1 ; $i <= $dias_del_mes ; $i++){
            $aux_dia = $i < 10 ? ("0" . $i) : $i;
            $fecha_a_buscar = "$anio-$mes-$aux_dia";
            if(buscarPosicion($events, $fecha_a_buscar, 'start') == -1) {
                $array_aux = [];
                $array_aux['id'] = "";
                $array_aux['start'] = $fecha_a_buscar;
                $array_aux['end'] = $fecha_a_buscar;
                $array_aux['backgroundColor'] = "#30d82f";
                $array_aux['borderColor'] = "#30d82f";
                array_push($events, $array_aux);
            }
        }


        $sql = "SELECT nombre_embalse FROM embalses WHERE id_embalse = '$id_embalse';";
        $query = mysqli_query($conn, $sql);
        $row_embalse = mysqli_fetch_array($query);
        $nombre_embalse = $row_embalse['nombre_embalse'];

    }


    closeConection($conn);



    if($detalles_mes_morosos == "si") {
?>
        <div class="text-center">
            <button type="button" class="btn btn-primary btn-sm" onclick="morosos('no', '');">
                <i class="fa fa-arrow-left"></i>
                Atrás
            </button>
        </div>
<?php
    }



            if(mysqli_num_rows($query) > 0) {
?>
                <h3 class="text-center">Reportes Faltantes en <?php echo ($mes != "" ? ($meses[($mes < 10 ? $mes % 10 : $mes)] . " de ") : "") . $anio . ($nombre_embalse != "" ? (" del Embalse $nombre_embalse") : "");?></h3>
<?php
                if($detalles_mes_morosos != "si") {

?>

                
                <div class="table-responsive">
                    <div class="mb-3">
                        <table class="table align-items-center text-sm text-center table-sm text-xs text-dark" id="table-morosos">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">#</th>
                                    <th scope="col" class="sort" data-sort="name">Embalse</th>
                                    <th scope="col" class="sort" data-sort="budget">Encargado</th>
<?php
                                if($mes == "") {
?>
                                    <th scope="col" class="sort" data-sort="budget">Reportes faltantes en el año</th>
<?php
                                }
                                else {
?>
                                    <th scope="col" class="sort" data-sort="budget">Reportes faltantes en el mes</th>
<?php
                                }

?>                           
                                    <th scope="col" style="min-width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody class="list">
                        

<?php
                $i = 0;
                while($row = mysqli_fetch_array($query)){
                    $i++;
?>


                                <tr>
                                    <th>
                                        <?php echo $i;?>
                                    </th>
                                    <td>
                                        <?php echo $row['nombre_embalse'];?>
                                    </td>
                                    <td> 
                                        <?php echo $row['encargado'] != "" ? $row['encargado'] : "-"; ?>
                                    </td>
<?php
                                //if($dia == ""){
?>
                                    <td> 
                                        <?php echo $row['reportes_faltantes']; ?>
                                    </td>
<?php
                                //}

?>  
                                    
                                    <td>
<?php
                                if($mes != ""){
?>
                                        <a class="btn btn-primary btn-xs px-3 mb-0" href="javascript:;" onclick="detalles_morosos_mes('<?php echo $row['id_embalse'];?>');">
                                            Detalles
                                        </a>
<?php
                                }
?>
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
                else {
?>


                    <style>
                        #calendar {
                            max-width: 800px;
                            margin: 0px auto;
                        }
                    </style>

                    <div class="row justify-content-center mb-1">
                        <div class="col-md-3 col-6 text-center">
                            <div style="background: #ff3c3c; color: white; max-width: 120px; margin: auto; border-radius: 5px;">Faltantes</div>
                        </div>
                    </div>
                    <div class="row justify-content-center mb-3">
                        <div class="col-md-3 col-6 text-center">
                            <div style="background: #30d82f; color: white; max-width: 120px; margin: auto; border-radius: 5px;">Reportados</div>
                        </div>
                    </div>

                    <div id="calendar"></div>

                    <script>
                        var events = <?php echo json_encode($events);?>;

                        var calendarEl = document.getElementById('calendar');
                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            //height: 600,
                            plugins: [ 'dayGrid', 'interaction' ],
                            defaultDate: "<?php echo $anio . "-" . $mes  . "-01";?>",
                            locale: 'es',
                            header: false,
                            showNonCurrentDates: false,
                            
                            /*dateClick: function(info) {
                                alert('Clicked on: ' + info.dateStr);
                            
                                eventsArray.push({
                                    date: info.dateStr,
                                    title: "test event added from click"
                                });
                                
                                calendar.refetchEvents();
                            },*/
                        
                            /*eventClick: function(info) {
                                alert(info.event.title)
                            },*/
                        
                            events: function(info, successCallback, failureCallback) {
                                successCallback(events);
                            }
                        });

                        calendar.render();
                    </script>

<?php
                }
            }
            else{
?>
                <h2 class="mb-1 text-dark font-weight-bold text-center mt-4">No hay morosos</h2>
<?php                  
            }
?>

<script>
    /*$("#mes").val("<?php echo $mes;?>");

    /*$("#anio, #mes").off("change");
    //function sinDecimales(){}

    $("#anio, #mes").change(function(){
        openModalHistory($("#id_embalse_aux").text(), $("#nombre_embalse_aux").text(), $("#anio").val(), $("#mes").val());
    })*/
</script>