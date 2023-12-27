<?php
include 'php/Conexion.php';

$queryEstados = mysqli_query($conn, "SELECT * FROM estados;");
$queryResponsable = mysqli_query($conn, "SELECT * FROM usuarios WHERE tipo = 'User';");
?>


<?php
if (!isset($_SESSION)) {
  session_start();
};
if (!isset($_SESSION["Id_usuario"])) {

  print "<script>window.location='index.php';</script>";
}

date_default_timezone_set("America/Caracas");
?>


<style>
  #show-batimetria {
    /* display: flex; */
    text-align: center;
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

  /* th,
  td {
    width: 33%;
    text-align: center;
    vertical-align: middle;
    border: 1px solid #000;
  } */

  
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
        <div id="form-contenedor">
          <form id="form-embalse" method="POST" action="php/proces_embalse.php" enctype="multipart/form-data">
            <div class="p-5 m-5">

              <h3 class="pb-3">Información principal:</h3>

              <div class="row">
                <div class="col-md-4 col-sm-12">
                  <div class="form-group">
                    <label for="embalse_nombre">Nombre del embalse</label>
                    <input type="text" class="form-control" id="embalse_nombre" name="embalse_nombre" placeholder="Ingrese el nomnbre del embalse" required>
                  </div>
                  <div class="form-group">
                    <label for="presa_nombre">Nombre de la presa</label>
                    <input type="text" class="form-control" id="presa_nombre" name="presa_nombre" placeholder="Ingrese el nomnbre de la presa">
                  </div>
                </div>
                <div class="col-md-4 col-sm-12">
                  <div class="form-group">
                    <label for="estado">Estado</label>
                    <select class="form-select" id="estado" name="estado" required>
                      <option value=""></option>
                      <?php
                      while ($row = mysqli_fetch_array($queryEstados)) {
                      ?>
                        <option value="<?php echo $row['id_estado']; ?>"><?php echo $row['estado']; ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="municipio">Municipio</label>
                    <select class="form-select" id="municipio" name="municipio" required>
                      <option value=""></option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="parroquia">Parroquia</label>
                    <select class="form-select" id="parroquia" name="parroquia" required>
                      <option value=""></option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4 col-sm-12">
                  <div class=" form-group">
                    <label for="norte">Norte</label>
                    <input type="number" step="0.001" class="form-control" id="norte" name="norte" placeholder="Ingrese los afluentes principales">
                  </div>
                  <div class=" form-group">
                    <label for="este">Este</label>
                    <input type="number" step="0.001" class="form-control" id="este" name="este" placeholder="Ingrese el area de la cuenca en km2">
                  </div>
                  <div class=" form-group">
                    <label for="huso">Huso</label>
                    <input type="number" step="0.001" class="form-control" id="huso" name="huso" placeholder="Ingrese el escurrimiento medio en m3/s">
                  </div>
                </div>
              </div>

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
                  <input type="number" step="0.001" class="form-control" id="area" name="area" placeholder="Ingrese el area de la cuenca en hm3">
                </div>
                <div class="col-xl-3 col-lg-6 form-group">
                  <label for="escurrimiento">Escurrimiento medio</label>
                  <input type="number" step="0.001" class="form-control" id="escurrimiento" name="escurrimiento" placeholder="Ingrese el escurrimiento medio en m3/s">
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
                    <input type="text" class="form-control" id="personal" name="personal" placeholder="Personal encargado a nivel central">
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
                  <input type="text" class="form-control" id="autoridad" name="autoridad" placeholder="Autoridad responsable del embalse">
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

              <div class="row">
                <div style="display:flex; flex-direction:column;" class="col-md-3 col-sm-12 justify-content-between">
                  <div class="form-group">
                    <label for="batimetria">Batimetría</label>
                    <input type="file" class="form-control" id="batimetria" name="batimetria" placeholder="Ingrese el tipo de batimetria" required>
                  </div>
                  <!-- <div class="form-group d-flex justify-content-center"> -->
                  <a onclick="$('#show-batimetria').modal('show');" class="show-bat no-visible btn btn-link text-dark text-sm"><i class="fas fa-eye text-lg me-1"></i> Ver</a>
                  <!-- </div> -->
                  <div class="form-group">
                    <label for="vida_util">Vida útil</label>
                    <input type="number" class="form-control" id="vida_util" name="vida_util" placeholder="Ingrese la vida util en años">
                  </div>
                </div>

                <div class="col-md-3 col-sm-12">
                  <div class=" form-group">
                    <label for="cota_min">Cota mínima</label>
                    <input type="number" step="0.001" class="form-control" id="cota_min" name="cota_min" placeholder="Ingrese los afluentes principales">
                  </div>
                  <div class=" form-group">
                    <label for="vol_min">Volumen mínimo</label>
                    <input type="number" step="0.001" class="form-control" id="vol_min" name="vol_min" placeholder="Ingrese el area de la cuenca en km2">
                  </div>
                  <div class=" form-group">
                    <label for="sup_min">Superficie mínima</label>
                    <input type="number" step="0.001" class="form-control" id="sup_min" name="sup_min" placeholder="Ingrese el escurrimiento medio en m3/s">
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <div class=" form-group">
                    <label for="cota_nor">Cota normal</label>
                    <input type="number" step="0.001" class="form-control" id="cota_nor" name="cota_nor" placeholder="Ingrese los afluentes principales">
                  </div>
                  <div class=" form-group">
                    <label for="vol_nor">Volumen normal</label>
                    <input type="number" step="0.001" class="form-control" id="vol_nor" name="vol_nor" placeholder="Ingrese el area de la cuenca en km2">
                  </div>
                  <div class=" form-group">
                    <label for="sup_nor">Superficie normal</label>
                    <input type="number" step="0.001" class="form-control" id="sup_nor" name="sup_nor" placeholder="Ingrese el escurrimiento medio en m3/s">
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <div class=" form-group">
                    <label for="cota_max">Cota máxima</label>
                    <input type="number" step="0.001" class="form-control" id="cota_max" name="cota_max" placeholder="Ingrese los afluentes principales">
                  </div>
                  <div class=" form-group">
                    <label for="vol_max">Volumen máximo</label>
                    <input type="number" step="0.001" class="form-control" id="vol_max" name="vol_max" placeholder="Ingrese el area de la cuenca en km2">
                  </div>
                  <div class=" form-group">
                    <label for="sup_max">Superficie máxima</label>
                    <input type="number" step="0.001" class="form-control" id="sup_max" name="sup_max" placeholder="Ingrese el escurrimiento medio en m3/s">
                  </div>
                </div>
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
                  <input type="number" step="0.001" class="form-control" id="altura" name="altura" placeholder="Ingrese la altura en metros">
                </div>
                <div class="col-xl-3 col-lg-6 form-group">
                  <label for="talud_arriba">Talud aguas arriba</label>
                  <input type="number" step="0.001" class="form-control" id="talud_arriba" name="talud_arriba" placeholder="Ingrese el talud aguas arriba en grados">
                </div>
                <div class="col-xl-3 col-lg-6 form-group">
                  <label for="talud_abajo">Talud aguas abajo</label>
                  <input type="number" step="0.001" class="form-control" id="talud_abajo" name="talud_abajo" placeholder="Ingrese el talud aguas abajo en grados">
                </div>
                <div class="col-xl-3 col-lg-6 form-group">
                  <label for="longitud_cresta">Longitud de la cresta</label>
                  <input type="number" step="0.001" class="form-control" id="longitud_cresta" name="longitud_cresta" placeholder="Ingrese la longitud de la cresta en metros">
                </div>
                <div class="col-xl-3 col-lg-6 form-group">
                  <label for="cota_cresta">Cota de la cresta</label>
                  <input type="number" step="0.001" class="form-control" id="cota_cresta" name="cota_cresta" placeholder="Ingrese la cota de la cresta en metros sobre el nivel del mar">
                </div>
                <div class="col-xl-3 col-lg-6 form-group">
                  <label for="ancho_cresta">Ancho de la cresta</label>
                  <input type="number" step="0.001" class="form-control" id="ancho_cresta" name="ancho_cresta" placeholder="Ingrese el ancho de la cresta en metros">
                </div>
                <div class="col-xl-3 col-lg-6 form-group">
                  <label for="volumen_terraplen">Volumen del terraplen</label>
                  <input type="number" step="0.001" class="form-control" id="volumen_terraplen" name="volumen_terraplen" placeholder="Ingrese el volumen del terraplen en m3">
                </div>
                <div class="col-xl-3 col-lg-6 form-group">
                  <label for="ancho_base">Ancho maximo de base</label>
                  <input type="number" step="0.001" class="form-control" id="ancho_base" name="ancho_base" placeholder="Ingrese el ancho maximo de base en metros">
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
                  <input type="number" step="0.001" class="form-control" id="carga_aliviadero" name="carga_aliviadero" placeholder="Ingrese la carga sobre el vertedero en metros">
                </div>
                <div class="col-xl-3 col-lg-6 form-group">
                  <label for="descarga_aliviadero">Descarga maxima</label>
                  <input type="number" step="0.001" class="form-control" id="descarga_aliviadero" name="descarga_aliviadero" placeholder="Ingrese la descarga maxima en m3/s">
                </div>
                <div class="col-xl-3 col-lg-6 form-group">
                  <label for="longitud_aliviadero">Longitud</label>
                  <input type="number" step="0.001" class="form-control" id="longitud_aliviadero" name="longitud_aliviadero" placeholder="Ingrese la longitud en metros">
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
                  <input type="number" step="0.001" class="form-control" id="gasto_toma" name="gasto_toma" placeholder="Ingrese el gasto maximo de la obra de toma en m3/s">
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
                  <input type="number" step="0.001" class="form-control" id="area_riego" name="area_riego" placeholder="Ingrese el area de riego beneficiada en km2">
                </div>
              </div>

              <h3 class="pb-3 pt-3">Responsable:</h3>

              <div class="row">
                <div class="col-xl-4 col-lg-6 form-group">
                  <label for="f_cargo">Cargo</label>
                  <input type="text" class="form-control" id="f_cargo" name="f_cargo" placeholder="Ingrese el nomnbre del embalse">
                </div>
                <div class="col-xl-4 col-lg-6 form-group">
                  <label for="f_cedula">Cédula</label>
                  <input type="text" class="form-control" id="f_cedula" name="f_cedula" placeholder="Ingrese el nomnbre del embalse">
                </div>
                <div class="col-xl-4 col-lg-6 form-group">
                  <label for="f_nombres">Nombres</label>
                  <input type="text" class="form-control" id="f_nombres" name="f_nombres" placeholder="Ingrese el nomnbre del embalse">
                </div>
                <div class="col-xl-4 col-lg-6 form-group">
                  <label for="f_apellidos">Apellidos</label>
                  <input type="text" class="form-control" id="f_apellidos" name="f_apellidos" placeholder="Ingrese el nomnbre del embalse">
                </div>
                <div class="col-xl-4 col-lg-6 form-group">
                  <label for="f_telefono">Teléfono</label>
                  <input type="text" class="form-control" id="f_telefono" name="f_telefono" placeholder="Ingrese el nomnbre del embalse">
                </div>
                <div class="col-xl-4 col-lg-6 form-group">
                  <label for="f_correo">Correo</label>
                  <input type="text" class="form-control" id="f_correo" name="f_correo" placeholder="Ingrese el nomnbre del embalse">
                </div>
              </div>

              <h3 class="pb-3 pt-3">Carga de imágenes:</h3>

              <div class="row">
                <div class="col-xl-6 col-lg-12 form-group">
                  <label for="imagen_uno">Ubicación relativa Estado/Municipio/Región hidrográfica</label>
                  <input type="file" accept="image/png,image/jpeg" class="form-control" id="imagen_uno" name="imagen_uno" placeholder="Ingrese el nombre del archivo de imagenes o N/A si no aplica">
                </div>
                <div class="col-xl-6 col-lg-12 form-group">
                  <label for="imagen_dos">Ubicación relativa de los componentes del embalse</label>
                  <input type="file" accept="image/png,image/jpeg" class="form-control" id="imagen_dos" name="imagen_dos" placeholder="Ingrese el nombre del archivo de imagenes o N/A si no aplica">
                </div>
              </div>


              <h3 class="pb-3 pt-3">Responsable del embalse:</h3>

              <div class="row">
                <div class="form-group col-xl-6 col-lg-12">
                  <label for="responsable">Responsable</label>
                  <select class="form-select" id="responsable" name="responsable">
                    <option value=""></option>
                    <?php
                    while ($row1 = mysqli_fetch_array($queryResponsable)) {
                    ?>
                      <option value="<?php echo $row1['Id_usuario']; ?>"><?php echo $row1['P_Nombre']; ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
              </div>

              <div class="row justify-content-center mt-5" style="margin: 0 auto;">
                <button id="submitFornBtn" type="submit" class="col-2 btn btn-primary" name="Guardar">Guardar embalse</button>
              </div>
          </form>
        </div>

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


<script src="assets/js/get-ubication-select.js"></script>
<script>
  var win = navigator.platform.indexOf('Win') > -1;
  if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
      damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
  }
</script>



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

<div class="modal fade" id="show-batimetria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
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
</div>

<script src="./assets/js/xlsx.full.min.js"></script>
<script>
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
      var modalBody = document.getElementById('modal-body');
      modalBody.innerHTML = "";
    }


    // console.log(cotasEmbalse);
    // agregarTablasAlModal(cotasEmbalse);

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

          for (var row = 1; row <= range.e.r; row++) {
            var cota = worksheet[XLSX.utils.encode_cell({
              r: row,
              c: 0
            })].v.toFixed(3);
            var area = worksheet[XLSX.utils.encode_cell({
              r: row,
              c: 1
            })].v;
            var capacidad = worksheet[XLSX.utils.encode_cell({
              r: row,
              c: 2
            })].v;
            cotaEmbalse[cota] = area + '-' + capacidad;
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
  }

  function construirTabla(embalse, data) {
    var tabla = '<table class="align-items-center mb-0 table-cota" border="1">';
    tabla += '<tr><th>Cota</th><th>Área</th><th>Capacidad</th></tr>';

    for (var cota in data) {
      var partes = data[cota].split('-');
      tabla += '<tr><td>' + cota + '</td><td>' + partes[0] + '</td><td>' + partes[1] + '</td></tr>';
    }

    tabla += '</table>';
    return tabla;
  }

  function agregarTablasAlModal(cotasEmbalse) {
    var modal_body = document.getElementById('modal-body');

    for (var embalse in cotasEmbalse) {
      var tablaHTML = construirTabla(embalse, cotasEmbalse[embalse]);

      var tablaContainer = document.createElement('div');
      tablaContainer.className = 'tabla'
      tablaContainer.innerHTML = '<h3>' + embalse + '</h3>' + tablaHTML;

      modal_body.appendChild(tablaContainer);
      // modal_body.innerHTML += tablaHTML;
    }
  }
</script>