<?php
    require_once '../../Conexion.php';
    date_default_timezone_set("America/Caracas");
    setlocale(LC_TIME, "spanish");

    $id_embalse = $_POST['id_embalse'];

    $sql = "SELECT DISTINCT nombre_embalse, archivo_importacion, fecha_importacion
            FROM embalses e, datos_embalse de
            WHERE e.id_embalse = de.id_embalse AND archivo_importacion <> '' AND de.estatus = 'activo' AND de.id_embalse = '$id_embalse'
            ORDER BY fecha_importacion DESC;";
    $query = mysqli_query($conn, $sql);
?>

<?php
            if(mysqli_num_rows($query) > 0){
?>
                

                <div class="table-responsive">
                    <div class="mb-3">
                        <table class="table align-items-center text-sm text-center table-sm" id="table-history-excel">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">#</th>
                                    <th scope="col" class="sort" data-sort="name">Archivo de Origen</th>
                                    <th scope="col" class="sort" data-sort="budget">Fecha de Adjunción</th>
                                    <th scope="col" style="min-width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody class="list">
                        

<?php
                $i = 0;
                while($row = mysqli_fetch_array($query)){
                    $i++;
                    $fecha = strftime("%d/%b/%Y", strtotime($row['fecha_importacion']));
                    //$hora = date("g:i a", strtotime($row['hora']));

?>


                                <tr>
                                    <th>
                                        <?php echo $i;?>
                                    </th>

                                    <td> 
                                        <?php echo $row['archivo_importacion']; ?>
                                    </td>
                                    <td>
                                        <?php echo $fecha;?>
                                    </td>
                                    <td>
                                        <a class="btn btn-danger btn-sm px-3 mb-0" href="javascript:;" onclick="openModalDeleteHistoryExcel('<?php echo $id_embalse;?>', '<?php echo $row['nombre_embalse'];?>', '<?php echo $row['archivo_importacion'];?>', '<?php echo $row['fecha_importacion'];?>', 'delete_data_excel');">
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
                <div class="text-center">
                    <button type="button" class="btn btn-secondary mt-4 mb-0 btn-edit" data-bs-dismiss="modal" onclick="openModalHistory(<?php echo $id_embalse;?>, $('#body-details #anio').val(), $('#body-details #mes').val())">Atrás</button>
                </div>