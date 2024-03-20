<?php
    require_once '../../Conexion.php';
    date_default_timezone_set("America/Caracas");
    setlocale(LC_TIME, "spanish");

    $id_embalse = $_POST['id_embalse'];

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
            WHERE id_embalse = '$id_embalse' AND de.estatus = 'activo'
            GROUP BY de.id_registro
            ORDER BY fecha DESC, id_registro DESC;";
    $query = mysqli_query($conn, $sql);
?>

<?php
            if(mysqli_num_rows($query) > 0){
?>
                <div class="table-responsive">
                    <div class="mb-3">
                        <table class="table align-items-center text-sm text-center table-sm" id="table-history">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">#</th>
                                    <th scope="col" class="sort" data-sort="name">Fecha y Hora (00)</th>
                                    <th scope="col" class="sort" data-sort="budget">Cota (01)</th>
                                    <th scope="col" class="sort" data-sort="budget">Extraccion (Hm<sup>3</sup>) (23)</th>
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

                    $extraccion = 0;
                    $extraccion_array = explode(";", $row['extraccion']);
                    for($j = 0 ; $j < count($extraccion_array) ; $j++) {
                        if($extraccion_array[$j] !== "") {
                            $fila = explode("&", $extraccion_array[$j]);
                            $extraccion += $fila[1];
                        }
                    }
?>


                                <tr>
                                    <td>
                                        <?php echo $i;?>
                                    </td>
                                    <th scope="row">
                                        <div class="media">
                                            <div class="media-body">
                                                <span class="name mb-0"><?php echo $fecha . " " . $hora;?></span>
                                            </div>
                                        </div>
                                    </th>
                                    <td>
                                        <?php echo $row['cota_actual'];?>
                                    </td>
                                    <td> 
                                        <?php echo $extraccion; ?>
                                    </td>
                                    <td>
                                        <?php echo ($row['encargado'] != "" && $row['encargado'] != NULL) ? $row['encargado'] : "-";?>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btn-sm px-3 mb-0" href="javascript:;" onclick="openModalDetalles('<?php echo $row['id_registro'];?>', '<?php echo $row['fecha'];?>', '<?php echo $row['hora'];?>', '<?php echo $row['cota_actual'];?>', '<?php echo $row['extraccion'];?>');">
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