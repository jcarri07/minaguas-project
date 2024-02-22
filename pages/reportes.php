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
          <h6>Embalses - Ficha Técnica</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-4">
            <table id="table-report" class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cuenca Principal</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 hide-cell">Area de la Cuenca</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nombre del Embalse</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 hide-cell">Uso Actual</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Reporte</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data as $row) { ?>
                  <tr>
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <span class="mb-0 text-sm"><?php echo $row["cuenca_principal"] ?></span>
                        </div>
                      </div>
                    </td>
                    <td class="hide-cell">
                      <p class="text-xs font-weight-bold mb-0"><?php echo $row['area_cuenca'] ?></p>
                      <p class="text-xs text-secondary mb-0">m³</p>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="badge badge-sm bg-gradient-success"><?php echo $row['nombre_embalse'] ?></span>
                    </td>
                    <td class="align-middle text-center hide-cell">
                      <span class="text-secondary text-xs font-weight-bold"><?php echo $row['uso_actual'] ?></span>
                    </td>
                    <td class="align-middle text-center align-center">
                      <a id="<?php echo $row['id_embalse']; ?>" onclick="getIdMonitoreo(<?php echo $row['id_embalse']; ?>, '<?php echo addslashes($row['nombre_embalse']); ?>')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                        <button type="button" class="p-1 py-1 btn btn-primary btn-block bg-danger" onclick="getIdMonitoreo(<?php echo $row['id_embalse']; ?>, '<?php echo addslashes($row['nombre_embalse']); ?>')">
                          Monitoreo <i class="fas fa-file-pdf text-lg me-1 mt-1"></i>
                        </button>
                      </a>
                      <a id="<?php echo $row['id_embalse']; ?>" onclick="getId(<?php echo $row['id_embalse']; ?>, '<?php echo addslashes($row['nombre_embalse']); ?>')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                        <button type="button" class="p-1 py-1 btn btn-primary btn-block bg-danger" onclick="getId(<?php echo $row['id_embalse']; ?>, '<?php echo addslashes($row['nombre_embalse']); ?>')">
                          Ficha Tecnica <i class="fas fa-file-pdf text-lg me-1 mt-1"></i>
                        </button>
                      </a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <div class="d-flex justify-content-end pt-4">
              <a href="#">
                <button type="button" class="btn btn-primary btn-block bg-danger" onclick="getIdPrioritarios()">
                  Embalses Prioritarios &nbsp; &nbsp;
                  <i class="fas fa-file-pdf text-lg me-1 mt-1"></i>
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


<!--   Core JS Files   -->
<script>
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
</script>