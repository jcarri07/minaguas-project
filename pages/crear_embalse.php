<!--
=========================================================
* Argon Dashboard 2 - v2.0.4
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">
<?php
if (!isset($_SESSION)) {
  session_start();
};
if (!isset($_SESSION["Id_usuario"])) {

  print "<script>window.location='index.php';</script>";
}

date_default_timezone_set("America/Caracas");
?>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/logos/cropped-mminaguas.webp">
  <title>
    Minaguas
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>
  <aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/argon-dashboard/pages/dashboard.html " target="_blank">
        <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold">Argon Dashboard 2</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <!-- SIDEBAR -->
      <?php include './sidebar.php'; ?>
    </div>
    <div class="sidenav-footer mx-3 ">
      <div class="card card-plain shadow-none" id="sidenavCard">
        <img class="w-50 mx-auto" src="../assets/img/illustrations/icon-documentation.svg" alt="sidebar_illustration">
        <div class="card-body text-center p-3 w-100 pt-0">
          <div class="docs-info">
            <h6 class="mb-0">Need help?</h6>
            <p class="text-xs font-weight-bold mb-0">Please check our docs</p>
          </div>
        </div>
      </div>
      <a href="https://www.creative-tim.com/learning-lab/bootstrap/license/argon-dashboard" target="_blank" class="btn btn-dark btn-sm w-100 mb-3">Documentation</a>
      <a class="btn btn-primary btn-sm mb-0 w-100" href="https://www.creative-tim.com/product/argon-dashboard-pro?ref=sidebarfree" type="button">Upgrade to pro</a>
    </div>
  </aside>
  <main class="main-content position-relative border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Embalses</a></li>
            <!--<li class="breadcrumb-item text-sm text-white active" aria-current="page">Billing</li>-->
          </ol>
          <h6 class="font-weight-bolder text-white mb-0">Fichas Técnicas de los Embalses</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group">
              <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
              <input type="text" class="form-control" placeholder="Type here...">
            </div>
          </div>
          <!--<ul class="navbar-nav  justify-content-end">
            <li class="nav-item d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-white font-weight-bold px-0">
                <i class="fa fa-user me-sm-1"></i>
                <span class="d-sm-inline d-none">Sign In</span>
              </a>
            </li>
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line bg-white"></i>
                  <i class="sidenav-toggler-line bg-white"></i>
                  <i class="sidenav-toggler-line bg-white"></i>
                </div>
              </a>
            </li>
            <li class="nav-item px-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-white p-0">
                <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
              </a>
            </li>
            <li class="nav-item dropdown pe-2 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-white p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-bell cursor-pointer"></i>
              </a>
              <ul class="dropdown-menu  dropdown-menu-end  px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                <li class="mb-2">
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="my-auto">
                        <img src="../assets/img/team-2.jpg" class="avatar avatar-sm  me-3 ">
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">New message</span> from Laur
                        </h6>
                        <p class="text-xs text-secondary mb-0">
                          <i class="fa fa-clock me-1"></i>
                          13 minutes ago
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="mb-2">
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="my-auto">
                        <img src="../assets/img/small-logos/logo-spotify.svg" class="avatar avatar-sm bg-gradient-dark  me-3 ">
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">New album</span> by Travis Scott
                        </h6>
                        <p class="text-xs text-secondary mb-0">
                          <i class="fa fa-clock me-1"></i>
                          1 day
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="avatar avatar-sm bg-gradient-secondary  me-3  my-auto">
                        <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                          <title>credit-card</title>
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
                              <g transform="translate(1716.000000, 291.000000)">
                                <g transform="translate(453.000000, 454.000000)">
                                  <path class="color-background" d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z" opacity="0.593633743"></path>
                                  <path class="color-background" d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z"></path>
                                </g>
                              </g>
                            </g>
                          </g>
                        </svg>
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          Payment successfully completed
                        </h6>
                        <p class="text-xs text-secondary mb-0">
                          <i class="fa fa-clock me-1"></i>
                          2 days
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
              </ul>
            </li>
          </ul>-->
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
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
            <!-- inicio -->

            <form method="POST" action="../php/proces_embalse.php" enctype="multipart/form-data">
              <div class="p-5 m-5">

                <h3 class="pb-3">Información de la cuenca:</h3>

                <div class="row">
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="cuenca">Cuenca principal</label>
                    <input type="text" class="form-control" id="cuenca" name="cuenca" placeholder="Ingrese la cuenca principal">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="afluentes">Afluentes principales</label>
                    <input type="text" class="form-control" id="afluentes" name="afluentes" placeholder="Ingrese los afluentes principales">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="area">Área de la cuenca</label>
                    <input type="number" class="form-control" id="area" name="area" placeholder="Ingrese el area de la cuenca en km2">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="escurrimiento">Escurrimiento medio</label>
                    <input type="number" class="form-control" id="escurrimiento" name="escurrimiento" placeholder="Ingrese el escurrimiento medio en m3/s">
                  </div>
                </div>

                <h3 class="pb-3 pt-3">Información de los embalses:</h3>

                <div class="row">
                  <div class="col-md-6 col-sm-12 form-group">
                    <label for="ubicacion_embalse">Ubicación del embalse</label>
                    <textarea class="form-control" id="ubicacion_embalse" name="ubicacion_embalse" rows="5" placeholder="Ingrese la ubicacion del embalse"></textarea>
                  </div>
                  <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                      <label for="organo">Órgano rector</label>
                      <input type="text" class="form-control" id="organo" name="organo" placeholder="Ingrese el organo rector">
                    </div>
                    <div class="form-group">
                      <label for="personal">Personal encargado a nivel central</label>
                      <select class="form-control" id="personal" name="personal">
                        <option value=""></option>
                        <option value="1">1 persona</option>
                        <option value="2">2 personas</option>
                        <option value="3">3 personas</option>
                        <option value="4">4 personas</option>
                        <option value="5">5 personas</option>
                        <option value="6">6 o más personas</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="operador">Operador</label>
                    <input type="text" class="form-control" id="operador" name="operador" placeholder="Ingrese el operador">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="autoridad">Autoridad responsable del embalse</label>
                    <select class="form-control" id="autoridad" name="autoridad">
                      <option value=""></option>
                      <option value="1">Ministerio del Poder Popular para el Ambiente</option>
                      <option value="2">Ministerio del Poder Popular para la Energía Eléctrica</option>
                      <option value="3">Ministerio del Poder Popular para la Agricultura y Tierras</option>
                      <option value="4">Ministerio del Poder Popular para el Turismo</option>
                      <option value="5">Ministerio del Poder Popular para la Defensa</option>
                      <option value="6">Otra autoridad</option>
                    </select>
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="proyectista">Proyectista</label>
                    <input type="text" class="form-control" id="proyectista" name="proyectista" placeholder="Ingrese el proyectista">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="constructor">Constructor</label>
                    <input type="text" class="form-control" id="constructor" name="constructor" placeholder="Ingrese el constructor">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="inicio_construccion">Año de inicio de construccion</label>
                    <input type="number" class="form-control" id="inicio_construccion" name="inicio_construccion" placeholder="Ingrese el año de inicio de construccion">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="duracion_construccion">Duración de construcción</label>
                    <input type="number" class="form-control" id="duracion_construccion" name="duracion_construccion" placeholder="Ingrese la duracion de construccion en años">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="inicio_operacion">Inicio de operación</label>
                    <input type="number" class="form-control" id="inicio_operacion" name="inicio_operacion" placeholder="Ingrese el año de inicio de operacion">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="monitoreo">Monitoreo de niveles del embalse</label>
                    <input type="text" class="form-control" id="monitoreo" name="monitoreo" placeholder="Ingrese el tipo de monitoreo del embalse">
                  </div>
                </div>

                <h3 class="pb-3 pt-3">Características de los embalses:</h3>

                <div class="form-group">
                  <label for="batimetria">Batimetría</label>
                  <input type="text" class="form-control" id="batimetria" name="batimetria" placeholder="Ingrese el tipo de batimetria">
                </div>
                <div class="form-group">
                  <label for="vida_util">Vida útil</label>
                  <input type="number" class="form-control" id="vida_util" name="vida_util" placeholder="Ingrese la vida util en años">
                </div>

                <h3 class="pb-3 pt-3">Presa:</h3>

                <div class="row">
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="numero_presas">Número de presas</label>
                    <input type="number" class="form-control" id="numero_presas" name="numero_presas" placeholder="Ingrese el numero de presas">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="tipo_presa">Tipo de presa</label>
                    <input type="text" class="form-control" id="tipo_presa" name="tipo_presa" placeholder="Ingrese el tipo de presa">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="altura">Altura</label>
                    <input type="number" class="form-control" id="altura" name="altura" placeholder="Ingrese la altura en metros">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="talud_arriba">Talud aguas arriba</label>
                    <input type="number" class="form-control" id="talud_arriba" name="talud_arriba" placeholder="Ingrese el talud aguas arriba en grados">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="talud_abajo">Talud aguas abajo</label>
                    <input type="number" class="form-control" id="talud_abajo" name="talud_abajo" placeholder="Ingrese el talud aguas abajo en grados">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="longitud_cresta">Longitud de la cresta</label>
                    <input type="number" class="form-control" id="longitud_cresta" name="longitud_cresta" placeholder="Ingrese la longitud de la cresta en metros">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="cota_cresta">Cota de la cresta</label>
                    <input type="number" class="form-control" id="cota_cresta" name="cota_cresta" placeholder="Ingrese la cota de la cresta en metros sobre el nivel del mar">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="ancho_cresta">Ancho de la cresta</label>
                    <input type="number" class="form-control" id="ancho_cresta" name="ancho_cresta" placeholder="Ingrese el ancho de la cresta en metros">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="volumen_terraplen">Volumen del terraplen</label>
                    <input type="number" class="form-control" id="volumen_terraplen" name="volumen_terraplen" placeholder="Ingrese el volumen del terraplen en m3">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="ancho_base">Ancho maximo de base</label>
                    <input type="number" class="form-control" id="ancho_base" name="ancho_base" placeholder="Ingrese el ancho maximo de base en metros">
                  </div>
                </div>

                <h3 class="pb-3 pt-3">Aliviadero:</h3>

                <div class="row">
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="ubicacion_aliviadero">Ubicacion del aliviadero</label>
                    <input type="text" class="form-control" id="ubicacion_aliviadero" name="ubicacion_aliviadero" placeholder="Ingrese la ubicacion del aliviadero">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="tipo_aliviadero">Tipo de aliviadero</label>
                    <input type="text" class="form-control" id="tipo_aliviadero" name="tipo_aliviadero" placeholder="Ingrese el tipo de aliviadero">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="numero_compuertas_aliviadero">Numero de compuertas del aliviadero</label>
                    <input type="number" class="form-control" id="numero_compuertas_aliviadero" name="numero_compuertas_aliviadero" placeholder="Ingrese el numero de compuertas del aliviadero">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="carga_aliviadero">Carga sobre el vertedero</label>
                    <input type="number" class="form-control" id="carga_aliviadero" name="carga_aliviadero" placeholder="Ingrese la carga sobre el vertedero en metros">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="descarga_aliviadero">Descarga maxima</label>
                    <input type="number" class="form-control" id="descarga_aliviadero" name="descarga_aliviadero" placeholder="Ingrese la descarga maxima en m3/s">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="longitud_aliviadero">Longitud</label>
                    <input type="number" class="form-control" id="longitud_aliviadero" name="longitud_aliviadero" placeholder="Ingrese la longitud en metros">
                  </div>
                </div>

                <h3 class="pb-3 pt-3">Obra de toma:</h3>

                <div class="row">
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="ubicacion_toma">Ubicación de la obra de toma</label>
                    <input type="text" class="form-control" id="ubicacion_toma" name="ubicacion_toma" placeholder="Ingrese la ubicacion de la obra de toma">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="tipo_toma">Tipo de obra de toma</label>
                    <input type="text" class="form-control" id="tipo_toma" name="tipo_toma" placeholder="Ingrese el tipo de obra de toma">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="numero_compuertas_toma">Numero de compuertas de la obra de toma</label>
                    <input type="number" class="form-control" id="numero_compuertas_toma" name="numero_compuertas_toma" placeholder="Ingrese el numero de compuertas de la obra de toma">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="emergencia_toma">Mecanismos de emergencia de la obra de toma</label>
                    <input type="text" class="form-control" id="emergencia_toma" name="emergencia_toma" placeholder="Ingrese los mecanismos de emergencia de la obra de toma">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="regulacion_toma">Mecanismos de regulacion de la obra de toma</label>
                    <input type="text" class="form-control" id="regulacion_toma" name="regulacion_toma" placeholder="Ingrese los mecanismos de regulacion de la obra de toma">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="gasto_toma">Gasto máximo de la obra de toma</label>
                    <input type="number" class="form-control" id="gasto_toma" name="gasto_toma" placeholder="Ingrese el gasto maximo de la obra de toma en m3/s">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="descarga_fondo">Descarga de fondo</label>
                    <input type="text" class="form-control" id="descarga_fondo" name="descarga_fondo" placeholder="Ingrese la descarga de fondo en m3/s o N/A si no aplica">
                  </div>
                </div>

                <h3 class="pb-3 pt-3">Obra hidraulica:</h3>

                <div class="row">
                  <div class="col-xl-4 col-lg-6 form-group">
                    <label for="obra_conduccion">Posee obra de conduccion</label>
                    <input type="text" class="form-control" id="obra_conduccion" name="obra_conduccion" placeholder="Ingrese SI o NO si posee obra de conduccion">
                  </div>
                  <div class="col-xl-4 col-lg-6 form-group">
                    <label for="tipo_conduccion">Tipo de obra de conduccion</label>
                    <input type="text" class="form-control" id="tipo_conduccion" name="tipo_conduccion" placeholder="Ingrese el tipo de obra de conduccion o N/A si no aplica">
                  </div>
                  <div class="col-xl-4 col-lg-6 form-group">
                    <label for="accion_conduccion">Accion requerida de la obra de conduccion</label>
                    <input type="text" class="form-control" id="accion_conduccion" name="accion_conduccion" placeholder="Ingrese la accion requerida de la obra de conduccion o N/A si no aplica">
                  </div>
                </div>

                <h3 class="pb-3 pt-3">Beneficios:</h3>

                <div class="row">
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="proposito">Propósito del embalse</label>
                    <input type="text" class="form-control" id="proposito" name="proposito" placeholder="Ingrese el proposito del embalse">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="uso">Uso actual del embalse</label>
                    <input type="text" class="form-control" id="uso" name="uso" placeholder="Ingrese el uso actual del embalse">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="sectores">Sectores beneficiados</label>
                    <input type="text" class="form-control" id="sectores" name="sectores" placeholder="Ingrese los sectores beneficiados">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="poblacion">Población beneficiada</label>
                    <input type="number" class="form-control" id="poblacion" name="poblacion" placeholder="Ingrese la poblacion beneficiada en habitantes">
                  </div>
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="area_riego">Área de riego beneficiada</label>
                    <input type="number" class="form-control" id="area_riego" name="area_riego" placeholder="Ingrese el area de riego beneficiada en km2">
                  </div>
                </div>

                <h3 class="pb-3 pt-3">Responsable:</h3>

                <div class="row">
                  <div class="col-xl-3 col-lg-6 form-group">
                    <label for="funcionario">Funcionario responsable</label>
                    <select class="form-control" id="funcionario" name="funcionario">
                      <option value=""></option>
                      <option value="1">Juan Pérez (Director General de Recursos Hídricos)</option>
                      <option value="2">María García (Gerente de Planificación y Proyectos)</option>
                      <option value="3">Carlos Rodríguez (Coordinador de Operaciones y Mantenimiento)</option>
                      <option value="4">Ana López (Jefa de Control de Calidad)</option>
                      <option value="5">Luis Sánchez (Asesor Técnico)</option>
                      <option value="6">Otro funcionario</option>
                    </select>
                  </div>
                </div>

                <h3 class="pb-3 pt-3">Carga de imágenes:</h3>

                <div class="form-group">
                  <label for="imagenes">Imagenes</label>
                  <input type="text" class="form-control" id="imagenes" name="imagenes" placeholder="Ingrese el nombre del archivo de imagenes o N/A si no aplica">
                </div>

                <div class="row justify-content-center mt-5">
                  <button type="submit" class="col-2 btn btn-primary" name="Guardar">Guardar embalse</button>
                </div>

            </form>

            <!-- fin -->
          </div>
        </div>
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
  </main>

  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/argon-dashboard.min.js?v=2.0.4"></script>
</body>

</html>


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