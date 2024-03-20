<script>
  $("#breadcrumb .modulo").text("Principal");
  $("#breadcrumb .submodulo").text("Carga de Datos");
</script>

<?php
  require_once 'php/Conexion.php';

  $add_where = "";

  if($_SESSION["Tipo"] == "User"){
    $add_where .= " AND id_encargado = '$_SESSION[Id_usuario]'";
  }

  $fecha = date("Y-m-d");

  $sql = "SELECT DISTINCT id_embalse, nombre_embalse, estado, municipio, parroquia, id_encargado, (SELECT (IF(COUNT(id_registro) > 0, 'si', 'no')) FROM datos_embalse de WHERE de.id_embalse = em.id_embalse AND estatus = 'activo' AND fecha = '$fecha' ) AS 'reportado_hoy'
          FROM embalses em, estados e, municipios m, parroquias p
          WHERE em.id_estado = e.id_estado AND em.id_municipio = m.id_municipio AND em.id_parroquia = p.id_parroquia AND m.id_estado = e.id_estado AND p.id_municipio = m.id_municipio AND em.estatus = 'activo' $add_where;";

  $query = mysqli_query($conn, $sql);


  $sql = "SELECT nombre, cantidad_primaria, unidad, codigo, leyenda_sistema, concepto, uso, ce.id AS 'id_codigo_extraccion', IF(leyenda_sistema <> '', leyenda_sistema, concepto) AS 'name'
          FROM tipo_codigo_extraccion tce, codigo_extraccion ce
          WHERE tce.id = ce.id_tipo_codigo_extraccion AND 
            ce.estatus = 'activo' AND 
            tce.estatus = 'activo' AND 
            tce.id <> '6' AND 
            tce.id <> '7' AND
            ce.concepto <> 'Subtotal'
          ORDER BY codigo ASC";
  $query_codigos = mysqli_query($conn, $sql);

  closeConection($conn);

  /*$options_extraccion = '<option value="">Seleccione</option>';
  $options_extraccion .='<option value="Riego">Riego</option>';
  $options_extraccion .='<option value="Hidroelectricidad">Hidroelectricidad</option>';
  $options_extraccion .='<option value="Consumo Humano">Consumo Humano</option>';
  $options_extraccion .='<option value="Control de Inundaciones (Aliviadero)">Control de Inundaciones (Aliviadero)</option>';
  $options_extraccion .='<option value="Recreación">Recreación</option>';*/

  $options_extraccion = "<option value=''>Seleccione</option>";

  while($row = mysqli_fetch_array($query_codigos)){
    $options_extraccion .= "<option value='$row[id_codigo_extraccion]'>$row[name] ($row[codigo])</option>";
  }
?>


    <div class="container-fluid py-4">
      <div class="row">
        
        <div class="col-lg-12">
          <div class="card h-100 mb-3">
            <div class="card-header pb-0">
              <!-- <div class="row"> -->
                <!-- <div class="col-6 d-flex align-items-center"> -->
                  <h6 class="">Carga de Datos</h6>
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
            if(mysqli_num_rows($query) > 0){
?>
                <div class="table-responsive mb-3">
                    <table class="table align-items-center text-sm text-center table-sm" id="table">
                      <thead class="table-primary">
                        <tr>
                            <th scope="col" class="sort" data-sort="name">Nombre</th>
                            <th scope="col" class="sort hide-cell" data-sort="budget">Ubicación</th>
                            <th scope="col"></th>
                        </tr>
                      </thead>
                      <tbody class="list">
                        

<?php
              while($row = mysqli_fetch_array($query)){
?>
                  

                <tr>
                  <th scope="row">
                    <div class="media">
                        <div class="media-body">
                            <span class="name mb-0"><?php echo $row['nombre_embalse'];?></span>
                        </div>
                    </div>
                  </th>
                  <td class="hide-cell">
                    <?php echo $row['estado'] . ", " . $row['municipio'] . ", " . $row['parroquia'];?>
                  </td>
                  <td>
                    <!--<a class="btn btn-primary btn-sm px-3 mb-0" href="javascript:;" onclick="$('#add').modal('show');">
                      <i class="fas fa-plus text-dark me-2" aria-hidden="true"></i>
                      Añadir Reporte
                    </a>-->
<?php
                if($row['reportado_hoy'] == "si"){
?>
                  <h6 class="mb-1 text-dark font-weight-bold text-sm">El reporte de hoy fue realizado <i class="fas fa-check text-lg text-green me-2"></i></h6>
<?php
                }

                if( ($_SESSION["Tipo"] == "Admin") || ($_SESSION["Tipo"] == "User" && $row['reportado_hoy'] == "no") ){
?>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="openModalAdd('<?php echo $row['id_embalse'];?>');">
                      <i class="fas fa-plus text-dark me-2" aria-hidden="true"></i>
                      <span class="hide-cell">Añadir Reporte</span>
                    </a>
<?php
                }

                if($_SESSION["Tipo"] == "Admin"){
?>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="openModalHistory('<?php echo $row['id_embalse'];?>');">
                      <i class="fas fa-history text-dark me-2" aria-hidden="true"></i>
                      <span class="hide-cell">Historial de Reportes</span>
                    </a>
<?php
                }
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
            }
            else{
?>
                  <h2 class="mb-1 text-dark font-weight-bold text-center mt-4">No hay información</h2>
                  <br><br><br>
<?php                  
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




    <div class="modal fade" id="modal-details" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-body p-0">
            <div class="card card-plain">
              <div class="card-header pb-0 text-left">
                <h3 class="font-weight-bolder text-primary text-gradient">Historial de Reportes</h3>
                <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">X</button>
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




    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-body p-0">
            <div class="card card-plain">
              <div class="card-header pb-0 text-left">
                <h3 class="font-weight-bolder text-primary text-gradient title"></h3>
                <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">X</button>
                <!--<p class="mb-0">Enter your email and password to register</p>-->
              </div>
              <div class="card-body pb-3">
    <?php
              if($_SESSION["Tipo"] == "Admin"){
    ?>
                <div class="row">
                  <div class="col-md-12 text-center">
                      <button class="btn btn-success" data-bs-dismiss="modal" id="btn-open-modal-import-data" onclick="$('#add-data-old').modal('show');" type="button">Adjuntar Historial de Reportes</button>
                  </div>
                </div>
    <?php
              }
    ?>

                <form role="form text-left" id="form">
    <?php
                if(date("H:i") . ":00" > "10:00:00"){
    ?>
                  <h6 class="text-red text-center text-retraso">Estás retrasado al enviar el reporte</h6>
    <?php
                }
    ?>
                  <div class="row">
                    <div class="col">
                      <label>Fecha</label>
                      <div class="input-group mb-3">
                        <input type="date" class="form-control" name="fecha" id="fecha" value="<?php echo date("Y-m-d");?>" disabled required>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col">
                      <label>Hora</label>
                      <div class="input-group mb-3">
                        <input type="time" class="form-control" name="hora" id="hora" value="<?php echo date("H:i") . ":00";?>" disabled required>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col">
                      <label>Cota (01)</label>
                      <div class="input-group mb-3">
                        <input type="number" step="0.00001" class="form-control" name="valor_cota" id="valor_cota" placeholder="Cota" aria-label="Cota" aria-describedby="name-addon" required>
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
                        <div class="input-group mb-3">
                          <select class="form-select" name="tipo_extraccion[]" id="tipo_extraccion_1" required>
                            <?php echo $options_extraccion;?>
                          </select>
                        </div>
                      </div>
                      <div class="col">
                        <label>Valor (Hm<sup>3</sup>)</label>
                        <div class="input-group mb-3">
                          <input type="number" step="0.00001" class="form-control" name="valor_extraccion[]" id="valor_extraccion_1" placeholder="Valor de la Extracción" required>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12 text-center" style="margin-top: 12px;">
                        <button class="btn btn-success btn-add-extraccion" id="addRows" type="button">Añadir Otra Extracción</button>
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
                    <button type="submit" class="btn bg-gradient-primary btn-lg btn-rounded w-100 mt-4 mb-0 btn-submit">Guardar</button>
                    <button type="button" class="btn btn-secondary btn-rounded mt-4 mb-0 btn-edit" data-bs-dismiss="modal" style="display: none;">Cerrar</button>
                    <!--<button type="button" class="btn bg-gradient-primary btn-rounded mt-4 mb-0 btn-edit" style="display: none;">Editar</button>-->
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


    <div class="modal fade" id="add-data-old" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-body p-0">
            <div class="card card-plain">
              <div class="card-header pb-0 text-left">
                <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal" onclick="$('#add').modal('show');">X</button>
                <h3 class="font-weight-bolder text-primary text-gradient mt-5 text-center">Adjuntar Excel de Reportes</h3>
              </div>
              <div class="card-body pb-3">

                <form id="form-add-data-old">
                  <div class="form-group col-sm-12">
                    <span style="color: red; margin: 0 2px 0 -5px;">*</span><label for="file" class="form-label">Archivo</label>
                    <input type="file" id="file" accept=".xlsx, .xls"  class="form-control input_file_oculto" oninvalid="setCustomValidity('Debe cargar un archivo')">
                    <div class="form-control button_fantasma">
                      <span></span>
                      <button type="button" class="btn btn-primary">Cargar Archivo</button>
                    </div>
                  </div>
                  <div class="text-center">
                    <button type="button" class="btn btn-secondary btn-rounded mt-4 mb-0 btn-edit" data-bs-dismiss="modal" onclick="$('#add').modal('show');">Atrás</button>
                    <button type="submit" class="btn bg-gradient-primary btn-rounded mt-4 mb-0 btn-submit">Examinar</button>
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

  


    <div id="id_embalse_aux" style="display: none;"></div>
    <div id="id_aux" style="display: none;"></div>
    <div id="opc_aux" style="display: none;"></div>
    <div id="nombre_hoja_aux" style="display: none;"></div>


  <script>
    /*var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }*/

    $(".button_fantasma").click(function(e) {
      e.preventDefault();
      $(this).prev().click();
      
    });
    $(".input_file_oculto").change(function() {
      var string_nombre = "";
      for(var i = 0 ; i < $(this)[0].files.length ; i++){
          string_nombre += $(this)[0].files[i].name;
          if(i + 1 != $(this)[0].files.length)
              string_nombre += ", "
      }    
      //$(this).next(".button_fantasma span").text(string_nombre);       
      $(this).next(".button_fantasma").find("span").text(string_nombre);
    });
  </script>

  <script>
    iniciarTabla('table');

    $( document ).ready(function() {
      /*$("#modal-generic .message").text("Registro exitoso");
      $("#modal-generic").modal("show");*/

      if("<?php echo $_SESSION["Tipo"];?>" == "Admin"){
        $("#fecha").attr("disabled", false);
        $("#hora").attr("disabled", false);
      }
    });


    var count = 1;
    $(document).on('click', '#addRows', function() { 
      count++;
      var htmlRows = "";
      htmlRows += '<div class="row">';
      htmlRows += '   <div class="col">';
      htmlRows += '       <label>Tipo</label>';
      htmlRows += '       <div class="input-group mb-3">';
      htmlRows += '           <select class="form-select" name="tipo_extraccion[]" id="tipo_extraccion_' + count + '" required>';
      htmlRows += "               <?php echo $options_extraccion;?>";
      htmlRows += '           </select>';
      htmlRows += '       </div>';
      htmlRows += '   </div>';
      htmlRows += '   <div class="col">';
      htmlRows += '       <label>Valor (Hm<sup>3</sup>)</label>';
      htmlRows += '       <div class="input-group mb-3">';
      htmlRows += '           <input type="number" step="0.00001" class="form-control" name="valor_extraccion[]" id="valor_extraccion_' + count + '" placeholder="Valor de la Extracción" required>';
      htmlRows += '       </div>';
      htmlRows += '   </div>';
      htmlRows += '   <div class="col" style="flex: 0 0 0% !important;">';
      htmlRows += '       <label style="color: transparent;">Valor</label>';
      htmlRows += '       <button class="btn btn-danger btn-sm removeRow" id="removeRow" type="button" style="padding: 10px;"><i class="fa fa-trash"></i></button>';
      htmlRows += '   </div>';
      htmlRows += '</div>';

      $('#box-extraccion').append(htmlRows);
    });

    $(document).on('click', '#removeRow', function(){
      $(this).closest('.row').remove();
    });


    function openModalAdd(id_embalse){
      $("#id_embalse_aux").text(id_embalse);
      $("#opc_aux").text("add");

      $("#add .title").text("Añadir Reporte");
      $("#btn-open-modal-import-data").show();

      $(".removeRow").attr("disabled", false);
      $(".removeRow").each(function( index ) {
        $(this).trigger("click");
      });
      $("#fecha").val("<?php echo date("Y-m-d");?>");
      $("#hora").val("<?php echo date("H:i") . ":00";?>");
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
      $('#add').modal('show');
    }


    $("#form").on("submit",function(event){
    	event.preventDefault();

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
      if("<?php echo $_SESSION["Tipo"];?>" == "Admin"){
        datos.append('fecha', $("#fecha").val());
        datos.append('hora', $("#hora").val());
      }

      $('.loaderParent').show();

      $.ajax({
        url: 			'php/datos/modelos/carga-datos-process.php',
        type:			'POST',
        data:			datos,
        cache:          false,
        contentType:    false,
        processData:    false,
        success: function(response){ console.log(response);
          $('.loaderParent').hide();
          if(response == 'si'){
            $("#modal-generic .message").text("Registro exitoso");
            $("#modal-generic .card-footer .btn-action").attr("onclick", "window.location.reload();");
            $("#modal-generic").modal("show");
          }
          else{
            if(response == "vacio"){
              //alertify.warning("Datos vacíos o sin modificación.");
                
            }
            else{
              $("#modal-generic .message").text("Error al registrar");
              $("#modal-generic").modal("show");
            } 
          }
        }
        ,
        error: function(response){
          $('.loaderParent').hide();
          $("#modal-generic .message").text("Error al registrar");
          $("#modal-generic").modal("show");
        }
      });

    });


    $("#valor_cota").on("blur", function() { 
      var datos = new FormData();
      datos.append('id', $("#id_embalse_aux").text());
      datos.append('valor', this.value);
      datos.append('anio', "<?php echo date("Y");?>");

      var valor = this.value;
      $.ajax({
        url: 			'php/datos/modelos/get-batimetria.php',
        type:			'POST',
        data:			datos,
        cache:          false,
        contentType:    false,
        processData:    false,
        dataType: 'json',
        success: function(response){ //console.log(response);
          var string_error =  '<div class="col text-center" style="font-size: 0.8em; color: red;">';
          string_error +=       '<span>Ingrese la cota correcta</span>';
          string_error +=     '</div>';
          $("#msg-error").html("");
          
          //Cota minima
          if(parseFloat(valor) < parseFloat(response[1])){
            $("#msg-error").html(string_error);

            var string =  '<div class="col" style="font-size: 1em; color: red;">';
            string +=       '<span><b>Error:</b> la cota ingresada es inferior a la mínima <b>(' + parseFloat(response[1]).toFixed(3) + ')</b></span>';
            string +=     '</div>';

            $("#add .btn-submit").attr("disabled", true);
          }
          else{

            if(parseFloat(valor) > parseFloat(response[2])){
              $("#msg-error").html(string_error);

              var string =  '<div class="col" style="font-size: 1em; color: red;">';
              string +=       '<span><b>Error:</b> la cota ingresada es mayor a la máxima <b>(' + parseFloat(response[2]).toFixed(3) + ')</b></span>';
              string +=     '</div>';

              $("#add .btn-submit").attr("disabled", true);
            }
            else{
              var string =  '<div class="col" style="font-size: 0.75em;">';
              string +=       '<span>Capacidad o Volumen (02): <b>' + response[0][1] + ' hm<sup>3</sup></b></span>';
              string +=     '</div>';
              string +=     '<div class="col small" style="font-size: 0.75em;">';
              string +=       '<span>Área o Superficie (03): <b>' + response[0][0] + ' hm<sup>2</sup></b></span>';
              string +=     '</div>';
              
              $("#add .btn-submit").attr("disabled", false);
            }
          }

          $("#box-area-volumen-by-cota").html(string);
          
        }
        ,
        error: function(response){
        }
      });
    });
  </script>


<?php
  if($_SESSION["Tipo"] == "Admin"){
?>

  <script>
    function openModalHistory(id_embalse){
      $("#id_embalse_aux").text(id_embalse);

      $("#body-details").html("<h3 class='text-center'>Cargando...</h3>");
      $("#modal-details").modal("show");

      var datos = new FormData();
      datos.append('id_embalse', id_embalse);

      $.ajax({
        url: 			'php/datos/vistas/historial_reportes.php',
        type:			'POST',
        data:			datos,
        cache:          false,
        contentType:    false,
        processData:    false,
        success: function(response){
          $("#body-details").html(response);
          iniciarTabla('table-history');
        }
        ,
        error: function(response){
        }
      });
    }

    function openModalDetalles(id_registro, fecha, hora, cota, extraccion){
      $("#id_aux").text(id_registro);
      //$("#opc_aux").text("edit");

      $(".removeRow").attr("disabled", false);
      $("#btn-open-modal-import-data").hide();

      $("#add .title").text("Detalles del Reporte");
      $(".removeRow").each(function( index ) {
        $(this).trigger("click");
      });

      $("#fecha").val(fecha);
      $("#hora").val(hora);
      $("#valor_cota").val(cota);
      var extraccion_array = extraccion.split(";");
      if(extraccion_array.length > 1){
        for(var i = 0 ; i < extraccion_array.length - 1 ; i++){
          $("#addRows").trigger("click");
        }
      }

      $(".removeRow").attr("disabled", true);

      //var ids_rows_extracciones = [];
      $("select[name='tipo_extraccion[]']").each(function(i) {
        var extraccion_aux = extraccion_array[i].split("&");

        this.value = extraccion_aux[0];
        var row = this.id.replace("tipo_extraccion_", "");
        $("#valor_extraccion_" + row).val(extraccion_aux[1]);

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
      $('#add').modal('show');

    }

    function openModalAction(id_registro, action){
      $("#id_aux").text(id_registro);
      $("#opc_aux").text(action);
      $("#modal-action .message").html("<h4 class='text-center'>¿Desea Eliminar?</h4>");
      $("#modal-action").modal("show");
    }

    function action(){
      var datos = new FormData();
      datos.append('id_registro', $("#id_aux").text());
      datos.append('opc', $("#opc_aux").text());

      $.ajax({
        url: 			'php/datos/modelos/carga-datos-process.php',
        type:			'POST',
        data:			datos,
        cache:          false,
        contentType:    false,
        processData:    false,
        success: function(response){ //console.log(response);
          $('.loaderParent').hide();
          $("#modal-action").modal("hide");
          if(response == 'si'){
            $("#modal-generic .message").text("Eliminado exitosamente");
            $("#modal-generic").modal("show");

            openModalHistory($("#id_embalse_aux").text());
          }
          else{
            if(response == "vacio"){
              //alertify.warning("Datos vacíos o sin modificación.");
                
            }
            else{
              $("#modal-generic .message").text("Error al eliminar");
              $("#modal-generic").modal("show");
            } 
          }
        }
        ,
        error: function(response){
          $('.loaderParent').hide();
          $("#modal-action").modal("hide");
          $("#modal-generic .message").text("Error al eliminar");
          $("#modal-generic").modal("show");
        }
      });
    }


    $("#form-add-data-old").on("submit",function(event){
    	event.preventDefault();

      var ext = $('#file')[0].files[0].name.split('.').pop();
      
      if(ext !== "xlsx" && ext !== "xls") {
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

      var datos = new FormData();

      if($("#opc_aux").text() == "importar_data") {
        datos.append('opc', $("#opc_aux").text());
        datos.append('id_embalse', $("#id_embalse_aux").text());
        datos.append('hoja', $("#nombre_hoja_aux").text());
        datos.append('nombre_archivo', $('#file')[0].files[0].name);
        datos.append('id_usuario', '<?php echo $_SESSION['Id_usuario'];?>');
      }
      else {
        datos.append('file', $('#file')[0].files[0]);
      }


      $("#hojas-excel").html(wait);
      $('#add-data-old .loaderParent').show();

      $.ajax({
        url: 			'php/datos/modelos/examinar-excel.php',
        type:			'POST',
        data:			datos,
        cache:          false,
        contentType:    false,
        processData:    false,
        success: function(response){
          $('#add-data-old .loaderParent').hide();

          $("#opc_aux").text("");
          $("#nombre_hoja_aux").text("");

          $("#hojas-excel").html(response);
          iniciarTabla("hojas-excel-table");
          /*if(response == 'si'){
            $("#modal-generic .message").text("Registro exitoso");
            $("#modal-generic .card-footer .btn-action").attr("onclick", "window.location.reload();");
            $("#modal-generic").modal("show");
          }
          else{
            if(response == "vacio"){
              //alertify.warning("Datos vacíos o sin modificación.");
                
            }
            else{
              $("#modal-generic .message").text("Error al registrar");
              $("#modal-generic").modal("show");
            } 
          }*/

          //console.log(response);
        }
        ,
        error: function(response){
          $('#add-data-old .loaderParent').hide();
          $("#modal-generic .message").text("Error al examinar");
          $("#modal-generic").modal("show");
        }
      });

    });

    function importarData(hoja) {
      $("#opc_aux").text("importar_data");
      $("#nombre_hoja_aux").text(hoja);
      $("#form-add-data-old").trigger("submit");
    }
    
  </script>

<?php
  }
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
                <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">X</button>
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
                <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">X</button>
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
