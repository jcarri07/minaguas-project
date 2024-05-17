<?php
require_once './php/Conexion.php';

$sql = "SELECT * FROM embalses WHERE estatus = 'activo'";

$res = mysqli_query($conn, $sql);
$data = array();

if (mysqli_num_rows($res) > 0) {

  while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
  }
} else {
  // echo "No se encontraron resultados.";
}
closeConection($conn);
?>


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

<style>
  @media screen and (max-width: 767px) {

    /* Oculta la columna AFLUENTES PRINCIPALES */
    .hide-cell {
      display: none !important;
    }

    /* Ajusta el ancho de las celdas restantes para llenar el espacio */
    #table-report th.text-center,
    #table-report td.text-center {
      width: 50%;
    }
  }
</style>


<div class="container-fluid py-4 pt-5">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h4>Reportes - Embalses</h4>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-4">
            <table id="table-report" class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-center text-secondary text-sm font-weight-bolder opacity-7">NOMBRE DEL EMBALSE</th>
                  <th class="text-center text-secondary text-sm font-weight-bolder opacity-7 ps-2 hide-cell">AFLUENTES PRINCIPALES</th>
                  <th class="text-center text-uppercase text-secondary text-sm font-weight-bolder opacity-7 hide-cell">FUNCIONARIO RESPONSABLE</th>
                  <th class="text-center text-uppercase text-secondary text-sm font-weight-bolder opacity-7">REPORTES</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data as $row) { ?>
                  <tr>
                    <td class="align-middle text-center">
                      <span class="mb-0 text-sm font-weight-bolder"><?php echo $row["nombre_embalse"] ?></span>
                    </td>
                    <td class="align-middle text-center hide-cell">
                      <p class="mb-0 text-sm font-weight-bolder"><?php echo $row['afluentes_principales'] ?>&nbsp;</p>
                    </td>
                    <td class="align-middle text-center hide-cell">
                      <span class="mb-0 text-sm font-weight-bolder"><?php echo $row['nombre_presa'] ?></span>
                    </td>
                    <td class="d-flex justify-content-center align-items-center">
                      <div class="d-flex align-items-center justify-content-center w-80 gap-2 ">
                        <div class="d-flex flex-row w-100">
                          <a id="openModal" class="text-secondary font-weight-bold text-xs w-100" data-toggle="tooltip" data-original-title="Edit user">
                            <button type="button" title="Generar pdf de monitoreo del embalse" class="py-1 btn mb-0 btn-outline-secondary btn-block border-2 w-100 btn-monitoreo" data-id="<?php echo $row['id_embalse']; ?>">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
                                <path d="M32 32c17.7 0 32 14.3 32 32V400c0 8.8 7.2 16 16 16H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H80c-44.2 0-80-35.8-80-80V64C0 46.3 14.3 32 32 32zM160 224c17.7 0 32 14.3 32 32v64c0 17.7-14.3 32-32 32s-32-14.3-32-32V256c0-17.7 14.3-32 32-32zm128-64V320c0 17.7-14.3 32-32 32s-32-14.3-32-32V160c0-17.7 14.3-32 32-32s32 14.3 32 32zm64 32c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32s-32-14.3-32-32V224c0-17.7 14.3-32 32-32zM480 96V320c0 17.7-14.3 32-32 32s-32-14.3-32-32V96c0-17.7 14.3-32 32-32s32 14.3 32 32z" />
                              </svg>&nbsp;&nbsp;Monitoreo
                            </button>
                          </a>
                        </div>
                        <div class="d-flex justify-content-around w-100">
                          <a class="text-secondary font-weight-bold text-xs w-100" id="<?php echo $row['id_embalse']; ?>" onclick="getId(<?php echo $row['id_embalse']; ?>, '<?php echo addslashes($row['nombre_embalse']); ?>')" data-toggle="tooltip" data-original-title="Edit user">
                            <button type="button" title="Generar pdf de ficha técnica de este embalse" class="py-1 btn mb-0 btn-outline-secondary btn-block border-2 w-100">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                <path fill="#d42b34" d="M64 464l48 0 0 48-48 0c-35.3 0-64-28.7-64-64L0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 304l-48 0 0-144-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z" />
                              </svg>&nbsp;&nbsp;Ficha
                            </button>
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <div class="d-flex justify-content-end align-items-end w-100 pt-4 px-4 gap-2">
              <a href=" #">
                <button type="button" title="Generar pdf de embalses priorizados" class=" btn btn-outline-secondary btn-block border-2 py-2" onclick="getIdPrioritarios()">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path fill="#d42b34" d="M64 464l48 0 0 48-48 0c-35.3 0-64-28.7-64-64L0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 304l-48 0 0-144-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z" />
                  </svg>&nbsp;&nbsp;Prioritarios
                </button>
                <a type="button" title="Generar pdf de estatus embalses priorizados" class=" btn btn-outline-secondary btn-block border-2 py-2" onclick="getIdEstatus()">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path fill="#d42b34" d="M64 464l48 0 0 48-48 0c-35.3 0-64-28.7-64-64L0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 304l-48 0 0-144-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z" />
                  </svg>&nbsp;&nbsp;Estatus
                </a>
                <!-- <a type="button" title="Generar pdf de Estatus de Embalse Los Tacariguas" class=" btn btn-outline-secondary btn-block border-2 py-2" onclick="getIdTacariguas()">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
                    <path fill="#d42b34" d="M64 464l48 0 0 48-48 0c-35.3 0-64-28.7-64-64L0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 304l-48 0 0-144-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z" />
                  </svg>&nbsp;&nbsp;Embalse Tacariguas
                </a> -->
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const btnsMonitoreo = document.querySelectorAll('.btn-monitoreo');
    const id = document.getElementById('embalse_id');


    btnsMonitoreo.forEach(btn => {
      btn.addEventListener('click', function() {
        const embalseId = this.getAttribute('data-id');
        id.innerHTML = `${embalseId}`;
        const embalseNombre = this.getAttribute('data-nombre');

        document.getElementById('modal').style.display = 'block';
      });
    });

    document.querySelector('.close').addEventListener('click', function() {
      document.getElementById('modal').style.display = 'none';
    });
  });
</script>
</div>

<div id="modal" class="modal" data-id_embalse="">
  <div class="d-flex modal-content">
    <div class="d-flex flex-row justify-content-between">
      <h4>Generar Monitoreo</h4>
      <p id="embalse_id" style="display:none;"></p>
      <span class="d-flex align-items-start close">&times;</span>
    </div>
    <div class="pt-4">
      <label for="range1" class="form-label">
        <h5>Seleccione fecha</h5>
      </label>
    </div>
    <div class="d-flex pb-0 h-100 align-items-end">
      <div class="row">
        <div class="col-6 mb-4">
          <div class="input-group date" id="datepicker">
            <input type="date" class="form-control" id="date" value="<?php echo date('Y-m-d', strtotime('-1 months', strtotime(date('Y-m-d')))); ?>" max="<?php echo date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))) ?>" />
            <span class="input-group-append">
              <span class="ml-2 input-group-text bg-light d-block">
                <i class="fa fa-calendar"></i>
              </span>
            </span>
          </div>
        </div>
        <div class="col-6 mb-4">
          <div class="input-group date" id="datepicker">
            <input type="date" class="form-control" id="date2" value="<?php echo date('Y-m-d') ?>" max="<?php echo date('Y-m-d') ?>" />
            <span class="ml-2 input-group-append">
              <span class="input-group-text bg-light d-block">
                <i class="fa fa-calendar"></i>
              </span>
            </span>
          </div>
        </div>
        <div class="col-12 text-center">
          <a id="<?php echo $row['id_embalse']; ?>" onclick="getIdMonitoreo('0', '<?php echo addslashes($row['nombre_embalse']); ?>')">
            <button type="button" title="Generar pdf de embalses priorizados" class="btn btn-outline-secondary btn-block border-2 mb-0">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path fill="#d42b34" d="M64 464l48 0 0 48-48 0c-35.3 0-64-28.7-64-64L0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 304l-48 0 0-144-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z" />
              </svg>&nbsp;&nbsp;Imprimir Reporte
            </button>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
    animation: fadeIn 0.3s ease;
  }

  .modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 90%;
    /* Porcentaje del ancho de la pantalla */
    max-width: 500px;
    /* Ancho máximo del modal */
    height: 700px;
    /* Altura del modal */
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    animation: zoomIn 0.3s ease;
  }

  .close {
    display: flex;
    justify-content: end;
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }

  @keyframes zoomIn {
    from {
      transform: scale(0);
    }

    to {
      transform: scale(1);
    }
  }

  /* Media Query para dispositivos de escritorio */
  @media only screen and (min-width: 768px) {
    .modal-content {
      width: 30%;
      /* Porcentaje del ancho de la pantalla */
      height: 300px;
      max-width: 1400px;
    }
  }
</style>
<!--   Core JS Files   -->
<script>
  document.getElementById('openModal').addEventListener('click', function() {
    document.getElementById('modal').style.display = 'block';
  });

  document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('modal').style.display = 'none';
  });

  window.addEventListener('click', function(event) {
    var modal = document.getElementById('modal');
    if (event.target == modal) {
      modal.style.display = 'none';
    }
  });


  iniciarTabla('table-report');

  function getIdMonitoreo(id, name) {
    window.open('php/Graficas/grafica_monitoreo.php?fecha1=' + $("#date").val() + '&fecha2=' + $("#date2").val() + '&id=' + $("#embalse_id").html() + '&name=' + name, '_blank');
  }

  function getId(id, name) {
    window.open('pages/reports/print_ficha_tecnica.php?id=' + id + "&name=" + name, '_blank');
  }

  function getIdPrioritarios() {
    window.open('php/Graficas/graficas.php?pri=1', '_blank');
  }

  function getIdEstatus() {
    window.open('php/Graficas/mapas_estatus.php', '_blank');
  }

  function getIdTacariguas() {
    window.open('pages/reports/print_tacararigua.php', '_blank');
  }
  // Función para manejar el evento de clic en el botón
  function toggleClass() {
    var body = document.body;
    body.classList.toggle('g-sidenav-pinned');
  }

  // Obtener el botón y agregar un event listener
  var toggleButton = document.getElementById('iconNavbarSidenav');
  toggleButton.addEventListener('click', toggleClass);
</script>

<script>
  var win = navigator.platform.indexOf('Win') > -1;
  if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
      damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
  }
  <?php require_once "../php/Graficas/graficas.php" ?>
</script>