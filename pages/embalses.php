<?php
require_once 'php/Conexion.php';
require_once 'php/batimetria.php';

$queryEmbalses = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo';");
$queryEstados = mysqli_query($conn, "SELECT * FROM estados;");
$queryUsers = mysqli_query($conn, "SELECT * FROM usuarios;");
// $result = mysqli_fetch_assoc($queriEstados);
$queryEmbalsesEliminados = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'inactivo';");
$numEliminados = mysqli_num_rows($queryEmbalsesEliminados);

$estados = array();
while ($row = mysqli_fetch_array($queryEstados)) {
  $id = $row['id_estado'];
  $estado = $row['estado'];
  $estados[$id] = $estado;
}

$encargados = array();
while ($row = mysqli_fetch_array($queryUsers)) {
  $id = $row['Id_usuario'];
  $nombre = $row['P_Nombre'] . " " . $row['P_Apellido'];
  $encargados[$id] = $nombre;
}



$embalseBat = new Batimetria('1', $conn);
$cota = implode("-",$embalseBat->getByCota("2024","268.455"));
$minima = $embalseBat->cotaMinima();
$año = implode("-", $embalseBat->getYears());
$closeYear = $embalseBat->getCloseYear();
$volMin = $embalseBat->volumenDisponible();
// $year = $embalseBat->getCloseYear("2015");
// $prueba = $embalseBat->getByCota("2001", 210.209);

// $prueba = $embalseBat->getCloseCota("2001","210.206");

// Ahora puedes acceder a la capital de un estado específico
// $capitalCarabobo = $estados["24"];

// Muestra la capital de Carabobo
// echo "La capital de Carabobo es: " . $capitalCarabobo;

// echo json_encode($arrayEstados);
// echo $arrayEstados[0]['2'];
// echo json_encode($arrayEstados[0][1], $estados['1']);
closeConection($conn);
?>
<div class="container-fluid py-4">
  <div class="row">
    <!--<div class="col-lg-8">
          <div class="row">
            <div class="col-xl-6 mb-xl-0 mb-4">
              <div class="card bg-transparent shadow-xl">
                <div class="overflow-hidden position-relative border-radius-xl" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/card-visa.jpg');">
                  <span class="mask bg-gradient-dark"></span>
                  <div class="card-body position-relative z-index-1 p-3">
                    <i class="fas fa-wifi text-white p-2"></i>
                    <h5 class="text-white mt-4 mb-5 pb-2">4562&nbsp;&nbsp;&nbsp;1122&nbsp;&nbsp;&nbsp;4594&nbsp;&nbsp;&nbsp;7852</h5>
                    <div class="d-flex">
                      <div class="d-flex">
                        <div class="me-4">
                          <p class="text-white text-sm opacity-8 mb-0">Card Holder</p>
                          <h6 class="text-white mb-0">Jack Peterson</h6>
                        </div>
                        <div>
                          <p class="text-white text-sm opacity-8 mb-0">Expires</p>
                          <h6 class="text-white mb-0">11/22</h6>
                        </div>
                      </div>
                      <div class="ms-auto w-20 d-flex align-items-end justify-content-end">
                        <img class="w-60 mt-2" src="../assets/img/logos/mastercard.png" alt="logo">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-6">
              <div class="row">
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header mx-4 p-3 text-center">
                      <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                        <i class="fas fa-landmark opacity-10"></i>
                      </div>
                    </div>
                    <div class="card-body pt-0 p-3 text-center">
                      <h6 class="text-center mb-0">Salary</h6>
                      <span class="text-xs">Belong Interactive</span>
                      <hr class="horizontal dark my-3">
                      <h5 class="mb-0">+$2000</h5>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mt-md-0 mt-4">
                  <div class="card">
                    <div class="card-header mx-4 p-3 text-center">
                      <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                        <i class="fab fa-paypal opacity-10"></i>
                      </div>
                    </div>
                    <div class="card-body pt-0 p-3 text-center">
                      <h6 class="text-center mb-0">Paypal</h6>
                      <span class="text-xs">Freelance Payment</span>
                      <hr class="horizontal dark my-3">
                      <h5 class="mb-0">$455.00</h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 mb-lg-0 mb-4">
              <div class="card mt-4">
                <div class="card-header pb-0 p-3">
                  <div class="row">
                    <div class="col-6 d-flex align-items-center">
                      <h6 class="mb-0">Payment Method</h6>
                    </div>
                    <div class="col-6 text-end">
                      <a class="btn bg-gradient-dark mb-0" href="javascript:;"><i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Card</a>
                    </div>
                  </div>
                </div>
                <div class="card-body p-3">
                  <div class="row">
                    <div class="col-md-6 mb-md-0 mb-4">
                      <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                        <img class="w-10 me-3 mb-0" src="../assets/img/logos/mastercard.png" alt="logo">
                        <h6 class="mb-0">****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;7852</h6>
                        <i class="fas fa-pencil-alt ms-auto text-dark cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Card"></i>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                        <img class="w-10 me-3 mb-0" src="../assets/img/logos/visa.png" alt="logo">
                        <h6 class="mb-0">****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;5248</h6>
                        <i class="fas fa-pencil-alt ms-auto text-dark cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Card"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>-->
    <div class="col-lg-12">
      <div class="card h-100">
        <div class="card-header pb-0">
          <!-- <div class="row"> -->
          <!-- <div class="col-6 d-flex align-items-center"> -->
          <h6 class="">Embalses <?php echo $cota."  --  ".$minima."  --  ".$año."  --  ".$closeYear."  --  ".$volMin?></h6>
          <!-- </div> -->
          <!--<div class="col-6 text-end">
                  <button class="btn btn-outline-primary btn-sm mb-0">View All</button>
                </div>-->
          <!-- </div> -->
        </div>
        <div class="card-body p-3 pb-0">
          <div class="text-center">
            <a href="?page=crear_embalse">
              <button type="button" class="btn btn-primary btn-block">
                Nuevo
              </button>
            </a>
          </div>

          <div class="dt-responsive table-responsive">
            <?php
            if (mysqli_num_rows($queryEmbalses) > 0) {
            ?>
              <table id="table-embalses" class="table table-striped table-bordered nowrap">
                <thead>
                  <tr>
                    <th>Embalse</th>
                    <th class="hide-cell">Volumen actual</th>
                    <th style="text-align: center;" class="hide-cell">Encargado</th>
                    <th style="text-align: center;">Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  while ($row = mysqli_fetch_array($queryEmbalses)) {
                  ?>
                    <tr>
                      <td>
                        <div class="d-flex flex-column px-3">
                          <h6 class="mb-1 text-dark font-weight-bold text-sm"> <?php echo $row['nombre_embalse'] ?> </h6>
                          <!-- <span class="text-xs"> <?php echo $estados[$row['id_estado']]; ?> </span> -->
                        </div>
                      </td>
                      <td class="hide-cell">
                        <div class="d-flex flex-column px-3">
                          <h6 class="mb-1 text-dark font-weight-bold text-sm">1.247,3 Hm3 (50%)</h6>
                          <span class="text-xs">20/12/2023</span>
                        </div>
                      </td>
                      <td style="vertical-align: middle;" class="hide-cell">
                        <div class="d-flex justify-content-center">
                          <div><?php
                                if ($row['id_encargado'] == '0' || $row['id_encargado'] == null || $row['id_encargado'] == '') { ?>
                              <h6 class="mb-1 text-dark font-weight-bold text-sm">No hay personal encargado</h6>
                            <?php
                                } else {
                            ?>
                              <h6 class="mb-1 text-dark font-weight-bold text-sm"><?php echo $encargados[$row['id_encargado']] ?></h6>
                            <?php } ?>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex align-items-center justify-content-center text-sm">
                          <a data-id="<?php echo $row['id_embalse']; ?>" class="editar-embalse btn btn-link text-dark px-2 mb-0" href="?page=editar_embalse"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i><span class="hide-cell">Editar</span></a>
                          <a data-id="<?php echo $row['id_embalse']; ?>" class="eliminar-embalse btn btn-link text-dark px-2 mb-0"><i class="fas fa-trash text-dark me-2" aria-hidden="true"></i><span class="hide-cell">Eliminar</span></a>
                          <!-- <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i><span class="hide-cell"> PDF</span></button> -->
                        </div>
                      </td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            <?php
            } else {
            ?>
              <h2 class="mb-1 text-dark font-weight-bold text-center mt-4">No existen embalses cargados</h2>
            <?php
            }
            ?>
          </div>
          <br><br><br>

        </div>
      </div>
    </div>
    <?php
    if ($numEliminados > 0) {
    ?>

      <div class="col-lg-12 mt-5">
        <div class="card h-100">
          <div class="card-header pb-0">
            <!-- <div class="row"> -->
            <!-- <div class="col-6 d-flex align-items-center"> -->
            <h6 class="">Embalses eliminados</h6>
            <!-- </div> -->
            <!--<div class="col-6 text-end">
                  <button class="btn btn-outline-primary btn-sm mb-0">View All</button>
                </div>-->
            <!-- </div> -->
          </div>
          <div class="card-body p-3 pb-0">
            <!-- <div class="text-center">
            <a href="?page=crear_embalse">
              <button type="button" class="btn btn-primary btn-block">
                Nuevo
              </button>
            </a>
          </div> -->

            <div class="dt-responsive table-responsive">
              <?php
              if (mysqli_num_rows($queryEmbalsesEliminados) > 0) {
              ?>
                <table id="table-embalses-eliminados" class="table table-striped table-bordered nowrap">
                  <thead>
                    <tr>
                      <th>Embalse</th>
                      <th class="hide-cell">Volumen actual</th>
                      <th style="text-align: center;" class="hide-cell">Encargado</th>
                      <th style="text-align: center;">Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    while ($row = mysqli_fetch_array($queryEmbalsesEliminados)) {
                    ?>
                      <tr>
                        <td>
                          <div class="d-flex flex-column px-3">
                            <h6 class="mb-1 text-dark font-weight-bold text-sm"> <?php echo $row['nombre_embalse'] ?> </h6>
                            <!-- <span class="text-xs"> <?php echo $estados[$row['id_estado']]; ?> </span> -->
                          </div>
                        </td>
                        <td class="hide-cell">
                          <div class="d-flex flex-column px-3">
                            <h6 class="mb-1 text-dark font-weight-bold text-sm">1.247,3 Hm3 (50%)</h6>
                            <span class="text-xs">20/12/2023</span>
                          </div>
                        </td>
                        <td style="vertical-align: middle;" class="hide-cell">
                          <div class="d-flex justify-content-center">
                            <div><?php
                                  if ($row['id_encargado'] == '0' || $row['id_encargado'] == null || $row['id_encargado'] == '') { ?>
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">No hay personal encargado</h6>
                              <?php
                                  } else {
                              ?>
                                <h6 class="mb-1 text-dark font-weight-bold text-sm"><?php echo $encargados[$row['id_encargado']] ?></h6>
                              <?php } ?>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="d-flex align-items-center justify-content-center text-sm">
                            <a data-id="<?php echo $row['id_embalse']; ?>" class="restaurar-embalse btn btn-link text-dark px-2 mb-0"><i class="fas fa-redo text-dark me-2" aria-hidden="true"></i><span class="hide-cell">Restaurar</span></a>
                            <!-- <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i><span class="hide-cell"> PDF</span></button> -->
                          </div>
                        </td>
                      </tr>
                    <?php
                    }
                    ?>
                  </tbody>
                </table>
              <?php
              } else {
              ?>
                <h2 class="mb-1 text-dark font-weight-bold text-center mt-4">No existen embalses cargados</h2>
              <?php
              }
              ?>
            </div>
            <br><br><br>

          </div>
        </div>
      </div>

    <?php
    }
    ?>
  </div>
  <!--<div class="row">
        <div class="col-md-7 mt-4">
          <div class="card">
            <div class="card-header pb-0 px-3">
              <h6 class="mb-0">Billing Information</h6>
            </div>
            <div class="card-body pt-4 p-3">
              <ul class="list-group">
                <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="mb-3 text-sm">Oliver Liam</h6>
                    <span class="mb-2 text-xs">Company Name: <span class="text-dark font-weight-bold ms-sm-2">Viking Burrito</span></span>
                    <span class="mb-2 text-xs">Email Address: <span class="text-dark ms-sm-2 font-weight-bold">oliver@burrito.com</span></span>
                    <span class="text-xs">VAT Number: <span class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                  </div>
                  <div class="ms-auto text-end">
                    <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="mb-3 text-sm">Lucas Harper</h6>
                    <span class="mb-2 text-xs">Company Name: <span class="text-dark font-weight-bold ms-sm-2">Stone Tech Zone</span></span>
                    <span class="mb-2 text-xs">Email Address: <span class="text-dark ms-sm-2 font-weight-bold">lucas@stone-tech.com</span></span>
                    <span class="text-xs">VAT Number: <span class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                  </div>
                  <div class="ms-auto text-end">
                    <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="mb-3 text-sm">Ethan James</h6>
                    <span class="mb-2 text-xs">Company Name: <span class="text-dark font-weight-bold ms-sm-2">Fiber Notion</span></span>
                    <span class="mb-2 text-xs">Email Address: <span class="text-dark ms-sm-2 font-weight-bold">ethan@fiber.com</span></span>
                    <span class="text-xs">VAT Number: <span class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                  </div>
                  <div class="ms-auto text-end">
                    <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-md-5 mt-4">
          <div class="card h-100 mb-4">
            <div class="card-header pb-0 px-3">
              <div class="row">
                <div class="col-md-6">
                  <h6 class="mb-0">Your Transaction's</h6>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                  <i class="far fa-calendar-alt me-2"></i>
                  <small>23 - 30 March 2020</small>
                </div>
              </div>
            </div>
            <div class="card-body pt-4 p-3">
              <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Newest</h6>
              <ul class="list-group">
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <button class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-down"></i></button>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">Netflix</h6>
                      <span class="text-xs">27 March 2020, at 12:30 PM</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold">
                    - $ 2,500
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">Apple</h6>
                      <span class="text-xs">27 March 2020, at 04:30 AM</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                    + $ 2,000
                  </div>
                </li>
              </ul>
              <h6 class="text-uppercase text-body text-xs font-weight-bolder my-3">Yesterday</h6>
              <ul class="list-group">
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">Stripe</h6>
                      <span class="text-xs">26 March 2020, at 13:45 PM</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                    + $ 750
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">HubSpot</h6>
                      <span class="text-xs">26 March 2020, at 12:30 PM</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                    + $ 1,000
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">Creative Tim</h6>
                      <span class="text-xs">26 March 2020, at 08:30 AM</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                    + $ 2,500
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <button class="btn btn-icon-only btn-rounded btn-outline-dark mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-exclamation"></i></button>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">Webflow</h6>
                      <span class="text-xs">26 March 2020, at 05:00 AM</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                    Pending
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>-->
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


<!--   Core JS Files   -->

<script>
  var win = navigator.platform.indexOf('Win') > -1;
  if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
      damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
  }

  // iniciarTabla('table-embalses');
  // if ($("#table-embalses-eliminados")) {
  //   iniciarTabla('table-embalses-eliminados');
  // }


  // $('#table-embalses').DataTable({
  //   dom: "<'top'<'d-flex align-items-center justify-content-between'lf>>rt<'bottom'<'d-flex flex-column align-items-center'ip>><'clear'>",
  //   language: {
  //     "decimal": "",
  //     "emptyTable": "No hay información",
  //     "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
  //     "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
  //     "infoFiltered": "(Filtrado de _MAX_ total entradas)",
  //     "infoPostFix": "",
  //     "thousands": ",",
  //     "lengthMenu": "Mostrar _MENU_ Entradas",
  //     "loadingRecords": "Cargando...",
  //     "processing": "Procesando...",
  //     "search": "Buscar:",
  //     "zeroRecords": "Sin resultados encontrados",
  //     "paginate": {
  //       "first": "Primero",
  //       "last": "Ultimo",
  //       "next": "Siguiente",
  //       "previous": "Anterior"
  //     }
  //   },
  // });

  $(document).ready(function() {
    $(".editar-embalse").click(function(e) {
      // Evitar que el enlace realice la acción predeterminada (navegación)
      e.preventDefault();

      // Obtener el valor del atributo data-id
      var id = $(this).data("id");
      // console.log(id)
      $.ajax({
        type: "POST",
        url: "pages/session_variable.php",
        data: {
          valor: id
        },
        success: function(response) {
          // console.log(response, "si")
          window.location.href = "?page=editar_embalse";

        }
      });
    });

    $(".eliminar-embalse").on("click", function(e) {
      // Realizar la consulta AJAX al servidor
      console.log("Eliminar");
      e.preventDefault();
      var id_embalse = $(this).data("id");
      console.log(id_embalse)
      $.ajax({
        url: "./php/get-embalse.php", // Ruta a tu script PHP de consulta
        type: "POST",
        data: {
          id: id_embalse,
          action: "eliminar"
        },
        success: function(data) {
          // Mostrar el resultado en la modal
          console.log(data)
          $("#embalseTitulo").text("¿Eliminar embalse?")
          $("#embalseNombre").text(data);
          $("#embalseIdInput")[0].value = id_embalse;
          $("#buttom-form")[0].name = "eliminar";
          $('#modal-form').modal('show');
        },
        error: function() {
          alert("Error al realizar la consulta.");
        }
      });
    });

    $(".restaurar-embalse").on("click", function(e) {
      // Realizar la consulta AJAX al servidor
      console.log("Restaurar");
      e.preventDefault();
      var id_embalse = $(this).data("id");
      console.log(id_embalse)
      $.ajax({
        url: "./php/get-embalse.php", // Ruta a tu script PHP de consulta
        type: "POST",
        data: {
          id: id_embalse,
          action: "restaurar"
        },
        success: function(data) {
          // Mostrar el resultado en la modal
          console.log(data)
          $("#embalseTitulo").text("¿Restaurar embalse?")
          $("#embalseNombre").text(data);
          $("#embalseIdInput")[0].value = id_embalse;
          $("#buttom-form")[0].name = "restaurar";
          $('#modal-form').modal('show');
        },
        error: function() {
          alert("Error al realizar la consulta.");
        }
      });
    });

    iniciarTabla('table-embalses');
    if ($("#table-embalses-eliminados")) {
      iniciarTabla('table-embalses-eliminados');
    }

  });
</script>



<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <button type="button" class="btn btn-secondary close-modal btn-rounded mb-0" data-bs-dismiss="modal">X</button>
        <div class="card card-plain">

          <div class="card-body">

            <div class="">
              <h5 style="text-align:center;" id="embalseTitulo" class="mb-0"></h5>
              <h3 style="text-align:center;" id="embalseNombre" class="mt-3"></h3>
            </div>
            <form method="POST" action="php/proces_embalse.php" enctype="multipart/form-data">

              <div class="input-group mb-2">
                <input style="display: none;" id="embalseIdInput" type="text" class="form-control" name="id_embalse" value="">
              </div>

              <div class="text-center d-flex flex-col-6 justify-content-center">
                <button type="submit" id="buttom-form" name="delete" class="btn btn-round btn-primary btn-lg  mt-3 mb-0">Confirmar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- Modal -->
<div class="modal fade" id="edit-embalse" tabindex="-1" role="dialog" aria-labelledby="edit-embalse" aria-hidden="true">
  <div class="modal-dialog modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <h3 class="font-weight-bolder text-primary text-gradient">Editar</h3>
            <!--<p class="mb-0">Enter your email and password to register</p>-->
          </div>
          <div class="card-body pb-3">
            <form role="form text-left">
              <div class="row">
                <div class="col">
                  <label>Nombre</label>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Nombre" aria-label="Nombre" value="Nombre" aria-describedby="name-addon">
                  </div>
                </div>
                <div class="col">
                  <label>Capacidad</label>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Capacidad" aria-label="Capacidad" value="Capacidad" aria-describedby="email-addon">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <label>Dirección</label>
                  <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Dirección" aria-label="Direccion" value="Direccion" aria-describedby="password-addon">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  .
                  <br>
                  .
                  <br>
                  .
                  <br>
                  .
                  <br>
                  .
                  <br>
                </div>
                <!--<div class="form-check form-check-info text-left">
                      <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked="">
                      <label class="form-check-label" for="flexCheckDefault">
                        I agree the <a href="javascrpt:;" class="text-dark font-weight-bolder">Terms and Conditions</a>
                      </label>
                    </div>-->
              </div>
              <div class="text-center">
                <button type="button" class="btn bg-gradient-primary btn-lg btn-rounded w-100 mt-4 mb-0">Guardar</button>
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