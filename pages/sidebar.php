<?php
//echo $currentPage = basename($_SERVER['PHP_SELF']);
$page = isset($_GET['page']) ? $_GET['page'] : "";
?>

<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4" id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-2" href="#">
      <img src="./assets/img/logos/cropped-mminaguas.webp" class="navbar-brand-img h-100" alt="main_logo">
      <img src="./assets/img/logos/minaguas-title.svg" class="navbar-brand-img h-100" alt="main_title">
    </a>
  </div>

  <hr class="horizontal dark mt-0">
  <div class="">
    <ul class="navbar-nav">
      <div class="d-flex flex-column">
        <li class="nav-item">
          <a class="nav-link <?php echo ($page == '') ? 'active' : ''; ?>" href="?page=">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Inicio</span>
          </a>
        </li>
        <?php if($_SESSION["Tipo"] == "Admin"){
          ?>
          <li class="nav-item">
          <a class="nav-link <?php echo ($page == 'usuarios') ? "active" : ''; ?>" href="?page=usuarios">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Usuarios</span>
          </a>
        </li>
        <?php
        } ?>
        
        <li class="nav-item">
          <a class="nav-link <?php echo ($page == 'embalses') ? "active" : ''; ?>" href="?page=embalses">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-square-pin text-primary text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Embalses</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($page == 'datos') ? "active" : ''; ?>" href="?page=datos">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-calendar-grid-58 text-success text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Carga de Datos</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($page == 'reportes') ? "active" : ''; ?>" href="?page=reportes">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-bullet-list-67 text-warning text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Reportes</span>
          </a>
        </li>
      </div>
      
      <div class="d-flex flex-column col-12" style="position: absolute; bottom: 0;">
        <hr class="horizontal dark mt-0">
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Cuenta</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($page == 'perfil') ? "active" : ''; ?>" href="?page=perfil">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Editar Perfil</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link <?php echo ($page == 'logout') ? "active" : ''; ?>" href="?page=cerrar_sesion">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-button-power text-info text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Salir</span>
          </a>
        </li>
      </div>
    </ul>
  </div>
</aside>