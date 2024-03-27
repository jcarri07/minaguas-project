<?php
require_once 'php/Conexion.php';
require_once 'php/batimetria.php';

$id_embalse = "";
$embalse = [];
if (isset($_SESSION['id_embalse'])) {
  $id_embalse = $_SESSION['id_embalse'];
  $embalse = new Batimetria($id_embalse, $conn);
}

$embalse_datos = $embalse->getEmbalse();

$vida_restante = "";
if ($embalse_datos['inicio_de_operacion'] != "") {
  $fecha_inicio = new DateTime($embalse_datos['inicio_de_operacion']);
  $hoy = new DateTime();
  $restante = $hoy->diff($fecha_inicio);
  $vida_restante = $restante->y;
} else {
  $vida_restante = "";
}

$proposito_array = explode(" - ", $embalse_datos['proposito']);
$uso_array = explode(" - ", $embalse_datos['uso_actual']);

$proposito = [];
$uso = [];

$query = mysqli_query($conn, "SELECT * FROM propositos");

while ($row = mysqli_fetch_array($query)) {
  if (in_array($row['id_proposito'], $proposito_array)) {
    array_push($proposito, $row['proposito']);
  }
  if (in_array($row['id_proposito'], $uso_array)) {
    array_push($uso, $row['proposito']);
  }
}


closeConection($conn);
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.7.5/proj4.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.min.js"></script> -->
<script src="./assets/js/Chart.js"></script>

<link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
<script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>

<style>

  #embalse-mapa {
    height: 100%;
    width: 100%;
  }

  @media(width >=2120px) {

    .embalse-info {
      width: auto;
      height: 100%;
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      grid-template-rows: 1fr 1fr;
      gap: 20px;
    }

    .embalse-card {
      background-color: white;
      /* height: 372.21px !important; */
      height: 100%;
      border: 3px dashed lightgrey;
      width: 574.45px !important;
      /* width: 100% !important; */
    }

    .embalse-batimetria {
      grid-row: span 2;
    }

  }

  @media(width <=2120px) {

    .embalse-info {
      width: auto;
      height: 100%;
      display: grid;
      grid-template-columns: 1fr 1fr;
      grid-template-rows: 1fr 1fr 1fr;
      gap: 20px;
    }

    .embalse-card {
      background-color: white;
      /* height: 372.21px !important; */
      height: 100%;
      border: 3px dashed lightgrey;
      /* width: 574.45px !important; */
      /* width: 100%; */
    }

    .embalse-batimetria {
      grid-row: span 2;
    }

  }

  @media(width <=1478px) {

    .embalse-info {
      width: auto;
      height: 100%;
      display: grid;
      grid-template-columns: 1fr;
      /* grid-template-rows: 1fr 1fr 1fr; */
      gap: 20px;
    }

    .embalse-card {
      background-color: white;
      /* height: 372.21px !important; */
      height: 100%;
      border: 3px dashed lightgrey;
      /* width: 574.45px !important; */
      width: 100%;
    }

    .embalse-batimetria {
      /* grid-row: span 2; */
    }

    .embalse-datos {
      order: -1;
      width: 100%;
    }

    /* .dataTables_wrapper{
      background-color: red !important;
      width: 80% !important;
      padding: 0 !important;
      margin: 0 !important;
    } */

    .pagination {
      /* width: 80% !important; */
      display: flex;
      flex-wrap: wrap;
    }
  }

  @media(width <= 1000px){
    .card-flex{
      flex-direction: column;
    }
  }

  @media(width <=600px) {
    .embalse-caracteristicas {
      font-size: 12px;
    }
  }

  @media(width <=550px) {
    .embalse-caracteristicas {
      font-size: 8px;
    }

  }

  .body-show {
    /* background: red; */
    height: 100vh;
  }

  .container-show {
    height: 85%;
    /* background-color: green; */
  }

  .card-show {
    height: 100%;
  }



  canvas {

    width: 100% !important;
    height: 100% !important;

  }

  .card-mapa {
    position: relative;
  }

  .nombre-embalse {
    position: absolute;
    bottom: 0;
    right: 0;
    z-index: 9999;
    background-color: white;
    width: 100%;
    text-align: center;
    border-radius: 0px 0px 0.375rem 0.375rem;
  }

  .b-t {
    border-top: 1px solid lightslategrey;
  }

  .b-b {
    border-bottom: 1px solid lightslategrey;
  }

  .b-r {
    border-right: 1px solid lightslategrey;
  }

  .b-l {
    border-left: 1px solid lightslategrey;
  }

  .border-cell {
    border: 1px solid lightslategrey;
  }

  .mini-card-info {
    border: 2px dashed lightgray;
    padding-top: 2px;
    padding-left: 8px;
    padding-right: 8px;
  }

  .card-batimetria {
    overflow-x: auto;
    overflow-y: auto;
  }

  .tabla {
    display: inline-block;
    vertical-align: top;
    white-space: normal;
    margin: 0 25px;
    text-align: center;
  }

  .table-cota {
    border: 1px solid #000000;
  }

  .tabla table {
    width: 100%;
    border-collapse: collapse !important;
  }

  .tabla td,
  .tabla th {
    border: 1px solid #dddddd;
    text-align: center;
    vertical-align: middle;
    padding: 8px;
  }

  /* .main-content{
  background-color: blue;
  height: 100vh;
}  */
</style>

<div class="container-fluid container-show py-4">

  <div class="row card-show">
    <div class="col-lg-12">
      <div class="card h-100 ">
        <div class="rounded embalse-info m-4 ">
          <div class="embalse-card rounded card-mapa">
            <div id="embalse-mapa" class="rounded">
            </div>
            <div class="nombre-embalse text-dark fw-bold">Ubicación geográfica</div>
          </div>
          <div class="embalse-card embalse-datos rounded px-3 d-flex flex-column gap-2">
            <div class="text-center" style="font-size: 32px;">
              <?php echo mb_strtoupper(mb_substr($embalse_datos["nombre_embalse"], 0), 'UTF-8') ?>
            </div>
            <div class="w-100 d-flex gap-4 card-flex">
              <div class="w-100 h-100 rounded mini-card-info">
                <div>
                  <span class="fw-bold" for="">Nombre de la presa:</span>
                  <p><?php echo $embalse_datos["nombre_presa"] ?></p>
                </div>
                <div>
                  <span class="fw-bold" for="">Cuenca principal:</span>
                  <p><?php echo $embalse_datos["cuenca_principal"] ?></p>
                </div>
                <div>
                  <span class="fw-bold" for="">Área de la cuenca:</span>
                  <p><?php echo $embalse_datos["area_cuenca"] ?> ha</p>
                </div>
                <div>
                  <span class="fw-bold" for="">Propósitos:</span>
                  <p><?php echo implode(", ", $proposito) ?></p>
                </div>
              </div>
              <div class="w-100 h-100 rounded mini-card-info">
                <div>
                  <span class="fw-bold" for="">Operador:</span>
                  <p><?php echo $embalse_datos["operador"] ?></p>
                </div>
                <div>
                  <span class="fw-bold" for="">Vida útil:</span>
                  <p><?php echo $embalse_datos["vida_util"] ?> años</p>
                </div>
                <div>
                  <span class="fw-bold" for="">Vida útil restante:</span>
                  <p><?php echo $vida_restante ?> años</p>
                </div>
                <div>
                  <span class="fw-bold" for="">Úso actual:</span>
                  <p><?php echo implode(", ", $uso) ?></p>
                </div>
              </div>
            </div>
          </div>
          <div class="embalse-card rounded p-3 embalse-batimetria d-flex flex-column gap-4">
            <!-- <div class="text-center">Batimetrias [ <?php echo implode(" - ", $embalse->getYears()) ?> ]</div> -->
            <div class="text-center">Batimetrias</div>
            <div class="text-center h-100 d-flex flex-column">

              <ul class="nav nav-tabs" role="tablist">
                <?php foreach ($embalse->getYears() as $key => $year) { ?>
                  <li class="nav-item" role="presentation">
                    <a class="nav-link <?php if ($key == 0) echo "active" ?>" id="simple-tab-<?php echo $year ?>" data-bs-toggle="tab" href="#simple-tabpanel-<?php echo $year ?>" role="tab" aria-controls="simple-tabpanel-<?php echo $year ?>" aria-selected="true"><?php echo $year ?></a>
                  </li>
                <?php } ?>
              </ul>

              <div class="tab-content pt-2" id="tab-content">

                <?php if ($embalse_datos["batimetria"] != "") {
                  $pre_batimetria = json_decode($embalse_datos["batimetria"], true);
                  $count = 0;
                  foreach ($pre_batimetria as $key => $anio) {


                ?>
                    <div class="tab-pane <?php if ($count == 0) {
                                            echo "active";
                                            $count++;
                                          } ?>" id="simple-tabpanel-<?php echo $key ?>" role="tabpanel" aria-labelledby="simple-tab-<?php echo $key ?>">
                      <div class="tabla table-responsive">
                        <p> <?php echo $key ?> </p>
                        <table id="tabla<?php echo $key ?>" class="align-items-center mb-0 table-cota ">
                          <thead>
                            <tr>
                              <th>Cota <span class="text-sm">(m s.n.m.)</span></th>
                              <th>Área <span></span class="text-sm">(m²)</th>
                              <th>Capacidad <span class="text-sm">(hm³)</span></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            foreach ($anio as $key => $value) {
                              $partes = explode("-", $value);
                            ?>
                              <tr>
                                <td><?php echo number_format(floatval($key), 3, ',', '.') ?></td>
                                <td><?php echo number_format(floatval($partes[0]), 2, ',', '.') ?></td>
                                <td><?php echo number_format(floatval($partes[1]), 2, ',', '.') ?></td>
                              </tr>
                            <?php }
                            ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                <?php }
                } ?>
              </div>


            </div>
          </div>
          <div class="embalse-card rounded p-3 text-sm text-dark d-flex flex-column justify-content-center gap-5">
            <div class="text-center" style="font-size: 30px;">
              Características del embalse
            </div>
            <div class="w-full embalse-caracteristicas">
              <div style="display: grid; grid-template-columns: 25% 25%; justify-content: end; justify-items:center;">
                <div class="w-100 text-center b-l b-t">Diseño</div>
                <div class="w-100 text-center b-l b-t b-r">Actual</div>
              </div>
              <div style="display: grid; grid-template-columns: 25% 25% 50%; justify-items:center; align-items: center">
                <div class="border-cell w-100 h-100 d-flex flex-column justify-content-center">
                  <div class="w-100 text-center">Mínimo</div>
                </div>
                <div class="w-100 d-flex flex-column">
                  <div class="w-100 b-t">
                    <div class="px-2">Cota (m s.n.m)</div>
                  </div>
                  <div class="w-100 b-t">
                    <div class="px-2">Volumen (hm³)</div>
                  </div>
                  <div class="w-100 b-t">
                    <div class="px-2">Superficie (ha)</div>
                  </div>
                </div>
                <div class="w-100" style="display: grid; grid-template-columns: 50% 50%; justify-items:center;">
                  <div class="w-100 text-center b-t b-l b-r" style="grid-column: span 2;"><?php echo $embalse->cotaMinima() ?></div>
                  <div class="w-100 text-center b-t b-l b-r"><?php echo number_format($embalse_datos["vol_min"], 2, ".", "") ?></div>
                  <div class="w-100 text-center b-t b-r"><?php echo number_format($embalse->volumenMinimo(), 2, ".", "") ?></div>
                  <div class="w-100 text-center b-t b-l b-r"><?php echo number_format($embalse_datos["sup_min"], 2, ".", "") ?></div>
                  <div class="w-100 text-center b-t b-r"><?php echo number_format($embalse->superficieMinima(), 2, ".", "") ?></div>
                </div>
              </div>
              <div style="display: grid; grid-template-columns: 25% 25% 50%; justify-items:center; align-items: center">
                <div class="b-l b-r w-100 h-100 d-flex flex-column justify-content-center">
                  <div class="w-100 text-center">Normal</div>
                </div>
                <div class="w-100 d-flex flex-column">
                  <div class="w-100 b-t">
                    <div class="px-2">Cota (m s.n.m)</div>
                  </div>
                  <div class="w-100 b-t">
                    <div class="px-2">Volumen (hm³)</div>
                  </div>
                  <div class="w-100 b-t">
                    <div class="px-2">Superficie (ha)</div>
                  </div>
                </div>
                <div class="w-100" style="display: grid; grid-template-columns: 50% 50%; justify-items:center;">
                  <div class="w-100 text-center b-t b-l b-r" style="grid-column: span 2;"><?php echo $embalse->cotaNormal() ?></div>
                  <div class="w-100 text-center b-t b-l b-r"><?php echo number_format($embalse_datos["vol_nor"], 2, ".", "") ?></div>
                  <div class="w-100 text-center b-t b-r"><?php echo number_format($embalse->volumenNormal(), 2, ".", "") ?></div>
                  <div class="w-100 text-center b-t b-l b-r"><?php echo number_format($embalse_datos["sup_nor"], 2, ".", "") ?></div>
                  <div class="w-100 text-center b-t b-r"><?php echo number_format($embalse->superficieNormal(), 2, ".", "") ?></div>
                </div>
              </div>
              <div style="display: grid; grid-template-columns: 25% 25% 50%; justify-items:center; align-items: center">
                <div class="border-cell w-100 h-100 d-flex flex-column justify-content-center">
                  <div class="w-100 text-center">Máximo</div>
                </div>
                <div class="w-100 d-flex flex-column">
                  <div class="w-100 b-t">
                    <div class="px-2">Cota (m s.n.m)</div>
                  </div>
                  <div class="w-100 b-t">
                    <div class="px-2">Volumen (hm³)</div>
                  </div>
                  <div class="w-100 b-t b-b">
                    <div class="px-2">Superficie (ha)</div>
                  </div>
                </div>
                <div class="w-100" style="display: grid; grid-template-columns: 50% 50%; justify-items:center;">
                  <div class="w-100 text-center b-t b-l b-r" style="grid-column: span 2;"><?php echo $embalse->cotaMaxima() ?></div>
                  <div class="w-100 text-center b-t b-l b-r"><?php echo number_format($embalse_datos["vol_max"], 2, ".", "") ?></div>
                  <div class="w-100 text-center b-t b-r"><?php echo number_format($embalse->volumenMaximo(), 2, ".", "") ?></div>
                  <div class="w-100 text-center b-t b-l b-r b-b"><?php echo number_format($embalse_datos["sup_max"], 2, ".", "") ?></div>
                  <div class="w-100 text-center b-t b-r b-b"><?php echo number_format($embalse->superficieMaxima(), 2, ".", "") ?></div>
                </div>
              </div>
            </div>
          </div>
          <div class="embalse-card rounded p-3 d-flex flex-column justify-content-center">
            <!-- <div class="ct-chart ct-perfect-fourth" style="width: 100%; height: 100%"></div> -->
            <div class="chart-js" style="width: 100%; height: 90%">
              <canvas id="chart-vol">

              </canvas>
            </div>
          </div>
          <input style="display: none;" type="text" name="" id="norte" value="<?php echo $embalse_datos['norte'] ?>">
          <input style="display: none;" type="text" name="" id="este" value="<?php echo $embalse_datos['este'] ?>">
          <input style="display: none;" type="text" name="" id="huso" value="<?php echo $embalse_datos['huso'] ?>">
        </div>
      </div>
    </div>
  </div>

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



  var tablas = $(".table-cota");

  for (let i = 0; i < tablas.length; i++) {
    console.log($(tablas[i])[0].id)
    // iniciarTabla($(tablas[i])[0].id);
    id = $(tablas[i])[0].id;
    console.log($(tablas[i]))

    $("#" + id).DataTable({
      dom: "<'top'<'d-flex align-items-center justify-content-between'f>>rt<'bottom'<'d-flex flex-column align-items-center'p>><'clear'>",
      language: {
        "decimal": "",
        "emptyTable": "No hay información",
        "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
        "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
        "infoFiltered": "(Filtrado de _MAX_ total entradas)",
        "infoPostFix": "",
        "thousands": ",",
        "lengthMenu": "Mostrar _MENU_ Entradas",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "search": "Buscar:",
        "zeroRecords": "Sin resultados encontrados",
        "paginate": {
          "first": "Primero",
          "last": "Ultimo",
          "next": "Siguiente",
          "previous": "Anterior"
        }
      },

    });
  }

  // var map = L.map('embalse-mapa').setView([8, -66], 6);

  // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { // Utilizar un proveedor de azulejos de OpenStreetMap
  //   attribution: '© OpenStreetMap contributors'
  // }).addTo(map);

  var norte = $("#norte").val();
  var este = $("#este").val();
  var huso = $("#huso").val();

  if (norte != "" && este != "" && huso != "") {

    norte = parseFloat(norte);
    este = parseFloat(este);
    huso = parseInt(huso)

    proj4.defs("EPSG:326" + huso, "+proj=utm +zone=" + huso + " +datum=WGS84 +units=m +no_defs");

    proj4.defs("EPSG:4326", "+proj=longlat +datum=WGS84 +no_defs");
    var coordenadasGeograficas = proj4("EPSG:326" + huso, "EPSG:4326", [este, norte]);

    var latitud = coordenadasGeograficas[1];
    var longitud = coordenadasGeograficas[0];

    var map = L.map('embalse-mapa').setView([latitud, longitud], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    L.marker([latitud, longitud]).addTo(map);

  } else {
    var map = L.map('embalse-mapa').setView([8, -66], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { // Utilizar un proveedor de azulejos de OpenStreetMap
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);
  }
  // new Chartist.Bar('.ct-chart', {
  //   labels: ['Original', 'Batimetria', 'Actual'],
  //   series: [
  //     [800000, 1200000, 1400000]
  //   ]
  // }, {
  //   stackBars: false,
  //   axisY: {
  //     labelInterpolationFnc: function(value) {
  //       return (value / 1000) + 'k';
  //     }
  //   }
  // }).on('draw', function(data) {
  //   if (data.type === 'bar') {
  //     data.element.attr({
  //       style: 'stroke-width: 30px'
  //     });
  //   }
  // });


  const ctx = document.getElementById('chart-vol');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Diseño', 'Batimetría', 'Actual'],
      datasets: [{
        label: 'Volumenes',
        data: [12, 19, 3],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        x: {
          ticks: {
            fonts: {
              size: 18
            }
          }
        },
        y: {
          ticks: {
            fonts: {
              size: 18
            }
          }
        }
      },
      responsive: true,
      mantainAspectRatio: false
    }
  });




  // console.log(tablas)
  // iniciarTabla('table-embalses');
  //   if ($("#table-embalses-eliminados")) {
  //     iniciarTabla('table-embalses-eliminados');
  //   }
</script>