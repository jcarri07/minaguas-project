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
                  <th class="text-center text-uppercase text-secondary text-sm font-weight-bolder opacity-7">FUNCIONARIO RESPONSABLE</th>
                  <th class="text-center text-uppercase text-secondary text-sm font-weight-bolder opacity-7 hide-cell">USO ACTUAL</th>
                  <th class="text-center text-uppercase text-secondary text-sm font-weight-bolder opacity-7">REPORTES</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data as $row) { ?>
                  <tr>
                    <td class="align-middle text-center">
                      <span class="mb-0 text-md font-weight-bolder"><?php echo $row["nombre_embalse"] ?></span>
                    </td>
                    <td class="align-middle text-center">
                      <p class="mb-0 text-md font-weight-bolder"><?php echo $row['afluentes_principales'] ?>&nbsp;m³</p>
                    </td>
                    <td class="align-middle text-center">
                      <span class="mb-0 text-md font-weight-bolder"><?php echo $row['nombre_presa'] ?></span>
                    </td>
                    <td class="align-middle text-center w-20">
                      <span class="text-secondary text-xs font-weight-bolder"><?php echo $row['uso_actual'] ?></span>
                    </td>
                    <td class="d-flex justify-content-center align-items-center gap-2" style="height: 80px;">
                      <!-- <a id="<?php echo $row['id_embalse']; ?>" onclick="getIdMonitoreo(<?php echo $row['id_embalse']; ?>, '<?php echo addslashes($row['nombre_embalse']); ?>')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                        <button type="button" class="p-1 py-1 btn btn-primary btn-block bg-danger" onclick="getIdMonitoreo(<?php echo $row['id_embalse']; ?>, '<?php echo addslashes($row['nombre_embalse']); ?>')">
                          Monitoreo <i class="fas fa-file-pdf text-lg me-1 mt-1"></i>
                        </button>
                      </a> -->
                      <div class="d-flex flex-column h-100 gap-2">
                        <div class="d-flex flex-row w-100">
                          <a id="openModal" class="text-secondary font-weight-bold text-xs  w-100" style="max-height: 35px;" data-toggle="tooltip" data-original-title="Edit user">
                            <button type="button" title="Generar pdf de monitoreo del embalse" class="py-1 btn btn-outline-secondary btn-block border-2 w-100">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
                                <path d="M32 32c17.7 0 32 14.3 32 32V400c0 8.8 7.2 16 16 16H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H80c-44.2 0-80-35.8-80-80V64C0 46.3 14.3 32 32 32zM160 224c17.7 0 32 14.3 32 32v64c0 17.7-14.3 32-32 32s-32-14.3-32-32V256c0-17.7 14.3-32 32-32zm128-64V320c0 17.7-14.3 32-32 32s-32-14.3-32-32V160c0-17.7 14.3-32 32-32s32 14.3 32 32zm64 32c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32s-32-14.3-32-32V224c0-17.7 14.3-32 32-32zM480 96V320c0 17.7-14.3 32-32 32s-32-14.3-32-32V96c0-17.7 14.3-32 32-32s32 14.3 32 32z" />
                              </svg>&nbsp;&nbsp;Monitoreo
                            </button>
                          </a>
                        </div>
                        <div class="d-flex flex-row justify-content-between w-100">
                          <a class="text-secondary font-weight-bold text-xs w-100" style="max-height: 35px;" id="<?php echo $row['id_embalse']; ?>" onclick="getId(<?php echo $row['id_embalse']; ?>, '<?php echo addslashes($row['nombre_embalse']); ?>')" data-toggle="tooltip" data-original-title="Edit user">
                            <button type="button" title="Generar pdf de ficha técnica de este embalse" class="py-1 btn btn-outline-secondary btn-block border-2 w-100" onclick="getId(<?php echo $row['id_embalse']; ?>, '<?php echo addslashes($row['nombre_embalse']); ?>')">
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
            <div class="d-flex justify-content-end pt-4 pb-0">
              <a href="#">
                <button type="button" title="Generar pdf de embalses priorizados" class="btn btn-outline-secondary btn-block border-2 py-2" onclick="getIdPrioritarios()">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path fill="#d42b34" d="M64 464l48 0 0 48-48 0c-35.3 0-64-28.7-64-64L0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 304l-48 0 0-144-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z" />
                  </svg>&nbsp;&nbsp;Prioritarios
                </button>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- <footer class="footer pt-3  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                © <script>
                  document.write(new Date().getFullYear())
                </script>,
                made with <i class="fa fa-heart"></i> by
                <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Creative Tim</a>
                for a better web.
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
      </footer> -->
</div>

<!-- <button id="openModal">Abrir Modal</button> -->

<div id="modal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p>Contenido del modal...</p>
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
    max-width: 400px;
    /* Ancho máximo del modal */
    height: 300px;
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
      width: 50%;
      /* Porcentaje del ancho de la pantalla */
      height: 500px;
      max-width: 1000px;
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
    window.open('pages/reports/print_monitoreo.php?id=' + id + "&name=" + name, '_blank');
  }

  function getId(id, name) {
    window.open('pages/reports/print_ficha_tecnica.php?id=' + id + "&name=" + name, '_blank');
  }

  function getIdPrioritarios() {
    window.open('php/Graficas/graficas.php?pri=1', '_blank');
  }
  // Función para manejar el evento de clic en el botón
  /*function toggleClass() {
    var body = document.body;
    body.classList.toggle('g-sidenav-pinned');
  }

  // Obtener el botón y agregar un event listener
  var toggleButton = document.getElementById('iconNavbarSidenav');
  toggleButton.addEventListener('click', toggleClass);*/
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