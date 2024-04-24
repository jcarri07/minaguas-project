<?php
require_once '../Conexion.php';
date_default_timezone_set("America/Caracas");
setlocale(LC_TIME, "spanish");

$id_encargado = $_POST['id_encargado'];

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
WHERE id_encargado = '$id_encargado' AND de.estatus = 'activo' 
GROUP BY de.id_registro
ORDER BY fecha DESC, id_registro DESC;";
$query = mysqli_query($conn, $sql);
//$array_codigos_sql = array();


?>

<?php
if (mysqli_num_rows($query) > 0) {
?>
    <div class="table-responsive">
        <div class="mb-3">
            <table class="table align-items-center text-sm text-center table-sm" id="table-history">
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
                    $fecha = strftime("%d/%b/%Y", strtotime($row['fecha']));
                    $hora = date("g:i a", strtotime($row['hora']));

                    $abertura = "";
                    $caudal = "";

                    $extraccion = 0;
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