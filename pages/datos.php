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

  $sql = "SELECT id_embalse, nombre_embalse, estado, municipio, parroquia, id_encargado
          FROM embalses em, estados e, municipios m, parroquias p
          WHERE em.id_estado = e.id_estado AND em.id_municipio = m.id_municipio AND em.id_parroquia = p.id_parroquia AND m.id_estado = e.id_estado AND p.id_municipio = m.id_municipio $add_where;";

  $query = mysqli_query($conn, $sql);

  closeConection($conn);

  $options_extraccion = '<option value="">Seleccione</option>';
  $options_extraccion .='<option value="Riego">Riego</option>';
  $options_extraccion .='<option value="Hidroelectricidad">Hidroelectricidad</option>';
  $options_extraccion .='<option value="Consumo Humano">Consumo Humano</option>';
  $options_extraccion .='<option value="Control de Inundaciones (Aliviadero)">Control de Inundaciones (Aliviadero)</option>';
  $options_extraccion .='<option value="Recreación">Recreación</option>';
?>


    <div class="container-fluid py-4">
      <div class="row">
        
        <div class="col-lg-12">
          <div class="card h-100 mb-3">
            <div class="card-header pb-0 p-3">
              <div class="row">
                <div class="col-6 d-flex align-items-center">
                  <h4 class="mb-3">Carga de Datos</h4>
                </div>
                <!--<div class="col-6 text-end">
                  <button class="btn btn-outline-primary btn-sm mb-0">View All</button>
                </div>-->
              </div>
            </div>

            <div class="card-body p-3 pb-0">
              <!--<div class="text-center">
                <button type="button" class="btn bg-gradient-info btn-block" data-bs-toggle="modal" data-bs-target="#add">
                  Nuevo
                </button>
              </div>-->
              <ul class="list-group">
                
<?php
            if(mysqli_num_rows($query) > 0){
?>
                <div class="table-responsive">
                  <div class="mb-3">
                    <table class="table align-items-center text-sm text-center table-sm" id="table">
                      <thead class="table-primary">
                        <tr>
                            <th scope="col" class="sort" data-sort="name">Nombre</th>
                            <th scope="col" class="sort" data-sort="budget">Ubicación</th>
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
                  <td class="budget">
                    <?php echo $row['estado'] . ", " . $row['municipio'] . ", " . $row['parroquia'];?>
                  </td>
                  <td class="budget">
                    <a class="btn btn-primary btn-sm px-3 mb-0" href="javascript:;" onclick="$('#add').modal('show');">
                      <i class="fas fa-plus text-dark me-2" aria-hidden="true"></i>
                      Añadir Reporte
                    </a>
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
              </ul>
            </div>
          </div>
        </div>
      </div>
      
      <footer class="footer pt-3  ">
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
      </footer>
    </div>

  







  <script>
    /*var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }*/
  </script>

  <script>
    iniciarTabla('table');


    var count = 1;
    $(document).on('click', '#addRows', function() { 
      count++;
      var htmlRows = '';
      htmlRows += '<div class="row">';
      htmlRows += '   <div class="col">';
      htmlRows += '       <label>Tipo</label>';
      htmlRows += '       <div class="input-group mb-3">';
      htmlRows += '           <select class="form-select" name="tipo_extraccion[]" id="tipo_extraccion_' + count + '" required>';
      htmlRows += '               <?php echo $options_extraccion;?>';
      htmlRows += '           </select>';
      htmlRows += '       </div>';
      htmlRows += '   </div>';
      htmlRows += '   <div class="col">';
      htmlRows += '       <label>Valor</label>';
      htmlRows += '       <div class="input-group mb-3">';
      htmlRows += '           <input type="text" class="form-control" name="valor_extraccion[]" id="valor_extraccion_' + count + '" placeholder="Valor de la Extracción" required>';
      htmlRows += '       </div>';
      htmlRows += '   </div>';
      htmlRows += '   <div class="col" style="flex: 0 0 0% !important;">';
      htmlRows += '       <label style="color: transparent;">Valor</label>';
      htmlRows += '       <button class="btn btn-danger btn-sm" id="removeRow" type="button" style="padding: 10px;"><i class="fa fa-trash"></i></button>';
      htmlRows += '   </div>';
      htmlRows += '</div>';

      $('#box-extraccion').append(htmlRows);
    });

    $(document).on('click', '#removeRow', function(){
      $(this).closest('.row').remove();
    });
  </script>





    <!-- Modal -->
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-body p-0">
            <div class="card card-plain">
              <div class="card-header pb-0 text-left">
                <h3 class="font-weight-bolder text-primary text-gradient">Añadir Reporte</h3>
                <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">X</button>
                <!--<p class="mb-0">Enter your email and password to register</p>-->
              </div>
              <div class="card-body pb-3">
                <form role="form text-left" id="form">
    <?php
                if(date("H:i") . ":00" > "10:00:00"){
    ?>
                  <h6 class="text-red text-center">Estás retrasado al enviar el reporte</h6>
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
                      <label>Cota</label>
                      <div class="input-group mb-3">
                        <input type="number" class="form-control" name="valor_cota" id="valor_cota" placeholder="Cota" aria-label="Cota" aria-describedby="name-addon" required>
                      </div>
                    </div>
                  </div>

                  <h6 class="mt-2">Extracción</h6>
                  <div id="box-extraccion">
                    <div class="row">
                      <div class="col">
                        <label>Tipo</label>
                        <div class="input-group mb-3">
                          <select class="form-select" name="tipo_extraccion[]" id="tipo_extraccion_1" required>
                            <?php echo $options_extraccion;?>
                          </select>
                        </div>
                      </div>
                      <div class="col">
                        <label>Valor</label>
                        <div class="input-group mb-3">
                          <input type="text" class="form-control" name="valor_extraccion[]" id="valor_extraccion_1" placeholder="Valor de la Extracción" required>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12 text-center" style="margin-top: 12px;">
                        <button class="btn btn-success" id="addRows" type="button">Añadir Otra Extracción</button>
                    </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn bg-gradient-primary btn-lg btn-rounded w-100 mt-4 mb-0">Guardar</button>
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




    <div class="modal fade" id="datos" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true">
      <div class="modal-dialog modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-body p-0">
            <div class="card card-plain">
              <div class="card-header pb-0 text-left">
                  <h3 class="font-weight-bolder text-primary text-gradient">Datos</h3>
                  <!--<p class="mb-0">Enter your email and password to register</p>-->
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
                  <!--<div class="text-center">
                    <button type="button" class="btn bg-gradient-primary btn-lg btn-rounded w-100 mt-4 mb-0">Guardar</button>
                  </div>-->
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


    