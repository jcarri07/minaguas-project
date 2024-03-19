<style>
  .arrow-color {

    color: black;

  }

  .icon-g:hover {
    transform: scale(1.2);
    /* Escala el tamaño del icono al 120% */
  }

  .rrss-container {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 50px;

  }

  .icon-center {}


  @media (max-width: 1100px) {
    .rrss-item {
      flex: 1 0 100%;
      max-width: 100px;

    }
  }
</style>

<?php
require_once 'php/Conexion.php';

// Obtén la lista de embalses para el menú desplegable
$sql_embalses = "SELECT id_embalse, nombre_embalse FROM embalses";
$result_embalses = $conn->query($sql_embalses);

// Procesar los resultados de la consulta

/*
while ($row = $result->fetch_assoc()) {
   $embalse = $row["id_embalse"];
    $mes = $row["mes"];
    $cantidad = $row["cantidad"];

    // Almacena los datos en un formato adecuado para el gráfico
    $datos[$embalse][$mes] = $cantidad;
}


$sql1 = "SELECT nombre_embalse FROM embalses WHERE id_embalse ='$embalse'";
$result1 = $conn->query($sql1);
$row1 = $result1->fetch_assoc();
$nombreEmbalse = $row1['nombre_embalse'];

closeConection($conn);*/
?>

<div class="container-fluid py-5">
  <div class="row justify-content-center">
    <?php if ($_SESSION["Tipo"] == "Admin") { ?>
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <a href="?page=usuarios">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">

                <div class="col-8">
                  <div class="numbers">
                    <p class="text-xs mb-0 font-weight-bold ">Usuarios registrados en el sistema</p>
                    <h5 class="font-weight-bolder" style="margin-top: 10px;">
                      USUARIOS
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">

                    <i class="fa fa-users text-lg opacity-10" aria-hidden="true"></i>

                  </div>
                </div>

              </div>
            </div>
          </div>
        </a>
      </div>
    <?php }  ?>
    <?php if ($_SESSION["Tipo"] == "Admin") { ?>
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <a href="?page=embalses">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-xs mb-0 font-weight-bold">Usuarios registrados en el sistema</p>


                    <h5 class="font-weight-bolder" style="margin-top: 10px;">

                      EMBALSES
                    </h5>

                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">

                    <i class="fa fa-tint text-lg opacity-10" aria-hidden="true"></i>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    <?php }  ?>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <a href="?page=datos">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-xs mb-0 font-weight-bold">Registro y carga de datos al sistema</p>

                  <h5 class="font-weight-bolder" style="margin-top: 10px;">
                    CARGA DE DATOS
                  </h5>

                  <!--<p class="mb-0 text-sm">
                      <span class="text-danger text-xs font-weight-bolder"></span>
                      Cargar datos a tiempo real
                    </p>-->
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                  <i class="fa fa-folder-open-o text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <?php if ($_SESSION["Tipo"] == "Admin") { ?>
      <div class="col-xl-3 col-sm-6">
        <a href="?page=reportes">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-xs mb-0 font-weight-bold">Reportes generados y disponibles</p>
                    <h5 class="font-weight-bolder" style="margin-top: 10px;">
                      REPORTES
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">

                    <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    <?php }  ?>
  </div>

  <?php

  ?>

  <!--grafica -->
  <div class="container-fluid py-5">
    <div class="row justify-content-center">
      <!-- ... (tu código HTML) ... -->
      <div class="col-lg-6 col-md-4 mt-4 mb-3" style="padding-left:20px;">
        <div class="card h-100">
          <div class="card-header">
            <h6 class="mb-0">Embalses</h6>
            <p class="text-sm">Monitoreo del volumen actual de los embalses registrados</p>
          </div>
          <hr class="dark horizontal">
          <div class="card-body col-12 h-100" id="contenedor" style="height:350px !important;">

            <?php include "php/Graficas/grafica_volumen_actual.php" ?>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-4 mt-4 mb-3" style="padding-left:20px;">
        <div class="card z-index-2">
          <div class="card-header">
            <h6 class="mb-0">Registro de Reportes</h6>
            <p class="text-sm">Cantidad de reportes realizados al mes</p>
            <hr class="dark horizontal">
            <div class="d-flex mb-3">
              <label for="embalseSelect" class="text-sm my-auto me-1">Selecciona un embalse:</label>
              <select style="width: 180px;" class="form-control form-select" id="embalseSelect" onchange="cargarGrafico()">
                <option style="display:none">Seleccione...</option>
                <?php
                while ($row_embalse = $result_embalses->fetch_assoc()) {
                  echo '<option value="' . $row_embalse['id_embalse'] . '">' . $row_embalse['nombre_embalse'] . '</option>';
                }
                ?>
              </select>

            </div>
          </div>
          <div class="card-body p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
            <div class="bg-white border shadow-dark border-radius-lg py-3 pe-1">
              <div class="chart mb-1" style="height:350px !important;">
                <canvas id="myChart" width="400" height="200"></canvas>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <div class="row mt-1">
    <div class="col-lg-9 mb-lg-0 mb-1">
      <div class="card user-card-full">
        <div class="row m-l-0 m-r-0">
          <div class="col-sm-4 bg-c-lite-green user-profile">
            <div class="card-block text-center text-white">
              <div class="m-b-25">
                <img src="./assets/img/logos/cropped-mminaguas.jpg" style="width: 200px; margin-top: 15px;" class="img-radius" alt="User-Profile-Image">
                <img src="./assets/img/logos/minaguas-title.svg" style="width: 200px; margin-top: 15px;">

              </div>

            </div>
          </div>
          <div class="col-sm-8" style="display: flex;">
            <div class="card-block">
              <h6 class="m-b-20 p-b-5 b-b-default f-w-600" style="margin-top: 15px;">UN HITO INNOVADOR</h6>
              <div class="row">
                <div class="col-sm-12" style="text-align: justify;">
                  <p class="m-b-10 text-sm" style="margin-right: 25px;">

                    En general, para la gestión del agua, los países de América Latina y el Caribe tienen mecanismos de coordinación a nivel del gobierno central. Venezuela se convierte en el primer país que cuenta en la actualidad con una cartera ministerial dedicada específica y exclusivamente al sector agua.

                    Este hito innovador trasciende el esquema establecido en América Latina y el Caribe donde otros sectores estratégicos y sensibles como la salud, la electricidad y la agricultura tienen atención específica pero en relación al sector agua se manejan en Ministerios de Tutela, organismos interministeriales, estructuras de alto nivel, entidades centrales, programas interentidades, mecanismos ministeriales o grupos coordinados por expertos.

                    Venezuela es hoy en día el país latinoamericano con un Ministerio dedicado exclusivamente a la Gestión Integral del Recurso Hídrico entendida como la atención a todos los usos y aprovechamiento de las aguas en fuentes (ríos, lagos, mares, acuíferos) en calidad y cantidad de aguas superficiales y subterráneas. Lo que atiende directamente la condición estratégica de nuestro territorio al ser la 4ta reserva de agua dulce en América Latina y el 11vo en el Mundo.

                  <h6 class="text-muted f-w-400"><a href="..\..\..\cdn-cgi\l\email-protection.htm" class="__cf_email__" data-cfemail="3a505f54437a5d575b535614595557"></a></h6>
                </div>
                <!-- <div class="col-sm-6">
                                                                    <p class="m-b-10 f-w-600">Agencia Bolivariana para Actividades Espaciales</p>
                                                                    <h6 class="text-muted f-w-400"></h6>
                                                                </div>
                                                            </div>
                                                            <h6 class="m-b-20 m-t-40 p-b-5 b-b-default f-w-600">Projects</h6>
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <p class="m-b-10 f-w-600">Recent</p>
                                                                    <h6 class="text-muted f-w-400">Guruable Admin</h6>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <p class="m-b-10 f-w-600">Most Viewed</p>
                                                                    <h6 class="text-muted f-w-400">Able Pro Admin</h6>
                                                                </div>-->
              </div>
              <!--<ul class="social-link list-unstyled m-t-40 m-b-10">
                                                                <li><a href="#!" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="facebook"><i class="feather icon-facebook facebook" aria-hidden="true"></i></a></li>
                                                                <li><a href="#!" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="twitter"><i class="feather icon-twitter twitter" aria-hidden="true"></i></a></li>
                                                                <li><a href="#!" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="instagram"><i class="feather icon-instagram instagram" aria-hidden="true"></i></a></li>
                                                            </ul>-->
            </div>
          </div>
        </div>

      </div>

      <!--<div class="card z-index-2 h-100">
          

          <div class="col-lg-2">
             <img src="./assets/img/minaguas.png" alt="Image" width="200px" height="100px">
           </div>
           <div class="col-lg-4">
           <div class="card-header pb-0 pt-3 bg-transparent">
                         <h6 class="text-capitalize">MINAGUAS</h6>
              <p class="text-sm mb-0">
                <i class="fa fa-arrow-up text-success"></i>
                <span class="font-weight-bold">MISIÓN</span>
               “Ejercer como Autoridad Nacional una gestión integral de las aguas, 
               elemento indispensable para la vida, el bienestar humano y el desarrollo sustentable del país,
                basada en la administración sostenible de las regiones hidrográficas e hidrogeológicas,
                 brindar un acceso justo y equitativo al servicio de agua potable, el saneamiento y sus otros usos.”
              </p>

              <p class="text-sm mb-0">
                <i class="fa fa-arrow-up text-success"></i>
                <span class="font-weight-bold">VISIÓN</span>
                “Ser el órgano del Ejecutivo Nacional que garantice la soberanía del Estado Venezolano 
                en materia de aprovechamiento responsable del recurso hídrico y la prestación de servicios de agua potable
                 y saneamiento para los ciudadanos y ciudadanas, aplicando un nuevo modelo de gestión integrador, efectivo y revolucionario,
                  con la participación protagónica del Gobierno Popular en el marco de un aprovechamiento responsable, óptimo y sostenible 
                  en todos los usos del agua, mediante la utilización de tecnología de vanguardia, liderado por servidores públicos conscientes,
                   capacitados y comprometidos en contribuir con el bienestar de la población venezolana”
              </p>
            </div>
    div>
            <div class="card-body p-3">
              <div class="chart">
                <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
              </div>
            </div>
          </div>-->
    </div>
    <div class="col-lg-3">
      <div class="card card-carousel overflow-hidden h-100 p-0">
        <div id="carouselExampleCaptions" class="carousel slide h-100" data-bs-ride="carousel">
          <div class="carousel-inner border-radius-lg h-100">

            <div class="carousel-item h-100 active">
              <img style="display: flex; margin-top:50px;" src="./assets/img/carousel/mision.png" class="d-block w-100" alt="Wild Landscape" />
            </div>
            <div class="carousel-item h-100">
              <img style="display: flex; margin-top:50px;" src="./assets/img/carousel/vision.png" class="d-block w-100" alt="Wild Landscape" />
            </div>
            <div class="carousel-item h-100">
              <img style="display: flex; margin-top:50px;" src="./assets/img/carousel/valores.png" class="d-block w-100" alt="Wild Landscape" />
            </div>
            <div class="carousel-item h-100">
              <img style="display: flex; margin-top:50px;" src="./assets/img/carousel/objetivos.png" class="d-block w-100" alt="Wild Landscape" />
            </div>
            <div class="carousel-item h-100">
              <img style="display: flex; margin-top:50px;" src="./assets/img/carousel/principios.png" class="d-block w-100" alt="Wild Landscape" />

            </div>

          </div>
        </div>
        <button style="display: flex; margin-left:10px;" class="carousel-control-prev w-5 me-3" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
          <span class="fa fa-chevron-left arrow-color" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next w-5 me-3" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
          <span class="fa fa-chevron-right arrow-color" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!--RRSS-->

<div class="container-fluid py-5">
  <div class="row justify-content-center rrss-container">
    <div class="col-xl-1 col-sm-2 mb-xl-0 mb-4 rrss-item">
      <div class="card">
        <div class="card-body p-3">
          <div class="row justify-content-center rrss-item">
            <div class="col-12 text-center">
              <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle icon-g">
                <a href="https://www.facebook.com/MinAguasVzla/" target="_blank"><i class="fa fa-facebook text-lg opacity-10 icon-g" aria-hidden="true"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-1 col-sm-2 mb-xl-0 mb-4 rrss-item">
      <div class="card">
        <div class="card-body p-3">
          <div class="row justify-content-center">
            <div class="col-12 text-center">
              <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle icon-g">
                <a href="https://twitter.com/minaguasoficial" class="download-icon" target="_blank"><i class="fa fa-twitter text-lg opacity-10 icon-g" aria-hidden="true"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-1 col-sm-2 mb-xl-0 mb-4 rrss-item">
      <div class="card">
        <div class="card-body p-3">
          <div class="row justify-content-center">
            <div class="col-12 text-center">
              <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle icon-g">
                <a href="https://www.instagram.com/mppaaguas/" class="download-icon" target="_blank"><i class="fa fa-instagram text-lg opacity-10 icon-g" aria-hidden="true"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-1 col-sm-2 mb-xl-0 mb-4 rrss-item">
      <div class="card">
        <div class="card-body p-3">
          <div class="row justify-content-center">
            <div class="col-12 text-center">
              <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle icon-g">
                <a href="https://t.me/minaguas/55" class="download-icon" target="_blank"><i class="fab fa-telegram text-lg opacity-10 icon-g" aria-hidden="true"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-1 col-sm-2 mb-xl-0 mb-4 rrss-item">
      <div class="card">
        <div class="card-body p-3">
          <div class="row justify-content-center">
            <div class="col-12 text-center">
              <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle icon-g">
                <a href="https://www.youtube.com/channel/UCMpEiajv0YEBTIr0nlA---g" class="download-icon" target="_blank"><i class="fa fa-youtube-play text-lg opacity-10 icon-g" aria-hidden="true"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-1 col-sm-2 mb-xl-0 mb-4 rrss-item">
      <div class="card">
        <div class="card-body p-3">
          <div class="row justify-content-center">
            <div class="col-12 text-center">
              <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle icon-g">
                <a href="https://www.tiktok.com/@minaguasven" class="download-icon" target="_blank"><i class="fab fa-tiktok text-lg opacity-10 icon-g" aria-hidden="true"></i></a>
              </div>
            </div>
          </div>
        </div>
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
                desarrollado por Dirección de Investigación e Innovación - ABAE

              </div>
            </div>
            
          </div>
        </div>
      </footer>-->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- <script async defer src="https://buttons.github.io/buttons.js"></script> -->
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<!--script src="./assets/js/material-dashboard.min.js?v=3.0.4"></script-->
<script src="./assets/js/core/popper.min.js"></script>
<script src="./assets/js/core/bootstrap.min.js"></script>
<script src="./assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="./assets/js/plugins/smooth-scrollbar.min.js"></script>
<script src="./assets/js/plugins/chartjs.min.js"></script>
<script src="./assets/js/jquery/jquery.min.js"></script>

<script>
  var config = {
    type: 'bar',
    data: {
      labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
      datasets: []
    },
    options: {
      scales: {
        y: {
          title: {
            display: true,
            text: 'Cantidad de Reportes'
          },
          beginAtZero: true,
          precision: 0
        }
      }
    }
  };
  var ctx = document.getElementById('myChart').getContext('2d');
  window.myChart = new Chart(ctx, config);
  $(document).ready(function() {
    cargarGrafico();

    $.ajax({
      url: './php/obtener_datos_embalses.php?id=inicial',
      type: 'GET',
      dataType: 'json',
      success: function(datos) {
        datos_inicial = datos[0];
        if (window.myChart) {
          window.myChart.destroy();
        }

        var datasets = [];
        var data = [];
        console.log('Los datos:', datos_inicial);
        $("#embalseSelect").val(datos[2]);

        for (var mes = 1; mes <= 12; mes++) {
          data.push(Math.round(datos_inicial[mes] || 0));
        }
        datasets.push({
          label: datos[1],
          data: data,
          backgroundColor: 'rgba(' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ', 0.7)',
          borderColor: 'white',
          borderWidth: 1
        });
        var config = {
          type: 'bar',
          data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datasets: datasets
          },
          options: {
            maintainAspectRatio: false,
            plugins:{
                legend: {                  
                                    labels: {

                                        // This more specific font property overrides the global property
                                        font: {
                                            size: 20
                                        },

                                    }
                                },},
            scales: {
              y: {
                title: {
                  display: true,
                  text: 'Cantidad de Reportes'
                },
                beginAtZero: true,
                precision: 0
              }
            }
          }
        };

        var ctx = document.getElementById('myChart').getContext('2d');
        window.myChart = new Chart(ctx, config);
      },
      error: function(error) {
        console.error('Error:', error);
      }
    });

  });

  function cargarGrafico() {


    $('#embalseSelect').change(function() {
      var embalseId = $(this).val();
      console.log('Hola embalse', embalseId);

      $.ajax({
        url: './php/obtener_datos_embalses.php?id=' + embalseId,
        type: 'GET',
        dataType: 'json',
        success: function(datos) {

          datos_inicial = datos[0];
          if (window.myChart) {
            window.myChart.destroy();
          }

          var datasets = [];
          var data = [];
          //console.log('Los datos:', datos_inicial);

          for (var mes = 1; mes <= 12; mes++) {
            data.push(Math.round(datos_inicial[mes] || 0));
          }
          datasets.push({
            label: $('#embalseSelect option:selected').text(),
            data: data,
            backgroundColor: 'rgba(' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ', 0.7)',
            borderColor: 'white',
            borderWidth: 3
          });
          console.log(datasets);
          var config = {
            type: 'bar',
            data: {
              labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
              datasets: datasets
            },
            options: {
              maintainAspectRatio: false,
              plugins:{
                legend: {                  
                                    labels: {

                                        // This more specific font property overrides the global property
                                        font: {
                                            size: 20
                                        },

                                    }
                                },},
              scales: {
                y: {
                  title: {
                    display: true,
                    text: 'Cantidad de Reportes'
                  },
                  beginAtZero: true,
                  precision: 0
                }
              }
            }
          };

          var ctx = document.getElementById('myChart').getContext('2d');
          window.myChart = new Chart(ctx, config);
        },
        error: function(error) {
          console.error('Error:', error);
        }
      });
    });
  }
</script>