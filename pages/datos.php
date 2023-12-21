<?php
require_once 'php/Conexion.php';

$sql = "SELECT * FROM embalses;";

$query = mysqli_query($conn, $sql);
/*$data = array();

if (mysqli_num_rows($res) > 0) {

  while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
  }
} else {
  // echo "No se encontraron resultados.";
}*/

closeConection($conn);
?>


    <div class="container-fluid py-4">
      <div class="row">
        
        <div class="col-lg-12">
          <div class="card h-100 mb-3">
            <div class="card-header pb-0 p-3">
              <div class="row">
                <div class="col-6 d-flex align-items-center">
                  <h4 class="mb-3">Embalses</h4>
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
                    <table class="table align-items-center text-center" id="table">
                      <thead class="table-dark">
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
                            <span class="name mb-0"><?php echo $row['Nombre_embalse'];?></span>
                        </div>
                    </div>
                  </th>
                  <td class="budget">
                    Sex
                  </td>
                  <td class="budget">
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" onclick="$('#add').modal('show');"><i class="fas fa-plus text-dark me-2" aria-hidden="true"></i>Añadir Reporte</a>
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
  </script>





    <!-- Modal -->
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-body p-0">
            <div class="card card-plain">
              <div class="card-header pb-0 text-left">
                <h3 class="font-weight-bolder text-primary text-gradient">Añadir</h3>
                <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">X</button>
                <!--<p class="mb-0">Enter your email and password to register</p>-->
              </div>
              <div class="card-body pb-3">
                <form role="form text-left" id="form">

                  <div class="row">
                    <div class="col">
                      <label>Fecha</label>
                      <div class="input-group mb-3">
                        <input type="date" class="form-control" value="<?php echo date("Y-m-d");?>" disabled required>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col">
                      <label>Hora</label>
                      <div class="input-group mb-3">
                        <input type="time" class="form-control" value="<?php echo date("H:i") . ":00";?>" disabled required>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col">
                      <label>Cota</label>
                      <div class="input-group mb-3">
                        <input type="number" class="form-control" placeholder="Cota" aria-label="Cota" aria-describedby="name-addon" required>
                      </div>
                    </div>
                  </div>

                  <h6 class="mt-2">Extracción</h6>
                  <div class="row">
                    <div class="col">
                      <label>Tipo</label>
                      <div class="input-group mb-3">
                        <select class="form-select" required>
                          <option value="">Seleccione</option>
                          <option value="Riego">Riego</option>
                          <option value="Hidroelectricidad">Hidroelectricidad</option>
                          <option value="Consumo Humano">Consumo Humano</option>
                          <option value="Control de Inundaciones (Aliviadero)">Control de Inundaciones (Aliviadero)</option>
                          <option value="Recreación">Recreación</option>
                        </select>
                      </div>
                    </div>
                    <div class="col">
                      <label>Valor</label>
                      <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Valor de la Extracción" required>
                      </div>
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


    