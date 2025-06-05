<script>
  $("#breadcrumb .modulo").text("Principal");
  $("#breadcrumb .submodulo").text("Carga de Datos");
</script>

<?php

require_once 'php/Conexion.php';

$add_where = "";

if ($_SESSION["Tipo"] == "User") {
  $add_where .= " AND id_encargado = '$_SESSION[Id_usuario]'";
}

$fecha = date("Y-m-d");

/*$sql = "SELECT DISTINCT id_embalse, nombre_embalse, estado, municipio, parroquia, id_encargado, (SELECT (IF(COUNT(id_registro) > 0, 'si', 'no')) FROM datos_embalse de WHERE de.id_embalse = em.id_embalse AND estatus = 'activo' AND fecha = '$fecha' ) AS 'reportado_hoy'
          FROM embalses em, estados e, municipios m, parroquias p
          WHERE em.id_estado = e.id_estado AND em.id_municipio = m.id_municipio AND em.id_parroquia = p.id_parroquia AND m.id_estado = e.id_estado AND p.id_municipio = m.id_municipio AND em.estatus = 'activo' $add_where;";*/


$sql = "SELECT DISTINCT em.id_embalse, 
            em.nombre_embalse,
            /*GROUP_CONCAT(DISTINCT e.estado) AS estado,*/
            GROUP_CONCAT(DISTINCT m.municipio) AS municipio,
            /*GROUP_CONCAT(DISTINCT p.parroquia) AS parroquia,*/
            em.id_encargado, (SELECT IF(COUNT(id_registro) > 0, 'si', 'no')
          FROM datos_embalse de
          WHERE FIND_IN_SET(de.id_embalse, em.id_embalse) AND
              estatus = 'activo' AND
              fecha = '$fecha'
            ) AS reportado_hoy
          FROM embalses em 
          /*LEFT JOIN estados e ON FIND_IN_SET(e.id_estado, em.id_estado) OR em.id_estado = '' */
          LEFT JOIN municipios m ON FIND_IN_SET(m.id_municipio, em.id_municipio) OR em.id_municipio = '' 
          /*LEFT JOIN parroquias p ON FIND_IN_SET(p.id_parroquia, em.id_parroquia) OR em.id_parroquia = '' */
          WHERE em.estatus = 'activo' $add_where
          GROUP BY em.id_embalse, em.nombre_embalse, em.id_encargado;";

//invalidando la consulta anterior
$sql = "WITH reporte_hoy AS (
          SELECT 
            id_embalse,
            IF(COUNT(id_registro) > 0, 'si', 'no') AS reportado_hoy
          FROM datos_embalse
          WHERE estatus = 'activo' AND fecha = '$fecha'
          GROUP BY id_embalse
        )
        SELECT DISTINCT 
          em.id_embalse, 
          em.nombre_embalse,
          GROUP_CONCAT(DISTINCT e.estado) AS estado,
          em.id_encargado,
          COALESCE(rh.reportado_hoy, 'no') AS reportado_hoy,
          CONCAT(P_Nombre, ' ', P_Apellido) AS 'encargado',
          o.operador AS 'operador'
        FROM embalses em
        LEFT JOIN estados e
          ON FIND_IN_SET(e.id_estado, em.id_estado) OR em.id_estado = ''
        LEFT JOIN reporte_hoy rh
          ON rh.id_embalse = em.id_embalse
        LEFT JOIN usuarios u
          ON em.id_encargado = u.id_usuario
        LEFT JOIN operadores o
          ON em.operador = o.id_operador
        WHERE em.estatus = 'activo' $add_where
        GROUP BY em.id_embalse, em.nombre_embalse, em.id_encargado;";

$query = mysqli_query($conn, $sql);


$sql = "SELECT nombre, cantidad_primaria, unidad, codigo, leyenda_sistema, concepto, uso, ce.id AS 'id_codigo_extraccion', IF(leyenda_sistema <> '', leyenda_sistema, concepto) AS 'name'
          FROM tipo_codigo_extraccion tce, codigo_extraccion ce
          WHERE tce.id = ce.id_tipo_codigo_extraccion AND 
            ce.estatus = 'activo' AND 
            tce.estatus = 'activo' AND 
            tce.id <> '6' AND 
            tce.id <> '7' AND
            ce.concepto <> 'Subtotal'
          ORDER BY (codigo + 0) ASC;";
$query_codigos = mysqli_query($conn, $sql);

$sql = "SELECT DISTINCT YEAR(fecha) AS 'anio'
    FROM datos_embalse
    WHERE estatus = 'activo'
    ORDER BY anio DESC;";
$query_anios = mysqli_query($conn, $sql);

closeConection($conn);

/*$options_extraccion = '<option value="">Seleccione</option>';
  $options_extraccion .='<option value="Riego">Riego</option>';
  $options_extraccion .='<option value="Hidroelectricidad">Hidroelectricidad</option>';
  $options_extraccion .='<option value="Consumo Humano">Consumo Humano</option>';
  $options_extraccion .='<option value="Control de Inundaciones (Aliviadero)">Control de Inundaciones (Aliviadero)</option>';
  $options_extraccion .='<option value="Recreación">Recreación</option>';*/

$array_codigos = array();
$options_extraccion = "<option value=''>Seleccione</option>";

while ($row = mysqli_fetch_array($query_codigos)) {
  $options_extraccion .= "<option value='$row[id_codigo_extraccion]'>$row[name] ($row[codigo])</option>";

  $array_aux = [];
  $array_aux['id_codigo_bd'] = $row['id_codigo_extraccion'];
  $array_aux['codigo'] = $row['codigo'];
  $array_aux['nombre'] = $row['nombre'];
  array_push($array_codigos, $array_aux);
}

?>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script> -->
<script src="./assets/js/xlsx.full/xlsx.full.min.js"></script>

<div class="container-fluid py-4">
  <div class="row">

    <div class="col-lg-12">
      <div class="card h-100 mb-3">
        <div class="card-header pb-0">
          <!-- <div class="row"> -->
          <!-- <div class="col-6 d-flex align-items-center"> -->
          <h4>CARGA DE DATOS</h4>
          <!-- </div> -->
          <!--<div class="col-6 text-end">
                  <button class="btn btn-outline-primary btn-sm mb-0">View All</button>
                </div>-->
          <!-- </div> -->
        </div>

        <div class="card-body p-3 pb-0">
          <!--<div class="text-center">
                <button type="button" class="btn bg-gradient-info btn-block" data-bs-toggle="modal" data-bs-target="#add">
                  Nuevo
                </button>
              </div>-->


<?php
          if (mysqli_num_rows($query) > 0) {
?>
            <div class="text-center">
<?php
            if ($_SESSION["Tipo"] == "Admin" || $_SESSION["Tipo"] == "SuperAdmin") {
?>
              <button type="button" class="btn btn-primary" onclick="exportExcel();">
                Exportar Extracciones en Excel
                <i class="fa fa-file-excel-o" style="margin-left: 5px; font-size: 18px;"></i>
              </button>
<?php
              }
              
              // El botón de morosos está disponible para Admin, SuperAdmin y Visitante
              if ($_SESSION["Tipo"] == "Admin" || $_SESSION["Tipo"] == "SuperAdmin" || $_SESSION["Tipo"] == "Visitante") {
?>
                <button type="button" class="btn btn-danger" onclick="morosos('no', '');">
                  Morosos por Reportar
                  <i class="fa fa-exclamation-triangle" style="margin-left: 5px; font-size: 18px;"></i>
                </button>
<?php
              }
?>
            </div>
            <div class="table-responsive mb-3">
              <table class="table align-items-center text-sm text-center table-sm" id="table">
                <thead class="table-primary">
                  <tr>
                    <th scope="col" class="sort" data-sort="name">ESTADO</th>
                    <th scope="col" class="sort" data-sort="name">EMBALSE</th>
                    <th scope="col" class="sort" data-sort="name">HIDROLÓGICA</th>
                    <th scope="col" class="sort" data-sort="name">ENCARGADO</th>
                    <th scope="col" class="sort" data-sort="name">MOROSIDAD (AÑO)</th>
                    <!-- <th scope="col" class="sort hide-cell" data-sort="budget">Municipio</th> Ubicación -->
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody class="list">


                  <?php
                  while ($row = mysqli_fetch_array($query)) {
                  ?>


                    <tr>
                      <th scope="row">
                        <div class="media">
                          <div class="media-body">
                            <?php
                              $estado = str_replace(",", ", ", $row['estado']);
                              if (strlen($estado) > 150)
                                $estado = substr($estado, 0, 150) . "...";
                              echo $estado;
                            ?>
                          </div>
                        </div>
                      </th>
                      <th scope="row">
                        <div class="media">
                          <div class="media-body">
                            <span class="name mb-0 text-dark"><?php echo $row['nombre_embalse']; ?></span>
                          </div>
                        </div>
                      </th>
                      <th scope="row">
                        <div class="media">
                          <div class="media-body">
                            <span class="name mb-0 text-dark"><?php echo $row['operador']; ?></span>
                          </div>
                        </div>
                      </th>
                      <th scope="row">
                        <div class="media">
                          <div class="media-body">
                            <span class="name mb-0 text-dark"><?php echo $row['encargado']; ?></span>
                          </div>
                        </div>
                      </th>
                      <th scope="row">
                        <div class="media">
                          <div class="media-body">
                            <span class="name mb-0 text-dark" id="morosidad_embalse_<?php echo $row['id_embalse']; ?>"></span>
                          </div>
                        </div>
                      </th>
                      <!-- <td class="hide-cell" style="white-space: normal !important;"> -->
                        <?php
                        //echo $row['estado'] . ", " . $row['municipio'] . ", " . $row['parroquia'];
                        // $municipio = str_replace(",", ", ", $row['municipio']);
                        // if (strlen($municipio) > 150)
                        //   $municipio = substr($municipio, 0, 150) . "...";
                        // echo $municipio;
                        ?>
                      <!-- </td> -->
                      <td>
                        <!--<a class="btn btn-primary btn-sm px-3 mb-0" href="javascript:;" onclick="$('#add').modal('show');">
                      <i class="fas fa-plus text-dark me-2" aria-hidden="true"></i>
                      Añadir Reporte
                    </a>-->
<?php
                        if ($row['reportado_hoy'] == "si") {
?>
                          <h6 class="mb-1 text-dark font-weight-bold text-sm">El reporte de hoy fue realizado <i class="fas fa-check text-lg text-green me-2"></i></h6>
<?php
                        }

                        if (($_SESSION["Tipo"] == "Admin" || $_SESSION["Tipo"] == "SuperAdmin") || ($_SESSION["Tipo"] == "User" && $row['reportado_hoy'] == "no")) {
?>
                          <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="openModalAdd('<?php echo $row['id_embalse']; ?>', '<?php echo $row['nombre_embalse']; ?>');">
                            <i class="fas fa-plus text-dark me-2" aria-hidden="true"></i>
                            <span class="hide-cell">Añadir Reporte</span>
                          </a>
<?php
                        }

                        //if ($_SESSION["Tipo"] == "Admin" || $_SESSION["Tipo"] == "SuperAdmin") {
?>
                          <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="openModalHistory('<?php echo $row['id_embalse']; ?>', '<?php echo $row['nombre_embalse']; ?>', '', '');">
                            <i class="fas fa-history text-dark me-2" aria-hidden="true"></i>
                            <span class="hide-cell">Historial de Extracciones</span>
                          </a>
<?php
                        //}
?>
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
            if ($_SESSION["Tipo"] == "User") {
            ?>
              <h2 class="mb-1 text-dark font-weight-bold text-center mt-4">No hay embalses asignados</h2>
              <br><br><br>
            <?php
            } else {
            ?>
              <h2 class="mb-1 text-dark font-weight-bold text-center mt-4">No hay información</h2>
              <br><br><br>
          <?php
            }
          }
          ?>
          <!--<li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark font-weight-bold text-sm">16 de Octubre 2023</h6>
                    <span class="text-xs">Boconó - Tucupido</span>
                  </div>
                  <div class="d-flex align-items-center text-sm">
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="$('#add').modal('show');"><i class="fas fa-plus text-dark me-2" aria-hidden="true"></i>Añadir Datos</a>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="$('#datos').modal('show');"><i class="fas fa-list text-dark me-2" aria-hidden="true"></i>Listar Historial de Datos</a>
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="text-dark mb-1 font-weight-bold text-sm">10 de Febrero 2021</h6>
                    <span class="text-xs">Embalse 2</span>
                  </div>
                  <div class="d-flex align-items-center text-sm">
         
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="$('#add').modal('show');"><i class="fas fa-plus text-dark me-2" aria-hidden="true"></i>Añadir Datos</a>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="$('#datos').modal('show');"><i class="fas fa-list text-dark me-2" aria-hidden="true"></i>Listar Historial de Datos</a>
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="text-dark mb-1 font-weight-bold text-sm">05 de April 2020</h6>
                    <span class="text-xs">Embalse 3</span>
                  </div>
                  <div class="d-flex align-items-center text-sm">
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="$('#add').modal('show');"><i class="fas fa-plus text-dark me-2" aria-hidden="true"></i>Añadir Datos</a>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="$('#datos').modal('show');"><i class="fas fa-list text-dark me-2" aria-hidden="true"></i>Listar Historial de Datos</a>
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="text-dark mb-1 font-weight-bold text-sm">25 de Junio 2019</h6>
                    <span class="text-xs">Embalse 4</span>
                  </div>
                  <div class="d-flex align-items-center text-sm">
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-plus text-dark me-2" aria-hidden="true"></i>Añadir Datos</a>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="$('#datos').modal('show');"><i class="fas fa-list text-dark me-2" aria-hidden="true"></i>Listar Historial de Datos</a>
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="text-dark mb-1 font-weight-bold text-sm">01 de Marzo 2019</h6>
                    <span class="text-xs">Embalse 5</span>
                  </div>
                  <div class="d-flex align-items-center text-sm">
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-plus text-dark me-2" aria-hidden="true"></i>Añadir Datos</a>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="$('#datos').modal('show');"><i class="fas fa-list text-dark me-2" aria-hidden="true"></i>Listar Historial de Datos</a>
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                  </div>
                </li>-->

        </div>
      </div>
    </div>
  </div>

  <!--<footer class="footer pt-3  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="copyright text-center text-sm text-muted text-lg-start">
                © <script>
                  document.write(new Date().getFullYear())
                </script>,
                desarrollado por
                <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Dirección de Investigación e Innovación - ABAE
                </a>
              </div>
            </div>
            <div class="col-lg-6">
              <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                <li class="nav-item">
                  <a href="https://www.creative-tim.com" class="nav-link text-muted" target="_blank">Creative Tim</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/presentation" class="nav-link text-muted" target="_blank">About Us</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/blog" class="nav-link text-muted" target="_blank">Blog</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/license" class="nav-link pe-0 text-muted" target="_blank">License</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>-->
</div>




<div class="modal fade" id="modal-details" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <h3 class="font-weight-bolder text-primary text-gradient text-title">Historial de Extracciones</h3>
            <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">
              <i class="fa fa-times"></i>
            </button>
<?php 
          if($_SESSION['Tipo'] != 'Visitante') {
?>
            <div class="text-center">
              <button type="button" class="btn btn-success mt-4 mb-0" title="Historial de Todas la importaciones Datos de Excel (Parte Base)" data-bs-dismiss="modal" onclick="openModalHistoryAdjunciones($('#id_embalse_aux').text());">Historial de Adjunciones</button>
            </div>
<?php 
          }
?>
          </div>
          <div class="card-body pb-3" id="body-details">

          </div>
          <div class="card-footer text-center pt-0 px-sm-4 px-1">
            <!--<p class="mb-4 mx-auto">
                  Already have an account?
                  <a href="javascrpt:;" class="text-primary text-gradient font-weight-bold">Guardar</a>
                </p>--->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>





<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <h3 class="font-weight-bolder text-primary text-gradient title"></h3>
            <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">
              <i class="fa fa-times"></i>
            </button>
            <!--<p class="mb-0">Enter your email and password to register</p>-->
          </div>
          <div class="card-body pb-3">
            <?php
            if ($_SESSION["Tipo"] == "Admin" || $_SESSION["Tipo"] == "SuperAdmin") {
            ?>
              <div class="row">
                <div class="col-md-12 text-center">
                  <button class="btn btn-success" data-bs-dismiss="modal" id="btn-open-modal-import-data" onclick="openModalAddDataOld();" type="button">Adjuntar Historial de Extracciones</button>
                </div>
              </div>
            <?php
            }
            ?>

            <form role="form text-left" id="form">
              <?php
              if (date("H:i") . ":00" > "10:00:00") {
              ?>
                <h6 class="text-red text-center text-retraso">Estás retrasado al enviar el reporte</h6>
              <?php
              }
              ?>
              <div class="row">
                <div class="col">
                  <label>Fecha (00)</label>
                  <div class="input-group mb-3">
                    <input type="date" class="form-control" name="fecha" id="fecha" value="<?php echo date("Y-m-d"); ?>" disabled required>
                  </div>
                </div>

                <div class="col">
                  <label>Hora</label>
                  <div class="input-group mb-3">
                    <input type="time" class="form-control" name="hora" id="hora" value="<?php echo date("H:i") . ":00"; ?>" disabled required>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <label>Cota (01)</label>
                  <div class="input-group mb-3">
                    <input type="number" step="0.00001" class="form-control" name="valor_cota" id="valor_cota" placeholder="Cota" aria-label="Cota" aria-describedby="name-addon" onkeydown="preventInvalidInput(event);" required>
                  </div>
                </div>
              </div>

              <div class="row" id="box-area-volumen-by-cota">

              </div>

              <h6 class="mt-2">Extracción</h6>
              <div id="box-extraccion">
                <div class="row">
                  <div class="col">
                    <label>Código</label>
                    <div class="input-group mb-4">
                      <select class="form-select" name="tipo_extraccion[]" id="tipo_extraccion_1" onchange="changeTipoExtraccion(this);" required>
                        <?php echo $options_extraccion; ?>
                      </select>
                    </div>
                  </div>
                  <div class="col">
                    <label>Valor</label> <!-- (1000 m<sup>3</sup>)-->
                    <div class="input-group mb-4">
                      <input type="number" step="0.00001" class="form-control" name="valor_extraccion[]" id="valor_extraccion_1" placeholder="Valor" onkeydown="preventInvalidInput(event);" required>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 text-center" style="margin-top: 12px;">
                  <button class="btn btn-success btn-add-extraccion" id="addRows" type="button">Añadir Otro</button>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 loaderParent">
                  <div class="loader">
                  </div>
                  Por favor, espere
                </div>
              </div>

              <div class="row" id="msg-error">

              </div>

              <div class="text-center">
                <button type="submit" class="btn bg-gradient-primary btn-lg w-100 mt-4 mb-0 btn-submit">Guardar</button>
                <button type="button" class="btn btn-secondary mt-4 mb-0 btn-edit" data-bs-dismiss="modal" style="display: none;">Cerrar</button>
                <!--<button type="button" class="btn bg-gradient-primary mt-4 mb-0 btn-edit" style="display: none;">Editar</button>-->
              </div>
            </form>
          </div>
          <div class="card-footer text-center pt-0 px-sm-4 px-1">
            <!--<p class="mb-4 mx-auto">
                  Already have an account?
                  <a href="javascrpt:;" class="text-primary text-gradient font-weight-bold">Guardar</a>
                </p>--->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="add-data-old" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal" onclick="$('#add').modal('show');">
              <i class="fa fa-times"></i>
            </button>
            <h3 class="font-weight-bolder text-primary text-gradient mt-5 text-center title">Adjuntar Excel de Reportes</h3>
          </div>
          <div class="card-body pb-3">

            <form id="form-add-data-old">
              <div class="form-group col-sm-12">
                <span style="color: red; margin: 0 2px 0 -5px;">*</span><label for="file" class="form-label">Archivo</label>
                <input type="file" id="file" accept=".xlsx, .xls" class="form-control input_file_oculto" oninvalid="setCustomValidity('Debe cargar un archivo')">
                <div class="form-control button_fantasma">
                  <span></span>
                  <button type="button" class="btn btn-primary">Cargar Archivo</button>
                </div>
              </div>
              <div class="text-center">
                <button type="button" class="btn btn-secondary mt-4 mb-0 btn-edit" data-bs-dismiss="modal" onclick="$('#add').modal('show');">Atrás</button>
                <button type="submit" class="btn bg-gradient-primary mt-4 mb-0 btn-submit">Examinar</button>
              </div>
            </form>

            <!--<div class="row">
                  <div class="col-md-12 loaderParent">
                    <div class="loader">
                    </div>
                    Por favor, espere
                  </div>
                </div>-->

            <div id="hojas-excel">

            </div>

          </div>
          <div class="card-footer text-center pt-0 px-sm-4 px-1">
            <!--<p class="mb-4 mx-auto">
                  Already have an account?
                  <a href="javascrpt:;" class="text-primary text-gradient font-weight-bold">Guardar</a>
                </p>--->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




<div class="modal fade" id="modal-history-excel" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <h3 class="font-weight-bolder text-primary text-gradient title">Historial de Adjunciones del Embalse</h3>
            <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal" onclick="$('#modal-details').modal('show');">
              <i class="fa fa-times"></i>
            </button>
          </div>
          <div class="card-body pb-3" id="body-history-excel">

          </div>
          <div class="card-footer text-center pt-0 px-sm-4 px-1">
            <!--<p class="mb-4 mx-auto">
                  Already have an account?
                  <a href="javascrpt:;" class="text-primary text-gradient font-weight-bold">Guardar</a>
                </p>--->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="select-anio-export-excel" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <h3 class="font-weight-bolder text-primary text-gradient title"></h3>
            <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">
              <i class="fa fa-times"></i>
            </button>
          </div>
          <div class="card-body pb-3">

            <form id="form-export-excel">
              <div class="row">
                <div class="col">
                  <label>Seleccione el Año</label>
                  <div class="input-group mb-4">
                    <select class="form-select" name="anio_export_excel" id="anio_export_excel" required>
                      <option value="">Seleccione</option>
                      <?php
                      while ($row = mysqli_fetch_array($query_anios)) {
                      ?>
                        <option value="<?php echo $row['anio']; ?>"><?php echo $row['anio']; ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 loaderParent">
                  <div class="loader">
                  </div>
                  Por favor, espere
                </div>
              </div>

              <div class="text-center">
                <button type="button" class="btn btn-secondary mt-4 mb-0 btn-edit" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn bg-gradient-primary mt-4 mb-0 btn-submit">Descargar</button>
              </div>
            </form>
          </div>
          <div class="card-footer text-center pt-0 px-sm-4 px-1">
            <!--<p class="mb-4 mx-auto">
                  Already have an account?
                  <a href="javascrpt:;" class="text-primary text-gradient font-weight-bold">Guardar</a>
                </p>--->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
require_once 'php/datos/vistas/morosos.php';
?>


<div id="id_embalse_aux" style="display: none;"></div>
<div id="nombre_embalse_aux" style="display: none;"></div>
<div id="id_aux" style="display: none;"></div>
<div id="opc_aux" style="display: none;"></div>
<div id="nombre_hoja_aux" style="display: none;"></div>

<div id="archivo_excel_aux" style="display: none;"></div>
<div id="fecha_excel_aux" style="display: none;"></div>


<script>
  /*var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }*/

  function preventInvalidInput(event) {
    // Lista de teclas permitidas (números, borrar, retroceso, flechas, tabulación, puntos y comas)
    const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', '.', ','];
    
    // Si la tecla es un número o está en las teclas permitidas, permitir la entrada
    if (!isNaN(event.key) || allowedKeys.includes(event.key)) {
        return;
    }

    // Prevenir cualquier otra tecla
    event.preventDefault();
  }

  
  $(".button_fantasma").click(function(e) {
    e.preventDefault();
    $(this).prev().click();

  });
  $(".input_file_oculto").change(function() {
    var string_nombre = "";
    for (var i = 0; i < $(this)[0].files.length; i++) {
      string_nombre += $(this)[0].files[i].name;
      if (i + 1 != $(this)[0].files.length)
        string_nombre += ", "
    }
    //$(this).next(".button_fantasma span").text(string_nombre);       
    $(this).next(".button_fantasma").find("span").text(string_nombre);
  });
</script>

<script>
  iniciarTabla('table');

  var edit = false;

  $(document).ready(function() {
    /*$("#modal-generic .message").text("Registro exitoso");
    $("#modal-generic").modal("show");*/

    if ("<?php echo $_SESSION["Tipo"]; ?>" == "Admin" || "<?php echo $_SESSION["Tipo"]; ?>" == "SuperAdmin") {
      $("#fecha").attr("disabled", false);
      $("#hora").attr("disabled", false);
    }

    $('#add-data-old').modal({
      backdrop: 'static',
      keyboard: false
    });
    $('#modal-generic').modal({
      backdrop: 'static',
      keyboard: false
    });
  });

  var array_codigos = <?php echo json_encode($array_codigos); ?>


  var count = 1;
  $(document).on('click', '#addRows', function() {
    count++;
    var htmlRows = "";
    htmlRows += '<div class="row">';
    htmlRows += '   <div class="col">';
    htmlRows += '       <label>Código</label>';
    htmlRows += '       <div class="input-group mb-4">';
    htmlRows += '           <select class="form-select" name="tipo_extraccion[]" id="tipo_extraccion_' + count + '" onchange="changeTipoExtraccion(this);" required>';
    htmlRows += "               <?php echo $options_extraccion; ?>";
    htmlRows += '           </select>';
    htmlRows += '       </div>';
    htmlRows += '   </div>';
    htmlRows += '   <div class="col">';
    htmlRows += '       <label>Valor</label>';
    htmlRows += '       <div class="input-group mb-4">';
    htmlRows += '           <input type="number" step="0.00001" class="form-control" name="valor_extraccion[]" id="valor_extraccion_' + count + '" placeholder="Valor" onkeydown="preventInvalidInput(event);" required>';
    htmlRows += '       </div>';
    htmlRows += '   </div>';
    htmlRows += '   <div class="col" style="flex: 0 0 0% !important;">';
    htmlRows += '       <label style="color: transparent;">Valor</label>';
    htmlRows += '       <button class="btn btn-danger btn-sm removeRow" id="removeRow" type="button" style="padding: 10px;"><i class="fa fa-trash"></i></button>';
    htmlRows += '   </div>';
    htmlRows += '</div>';

    $('#box-extraccion').append(htmlRows);
  });

  $(document).on('click', '#removeRow', function() {
    $(this).closest('.row').remove();
  });


  function changeTipoExtraccion(select) {
    //console.log(select.id);
    var row = select.id.replace("tipo_extraccion_", "");
    var value_select = select.value;
    var label_valor = $("#valor_extraccion_" + row).parent().prev();

    if (value_select == '' || value_select == '30') { //antes estaba 31
      label_valor.text("Valor");
      $("#valor_extraccion_" + row).attr("type", "text");
      $("#valor_extraccion_" + row).attr("onkeydown", "");
    } else {
      label_valor.html("Valor (1000 m<sup>3</sup>)");
      $("#valor_extraccion_" + row).attr("type", "number");
      $("#valor_extraccion_" + row).attr("onkeydown", "preventInvalidInput(event);");
    }


    if (value_select != '') {
      if ($("#descripcion_extraccion_" + row).length > 0)
        $("#descripcion_extraccion_" + row).remove();

      var descripcion = '<span style="font-size: 0.76rem; display: block; margin-top: -25px;" id="descripcion_extraccion_' + row + '" class="text-dark">' + array_codigos.find(e => e.id_codigo_bd === value_select)['nombre'] + '</span>';
      $(select).parent().after(descripcion);
    } else {
      if ($("#descripcion_extraccion_" + row).length > 0)
        $("#descripcion_extraccion_" + row).remove();
    }

    /*tipo_extraccion[i] = this.value;
    valor_extraccion[i] = $("#valor_extraccion_" + row).val();*/
  }


  function openModalAdd(id_embalse, nombre_embalse) {
    edit = false;
    $("#id_embalse_aux").text(id_embalse);
    $("#nombre_embalse_aux").text(nombre_embalse);
    $("#opc_aux").text("add");

    $("#add .title").text("Añadir Reporte de Extracción en " + nombre_embalse);
    $("#btn-open-modal-import-data").show();

    $(".removeRow").attr("disabled", false);
    $(".removeRow").each(function(index) {
      $(this).trigger("click");
    });
    $("#fecha").val("<?php echo date("Y-m-d"); ?>");
    $("#hora").val("<?php echo date("H:i") . ":00"; ?>");
    $("#valor_cota").val("");
    $("#tipo_extraccion_1").val("");
    $("#valor_extraccion_1").val("");

    $("#valor_cota").attr("disabled", false);
    $("#tipo_extraccion_1").attr("disabled", false);
    $("#valor_extraccion_1").attr("disabled", false);

    $("#box-area-volumen-by-cota").empty();

    $("#add .text-retraso").show();
    $("#add .btn-submit").show();
    $("#add .btn-add-extraccion").show();
    $("#add .btn-edit").hide();
    $("#add .btn-edit").attr("onclick", "");
    $("#add .btn-edit").text("Cerrar");
    $('#add').modal('show');



    $("#valor_extraccion_1").attr("type", "number");
    $("#valor_extraccion_1").attr("onkeydown", "preventInvalidInput(event);");
    $("#valor_cota").attr("type", "number");
  }


  $("#form").on("submit", function(event) {
    event.preventDefault();

    var currentDateTime = new Date('<?php echo date("Y-m-d H:i:s"); ?>');
    var fecha_input = $("#fecha").val();
    var hora_input = $("#hora").val();

    if (!fecha_input || !hora_input || !this.valor_cota.value) {
      $("#modal-generic .message").text("Complete el formulario.");
      $("#modal-generic").modal("show");
      return false;
    }

    var inputDateTime = new Date(fecha_input + 'T' + hora_input);
    var inputDateOnly = inputDateTime.toISOString().split('T')[0];
    var currentDateOnly = currentDateTime.toISOString().split('T')[0];

    if (inputDateOnly === currentDateOnly) {
      if (inputDateTime > currentDateTime) {
        $("#modal-generic .message").text("La hora no puede ser mayor a la actual.");
        $("#modal-generic").modal("show");
        return false; 
      }
    }
    else {
      if (inputDateTime > currentDateTime) {
        $("#modal-generic .message").text("La fecha no puede ser mayor a la actual.");
        $("#modal-generic").modal("show");
        return false; 
      }
    }
    //console.log("aja");
    //return false;

    var tipo_extraccion = [];
    var valor_extraccion = [];
    $("select[name='tipo_extraccion[]']").each(function(i) {
      var row = this.id.replace("tipo_extraccion_", "");
      tipo_extraccion[i] = this.value;
      valor_extraccion[i] = $("#valor_extraccion_" + row).val();
    });

    tipo_extraccion = JSON.stringify(tipo_extraccion);
    valor_extraccion = JSON.stringify(valor_extraccion);

    var datos = new FormData();
    datos.append('opc', $("#opc_aux").text());
    datos.append('id_embalse', $("#id_embalse_aux").text());
    datos.append('cota', this.valor_cota.value);
    datos.append('tipo_extraccion', tipo_extraccion);
    datos.append('valor_extraccion', valor_extraccion);
    if ("<?php echo $_SESSION["Tipo"]; ?>" == "Admin" || "<?php echo $_SESSION["Tipo"]; ?>" == "SuperAdmin") {
      datos.append('fecha', $("#fecha").val());
      datos.append('hora', $("#hora").val());
    }

    $('.loaderParent').show();

    $.ajax({
      url: 'php/datos/modelos/carga-datos-process.php',
      type: 'POST',
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function(response) {
        console.log(response);
        $('.loaderParent').hide();
        if (response == 'si') {
          $("#modal-generic .message").text("Registro exitoso");
          $("#modal-generic .card-footer .btn-action").attr("onclick", "window.location.reload();");
          $("#modal-generic .card-header .close-modal").attr("onclick", "window.location.reload();");
          $("#modal-generic").modal("show");
        } else {
          if (response == "vacio") {
            //alertify.warning("Datos vacíos o sin modificación.");

          } else {
            $("#modal-generic .message").text("Error al registrar");
            $("#modal-generic").modal("show");
          }
        }
      },
      error: function(response) {
        $('.loaderParent').hide();
        $("#modal-generic .message").text("Error al registrar");
        $("#modal-generic").modal("show");
      }
    });

  });


  $("#valor_cota").on("blur", function() {
    var valor = this.value;

    //En el caso que se crea string en Formato 123.456.789,10 se formatea a numerico para poder calcularse
    if (typeof valor === 'string' && valor.includes('.') && edit) {
      valor = parseFloat(valor.replace(/\./g, '').replace(',', '.'));
    }

    var datos = new FormData();
    datos.append('id', $("#id_embalse_aux").text());
    datos.append('valor', valor);
    datos.append('anio', "<?php echo date("Y"); ?>");

    var valor = this.value;
    $.ajax({
      url: 'php/datos/modelos/get-batimetria.php',
      type: 'POST',
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function(response) { //console.log(response);
        var string_error = '<div class="col text-center" style="font-size: 0.8em; color: red;">';
        string_error += '<span>Ingrese la cota correcta</span>';
        string_error += '</div>';
        $("#msg-error").html("");

        valor = response[3];

        //Cota minima
        //console.log(parseFloat(valor) + " y " + parseFloat(response[1]));
        if (parseFloat(valor) < parseFloat(response[1])) {
          $("#msg-error").html(string_error);

          var string = '<div class="col" style="font-size: 1em; color: red;">';
          string += '<span><b>Error:</b> la cota ingresada es inferior a la mínima <b>(' + parseFloat(response[1]).toFixed(3) + ')</b></span>';
          string += '</div>';

          $("#add .btn-submit").attr("disabled", true);
        } else {

          if (parseFloat(valor) > parseFloat(response[2])) {
            $("#msg-error").html(string_error);

            var string = '<div class="col" style="font-size: 1em; color: red;">';
            string += '<span><b>Error:</b> la cota ingresada es mayor a la máxima <b>(' + parseFloat(response[2]).toFixed(3) + ')</b></span>';
            string += '</div>';

            $("#add .btn-submit").attr("disabled", true);
          } else {
            var string = '<div class="col" style="font-size: 0.75em;">';
            // string += '<span>Capacidad o Volumen (02): <b>' + response[0][1] + ' (1000 m<sup>3</sup>)</b></span>';
            string += '<span>Capacidad o Volumen (02): <b>' + response[0][1] + ' (Hm<sup>3</sup>)</b></span>';
            string += '</div>';
            string += '<div class="col small" style="font-size: 0.75em;">';
            // string += '<span>Área o Superficie (03): <b>' + response[0][0] + ' (1000 m<sup>2</sup>)</b></span>';
            string += '<span>Área o Superficie (03): <b>' + response[0][0] + ' (Ha<sup></sup>)</b></span>';
            string += '</div>';

            $("#add .btn-submit").attr("disabled", false);
          }
        }

        $("#box-area-volumen-by-cota").html(string);

      },
      error: function(response) {}
    });
  });
</script>


<?php
//if ($_SESSION["Tipo"] == "Admin" || $_SESSION["Tipo"] == "SuperAdmin") {
?>

  <script>
    function openModalHistory(id_embalse, nombre_embalse, anio, mes) {
      $("#id_embalse_aux").text(id_embalse);
      $("#nombre_embalse_aux").text(nombre_embalse);

      $("#body-details").html("<h3 class='text-center'>Cargando...</h3>");
      $("#modal-details .card-header .text-title").text("Historial de Extracciones de " + nombre_embalse);
      $("#modal-details").modal("show");

      var datos = new FormData();
      datos.append('id_embalse', id_embalse);
      datos.append('anio', anio);
      datos.append('mes', mes);

      $.ajax({
        url: 'php/datos/vistas/historial_reportes.php',
        type: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
          $("#body-details").html(response);
          iniciarTabla('table-history');
        },
        error: function(response) {}
      });
    }

    function openModalDetalles(id_registro, fecha, hora, cota, extraccion) {
      edit = true;
      $("#id_aux").text(id_registro);
      $("#valor_cota").attr("type", "text");
      //$("#opc_aux").text("edit");

      $(".removeRow").attr("disabled", false);
      $("#btn-open-modal-import-data").hide();

      $("#add .title").text("Detalles del Reporte de la Extracción");
      $(".removeRow").each(function(index) {
        $(this).trigger("click");
      });
      // console.log();
      $("#fecha").val(fecha);
      $("#hora").val(hora);
      let valor_cota = parseFloat(cota).toFixed(3);
      
      let cota_formateado = new Intl.NumberFormat('de-DE', {
        minimumFractionDigits: 3,
        maximumFractionDigits: 3
      }).format(valor_cota);
      $("#valor_cota").val(cota_formateado);
      $("#valor_cota").trigger("blur");
      var extraccion_array = extraccion.split(";");
      if (extraccion_array.length > 1) {
        for (var i = 0; i < extraccion_array.length - 1; i++) {
          $("#addRows").trigger("click");
        }
      }

      $(".removeRow").attr("disabled", true);

      //var ids_rows_extracciones = [];
      $("select[name='tipo_extraccion[]']").each(function(i) {
        var extraccion_aux = extraccion_array[i].split("&");

        this.value = extraccion_aux[0];
        $(this).trigger("change");
        var row = this.id.replace("tipo_extraccion_", "");

        $("#valor_extraccion_" + row).attr("type", "text");
        $("#valor_extraccion_" + row).attr("onkeydown", "");


        var valor_extraccion = extraccion_aux[1];
        if (this.value == "30") {
          if ($.isNumeric(extraccion_aux[1])) {
            valor_extraccion = extraccion_aux[1] + "%";
            if (extraccion_aux[1] < 1) {
              valor_extraccion = (extraccion_aux[1] * 100) + "%";
            }
          }
        }
        if (this.value == "31") {
          if ($.isNumeric(extraccion_aux[1]))
            valor_extraccion = Number(extraccion_aux[1]).toFixed(4);
        }
        if (this.value != "30" && this.value != "31" && $.isNumeric(extraccion_aux[1]))
          valor_extraccion = Number(extraccion_aux[1]).toFixed(2);


        let valor_formateado = "";
        valor_extraccion = valor_extraccion ? valor_extraccion.replace(",", ".") : '';

        if(this.value != "30") {
          valor_formateado = new Intl.NumberFormat('de-DE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          }).format(valor_extraccion);

          if(this.value == "31" && !$.isNumeric(valor_extraccion)) {
            valor_formateado = "";
          }
        }

        $("#valor_extraccion_" + row).val( (valor_formateado != '') ? valor_formateado : valor_extraccion);

        $(this).attr("disabled", true);
        $("#valor_extraccion_" + row).attr("disabled", true);

        //En este atributo se guarda el id del detalle de la extraccion en caso de editar
        //$(this).attr("id_detalle_edit", extraccion_aux[2]);
      });

      $("#valor_cota").attr("disabled", true);

      $("#add .text-retraso").hide();
      $("#add .btn-submit").hide();
      $("#add .btn-add-extraccion").hide();
      $("#add .btn-edit").show();
      $("#add .btn-edit").attr("onclick", "$('#modal-details').modal('show')");
      $("#add .btn-edit").text("Atrás");
      $('#add').modal('show');

    }

    function openModalAction(id_registro, action) {
      $("#id_aux").text(id_registro);
      $("#opc_aux").text(action);
      $("#modal-action .message").html("<h4 class='text-center'>¿Desea Eliminar?</h4>");
      $("#modal-action").modal("show");
    }

    function action() {
      var datos = new FormData();
      datos.append('id_registro', $("#id_aux").text());
      datos.append('opc', $("#opc_aux").text());

      var url = 'php/datos/modelos/carga-datos-process.php';

      if ($("#opc_aux").text() == "delete_data_excel") {
        url = 'php/datos/modelos/data-excel-process.php';

        datos.append('archivo_excel', $("#archivo_excel_aux").text());
        datos.append('fecha_excel', $("#fecha_excel_aux").text());
      }

      $.ajax({
        url: url,
        type: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) { //console.log(response);
          $('.loaderParent').hide();
          $("#modal-action").modal("hide");
          if (response == 'si') {
            $("#modal-generic .message").text("Eliminado exitosamente");
            $("#modal-generic").modal("show");

            if ($("#opc_aux").text() != "delete_data_excel") {
              var anio = '',
                mes = '';
              if ($("#body-details #anio").length > 0)
                anio = $("#body-details #anio").val();
              if ($("#body-details #mes").length > 0)
                mes = $("#body-details #mes").val();

              openModalHistory($("#id_embalse_aux").text(), $("#nombre_embalse_aux").text(), anio, mes);
            } else {
              openModalHistoryAdjunciones($("#id_embalse_aux").text());
            }
          } else {
            if (response == "vacio") {
              //alertify.warning("Datos vacíos o sin modificación.");

            } else {
              $("#modal-generic .message").text("Error al eliminar");
              $("#modal-generic").modal("show");
            }
          }
        },
        error: function(response) {
          $('.loaderParent').hide();
          $("#modal-action").modal("hide");
          $("#modal-generic .message").text("Error al eliminar");
          $("#modal-generic").modal("show");
        }
      });
    }

    function openModalAddDataOld() {
      $('#add-data-old .title').text("Adjuntar Excel de Reportes en " + $("#nombre_embalse_aux").text());
      $('#add-data-old').modal('show');
    }


    $("#form-add-data-old").on("submit", function(event) {
      event.preventDefault();

      var ext = $('#file')[0].files[0].name.split('.').pop();

      if (ext !== "xlsx" && ext !== "xls") {
        $("#modal-generic .message").text("Adjunte un archivo .xls o .xlsx");
        $("#modal-generic").modal("show");
        return false;
      }

      var wait = '<div class="row mt-5">';
      wait += '     <div class="col-md-12 loaderParent">';
      wait += '       <div class="loader">';
      wait += '       </div>';
      wait += '       Cargando...';
      wait += '     </div>';
      wait += '   </div>';

      $("#hojas-excel").html(wait);
      $('#add-data-old .loaderParent').show();

      var datos = new FormData();

      if ($("#opc_aux").text() == "importar_data") {
        datos.append('opc', $("#opc_aux").text());
        datos.append('id_embalse', $("#id_embalse_aux").text());
        datos.append('hoja', $("#nombre_hoja_aux").text());
        datos.append('nombre_archivo', $('#file')[0].files[0].name);
        datos.append('id_usuario', '<?php echo $_SESSION['Id_usuario']; ?>');

        readFile($('#file')[0].files[0], $("#nombre_hoja_aux").text());
      } else {
        //datos.append('file', $('#file')[0].files[0]);
        
        listarHojas($('#file')[0].files[0]);

        
      }


      

      /*$.ajax({
        url: 'php/datos/modelos/examinar-excel.php',
        type: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
          $('#add-data-old .loaderParent').hide();



          if ($("#opc_aux").text() == "importar_data") {
            $("#opc_aux").text("");
            $("#nombre_hoja_aux").text("");

            console.log(response);

            if (response == 'si') {
              $("#modal-generic .message").text("Registro exitoso");
              $("#modal-generic .card-footer .btn-action").attr("onclick", "$('#add-data-old').modal('hide');");
              $("#modal-generic").modal("show");
            } else {
              if (response == "ya se importo") {
                $("#modal-generic .message").text("La información de este archivo ya fue añadida al embalse " + $("#nombre_embalse_aux").text() + ". Intente con otro archivo.");
                $("#modal-generic").modal("show");
              } else {
                $("#modal-generic .message").text("Error al registrar");
                $("#modal-generic").modal("show");
              }
            }
          } else {
            $("#hojas-excel").html(response);
            iniciarTabla("hojas-excel-table");
          }

          //console.log(response);
        },
        error: function(response) {
          $('#add-data-old .loaderParent').hide();
          $("#modal-generic .message").text("Error al examinar");
          $("#modal-generic").modal("show");
        }
      });*/

    });

    function importarData(hoja) {
      $("#opc_aux").text("importar_data");
      $("#nombre_hoja_aux").text(hoja);
      $("#form-add-data-old").trigger("submit");
    }

    function listarHojas(file) {
      //const file = file;
      const reader = new FileReader();

      reader.onload = function (e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });

        // Obtener los nombres de las hojas
        const sheetNames = workbook.SheetNames;
        //console.log('Hojas:', sheetNames);

        // Leer la primera hoja
        /*const worksheet = workbook.Sheets[sheetNames[0]];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

        // Mostrar los datos
        console.log(jsonData);
        document.getElementById('output').innerText = JSON.stringify(jsonData, null, 2);*/
        
        //console.log("termino");

        var html = '<div class="table-responsive mb-3 mt-5">';
        html += '     <table class="table align-items-center text-sm text-center table-sm" id="hojas-excel-table">';
        html += '       <thead class="table-primary">';
        html += '         <tr>';
        html += '           <th scope="col" class="sort" data-sort="name">#</th>';
        html += '           <th scope="col" class="sort" data-sort="name">Nombre</th>';
        html += '           <th scope="col"></th>';
        html += '         </tr>';
        html += '       </thead>';
        html += '       <tbody class="list">';
        
        for(i = 0 ; i < sheetNames.length ; i++) {
          var onclick = "importarData('" + sheetNames[i] + "');";
          html += '       <tr>';
          html += '         <th scope="row">';
          html +=             i + 1;
          html += '         </th>';
          html += '         <td scope="row">';
          html += '           <div class="media">';
          html += '             <div class="media-body">';
          html += '               <span class="name mb-0">' + sheetNames[i] + '</span>';
          html += '             </div>';
          html += '           </div>';
          html += '         </td>';
          html += '         <td>';
          html += '           <button type="button" title="Importar data" class="btn btn-outline-secondary border-2 mb-0" style="padding: 0.3rem 0.6rem" onclick="' + onclick + '">';
          html += '             <i class="fa fa-upload"></i>';
          html += '           </button>';
          html += '         </td>';
          html += '       </tr>';
        }

        html += '       </tbody>';
        html += '     </table>';
        html += '   </div>';
        //$('#add-data-old .loaderParent').hide();
        $("#hojas-excel").html(html);
        iniciarTabla("hojas-excel-table");
      };

      reader.readAsArrayBuffer(file);
    }

    function readFile(file, hoja) {
      const reader = new FileReader();

      reader.onload = function (e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });

        // Obtener los nombres de las hojas
        //const sheetNames = workbook.SheetNames;
        //console.log('Nombres de hojas:', sheetNames);

        // Leer una hoja específica (por nombre)
        const sheetName = hoja; // Cambia esto al nombre de tu hoja
        const worksheet = workbook.Sheets[sheetName];
        if (!worksheet) {
          console.error(`La hoja "${sheetName}" no existe.`);
          return;
        }

        // Convertir los datos de la hoja en JSON
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 }); // Cambia "header" según lo que necesites
        //console.log('Datos de la hoja:', jsonData);

        // Mostrar los datos en pantalla
        //document.getElementById('output').innerText = JSON.stringify(jsonData, null, 2);
        //$("#hojas-excel").html(JSON.stringify(jsonData, null, 2));

        var columnaInicio = "E";
        var indexColumnaInicio = columnLetterToNumber(columnaInicio);
        var col = indexColumnaInicio;

        while (col) {
          const cellRef = XLSX.utils.encode_cell({ r: 7, c: col });
          const cellValue = worksheet[cellRef] ? worksheet[cellRef].v : null;
          //console.log(`Valor en ${cellRef}:`, cellValue);
          if(cellValue === null) {
            col = col - 3;
            break;
          }
          col++;
        }

        var indexColumnaFin = col;
        var columnaFin = columnNumberToLetter(col);

        //console.log('inicio: ' + columnaInicio + ". \n fin: " + columnaFin);



        //obteniendo los codigos
        array_codigos_consulta = [];
        for(i = indexColumnaInicio; i <= indexColumnaFin ; i++){
          const cellRef = XLSX.utils.encode_cell({ r: 7, c: i });
          const cellValue = worksheet[cellRef] ? worksheet[cellRef].v : null;
          celda_codigo = cellValue;

          //let celda_codigo = '=funcion("argumento")';
          let codigo;

          if (celda_codigo.includes("=")) {
              let string = celda_codigo.replace('=', '').replace(/\s+/g, '');
              let array = string.split('"');

              var array_codigo = array[1];
              codigo = array_codigo.split("(");
          } else {
              let array_codigo = celda_codigo.split("(");

              codigo = ["", array_codigo[array_codigo.length - 1].replace(')', ''), ""];
              for (let j = 0; j < array_codigo.length - 1; j++) {
                  codigo[0] += array_codigo[j];

                  if (j + 1 < array_codigo.length - 1) {
                      codigo[0] += "(";
                  }
              }
          }

         // console.log(codigo);

          if (codigo[0] && codigo[0] !== "" && codigo[1]) {
            let array_aux = {
              columna: columnNumberToLetter(i),
              fila: '8',
              codigo: codigo[1].split("\r")[0].replace(')', ''),
              nombre_codigo: codigo[0]
            };

            array_codigos_consulta.push(array_aux);
          }

        }
        //console.log(array_codigos_consulta);






        //consultando los datos
        fecha = "";
        fila = 9;
        let fullData = [];
        while(fila){
          var cellRef = XLSX.utils.encode_cell({ r: fila - 1, c: 1 }); // Columna B
          const cellValue = worksheet[cellRef] ? worksheet[cellRef].v : null;
          var valorCelda = cellValue;
          if(valorCelda == "") {
            //Si el valor es blanco y ademas no hay fecha o ha recorrido mas de 10 filas y no ha encontrado nada se sale del ciclo
            //esto se hace para evitar que se haga un ciclo infitino
            if(fecha != "" || fila >= 19){
              fila--;
              break;
            }
          }


          

          // El objeto resultante contiene los componentes de la fecha:
          //console.log(dateObject);
          // { y: 2023, m: 12, d: 6, H: 0, M: 0, S: 0 }

          // Formatea la fecha en el formato Y-m-d
          
          //console.log(formattedDate);

          if (isNumeric(valorCelda)) {
            //$fecha_obj = Date::excelToDateTimeObject($valorCelda);
            //$fecha = $fecha_obj->format('Y-m-d');
            let dateObject = XLSX.SSF.parse_date_code(valorCelda);
            fecha = `${dateObject.y}-${String(dateObject.m).padStart(2, '0')}-${String(dateObject.d).padStart(2, '0')}`;

          }
          else {
            fila++;
            continue;
          }
          //fullData[] = valorCelda;


          var cellRef = XLSX.utils.encode_cell({ r: fila - 1, c: 2 }); // Columna C
          const cota = worksheet[cellRef] ? worksheet[cellRef].v : null;

          valores_extracciones = [];
          for(var i = 0 ; i < array_codigos_consulta.length ; i++) {
            columna_valor_extraccion = array_codigos_consulta[i].columna;
            celda = worksheet[columna_valor_extraccion + '' + (fila)];
            valor_extraccion_aux = 0;
            //console.log(valor_extraccion_aux);
            //if(celda != ""){
              if (celda && celda.f) { 
                valor_extraccion_aux = XLSX.utils.format_cell(celda);
                if(!isNumeric(valor_extraccion_aux))
                  valor_extraccion_aux = 0;
              }
              else {
                valor_extraccion_aux = celda ? celda.v : null;
              }

              let array_aux = {
                valor: valor_extraccion_aux,
                codigo: array_codigos_consulta[i].codigo
              };
              valores_extracciones.push(array_aux);
            //}
          }

          let array_aux = {
            fecha: fecha,
            cota_actual: cota,
            extracciones: valores_extracciones
          };

          fullData.push(array_aux);


          let fecha1 = new Date("<?php echo date("Y-m-d");?>");
          let fecha2 = new Date(fecha);

          if (fecha1 <= fecha2) {
            break;
          }
          fila++;
        }

        //console.log(fullData);
        //enviar datos para registrar
        sendData(array_codigos_consulta, fullData, file.name);


      };

      reader.readAsArrayBuffer(file);
    }

    function columnLetterToNumber(letter) {
      let number = 0;
      for (let i = 0; i < letter.length; i++) {
        number = number * 26 + (letter.charCodeAt(i) - 64); // 'A' tiene valor ASCII 65
      }
      return number - 1; // Retorna índice base 0
    }

    function columnNumberToLetter(number) {
      let letter = '';
      while (number >= 0) {
        letter = String.fromCharCode((number % 26) + 65) + letter;
        number = Math.floor(number / 26) - 1;
      }
      return letter;
    }
    function isNumeric(value) {
      return !isNaN(value) && isFinite(value);
    }
    function sendData(array_codigos_consulta, fullData, namefile) {
      var datos = new FormData();
      datos.append('opc', $("#opc_aux").text());
      datos.append('id_embalse', $("#id_embalse_aux").text());
      datos.append('nombre_archivo', namefile);
      datos.append('array_codigos_consulta', JSON.stringify(array_codigos_consulta));
      datos.append('fullData', JSON.stringify(fullData));
      datos.append('id_usuario', '<?php echo $_SESSION['Id_usuario']; ?>');

      $.ajax({
        url: 'php/datos/modelos/examinar-excel.php',
        type: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
          $('#add-data-old .loaderParent').hide();

          //if ($("#opc_aux").text() == "importar_data") {
            $("#opc_aux").text("");
            $("#nombre_hoja_aux").text("");

            //console.log(response);

            if (response == 'si') {
              $("#modal-generic .message").text("Registro exitoso");
              $("#modal-generic .card-footer .btn-action").attr("onclick", "$('#add-data-old').modal('hide');");
              $("#modal-generic").modal("show");
            } else {
              if (response == "ya se importo") {
                $("#modal-generic .message").text("La información de este archivo ya fue añadida al embalse " + $("#nombre_embalse_aux").text() + ". Intente con otro archivo.");
                $("#modal-generic").modal("show");
              } else {
                $("#modal-generic .message").text("Error al registrar");
                $("#modal-generic").modal("show");
              }
            }
          //}

          console.log(response);
        },
        error: function(response) {
          $('#add-data-old .loaderParent').hide();
          $("#modal-generic .message").text("Error al examinar");
          $("#modal-generic").modal("show");
        }
      });
    }

    function openModalHistoryAdjunciones(id_embalse) {

      $("#body-history-excel").html("<h3 class='text-center'>Cargando...</h3>");
      $("#modal-history-excel h3.title").text("Historial de Adjunciones del Embalse");

      $("#modal-history-excel").modal("show");

      var datos = new FormData();
      datos.append('id_embalse', id_embalse);
      datos.append('nombre_embalse', $("#nombre_embalse_aux").text());

      $.ajax({
        url: 'php/datos/vistas/historial-adjunciones-excel.php',
        type: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
          $("#body-history-excel").html(response);
          iniciarTabla('table-history-excel');
        },
        error: function(response) {}
      });
    }


    function openModalDeleteHistoryExcel(id_embalse, nombre_embalse, archivo, fecha_excel, action) {
      $("#id_aux").text(id_embalse);
      $("#opc_aux").text(action);
      $("#archivo_excel_aux").text(archivo);
      $("#fecha_excel_aux").text(fecha_excel);
      $("#modal-action .message").html("<h4 class='text-center'>¿Desea Eliminar el conjunto de datos adjuntados del archivo " + archivo + " al embalse de " + nombre_embalse + "?</h4>");
      $("#modal-action").modal("show");
    }

    function openModalParametrosAnio(id_embalse, anio, mes) {
      $("#body-history-excel").html("<h3 class='text-center'>Cargando...</h3>");
      $("#modal-history-excel h3.title").text("Detalles e Información de Reportes de " + $("#nombre_embalse_aux").text());
      $("#modal-history-excel").modal("show");

      var datos = new FormData();
      datos.append('id_embalse', id_embalse);
      datos.append('nombre_embalse', $("#nombre_embalse_aux").text());
      datos.append('anio', anio);
      datos.append('mes', mes);

      $.ajax({
        url: 'php/datos/vistas/historial_anio_reportes.php',
        type: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
          $("#body-history-excel").html(response);
          iniciarTabla('table-history-excel');
        },
        error: function(response) {}
      });
    }

    function exportExcel() {
      $("#select-anio-export-excel").modal("show");
    }



    $("#form-export-excel").on("submit", function(event) {
      event.preventDefault();

      var url = "php/datos/modelos/loader-extracciones-excel.php?anio=" + this.anio_export_excel.value;
      window.open(url, "_blank");
    });
  </script>

<?php
//}
?>




<!--
    <div class="modal fade" id="datos" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true">
      <div class="modal-dialog modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-body p-0">
            <div class="card card-plain">
              <div class="card-header pb-0 text-left">
                  <h3 class="font-weight-bolder text-primary text-gradient">Datos</h3>
              </div>
              <div class="card-body pb-3">
                <form role="form text-left">
                  <div class="row">
                    <div class="col">
                      
                      <div class="table-responsive">
                        <div>
                            <table class="table align-items-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name">Fecha</th>
                                        <th scope="col" class="sort" data-sort="budget">Cota</th>
                                        <th scope="col" class="sort" data-sort="status">Extracción</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <tr>
                                        <th scope="row">
                                            <div class="media align-items-center">
                                                <div class="media-body">
                                                    <span class="name mb-0 text-sm">12 de Septiembre 2023</span>
                                                </div>
                                            </div>
                                        </th>
                                        <td class="budget">
                                            Valor Cota
                                        </td>
                                        <td class="budget">
                                            Valor Extracción
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <div class="media align-items-center">
                                                <div class="media-body">
                                                    <span class="name mb-0 text-sm">18 de Septiembre 2023</span>
                                                </div>
                                            </div>
                                        </th>
                                        <td class="budget">
                                            Valor Cota
                                        </td>
                                        <td class="budget">
                                            Valor Extracción
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <div class="media align-items-center">
                                                <div class="media-body">
                                                    <span class="name mb-0 text-sm">24 de Septiembre 2023</span>
                                                </div>
                                            </div>
                                        </th>
                                        <td class="budget">
                                            Valor Cota
                                        </td>
                                        <td class="budget">
                                            Valor Extracción
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                      </div>

                    </div>
                  </div>

                </form>
              </div>
              <div class="card-footer text-center pt-0 px-sm-4 px-1">

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
-->

<div class="modal fade" id="modal-action" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true">
  <div class="modal-dialog modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-center">
            <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">
              <i class="fa fa-times"></i>
            </button>
          </div>

          <div class="card-body pb-0 text-center mt-3">
            <h3 class="font-weight-bolder text-primary text-gradient message"></h3>
            <div class="row">
              <div class="col-md-12 loaderParent">
                <div class="loader">
                </div>
                Por favor, espere
              </div>
            </div>
          </div>

          <div class="card-footer text-center pt-0 px-sm-4 px-1 mt-4">
            <a href="javascrpt:;" class="btn btn-secondary font-weight-bold mb-0 btn-action" data-bs-dismiss="modal">Cerrar</a>
            <a href="javascrpt:;" class="btn btn-primary font-weight-bold mb-0 btn-action" onclick="action();">Aceptar</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-generic" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true">
  <div class="modal-dialog modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-center">
            <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">
              <i class="fa fa-times"></i>
            </button>
          </div>

          <div class="card-body pb-0 text-center mt-3">
            <h3 class="font-weight-bolder text-primary text-gradient message"></h3>
          </div>

          <div class="card-footer text-center pt-0 px-sm-4 px-1 mt-4">
            <a href="javascrpt:;" class="btn btn-primary font-weight-bold mb-0 btn-action" data-bs-dismiss="modal">Aceptar</a>
            <!--<p class="mb-4 mx-auto">
                  Already have an account?
                  <a href="javascrpt:;" class="text-primary text-gradient font-weight-bold">Guardar</a>
                </p>--->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>