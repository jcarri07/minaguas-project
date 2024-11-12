<?php
include 'php/Conexion.php';
require_once 'php/batimetria.php';



if (!isset($_SESSION)) {
  session_start();
};
if (!isset($_SESSION["Id_usuario"])) {

  print "<script>window.location='index.php';</script>";
}
$id_embalse = "";
if (isset($_SESSION['id_embalse'])) {
  $id_embalse = $_SESSION['id_embalse'];
  // echo $id_embalse;
}

// var_dump($id_embalse);
$bat_emb = new Batimetria($id_embalse, $conn);
$queryEstados = mysqli_query($conn, "SELECT * FROM estados;");
$queryResponsable = mysqli_query($conn, "SELECT * FROM usuarios WHERE tipo = 'User' OR tipo = 'Admin';");
$queryEmbalse = mysqli_query($conn, "SELECT * FROM embalses WHERE id_embalse = '$id_embalse'");
$queryPropositos = mysqli_query($conn, "SELECT * FROM propositos WHERE estatus = 'activo'");
$queryOperador = mysqli_query($conn, "SELECT * FROM operadores WHERE estatus = 'activo' ORDER BY operador asc");
$queryRegion = mysqli_query($conn, "SELECT * FROM regiones WHERE estatus = 'activo' ORDER BY region asc");
$nombresEmbalses = array_column(mysqli_fetch_all(mysqli_query($conn, "SELECT nombre_embalse FROM embalses WHERE estatus = 'activo' OR estatus = 'inactivo'"), MYSQLI_ASSOC), 'nombre_embalse');



$embalse = mysqli_fetch_assoc($queryEmbalse);
$idE = $embalse['id_estado'];
$idM = $embalse['id_municipio'];
$SeE = $embalse['sectores_estado'];
$SeM = $embalse['sectores_municipio'];
// $queryMunicipio = mysqli_query($conn, "SELECT * FROM municipios WHERE id_estado = $idE");
// $queryParroquia = mysqli_query($conn, "SELECT * FROM parroquias WHERE id_municipio = $idM");
if ($idE == "") {
  $idE = 0;
}
if ($idM == "") {
  $idM = 0;
}
if ($SeE == "") {
  $SeE = 0;
}
if ($SeM == "") {
  $SeM = 0;
}
$queryMunicipio = mysqli_query($conn, "SELECT * FROM municipios WHERE id_estado IN ($idE)");
$queryParroquia = mysqli_query($conn, "SELECT * FROM parroquias WHERE id_municipio IN ($idM)");

$querySectoresMunicipio = mysqli_query($conn, "SELECT * FROM municipios WHERE id_estado IN ($SeE)");
$querySectoresParroquia = mysqli_query($conn, "SELECT * FROM parroquias WHERE id_municipio IN ($SeM)");


$estados = explode(",", $embalse["id_estado"]);
$municipios = explode(",", $embalse["id_municipio"]);
$parroquias = explode(",", $embalse["id_parroquia"]);

$sectores_estados = explode(",", $embalse["sectores_estado"]);
$sectores_municipios = explode(",", $embalse["sectores_municipio"]);
$sectores_parroquias = explode(",", $embalse["sectores_parroquia"]);
// var_dump($sectores_parroquias);


date_default_timezone_set("America/Caracas");

function formatoNumero($valor)
{
  if ($valor === null || $valor === '') {
    return '';
  }

  $partes = explode(',', $valor);
  $parteEntera = $partes[0];
  $parteDecimal = isset($partes[1]) ? $partes[1] : '';

  $parteEntera = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $parteEntera);

  $resultado = $parteEntera . ($parteDecimal !== '' ? ',' . substr(str_pad($parteDecimal, 3, '0'), 0, 3) : ',000');

  return $resultado;
}


function stringFloatttt($num, $dec = 2)
{
  // return number_format(floatval(str_replace(',', '.', $num)), $dec, ',', '.');
  // return floatval(str_replace('.', ',', $num));
  // return $num;
  $numero_limpio = str_replace('.', '', $num);
  $numero_limpio = str_replace(',', '.', $numero_limpio);
  $numero = floatval($numero_limpio);
  return number_format($numero, $dec, ",", ".");
}

function stringFloat($num, $dec = 2)
{
  // return number_format(floatval(str_replace(',', '.', $num)), $dec, ',', '.');
  // return floatval(str_replace('.', ',', $num));
  // return $num;
  // $numero_limpio = str_replace('.', ',', $num);
  // $numero_limpio = str_replace(',', '.', $numero_limpio);
  $numero = floatval($num);
  return number_format($numero, $dec, ",", ".");
}

function explodeBat($value, $i = null)
{
  $value = strval($value);
  $pattern = "/^(-?[\d,.]+)-(-?[\d,.]+)$/";

  if (preg_match($pattern, $value, $matches)) {
    $valores = [$matches[1], $matches[2]]; // Valores capturados

    if ($i !== null) {
      return $valores[$i];
    } else {
      return $valores;
    }
  } else {
    $valores = [0, 0]; // Valores predeterminados en caso de no coincidencia

    if ($i !== null) {
      return $valores[$i];
    } else {
      return $valores;
    }
  }
}
?>

<link rel="stylesheet" href="./assets/css/nice-select2.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.7.5/proj4.js"></script>

<style>
  @media (min-width: 1000px) {
    .p-5-lg {
      padding: 3rem;
    }
  }

  #show-batimetria,
  #show-pre-batimetria {
    /* display: flex; */
    text-align: center;
  }

  #modal-body,
  #modal-pre-body {
    overflow-x: auto;
  }

  #modal-show {
    max-width: 70%;
    display: inline-block;
    overflow-x: auto;
    white-space: nowrap;

  }

  .tabla {
    display: inline-block;
    vertical-align: top;
    white-space: normal;
    margin: 0 25px;
    text-align: center;
  }

  /* Opcional: Para darle estilo a las tablas dentro del modal */
  .table-cota {
    /* width: 100%; */
    border: 1px solid #000000;
  }

  .tabla table {
    width: 100%;
    /* Ancho del 100% para ocupar todo el espacio disponible */
    border-collapse: collapse;
    /* Colapso de bordes para evitar espacios entre las celdas */
  }

  .tabla td,
  .tabla th {
    border: 1px solid #dddddd;
    text-align: center;
    vertical-align: middle;
    padding: 8px;
    /* Ajusta el relleno según sea necesario */
  }

  .no-visible {
    display: none;
  }

  .padre-relative {
    position: relative;
  }

  .modal-absolute {
    position: absolute;
    bottom: 1;
    left: 1;
    margin-top: 8px;
    display: none;
  }

  .desplegar {
    display: block;
    z-index: 100 !important;
  }

  #region,
  #operador,
  #proposito,
  #uso,
  #cap-util,
  #norte,
  #este,
  #huso {
    background: white;
  }

  textarea {
    resize: none;
    overflow: auto;
  }

  #show-pre-bat,
  #change-bat {
    background: lightgray;
    transition-duration: .5s;
    transition-property: background;
  }

  #show-pre-bat:hover,
  #change-bat:hover {
    background: #c4c4c4;
  }

  /* .group-estados,
  .group-municipios,
  .group-parroquias {
    position: relative;
  }

  .label-estados,
  .label-municipios,
  .label-parroquias {
    position: absolute;
    right: 5px;
    bottom: -25px;
    color: gray;
    font-weight: normal;
  } */

  .label-estados,
  .label-municipios,
  .label-parroquias,
  .label-estados-sectores,
  .label-municipios-sectores,
  .label-parroquias-sectores {
    text-align: right;
    color: gray;
    font-weight: normal;
  }

  .label-founded {
    text-align: right;
    color: #ff8f8f;
    font-weight: normal;
  }

  #modal-proposito,
  #modal-uso {
    z-index: 9999;
  }

  #mapa {
    height: 600px;
    width: 80%;
    position: absolute;
    /* top: 0;
    left: 0; */
    z-index: 9999999;
  }

  #map {
    height: 600px;
    width: 100%;
    /* position: absolute; */
    /* top: 50%;
    left: 50%;
    transform: translate(-50%, -50%); */
    z-index: 99999;
  }

  .map-no-visible {
    top: -100%;
    left: -100%;
    z-index: -100 !important;
  }

  .map-visible {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 999999 !important;
  }

  #show-map {
    background: lightgray;
    transition-duration: .5s;
    transition-property: background;
  }

  #close-map {
    position: absolute;
    top: -50px;
    right: 20px;
    /* transform: translate(-50%, -50%); */
    z-index: 99999999;
  }

  #text-map {
    position: absolute;
    top: -55px;
    left: 20px;
    /* transform: translate(-50%, -50%); */
    z-index: 99999999;
    font-size: 32px;
    border-radius: 0.5rem;
    background-color: white;
    padding-left: 3px;
    padding-right: 3px;
    border: 1px solid #c4c4c4;
  }

  #show-map:hover {
    background: #c4c4c4;
  }

  .fade-in-image {
    animation: fadeIn .8s;
  }

  @keyframes fadeIn {
    0% {
      opacity: 0;
    }

    100% {
      opacity: 1;
    }
  }

  .input-error {
    /* background: #ffd6d6; */
    border-color: #ff8f8f;
  }

  .input-error::placeholder {
    color: #fc8383;
  }

  .form-embalse {
    position: relative;
  }

  .boton-stikcy-save {
    position: fixed;
    bottom: 100px;
    left: 50%;
  }

  .founded {
    border-color: #ff8f8f;
    outline: none;
    color: #ff8f8f
  }

  .founded:focus {
    color: red;
    border-color: red;
  }

  #modal-operador,
  #modal-region {
    /* width: 450px; */
    width: auto;
    height: 400px;
    overflow-y: auto;
    padding-left: 10px;
    padding-right: 10px;
  }

  .div-opcion {
    margin-left: 5px;
    margin-right: 5px;
  }

  .div-opcion:hover {
    border-radius: 0.125rem !important;
    /* padding-left: 3px !important; */
    background-color: #e6e6e6 !important;
  }

  .expanded {
    height: 150px;
    /* padding: 10px; */
  }
</style>



<!-- Navbar -->
<!--<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Embalses</a></li>

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
        
        </div>
      </div>
    </nav>-->
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


        <form id="form-embalse" method="POST" action="php/proces_embalse.php" enctype="multipart/form-data">
          <div class="p-5-lg m-5">

            <h3 class="pb-3">Información principal:</h3>

            <div class="row">
              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  <label for="embalse_nombre">Nombre del embalse</label>
                  <input type="text" class="form-control Vrequerido Viguales" id="embalse_nombre" name="embalse_nombre" placeholder="Ingrese el nombre del embalse" value="<?php echo $embalse["nombre_embalse"]; ?>">
                  <label class="label-founded no-visible">Nombre ya registrado.</label>
                </div>
                <div class="form-group">
                  <label for="presa_nombre">Nombre de la presa</label>
                  <input type="text" class="form-control" id="presa_nombre" name="presa_nombre" placeholder="Ingrese el nombre de la presa" value="<?php echo $embalse["nombre_presa"]; ?>">
                </div>
                <div class="form-group">
                  <label for="responsable">Responsable de la carga de datos</label>
                  <select class="wide" id="responsable" name="responsable">
                    <option value="">Sin encargado</option>
                    <?php
                    while ($row1 = mysqli_fetch_array($queryResponsable)) {
                    ?>
                      <option <?php if ($row1['Id_usuario'] == $embalse['id_encargado']) echo "selected"; ?> value="<?php echo $row1['Id_usuario']; ?>"><?php echo $row1['P_Nombre'] . " " . $row1['S_Nombre'] . " " . $row1['P_Apellido'] . " " . $row1['S_Apellido'] . " - (" . $row1['Tipo'] . ")"; ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
              </div>

              <div class="col-md-4 col-sm-12">
                <div class=" form-group">
                  <label for="norte">Norte</label>
                  <div class="input-group">
                    <!-- echo number_format(floatval($embalse["norte"]), 2, ',', '.'); -->
                    <input value="<?php echo $embalse["norte"] ?>" type="text" class="form-control numero" id="norte" name="norte" placeholder="Norte">
                    <span id="show-map" class="input-group-text show-map cursor-pointer text-bold px-3"><i class="fas fa-map-marker-alt text-sm"></i></span>
                  </div>
                </div>
                <div class=" form-group">
                  <label for="este">Este</label>
                  <input value="<?php echo $embalse["este"]; ?>" type="text" class="form-control numero" id="este" name="este" placeholder="Este">
                </div>
                <div class=" form-group">
                  <label for="huso">Huso</label>
                  <input value="<?php echo $embalse["huso"]; ?>" type="text" class="form-control numero" id="huso" name="huso" placeholder="Huso">
                </div>
              </div>

              <div class="d-flex flex-column justify-content-between col-md-4 col-sm-12">
                <div class="form-group group-estados d-flex flex-column">
                  <label for="estado">Estado</label>
                  <select multiple class="border wide" id="estado" name="estado[]">
                    <!-- <option value=""></option> -->
                    <?php
                    $cadena = [];
                    while ($row = mysqli_fetch_array($queryEstados)) {
                    ?>
                      <option <?php if (in_array($row["id_estado"], $estados)) {
                                echo "selected";
                                array_push($cadena, $row["estado"]);
                              } ?> value="<?php echo $row['id_estado']; ?>"><?php echo $row['estado']; ?> </option>
                    <?php
                    }
                    ?>
                  </select>
                  <label class="label-estados"><?php echo implode(", ", $cadena) ?></label>
                </div>
                <div class="form-group group-municipios d-flex flex-column">
                  <label for="municipio">Municipio</label>
                  <select multiple class="border wide" id="municipio" name="municipio[]">
                    <!-- <option value=""></option> -->
                    <?php
                    $cadena = [];
                    while ($row = mysqli_fetch_array($queryMunicipio)) {
                    ?>
                      <option <?php if (in_array($row["id_municipio"], $municipios)) {
                                echo "selected";
                                array_push($cadena, $row["municipio"]);
                              } ?> value="<?php echo $row['id_municipio']; ?>"><?php echo $row['municipio']; ?> </option>
                    <?php
                    }
                    ?>
                  </select>
                  <label class="label-municipios"><?php echo implode(", ", $cadena) ?></label>
                </div>
                <div class="form-group group-parroquias d-flex flex-column">
                  <label for="parroquia">Parroquia</label>
                  <select multiple class="border wide" id="parroquia" name="parroquia[]">
                    <!-- <option value=""></option> -->
                    <?php
                    $cadena = [];
                    while ($row = mysqli_fetch_array($queryParroquia)) {
                    ?>
                      <option <?php if (in_array($row['id_parroquia'], $parroquias)) {
                                echo "selected";
                                array_push($cadena, $row["parroquia"]);
                              } ?> value="<?php echo $row['id_parroquia']; ?>"><?php echo $row['parroquia']; ?> </option>
                    <?php
                    }
                    ?>
                  </select>
                  <label class="label-parroquias"><?php echo implode(", ", $cadena) ?></label>
                </div>
              </div>
            </div>

            <h3 class="pb-3 pt-3">Características de diseño de los embalses:</h3>

            <div class="row">
              <div style="display:flex; flex-direction:column;" class="col-md-3 col-sm-12 justify-content-between">
                <?php if ($embalse["batimetria"] != "") { ?>
                  <div class="form-group">
                    <label for="batimetria">Batimetría</label>
                    <div id="pre-bat" class="input-group">
                      <input type="text" class="form-control" id="" name="" placeholder="" value="<?php echo $embalse["nombre_embalse"] ?> :[ <?php echo implode(" - ", $bat_emb->getYears()) ?> ]"> <!--el que muestra la batimetria de la base de datos. -->
                      <span onclick="$('#show-pre-batimetria').modal('show');" id="show-pre-bat" class="input-group-text  cursor-pointer text-bold"><i class="fas fa-eye text-sm "></i></span>
                      <span id="change-bat" class="input-group-text  cursor-pointer text-bold"><i class="fas fa-trash text-sm me-1"></i></span>
                    </div>
                    <input hidden type="file" accept=".xlsx, .xls" class="form-control" id="batimetria" name="batimetria" placeholder=""> <!--el normal, el que carga la batimetria nueva. -->
                    <input style="display: none;" value="precargada" type="text" class="form-control" id="batimetria-pre" name="pre_batimetria" placeholder=""> <!--guarda si hubo precargado o se elimina. -->
                  </div>
                  <div class="form-group d-flex justify-content-center">
                    <a class="down-bat btn text-dark text-sm d-flex align-items-center "><i class="fa fa-download text-lg me-1"></i> Plant.</a>
                    <div class="down-excel" data-id="<?php echo $embalse["id_embalse"]; ?>"><a class=" btn  text-dark text-sm d-flex align-items-center" data-id="<?php echo $embalse["id_embalse"]; ?>"><i class="fas fa-file-export text-lg me-1"></i> Exp.</a></div>
                    <div class="show-bat no-visible"><a onclick="$('#show-batimetria').modal('show');" class="d-flex align-items-center btn  text-dark text-sm"><i class="fas fa-eye text-lg me-1"></i> Ver</a></div>
                    <div hidden class="change-redo" id="change-redo"><a class=" btn text-dark text-sm d-flex align-items-center" data-id=""><i class="fas fa-redo text-lg me-1"></i>.</a></div>
                  </div>
                <?php } else { ?>
                  <div class="form-group">
                    <label for="batimetria">Batimetría</label>
                    <input type="file" accept=".xlsx, .xls" class="form-control" id="batimetria" name="batimetria" placeholder="Ingrese el tipo de batimetria">
                  </div>
                  <div class="form-group d-flex justify-content-center">
                    <a class="down-bat visible btn text-dark text-sm d-flex align-items-center"><i class="fa fa-download text-lg me-1"></i> Plantilla</a>
                    <div class="show-bat no-visible"><a onclick="$('#show-batimetria').modal('show');" class="d-flex align-items-center btn text-dark text-sm"><i class="fas fa-eye text-lg me-1"></i> Ver</a></div>
                  </div>
                <?php } ?>

                <div class="form-group">
                  <label for="vida_util">Vida útil (años)</label>
                  <input value="<?php echo $embalse["vida_util"]; ?>" type="text" class="form-control" id="vida_util" name="vida_util" placeholder="Ingrese la vida útil en años">
                </div>
              </div>

              <!--  -->
              <div class="col-md-3 col-sm-12">
                <div class=" form-group">
                  <label for="cota_min">Cota mínima (m s.m.n.)</label>
                  <input value="<?php echo stringFloat($embalse["cota_min"]) ?>" type="text" class="form-control Vnumero" id="cota_min" name="cota_min" placeholder="Ingrese la cota mínima">
                </div>
                <div class=" form-group">
                  <label for="vol_min">Volumen mínimo (Hm³)</label>
                  <input value="<?php echo stringFloat($embalse["vol_min"]) ?>" type="text" class="form-control Vnumero" id="vol_min" name="vol_min" placeholder="Ingrese el volumen mínimo">
                </div>
                <div class=" form-group">
                  <label for="sup_min">Superficie mínima (ha)</label>
                  <input value="<?php echo stringFloat($embalse["sup_min"]) ?>" type="text" class="form-control Vnumero" id="sup_min" name="sup_min" placeholder="Ingrese la superficie mínima">
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class=" form-group">
                  <label for="cota_nor">Cota normal (m s.m.n.)</label>
                  <input value="<?php echo stringFloat($embalse["cota_nor"]) ?>" type="text" class="form-control Vnumero" id="cota_nor" name="cota_nor" placeholder="Ingrese la cota normal">
                </div>
                <div class=" form-group">
                  <label for="vol_nor">Volumen normal (Hm³)</label>
                  <input value="<?php echo stringFloat($embalse["vol_nor"]) ?>" type="text" class="form-control Vnumero" id="vol_nor" name="vol_nor" placeholder="Ingrese el volumen normal">
                </div>
                <div class=" form-group">
                  <label for="sup_nor">Superficie normal (ha)</label>
                  <input value="<?php echo stringFloat($embalse["sup_nor"]) ?>" type="text" class="form-control Vnumero" id="sup_nor" name="sup_nor" placeholder="Ingrese la superficie normal">
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class=" form-group">
                  <label for="cota_max">Cota máxima (m s.m.n.)</label>
                  <input value="<?php echo stringFloat($embalse["cota_max"]) ?>" type="text" class="form-control Vnumero" id="cota_max" name="cota_max" placeholder="Ingrese la cota máxima">
                </div>
                <div class=" form-group">
                  <label for="vol_max">Volumen máximo (Hm³)</label>
                  <input value="<?php echo stringFloat($embalse["vol_max"]) ?>" type="text" class="form-control Vnumero" id="vol_max" name="vol_max" placeholder="Ingrese el volumen máximo">
                </div>
                <div class=" form-group">
                  <label for="sup_max">Superficie máxima (ha)</label>
                  <input value="<?php echo stringFloat($embalse["sup_max"]) ?>" type="text" class="form-control Vnumero" id="sup_max" name="sup_max" placeholder="Ingrese la superficie máxima">
                </div>
              </div>
              <!--  -->
            </div>

            <?php
            $op = "";
            while ($operador = mysqli_fetch_array($queryOperador)) {
              if ($operador['id_operador'] == $embalse["operador"]) {
                $op = $operador["operador"];
                break;
              }
            }
            $queryOperador->data_seek(0);

            $reg = "";
            while ($region = mysqli_fetch_array($queryRegion)) {
              if ($region['id_region'] == $embalse["region"]) {
                $reg = $region["region"];
                break;
              }
            }
            $queryRegion->data_seek(0);
            ?>
            <div class="row justify-content-center">
              <div class="col-xl-3 col-lg-6 form-group padre-relative">
                <label for="operador">Operador</label>
                <textarea readonly class="form-control Vrequerido" name="" id="operador" cols="30" rows="1" placeholder="Operador"><?php echo $op; ?></textarea>
                <input readonly hidden type="text" class="form-control" id="operador-input" name="operador" placeholder="" value="<?php echo $embalse["operador"]; ?>">
                <div id="modal-operador" class="bg-gray-200 rounded p-3 modal-absolute" style="width: 75%;">

                  <?php
                  while ($operador = mysqli_fetch_array($queryOperador)) {
                  ?>
                    <div class="form-check opcion div-opcion">
                      <!-- <input type="radio" name="" id="<?php //echo $operador['id_proposito'] 
                                                            ?>-prop" class="prop-opcion form-check-input opcion"> -->
                      <input <?php if ($operador['id_operador'] == $embalse["operador"]) echo "checked" ?> id="<?php echo $operador['id_operador'] ?>-ope" type="radio" value="" name="ope-radio" class="ope-opcion form-check-input opcion">
                      <label for="<?php echo $operador['id_operador'] ?>-ope" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 opcion-<?php echo $operador['id_operador'] ?>-ope opcion"><?php echo $operador['operador'] ?></label>
                    </div>
                  <?php
                  }
                  ?>

                </div>
              </div>

              <div class="col-xl-3 col-lg-6 form-group padre-relative">
                <label for="region">Región</label>
                <textarea readonly class="form-control Vrequerido" name="" id="region" cols="30" rows="1" placeholder="region"><?php echo $reg; ?></textarea>
                <input readonly hidden type="text" class="form-control" id="region-input" name="region" placeholder="" value="<?php echo $embalse["region"]; ?>">
                <div id="modal-region" class="bg-gray-200 rounded p-3 modal-absolute" style="width: 75%;">

                  <?php
                  while ($region = mysqli_fetch_array($queryRegion)) {
                  ?>
                    <div class="form-check opcion div-opcion">
                      <!-- <input type="radio" name="" id="<?php //echo $region['id_proposito'] 
                                                            ?>-prop" class="prop-opcion form-check-input opcion"> -->
                      <input <?php if ($region['id_region'] == $embalse["region"]) echo "checked" ?> id="<?php echo $region['id_region'] ?>-reg" type="radio" value="" name="reg-radio" class="reg-opcion form-check-input opcion">
                      <label for="<?php echo $region['id_region'] ?>-reg" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 opcion-<?php echo $region['id_region'] ?>-reg opcion"><?php echo $region['region'] ?></label>
                    </div>
                  <?php
                  }
                  ?>

                </div>
              </div>

              <div class="col-md-3 col-sm-12">
                <div class=" form-group">
                  <label for="cap-util">Capacidad útil (Hm³)</label>
                  <input readonly type="text" class="form-control" id="cap-util" value="0">
                </div>
              </div>
            </div>

            <h3 class="pb-3 pt-5">Información de la cuenca:</h3>

            <div class="row">
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="cuenca">Cuenca principal</label>
                <input value="<?php echo $embalse["cuenca_principal"]; ?>" type="text" class="form-control" id="cuenca" name="cuenca" placeholder="Ingrese la cuenca principal">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="afluentes">Afluentes principales</label>
                <input value="<?php echo $embalse["afluentes_principales"]; ?>" type="text" class="form-control" id="afluentes" name="afluentes" placeholder="Ingrese los afluentes principales">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="area">Área de la cuenca (ha)</label>
                <input value="<?php echo $embalse["area_cuenca"]; ?>" type="text" class="form-control" id="area" name="area" placeholder="Ingrese el area de la cuenca">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="escurrimiento">Escurrimiento medio (Hm³)</label>
                <input value="<?php echo $embalse["escurrimiento_medio"]; ?>" type="text" class="form-control" id="escurrimiento" name="escurrimiento" placeholder="Ingrese el escurrimiento medio">
              </div>
            </div>

            <h3 class="pb-3 pt-3">Información de los embalses:</h3>

            <div class="row">
              <div class="col-md-6 col-sm-12 form-group">
                <label for="ubicacion_embalse">Ubicación del embalse</label>
                <textarea class="form-control" id="ubicacion_embalse" name="ubicacion_embalse" rows="5" placeholder="Ingrese la ubicacion del embalse"><?php echo $embalse["ubicacion_embalse"]; ?></textarea>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="form-group">
                  <label for="organo">Órgano rector</label>
                  <input value="<?php echo $embalse["organo_rector"]; ?>" type="text" class="form-control" id="organo" name="organo" placeholder="Ingrese el organo rector">
                </div>
                <div class="form-group">
                  <label for="personal">Personal encargado a nivel central</label>
                  <input value="<?php echo $embalse["personal_encargado"]; ?>" type="text" class="form-control" id="personal" name="personal" placeholder="Personal encargado a nivel central">
                </div>
              </div>
            </div>

            <div class="row">
              <!-- <div class="col-xl-3 col-lg-6 form-group">
                <label for="operador">Operador</label>
                <input value="<?php //echo $embalse["operador"]; 
                              ?>" type="text" class="form-control Vrequerido" id="operadorrr" name="operadorrr" placeholder="Ingrese el operador">
              </div> -->
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="autoridad">Autoridad responsable del embalse</label>
                <input value="<?php echo $embalse["autoridad_responsable"]; ?>" type="text" class="form-control" id="autoridad" name="autoridad" placeholder="Autoridad responsable del embalse">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="proyectista">Proyectista</label>
                <input value="<?php echo $embalse["proyectista"]; ?>" type="text" class="form-control" id="proyectista" name="proyectista" placeholder="Ingrese el proyectista">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="constructor">Constructor</label>
                <input value="<?php echo $embalse["constructor"]; ?>" type="text" class="form-control" id="constructor" name="constructor" placeholder="Ingrese el constructor">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="inicio_construccion">Año de inicio de construcción</label>
                <input value="<?php echo $embalse["inicio_construccion"]; ?>" type="text" class="form-control" id="inicio_construccion" name="inicio_construccion" placeholder="Ingrese el año de inicio de construcción">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="duracion_construccion">Duración de construcción (años)</label>
                <input value="<?php echo $embalse["duracion_de_construccion"]; ?>" type="text" class="form-control" id="duracion_construccion" name="duracion_construccion" placeholder="Ingrese la duración de construcción en años">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="inicio_operacion">Inicio de operación (año)</label>
                <input value="<?php echo $embalse["inicio_de_operacion"]; ?>" type="text" class="form-control" id="inicio_operacion" name="inicio_operacion" placeholder="Ingrese el año de inicio de operacion">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="monitoreo">Monitoreo de niveles del embalse</label>
                <input value="<?php echo $embalse["monitoreo_del_embalse"]; ?>" type="text" class="form-control" id="monitoreo" name="monitoreo" placeholder="Ingrese el tipo de monitoreo del embalse">
              </div>
            </div>

            <h3 class="pb-3 pt-3">Presa:</h3>

            <div class="row">
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="numero_presas">Número de presas</label>
                <input value="<?php echo $embalse["numero_de_presas"]; ?>" type="text" class="form-control" id="numero_presas" name="numero_presas" placeholder="Ingrese el número de presas">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="tipo_presa">Tipo de presa</label>
                <input value="<?php echo $embalse["tipo_de_presa"]; ?>" type="text" class="form-control" id="tipo_presa" name="tipo_presa" placeholder="Ingrese el tipo de presa">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="altura">Altura (m)</label>
                <input value="<?php echo $embalse["altura"]; ?>" type="text" class="form-control" id="altura" name="altura" placeholder="Ingrese la altura en metros">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="talud_arriba">Talud aguas arriba</label>
                <input value="<?php echo $embalse["talud_aguas_arriba"]; ?>" type="text" class="form-control" id="talud_arriba" name="talud_arriba" placeholder="Ingrese el talud aguas arriba">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="talud_abajo">Talud aguas abajo</label>
                <input value="<?php echo $embalse["talud_aguas_abajo"]; ?>" type="text" class="form-control" id="talud_abajo" name="talud_abajo" placeholder="Ingrese el talud aguas abajo">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="longitud_cresta">Longitud de la cresta (m)</label>
                <input value="<?php echo $embalse["longitud_cresta"]; ?>" type="text" class="form-control" id="longitud_cresta" name="longitud_cresta" placeholder="Ingrese la longitud de la cresta en metros">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="cota_cresta">Cota de la cresta (m s.m.n.)</label>
                <input value="<?php echo $embalse["cota_cresta"]; ?>" type="text" class="form-control" id="cota_cresta" name="cota_cresta" placeholder="Ingrese la cota de la cresta en metros">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="ancho_cresta">Ancho de la cresta (m)</label>
                <input value="<?php echo $embalse["ancho_cresta"]; ?>" type="text" class="form-control" id="ancho_cresta" name="ancho_cresta" placeholder="Ingrese el ancho de la cresta en metros">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="volumen_terraplen">Volumen del terraplen (m³)</label>
                <input value="<?php echo $embalse["volumen_terraplen"]; ?>" type="text" class="form-control" id="volumen_terraplen" name="volumen_terraplen" placeholder="Ingrese el volumen del terraplen">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="ancho_base">Ancho máximo de base (m)</label>
                <input value="<?php echo $embalse["ancho_base"]; ?>" type="text" class="form-control" id="ancho_base" name="ancho_base" placeholder="Ingrese el ancho máximo de base en metros">
              </div>
            </div>

            <h3 class="pb-3 pt-3">Aliviadero:</h3>

            <div class="row">
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="ubicacion_aliviadero">Ubicación del aliviadero</label>
                <input value="<?php echo $embalse["ubicacion_aliviadero"]; ?>" type="text" class="form-control" id="ubicacion_aliviadero" name="ubicacion_aliviadero" placeholder="Ingrese la ubicación del aliviadero">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="tipo_aliviadero">Tipo de aliviadero</label>
                <input value="<?php echo $embalse["tipo_aliviadero"]; ?>" type="text" class="form-control" id="tipo_aliviadero" name="tipo_aliviadero" placeholder="Ingrese el tipo de aliviadero">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="numero_compuertas_aliviadero">Número de compuertas del aliviadero</label>
                <input value="<?php echo $embalse["numero_compuertas_aliviadero"]; ?>" type="text" class="form-control" id="numero_compuertas_aliviadero" name="numero_compuertas_aliviadero" placeholder="Ingrese el número de compuertas del aliviadero">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="carga_aliviadero">Carga sobre el vertedero (m)</label>
                <input value="<?php echo $embalse["carga_vertedero"]; ?>" type="text" class="form-control" id="carga_aliviadero" name="carga_aliviadero" placeholder="Ingrese la carga sobre el vertedero">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="descarga_aliviadero">Descarga máxima (m³/s)</label>
                <input value="<?php echo $embalse["descarga_maxima"]; ?>" type="text" class="form-control" id="descarga_aliviadero" name="descarga_aliviadero" placeholder="Ingrese la descarga máxima">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="longitud_aliviadero">Longitud (m)</label>
                <input value="<?php echo $embalse["longitud_aliviadero"]; ?>" type="text" class="form-control" id="longitud_aliviadero" name="longitud_aliviadero" placeholder="Ingrese la longitud en metros">
              </div>
            </div>

            <h3 class="pb-3 pt-3">Obra de toma:</h3>

            <div class="row">
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="ubicacion_toma">Ubicación de la obra de toma</label>
                <input value="<?php echo $embalse["ubicacion_toma"]; ?>" type="text" class="form-control" id="ubicacion_toma" name="ubicacion_toma" placeholder="Ingrese la ubicación de la obra de toma">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="tipo_toma">Tipo de obra de toma</label>
                <input value="<?php echo $embalse["tipo_toma"]; ?>" type="text" class="form-control" id="tipo_toma" name="tipo_toma" placeholder="Ingrese el tipo de obra de toma">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="numero_compuertas_toma">Número de compuertas de la obra de toma</label>
                <input value="<?php echo $embalse["numero_compuertas_toma"]; ?>" type="text" class="form-control" id="numero_compuertas_toma" name="numero_compuertas_toma" placeholder="Ingrese el número de compuertas de la obra de toma">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="emergencia_toma">Mecanismos de emergencia de la obra de toma</label>
                <input value="<?php echo $embalse["mecanismos_de_emergencia"]; ?>" type="text" class="form-control" id="emergencia_toma" name="emergencia_toma" placeholder="Ingrese los mecanismos de emergencia de la obra de toma">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="regulacion_toma">Mecanismos de regulación de la obra de toma</label>
                <input value="<?php echo $embalse["mecanismos_de_regulacion"]; ?>" type="text" class="form-control" id="regulacion_toma" name="regulacion_toma" placeholder="Ingrese los mecanismos de regulación de la obra de toma">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="gasto_toma">Gasto máximo de la obra de toma (m³/s)</label>
                <input value="<?php echo $embalse["gasto_maximo"]; ?>" type="text" class="form-control" id="gasto_toma" name="gasto_toma" placeholder="Ingrese el gasto máximo de la obra de toma">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="descarga_fondo">Descarga de fondo</label>
                <input value="<?php echo $embalse["descarga_de_fondo"]; ?>" type="text" class="form-control" id="descarga_fondo" name="descarga_fondo" placeholder="Ingrese la descarga de fondo o N/A si no aplica">
              </div>
            </div>

            <h3 class="pb-3 pt-3">Obra hidráulica:</h3>

            <div class="row">
              <div class="col-xl-4 col-lg-6 form-group">
                <label for="obra_conduccion">Posee obra</label>
                <input value="<?php echo $embalse["posee_obra"]; ?>" type="text" class="form-control" id="obra_conduccion" name="obra_conduccion" placeholder="Ingrese SI o NO si posee obra">
              </div>
              <div class="col-xl-4 col-lg-6 form-group">
                <label for="tipo_conduccion">Tipo de obra</label>
                <input value="<?php echo $embalse["tipo_de_obra"]; ?>" type="text" class="form-control" id="tipo_conduccion" name="tipo_conduccion" placeholder="Ingrese el tipo de obra">
              </div>
              <div class="col-xl-4 col-lg-6 form-group">
                <label for="accion_conduccion">Acción requerida de la obra</label>
                <input value="<?php echo $embalse["accion_requerida"]; ?>" type="text" class="form-control" id="accion_conduccion" name="accion_conduccion" placeholder="Ingrese la acción requerida">
              </div>
            </div>

            <h3 class="pb-3 pt-3">Beneficios:</h3>

            <div class="row">
              <div class="col-xl-3 col-lg-6 form-group padre-relative">
                <label for="proposito">Propósito del embalse</label>
                <textarea readonly class="form-control" name="" id="proposito" cols="30" rows="2" placeholder="Seleccione los propósitos del embalse"></textarea>
                <input readonly hidden type="text" class="form-control" id="proposito-input" name="proposito" placeholder="Seleccione los propósitos del embalse" value="<?php echo $embalse["proposito"]; ?>">
                <div id="modal-proposito" class="bg-gray-200 rounded p-3 modal-absolute" style="width: 75%;">

                  <?php
                  $propositos_check = explode("-", $embalse["proposito"]);
                  while ($proposito = mysqli_fetch_array($queryPropositos)) {
                  ?>
                    <div class="form-check opcion-prop"><input <?php if (in_array($proposito['id_proposito'], $propositos_check)) echo "checked" ?> type="checkbox" name="" id="<?php echo $proposito['id_proposito'] ?>-prop" class="prop-opcion form-check-input opcion-prop"><label class="text-sm cursor-pointer opcion-<?php echo $proposito['id_proposito'] ?>-prop opcion-prop" for="<?php echo $proposito['id_proposito'] ?>-prop"><?php echo $proposito['proposito'] ?></label></div>
                  <?php
                  }
                  $queryPropositos->data_seek(0);
                  ?>

                </div>
              </div>
              <div class="col-xl-3 col-lg-6 form-group padre-relative">
                <label for="uso">Uso actual del embalse</label>
                <textarea readonly class="form-control" name="" id="uso" cols="30" rows="2" placeholder="Seleccione los usos del embalse"></textarea>
                <input readonly hidden type="text" class="form-control" id="uso-input" name="uso" placeholder="Seleccione los usos actuales del embalse" value="<?php echo $embalse["uso_actual"]; ?>">
                <div id="modal-uso" class="bg-gray-200 rounded p-3 modal-absolute" style="width: 75%;">

                  <?php
                  $usos_check = explode("-", $embalse["uso_actual"]);
                  while ($proposito = mysqli_fetch_array($queryPropositos)) {
                  ?>
                    <div class="form-check opcion-uso"><input <?php if (in_array($proposito['id_proposito'], $usos_check)) echo "checked" ?> type="checkbox" name="" id="<?php echo $proposito['id_proposito'] ?>-uso" class="prop-uso form-check-input opcion-uso"><label class="text-sm cursor-pointer opcion-<?php echo $proposito['id_proposito'] ?>-uso opcion-uso" for="<?php echo $proposito['id_proposito'] ?>-uso"><?php echo $proposito['proposito'] ?></label></div>
                  <?php
                  }
                  ?>
                </div>
              </div>

              <div class="col-xl-3 col-lg-6 form-group">
                <label for="poblacion">Población beneficiada (hab.)</label>
                <input value="<?php echo $embalse["poblacion_beneficiada"]; ?>" type="text" class="form-control" id="poblacion" name="poblacion" placeholder="Ingrese la población beneficiada en habitantes">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="area_riego">Área de riego beneficiada (ha)</label>
                <input value="<?php echo $embalse["area_de_riego_beneficiada"]; ?>" type="text" class="form-control" id="area_riego" name="area_riego" placeholder="Ingrese el area de riego beneficiada">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="area_riego">Área protegida (ha)</label>
                <input value="<?php echo $embalse["area_protegida"]; ?>" type="text" class="form-control" id="area_protegida" name="area_protegida" placeholder="Ingrese el area pretegida">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="area_riego">Población protegida (hab.)</label>
                <input value="<?php echo $embalse["poblacion_protegida"]; ?>" type="text" class="form-control" id="poblacion_prote" name="poblacion_prote" placeholder="Ingrese la población protegida">
              </div>
              <div class="col-xl-3 col-lg-6 form-group">
                <label for="area_riego">Producción hidroeléctrica (MW)</label>
                <input value="<?php echo $embalse["produccion_hidro"]; ?>" type="text" class="form-control" id="produccion_hidro" name="produccion_hidro" placeholder="Ingrese la producción hifroelectrica">
              </div>
              <div class="col-xl-9 col-lg-6 form-group">
                <label class="" for="">Sectores beneficiados</label>
                <div class="row">
                  <div class="col-xl-4 col-md-6 d-flex flex-column" id="sectoresEstados">
                    <select multiple class="border sectores-select" id="SectoresEstado" name="sectoresEstado[]">
                      <?php
                      $queryEstados->data_seek(0);
                      $cadena = [];
                      while ($row = mysqli_fetch_array($queryEstados)) {
                      ?>
                        <option <?php if (in_array($row["id_estado"], $sectores_estados)) {
                                  echo "selected";
                                  array_push($cadena, $row["estado"]);
                                } ?> value="<?php echo $row['id_estado']; ?>"><?php echo $row['estado']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>
                    <label class="label-estados-sectores ml-auto"><?php echo implode(", ", $cadena) ?></label>
                  </div>
                  <div class="col-xl-4 col-md-6 d-flex flex-column" id="sectoresMunicipios">

                    <select multiple class="border sectores-select" id="SectoresMunicipio" name="sectoresMunicipio[]">
                      <?php
                      $cadena = [];
                      while ($row = mysqli_fetch_array($querySectoresMunicipio)) {
                        var_dump(in_array($row["id_municipio"], $sectores_municipios));
                      ?>
                        <option <?php if (in_array($row["id_municipio"], $sectores_municipios)) {
                                  echo "selected";
                                  array_push($cadena, $row["municipio"]);
                                } ?> value="<?php echo $row['id_municipio']; ?>"><?php echo $row['municipio']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>
                    <label class="label-municipios-sectores"><?php echo implode(", ", $cadena) ?></label>
                  </div>
                  <div class="col-xl-4 col-md-6 d-flex flex-column" id="sectoresParroquias">

                    <select multiple class="border sectores-select" id="SectoresParroquia" name="sectoresParroquia[]">
                      <?php
                      $cadena = [];
                      while ($row = mysqli_fetch_array($querySectoresParroquia)) {
                        var_dump(in_array($row['id_parroquia'], $sectores_parroquias));
                      ?>
                        <option <?php if (in_array($row['id_parroquia'], $sectores_parroquias)) {
                                  echo "selected";
                                  array_push($cadena, $row["parroquia"]);
                                } ?> value="<?php echo $row['id_parroquia']; ?>"><?php echo $row['parroquia']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>
                    <label class="label-parroquias-sectores"><?php echo implode(", ", $cadena) ?></label>
                  </div>
                </div>
              </div>
            </div>

            <h3 class="pb-3 pt-3">Responsable:</h3>

            <div class="row">
              <div class="col-xl-4 col-lg-6 form-group">
                <label for="f_cargo">Cargo</label>
                <input value="<?php echo $embalse["f_cargo"]; ?>" type="text" class="form-control" id="f_cargo" name="f_cargo" placeholder="Ingrese el cargo">
              </div>
              <div class="col-xl-4 col-lg-6 form-group">
                <label for="f_cedula">Cédula</label>
                <input value="<?php echo $embalse["f_cedula"]; ?>" type="text" class="form-control" id="f_cedula" name="f_cedula" placeholder="Ingrese la cédula">
              </div>
              <div class="col-xl-4 col-lg-6 form-group">
                <label for="f_nombres">Nombres</label>
                <input value="<?php echo $embalse["f_nombres"]; ?>" type="text" class="form-control" id="f_nombres" name="f_nombres" placeholder="Ingrese los nombres">
              </div>
              <div class="col-xl-4 col-lg-6 form-group">
                <label for="f_apellidos">Apellidos</label>
                <input value="<?php echo $embalse["f_apellidos"]; ?>" type="text" class="form-control" id="f_apellidos" name="f_apellidos" placeholder="Ingrese los apellidos">
              </div>
              <div class="col-xl-4 col-lg-6 form-group">
                <label for="f_telefono">Teléfono</label>
                <input value="<?php echo $embalse["f_telefono"]; ?>" type="text" class="form-control" id="f_telefono" name="f_telefono" placeholder="Ingrese el teléfono">
              </div>
              <div class="col-xl-4 col-lg-6 form-group">
                <label for="f_correo">Correo</label>
                <input value="<?php echo $embalse["f_correo"]; ?>" type="text" class="form-control" id="f_correo" name="f_correo" placeholder="Ingrese el correo">
              </div>
            </div>

            <h3 class="pb-3 pt-3">Carga de imágenes:</h3>

            <?php
            $url_uno = ($embalse["imagen_uno"] != null && $embalse["imagen_uno"] != "") ? "./pages/reports_images/" . $embalse["imagen_uno"] : "./assets/img/default-img.png";
            $url_dos = ($embalse["imagen_dos"] != null && $embalse["imagen_dos"] != "") ? "./pages/reports_images/" . $embalse["imagen_dos"] : "./assets/img/default-img.png";
            $url_tres = ($embalse["imagen_tres"] != null && $embalse["imagen_tres"] != "") ? "./pages/reports_images/" . $embalse["imagen_tres"] : "./assets/img/default-img.png";
            ?>

            <input style="display: none;" value="<?php echo $embalse["imagen_uno"] ?>" type="text" class="form-control" id="imagen_uno-pre" name="pre_imagen_uno" placeholder="">
            <input style="display: none;" value="<?php echo $embalse["imagen_dos"] ?>" type="text" class="form-control" id="imagen_dos-pre" name="pre_imagen_dos" placeholder="">
            <input style="display: none;" value="<?php echo $embalse["imagen_tres"] ?>" type="text" class="form-control" id="imagen_tres-pre" name="pre_imagen_tres" placeholder="">


            <div class="row">
              <div class="col-xl-4 col-md-6 col-lg-12 form-group">
                <label style="width: 100%;" for="imagen_uno">Ubicación relativa Estado/Municipio/Región hidrográfica<br>
                  <div style="width:100%; display:flex; justify-content:center;">
                    <div style="height: 250px; width:300px;" class="my-3"><img src="<?php echo $url_uno ?>" id="imagen_uno-preview" alt="" style="object-fit: cover;" width="100%" height="100%"></div>
                  </div>
                  <div style="display: flex; justify-content:center;">
                    <span class="mx-2"><a class="btn btn-primary">Subir archivo</a></span> <span><a id="imagen_uno-remove" class="btn btn-primary"><i class="fas fa-backspace text-lg me-1"></i></a></span>
                  </div>
                </label>
                <input style="display: none;" type="file" accept="image/png,image/jpeg" class="form-control" id="imagen_uno" name="imagen_uno" placeholder="Ingrese el nombre del archivo de imagenes o N/A si no aplica">
              </div>
              <div class="col-xl-4 col-md-6 col-lg-12 form-group">
                <label style="width: 100%;" for="imagen_dos">Ubicación relativa de los componentes del embalse<br>
                  <div style="width:100%; display:flex; justify-content:center;">
                    <div style="height: 250px; width:300px;" class="my-3"><img src="<?php echo $url_dos ?>" id="imagen_dos-preview" alt="" style="object-fit: cover;" width="100%" height="100%"></div>
                  </div>
                  <div style="display: flex; justify-content:center;">
                    <span class="mx-2"><a class="btn btn-primary">Subir archivo</a></span> <span><a id="imagen_dos-remove" class="btn btn-primary"><i class="fas fa-backspace text-lg me-1"></i></a></span>
                  </div>
                </label>
                <input style="display: none;" type="file" accept="image/png,image/jpeg" class="form-control" id="imagen_dos" name="imagen_dos" placeholder="Ingrese el nombre del archivo de imagenes o N/A si no aplica">
              </div>
              <div class="col-xl-4 col-md-6 col-lg-12 form-group">
                <label style="width: 100%;" for="imagen_tres">Ubicación relativa de los componentes del embalse<br>
                  <div style="width:100%; display:flex; justify-content:center;">
                    <div style="height: 250px; width:300px;" class="my-3"><img src="<?php echo $url_tres ?>" id="imagen_tres-preview" alt="" style="object-fit: cover;" width="100%" height="100%"></div>
                  </div>
                  <div style="display: flex; justify-content:center;">
                    <span class="mx-2"><a class="btn btn-primary">Subir archivo</a></span> <span><a id="imagen_tres-remove" class="btn btn-primary"><i class="fas fa-backspace text-lg me-1"></i></a></span>
                  </div>
                </label>
                <input style="display: none;" type="file" accept="image/png,image/jpeg" class="form-control" id="imagen_tres" name="imagen_tres" placeholder="Ingrese el nombre del archivo de imagenes o N/A si no aplica">
              </div>
            </div>


            <!-- <h3 class="pb-3 pt-3">Responsable del embalse:</h3>

            <div class="row">
              <div class="form-group col-xl-6 col-lg-12">
                <label for="responsable">Responsable</label>
                <select class="form-select" id="responsable" name="responsable">
                  <option value=""></option>
                  <?php
                  // while ($row1 = mysqli_fetch_array($queryResponsable)) {
                  ?>
                    <option <?php //if ($row1['Id_usuario'] == $embalse['id_encargado']) echo "selected"; 
                            ?> value="<?php //echo $row1['Id_usuario']; 
                                      ?>"><?php //echo $row1['P_Nombre']; 
                                          ?></option>
                  <?php
                  // }
                  ?>
                </select>
              </div>
            </div> -->

            <input style="display: none;" value="<?php echo $id_embalse ?>" type="text" class="form-control" id="id_embalse" name="id_embalse" placeholder="Ingrese el nomnbre del embalse">

            <div class="text-center mt-5 boton-stikcy-save" style="margin: 0 auto;">
              <button type="submit" class="btn btn-primary" name="Update">Guardar cambios</button>
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


<script src="assets/js/get-ubication-select.js"></script>
<script src="./assets/js/nice-select2.js"></script>
<script>
  var optionsEstados = {
    searchable: true,
    placeholder: 'Seleccionar estados',
    searchtext: 'buscar',
    selectedtext: 'estados seleccionados'
  };
  var optionsMuni = {
    searchable: true,
    placeholder: 'Seleccionar municipios',
    searchtext: 'buscar',
    selectedtext: 'municipios seleccionados'
  };
  var optionsParro = {
    searchable: true,
    placeholder: 'Seleccionar parroquias',
    searchtext: 'buscar',
    selectedtext: 'parroquias seleccionadas'
  };
  var optionsResponsable = {
    searchable: true,
    placeholder: 'Seleccionar responsable',
    searchtext: 'buscar',
    selectedtext: 'Responsable Seleccionado'
  };
  EstadoSelect = NiceSelect.bind(document.getElementById("estado"), optionsEstados);
  MunicipioSelect = NiceSelect.bind(document.getElementById("municipio"), optionsMuni);
  ParroquiaSelect = NiceSelect.bind(document.getElementById("parroquia"), optionsParro);
  ResponsableSelect = NiceSelect.bind(document.getElementById("responsable"), optionsResponsable);

  SectoresEstado = NiceSelect.bind(document.getElementById("SectoresEstado"), optionsEstados);
  SectoresMunicipio = NiceSelect.bind(document.getElementById("SectoresMunicipio"), optionsMuni);
  SectoresParroquia = NiceSelect.bind(document.getElementById("SectoresParroquia"), optionsParro);

  var win = navigator.platform.indexOf('Win') > -1;
  if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
      damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
  }

  // form-embalse
  const form = document.getElementById('form-embalse');

  form.querySelectorAll('input').forEach(function(input, index, inputs) {
    input.addEventListener('keydown', function(event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        const nextIndex = index < inputs.length - 1 ? index + 1 : 0;
        inputs[nextIndex].focus();
      }
    });

    $(input).on('dblclick', function() {
      var $input = $(this);
      var $div = $('<div contenteditable="true" class="editable-div"></div>')
        .text($input.val())
        .insertAfter($input)
        .addClass($input.attr('class'));

      $input.hide();
      $div.show().focus().addClass('expanded');

      $div.on('blur', function() {
        $input.val($div.text());
        $div.remove();
        $input.show().removeClass('expanded');
      });

      $div.on('keydown', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          $div.blur();
        }
      });
    });

  });
</script>



<!-- Modal -->
<div class="modal fade" id="edit-embalse" tabindex="-1" role="dialog" aria-labelledby="edit-embalse" aria-hidden="true" data-bs-backdrop="static">
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

<div class="modal fade" id="show-batimetria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-bs-backdrop="static">
  <div id="modal-show" class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">COTAS</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modal-body" class="modal-body">
        <div id="table-container" class="table-container">
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn bg-gradient-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade px-5" id="modal-validate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Errores de campos.</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="modal-body-validate" class="text-sm">

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade px-5" id="modal-formato" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content" style="width: 450px !important;">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Formato de batimería.</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="modal-body-formato d-flex flex-col justify-content-center" class="text-sm">
          <div class="text-center">
            <img width="400" height="500" src="./assets/img/FormatoBatimetría.png">
          </div>
          <div class="text-center" style="color:#000000">
            <b>En cada hoja colocar cada Batimetría, e identificar cada hoja con el año de la Batimetría correspondiente.</b>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="aceptar-formato" type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="show-pre-batimetria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-bs-backdrop="static">
  <div id="modal-show" class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">COTAS</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modal-pre-body" class="modal-pre-body">
        <div id="table-container" class="table-container">
        </div>

        <?php if ($embalse["batimetria"] != "") {
          $pre_batimetria = json_decode($embalse["batimetria"], true);
          // var_dump($pre_batimetria);
          foreach ($pre_batimetria as $key => $anio) {


        ?>
            <div class="tabla">
              <h3> <?php echo $key ?> </h3>
              <table class="align-items-center mb-0 table-cota" border="1">
                <tr>
                  <th style="background-color: #5e72e4; color:white">Cota</th>
                  <th style="background-color: #5e72e4; color:white">Área</th>
                  <th style="background-color: #5e72e4; color:white">Capacidad</th>
                </tr>
                <?php
                foreach ($anio as $key => $value) {
                  // $partes = explode("-", $value);
                  $partes = explodeBat($value);
                ?>
                  <tr>
                    <td><?php echo $key ?></td>
                    <td><?php echo number_format($partes[0], 2, ",", "") ?></td>
                    <td><?php echo number_format($partes[1], 2, ",", "") ?></td>
                  </tr>
                <?php }
                ?>
              </table>
            </div>
        <?php }
        }
        closeConection($conn); ?>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
        <!-- <button type="button" class="btn bg-gradient-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>

<script src="./assets/js/xlsx.full.min.js"></script>
<script>
  function previewImage(id) {
    document.querySelector("#" + id).addEventListener("change", function(e) {
      if (e.target.files.length == 0) {
        document.querySelector("#" + id + "-preview").src = "./assets/img/default-img.png";
        document.querySelector("#" + id + "-pre").value = "";
        return;
      }
      let file = e.target.files[0];
      let url = URL.createObjectURL(file);
      document.querySelector("#" + id + "-preview").src = url;
    });

    document.querySelector("#" + id + "-remove").addEventListener("click", function(e) {
      e.preventDefault();
      document.querySelector("#" + id + "-preview").src = "./assets/img/default-img.png";
      document.querySelector("#" + id + "-pre").value = "";
    });
  }

  previewImage("imagen_uno");
  previewImage("imagen_dos");
  previewImage("imagen_tres");


  $(".down-bat").on("click", function() {

    // var id = $(this).data("id");
    // console.log(id)
    var id = "";
    window.location.href = "./php/download_excel_batimetria.php?type=plantilla&id=" + id;

  });

  $(".down-excel").on("click", function() {

    var id = $(this).data("id");
    // console.log(id)
    window.location.href = "./php/download_excel_batimetria.php?type=excel&id=" + id;

  });


  var batimetria = document.getElementById('batimetria');
  // console.log(batimetria.files[0] != null);

  var cotasEmbalse = {};

  batimetria.addEventListener('change', function(event) {

    if (batimetria.files[0] != null) {
      iniciar();
    } else {
      if (!$(".show-bat").hasClass("no-visible")) {
        $(".show-bat").addClass("no-visible");
      }
      if ($(".down-excel").hasClass("no-visible")) {
        $(".down-excel").removeClass("no-visible");
      }
      var modalBody = document.getElementById('modal-body');
      modalBody.innerHTML = "";
    }


    // console.log(cotasEmbalse);
    // agregarTablasAlModal(cotasEmbalse);

  });

  $(".down-bat").on("click", function() {

    // var id = $(this).data("id");
    // console.log(id)
    window.location.href = "./php/download_excel_batimetria.php?type=plantilla";

  });



  function cargar_datos_asincrono() {
    return new Promise(resolve => {
      var batimetria = document.getElementById('batimetria'); // Reemplaza 'excelInput' con el ID real de tu input file
      var archivo = batimetria.files[0];

      var reader = new FileReader();
      reader.onload = function(e) {
        var data = new Uint8Array(e.target.result);
        var workbook = XLSX.read(data, {
          type: 'array'
        });

        workbook.SheetNames.forEach(function(sheetName) {
          var cotaEmbalse = {};
          var worksheet = workbook.Sheets[sheetName];
          var range = XLSX.utils.decode_range(worksheet['!ref']);

          // let count = 0;
          // for (var row = 1; row <= range.e.r; row++) {
          //   var cota = worksheet[XLSX.utils.encode_cell({
          //     r: row,
          //     c: 1
          //   })].v.toFixed(3);
          //   var area = worksheet[XLSX.utils.encode_cell({
          //     r: row,
          //     c: 2
          //   })].v;
          //   var capacidad = worksheet[XLSX.utils.encode_cell({
          //     r: row,
          //     c: 3
          //   })].v;
          //   cotaEmbalse[cota] = area + '-' + capacidad;
          //   // count++
          //   // if(count == 50) {row++; count = 0;}
          // }

          let row = 1;
          let count = 0;

          while (row <= range.e.r) {
            // console.log(row);
            // for (let i = 0; i < 50; i++) {
            //   if (row > range.e.r) {
            //     break;
            //   }
            let celda;
            celda = worksheet[XLSX.utils.encode_cell({
              r: row,
              c: 0
            })];
            var cota = celda ? celda.v.toFixed(3) : "";
            celda = worksheet[XLSX.utils.encode_cell({
              r: row,
              c: 1
            })];
            var area = celda ? celda.v : "";
            celda = worksheet[XLSX.utils.encode_cell({
              r: row,
              c: 2
            })];
            var capacidad = celda ? celda.v : "";
            cotaEmbalse[cota] = area + '-' + capacidad;
            row++;
            // }
            // row = row - 50;
            // for (let i = 0; i < 50; i++) {
            //   if (row > range.e.r) {
            //     break;
            //   }
            //   var cota = worksheet[XLSX.utils.encode_cell({
            //     r: row,
            //     c: 5
            //   })].v.toFixed(3);
            //   var area = worksheet[XLSX.utils.encode_cell({
            //     r: row,
            //     c: 6
            //   })].v;
            //   var capacidad = worksheet[XLSX.utils.encode_cell({
            //     r: row,
            //     c: 7
            //   })].v;
            //   cotaEmbalse[cota] = area + '-' + capacidad;
            //   row++;
            // }

            if (row <= range.e.r) {
              row++;
            }
          }

          cotasEmbalse[sheetName] = cotaEmbalse;
        });

      };
      reader.readAsArrayBuffer(archivo);
      console.log('Inicio de la operación asincrónica');
      setTimeout(() => {
        console.log('Fin de la operación asincrónica');
        resolve();
      }, 1000);
    });
  }

  async function cargarDatos() {
    try {
      console.log('Inicio de cargarDatos');
      await cargar_datos_asincrono();
      console.log('Después de await en cargarDatos');
    } catch (error) {
      console.error('Error al cargar datos:', error);
    }
  }

  async function iniciar() {
    console.log('Inicio del script');
    await cargarDatos();
    console.log('Fin del script');
    console.log(cotasEmbalse['2001']);
    agregarTablasAlModal(cotasEmbalse);
    // console.log($(".show-bat").hasClass("no-visible"));

    var batimetria = document.getElementById('batimetria');
    // console.log(batimetria.files[0]);
    // console.log(batimetria.files[0]!=null);


    if (batimetria.files[0] != null) {
      if ($(".show-bat").hasClass("no-visible")) {
        $(".show-bat").removeClass("no-visible");
      }
    } else {
      if (!$(".show-bat").hasClass("no-visible")) {
        $(".show-bat").addClass("no-visible");
      }
    }

    if (batimetria.files[0] != null) {
      if ($(".show-bat").hasClass("no-visible")) {
        $(".show-bat").removeClass("no-visible");
      }
      if (!$(".down-excel").hasClass("no-visible")) {
        $(".down-excel").addClass("no-visible");
      }
    } else {
      if (!$(".show-bat").hasClass("no-visible")) {
        $(".show-bat").addClass("no-visible");
      }
      if ($(".down-excel").hasClass("no-visible")) {
        $(".down-excel").removeClass("no-visible");
      }
    }


  }

  function construirTabla(embalse, data) {
    var tabla = '<table class="align-items-center mb-0 table-cota" border="1">';
    tabla += '<tr><th style="background-color: #5e72e4; color:white">Cota</th><th style="background-color: #5e72e4; color:white">Área</th><th style="background-color: #5e72e4; color:white">Capacidad</th></tr>';

    for (var cota in data) {
      // var partes = data[cota].split('-');
      var partes = explodeBat(data[cota]);
      tabla += '<tr><td>' + cota + '</td><td>' + parseFloat(partes[0]).toFixed(2) + '</td><td>' + parseFloat(partes[1]).toFixed(2) + '</td></tr>';
    }

    tabla += '</table>';
    return tabla;
  }

  function agregarTablasAlModal(cotasEmbalse) {
    var modal_body = document.getElementById('modal-body');
    modal_body.innerHTML = "";

    for (var embalse in cotasEmbalse) {
      var tablaHTML = construirTabla(embalse, cotasEmbalse[embalse]);

      var tablaContainer = document.createElement('div');
      tablaContainer.className = 'tabla'
      tablaContainer.innerHTML = '<h3>' + embalse + '</h3>' + tablaHTML;

      modal_body.appendChild(tablaContainer);
      // modal_body.innerHTML += tablaHTML;
    }


  }

  function explodeBat(value, i = null) {
    // Expresión regular para manejar ambos formatos
    const pattern = /^(-?[\d,.]+)-(-?[\d,.]+)$/;
    const matches = value.match(pattern);

    if (matches) {
      const valores = [matches[1], matches[2]]; // Valores capturados

      if (i !== null) {
        return valores[i];
      } else {
        return valores;
      }
    } else {
      const valores = [0, 0]; // Valores predeterminados en caso de no coincidencia

      if (i !== null) {
        return valores[i];
      } else {
        return valores;
      }
    }
  }


  $("#proposito").on("click", function() {
    $("#modal-proposito").toggleClass('desplegar');
  });

  var propositos = [];
  var id_propositos = [];

  var props_checkeados = $('input[type="checkbox"]:checked.opcion-prop');
  $.each(props_checkeados, function(index, input) {
    propositos.push($(".opcion-" + input.id)[0].innerText);
    id_propositos.push(input.id.split("-")[0])
  });

  $("#proposito")[0].value = propositos.join(" - ");

  $(".prop-opcion").on("change", function() {
    if ($(this).is(':checked')) {
      propositos.push($(".opcion-" + this.id)[0].innerText)
      id_propositos.push(this.id.split("-")[0]);
    } else {
      propositos = propositos.filter((proposito) => {
        return proposito != $(".opcion-" + this.id)[0].innerText
      })

      id_propositos = id_propositos.filter((id) => {
        return id != this.id.split("-")[0]
      })
    }
    $("#proposito")[0].value = propositos.join(" - ");
    $("#proposito-input")[0].value = id_propositos.join(" - ");
  })


  $("#uso").on("click", function() {
    $("#modal-uso").toggleClass('desplegar');
  });

  var usos = [];
  var id_usos = [];

  var usos_checkeados = $('input[type="checkbox"]:checked.opcion-uso');
  $.each(usos_checkeados, function(index, input) {
    usos.push($(".opcion-" + input.id)[0].innerText);
    id_usos.push(input.id.split("-")[0])
  });

  $("#uso")[0].value = usos.join(" - ");

  $(".prop-uso").on("change", function() {
    if ($(this).is(':checked')) {
      usos.push($(".opcion-" + this.id)[0].innerText);
      id_usos.push(this.id.split("-")[0]);
    } else {
      usos = usos.filter((uso) => {
        return uso != $(".opcion-" + this.id)[0].innerText
      })
      id_usos = id_usos.filter((id) => {
        return id != this.id.split("-")[0]
      })
    }
    $("#uso")[0].value = usos.join(" - ");
    $("#uso-input")[0].value = id_usos.join(" - ");
  });

  $("#operador").on("click", function() {
    $("#modal-operador").toggleClass('desplegar');
  });

  $(".ope-opcion").on("change", function() {
    // console.log($(".opcion-" + this.id)[0].innerText, this.id.split("-")[0]);
    operador = "";
    id = "";
    if ($(this).is(':checked')) {
      operador = $(".opcion-" + this.id)[0].innerText;
      id = this.id.split("-")[0];

      $("#operador")[0].value = operador;
      $("#operador-input")[0].value = id;
    }
  });

  $("#region").on("click", function() {
    $("#modal-region").toggleClass('desplegar');
  });

  $(".reg-opcion").on("change", function() {
    // console.log($(".opcion-" + this.id)[0].innerText, this.id.split("-")[0]);
    region = "";
    id = "";
    if ($(this).is(':checked')) {
      region = $(".opcion-" + this.id)[0].innerText;
      id = this.id.split("-")[0];

      $("#region")[0].value = region;
      $("#region-input")[0].value = id;

      // console.log($("#region")[0].value, $("#region-input")[0].value, $("#operador")[0].value, $("#operador-input")[0].value = id)
    }
  });

  document.documentElement.addEventListener('click', function(e) {
    const excepciones = ["proposito", "modal-proposito", "uso", "modal-uso", "operador", "modal-operador", "region", "modal-region"];
    if (!excepciones.includes(e.target.id) && (!$(e.target).hasClass("opcion-prop") && !$(e.target).hasClass("opcion-uso") && !$(e.target).hasClass("opcion"))) {
      removerClase($("#modal-proposito"), "desplegar");
      removerClase($("#modal-uso"), "desplegar");
      removerClase($("#modal-operador"), "desplegar");
      removerClase($("#modal-region"), "desplegar");
    }
  });

  function agregarClase(elemento, clase) {
    if (!elemento.hasClass(clase)) {
      elemento.addClass(clase);
    }
  }

  function removerClase(elemento, clase) {
    if (elemento.hasClass(clase)) {
      elemento.removeClass(clase);
    }
  }


  $("#change-bat").on("click", function(e) {
    document.querySelector("#pre-bat").setAttribute("hidden", "hidden");
    document.querySelector(".down-excel").setAttribute("hidden", "hidden");
    document.querySelector("#batimetria").removeAttribute("hidden");
    document.querySelector("#change-redo").removeAttribute("hidden");
    document.querySelector("#batimetria-pre").value = "";

    if (document.querySelector("#batimetria").files.length > 0) {
      removerClase($(".show-bat"), "no-visible");
    }

  });

  $("#change-redo").on("click", function(e) {
    document.querySelector("#batimetria").setAttribute("hidden", "hidden");
    document.querySelector("#change-redo").setAttribute("hidden", "hidden");
    document.querySelector("#pre-bat").removeAttribute("hidden");
    document.querySelector(".down-excel").removeAttribute("hidden");
    document.querySelector("#batimetria-pre").value = "precargada";
    agregarClase($(".show-bat"), "no-visible");
    removerClase($(".down-excel"), "no-visible");
  });


  $("#vol_nor").on("change", capacidadUtil);
  $("#vol_min").on("change", capacidadUtil);

  document.addEventListener('DOMContentLoaded', capacidadUtil);

  function capacidadUtil() {
    let vol_nor = $("#vol_nor").val();
    let vol_min = $("#vol_min").val();

    if (vol_min != "" && vol_nor != "") {
      vol_nor = parseFloat(vol_nor.replace(/\./g, '').replace(',', '.'));
      vol_min = parseFloat(vol_min.replace(/\./g, '').replace(',', '.'));
      let capacidad = vol_nor - vol_min;
      $("#cap-util")[0].value = capacidad;
    } else {
      $("#cap-util")[0].value = 0;
    }

  }

  // type="text" pattern="[0-9]+([,.][0-9]{0,2})?"

  var inputs = $(".numero");

  for (let i = 0; i < inputs.length; i++) {
    inputs[i].addEventListener("keydown", function(event) {
      validarNumero(event, inputs[i]);
    });
    // inputs[i].addEventListener("change", function(event) {
    //   formatoNumero(inputs[i]);
    // });
    // formatoNumero(inputs[i]);
  }

  function validarNumero(event, input) {
    let valorInput = input.value;
    const codigoTecla = event.key;
    console.log(input.id)
    if (input.id == "huso" && (codigoTecla < '0' || codigoTecla > '9') && codigoTecla !== 'Backspace') {
      event.preventDefault();
    }
    // Permitir solo números y una coma
    if ((codigoTecla < '0' || codigoTecla > '9') && codigoTecla !== '.' && codigoTecla !== 'Backspace') {
      event.preventDefault();
    }

    // Permitir solo una coma
    // if (codigoTecla === ',' && valorInput.includes(',')) {
    //   event.preventDefault();
    // }
    if (codigoTecla === '.' && valorInput == "") {
      event.preventDefault();
    }

    if (codigoTecla === '.' && valorInput.includes('.')) {
      event.preventDefault();
    }

  }

  function formatearNumero(input) {
    // Convertir a número y verificar si es un número válido
    const numero = parseFloat(input.value);
    if (isNaN(numero)) {
      console.error("Entrada no válida. Por favor, ingrese un número.");
      return input;
    }

    // Formatear el número con separadores de miles y tres dígitos decimales
    const numeroFormateado = numero.toLocaleString('es-ES', {
      minimumFractionDigits: 3,
      maximumFractionDigits: 3
    });

    // Reemplazar el separador decimal por coma
    return numeroFormateado.replace('.', ',');

    input.value = numeroFormateado;
  }

  //esta es la buena

  function formatoNumero(input) {

    let valor = input.value;

    if (valor === undefined || valor === null) {
      return;
    }

    let partes = valor.split(',');
    let parteEntera = partes[0];
    let parteDecimal = partes[1] || '';

    console.log(parteEntera + " - " + parteDecimal)

    parteEntera = parteEntera.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    console.log(parteEntera + " - " + parteDecimal)

    let resultado = parteEntera + (parteDecimal !== '' ? ',' + parteDecimal.slice(0, 3).padEnd(3, '0') : ',000');

    input.value = resultado;
  }

  function formatoNumeroo(input) {

    let valor = parseFloat(input.value);

    const numeroFormateado = valor.toLocaleString('es-ES', {
      minimumFractionDigits: 3,
      maximumFractionDigits: 3
    });

    input.value = numeroFormateado;
  }

  // document.getElementById("norte").addEventListener("input", function(event) {
  //   let input = event.target;
  //   let cursorPos = input.selectionStart;
  //   let isBackspace = event.inputType === 'deleteContentBackward';

  //   // Elimina caracteres no numéricos del valor del input
  //   let newValue = input.value.replace(/[^\d\,]/g, '');

  //   // Divide el valor en parte entera y parte decimal
  //   let partes = newValue.split(",");
  //   let parteEntera = partes[0];
  //   let parteDecimal = partes.length > 1 ? partes[1] : "";

  //   // Aplica formato a la parte entera
  //   parteEntera = parteEntera.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

  //   // Limita la parte decimal a dos dígitos
  //   parteDecimal = parteDecimal.slice(0, 2);

  //   // Si se está intentando borrar la coma y el cursor está a la derecha de la coma
  //   if (isBackspace && cursorPos > parteEntera.length) {
  //     // Mueve el cursor a la izquierda de la coma
  //     cursorPos = parteEntera.length + 1;
  //   }

  //   // Actualiza el valor del input con el formato deseado
  //   input.value = parteEntera + (parteDecimal ? "," + parteDecimal : ",00");

  //   // Restaura la posición del cursor
  //   input.setSelectionRange(cursorPos, cursorPos);
  // });

  // if (input.value.length > newValue.length && input.value[cursorPos - 1] === ",") {
  //   cursorPos--;
  // }
  // console.log(input);


  // MAPA PARA EXTRAER EL NORTE, ESTE, HUSO
  var map = L.map('map').setView([8, -66], 6);
  // map.scrollWheelZoom.disable();
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { // Utilizar un proveedor de azulejos de OpenStreetMap
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  var marker;

  if ($("#norte").val() != "" && $("#este").val() != "" && $("#huso").val() != "") {
    console.log("Hay coordenadas")
    if (marker) {
      // map.removeLayer(marker);
    }

    var norte = parseInt($("#norte").val(), 10)
    var este = parseInt($("#este").val(), 10)
    var huso = parseInt($("#huso").val(), 10)

    var utm = '+proj=utm +zone=' + huso + ' +ellps=WGS84';
    var wgs84 = '+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs';
    var latlng = proj4(utm, wgs84, [este, norte]);
    var latitud = latlng[1];
    var longitud = latlng[0];

    marker = new L.marker([latitud, longitud]).addTo(map);
  }

  map.on('click', function(e) {

    var latlng = e.latlng;
    var latitud = latlng.lat;
    var longitud = latlng.lng;

    if (marker) {
      // map.removeLayer(marker);
      marker.remove()
    }

    marker = L.marker([latitud, longitud]).addTo(map);

    console.log("Latitud: " + latitud + ", Longitud: " + longitud);

    // // Conversion de Coordenadas normales a UTM
    // var utmCoords = proj4(proj4.defs('EPSG:4326'), proj4.defs('EPSG:32600'), [longitud, latitud]);
    // var norte = utmCoords[1];
    // var este = utmCoords[0];
    // var huso = Math.floor((longitud + 180) / 6) + 1;

    // //Conversion de Coordenadas UTM a Normales
    // var utm = '+proj=utm +zone=' + huso + ' +ellps=WGS84';
    // var wgs84 = '+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs';
    // var latlng = proj4(utm, wgs84, [este, norte]);
    // var latitud = latlng[1];
    // var longitud = latlng[0];

    proj4.defs("EPSG:4326", "+proj=longlat +datum=WGS84 +no_defs");

    var zonaUTM = Math.floor((longitud + 180) / 6) + 1;
    proj4.defs("EPSG:326" + zonaUTM, "+proj=utm +zone=" + zonaUTM + " +datum=WGS84 +units=m +no_defs");
    var coordenadasUTM = proj4("EPSG:4326", "EPSG:326" + zonaUTM, [longitud, latitud]);

    var norte = coordenadasUTM[1];
    var este = coordenadasUTM[0];
    var huso = zonaUTM;


    $("#norte").val(norte);
    $("#este").val(este);
    $("#huso").val(huso);

    // console.log("Norte: " + norte + ", Este: " + este + ", Huso: " + huso);

  });

  $(".show-map").on('click', function() {
    // $("#modal-map").modal('show');
    removerClase($("#mapa"), "map-no-visible")
    agregarClase($("#mapa"), "map-visible")
    agregarClase($("#mapa"), "fade-in-image")

    if (marker) {
      // map.removeLayer(marker);
      // marker.remove()
    }

    var norte = parseInt($("#norte").val(), 10)
    var este = parseInt($("#este").val(), 10)
    var huso = parseInt($("#huso").val(), 10)

    var utm = '+proj=utm +zone=' + huso + ' +ellps=WGS84';
    var wgs84 = '+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs';
    var latlng = proj4(utm, wgs84, [este, norte]);
    var latitud = latlng[1];
    var longitud = latlng[0];

    // marker = L.marker([latitud, longitud]).addTo(map);
    console.log("marcador agregado")
  });

  $("#close-map").on('click', function(e) {
    e.preventDefault();
    removerClase($("#mapa"), "map-visible")
    removerClase($("#mapa"), "fade-in-image")
    agregarClase($("#mapa"), "map-no-visible")
  });


  //VALIDACION DE FORMULARIO.

  document.getElementById("form-embalse").addEventListener("submit", function(event) {
    // event.preventDefault();
    console.log("A validar");
    var regex = /^(\d{1,3}(\.\d{3})*|\d+)(,\d+)?$/; //PERFECTA

    var campos = document.querySelectorAll('.Vnumero, .Vrequerido, .Varchivo, .Viguales');
    var errorMessages = [];

    campos.forEach(function(campo) {

      var label = campo.previousElementSibling.innerText;

      if (campo.classList.contains('Vnumero')) {
        if (campo.value.trim() === "") {
          errorMessages.push("El campo '<b>" + label + "</b>' no puede estar vacío.");
          if (!campo.classList.contains('input-error')) {
            campo.className += " input-error";
          }
        } else if ((!regex.test(campo.value))) {
          errorMessages.push("El campo '<b>" + label + "</b>' debe contener solo números.");
          if (!campo.classList.contains('input-error')) {
            campo.className += " input-error";
          }
        }
      }

      if (campo.classList.contains('Vrequerido')) {
        if (campo.value.trim() === "") {
          errorMessages.push("El campo '<b>" + label + "</b>' no puede estar vacío.");
          if (!campo.classList.contains('input-error')) {
            campo.className += " input-error";
          }
        }
      }

      if (campo.classList.contains('Varchivo')) {
        +
        console.log("archivooo")
        if (campo.files.length === 0) {
          errorMessages.push("Debe seleccionar un archivo para el campo '<b>" + label + "</b>'.");
          if (!campo.classList.contains('input-error')) {
            campo.className += " input-error";
          }
        }
      }

      if (campo.classList.contains('Viguales')) {
        let nombre_input = $(campo).val().trim().toLocaleLowerCase();

        let busqueda = nombresEmbalses.filter((nombre) => {
          return nombre.trim().toLocaleLowerCase() == nombre_input.trim().toLocaleLowerCase() && nombre.trim().toLocaleLowerCase() != embalseActual.trim().toLocaleLowerCase()
        });
        if (busqueda.length > 0) {
          errorMessages.push("El nombre del Embalse '<b>" + nombre_input.charAt(0).toUpperCase() + nombre_input.slice(1) + "</b>'  ya está registrado.");
          if (!campo.classList.contains('input-error')) {
            campo.className += " input-error";
          }
        }
      }

    });

    if (errorMessages.length > 0) {
      event.preventDefault();
      var errorContainer = document.getElementById("modal-body-validate");
      console.log(errorContainer)
      errorContainer.innerHTML = "<ul><li>" + errorMessages.join("</li><li>") + "</li></ul>";
      $('#modal-validate').modal('show');
    }
  });

  let cargar = false;

  $("#batimetria").on("click", function(e) {
    console.log("HOLA")
    if (!cargar) {
      e.preventDefault();
      $('#modal-formato').modal('show');
    } else {
      cargar = false;
    }
  });

  $("#aceptar-formato").on("click", function() {
    console.log("Boton")
    cargar = true;
    $("#batimetria").click();
  })

  let nombresEmbalses = <?php echo json_encode($nombresEmbalses) ?>;
  let embalseActual = <?php echo json_encode($embalse["nombre_embalse"]) ?>;
  console.log(nombresEmbalses)

  $("#embalse_nombre").on('input', function() {
    let nombre_input = $(this).val().trim().toLocaleLowerCase();

    let busqueda = nombresEmbalses.filter((nombre) => {
      return nombre.trim().toLocaleLowerCase() == nombre_input.trim().toLocaleLowerCase() && nombre.trim().toLocaleLowerCase() != embalseActual.trim().toLocaleLowerCase()
    });
    if (busqueda.length > 0) {
      if (!$(this).hasClass("founded")) {
        $(this).toggleClass("founded")
        $(".label-founded").toggleClass("no-visible")
      }
    } else {
      if ($(this).hasClass("founded")) {
        $(this).toggleClass("founded")
        $(".label-founded").toggleClass("no-visible")
      }
    }
    // console.log($(this).val().trim().toLocaleLowerCase())
  })
</script>