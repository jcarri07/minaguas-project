<?php
    $currentPage = basename($_SERVER['PHP_SELF']);
    echo "Current Page: $currentPage";
?>

<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-2" href="#">
        <img src="../assets/img/logos/cropped-mminaguas.webp" class="navbar-brand-img h-100" alt="main_logo">
        <img src="../assets/img/logos//minaguas-title.svg" class="navbar-brand-img h-100" alt="main_title">
      </a>
    </div>

    <hr class="horizontal dark mt-0">
    <div class="s">
      <ul class="navbar-nav">
        <div class="d-flex flex-column">
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" href="../index.php">
                  <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Dashboard</span>
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'usuarios.php') ? "active" : ''; ?>" href="/minaguas-project/pages/usuarios.php">
                  <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                  <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Usuarios</span>
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'embalses.php') ? "active" : ''; ?>" href="/minaguas-project/pages/embalses.php">
                  <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-chart-pie-35 text-primary text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Embalses</span>
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'datos.php') ? "active" : ''; ?>" href="/minaguas-project/pages/datos.php">
                  <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                  <i class="ni ni-credit-card text-success text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Carga de Datos</span>
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'reportes.php') ? "active" : ''; ?>" href="/minaguas-project/pages/reportes.php">
                  <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                  <i class="ni ni-calendar-grid-58 text-warning text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Reportes</span>
              </a>
          </li>
        </div>
        <div class="d-flex flex-column">
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account pages</h6>
        </li>
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'perfil.php') ? "active" : ''; ?>" href="/minaguas-project/pages/perfil.php">
                  <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                  <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Perfil</span>
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'sign-in.php') ? "active" : ''; ?>" href="/minaguas-project/pages/sign-in.php">
                  <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                  <i class="ni ni-single-copy-04 text-warning text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Iniciar sesion</span>
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link <?php echo ($currentPage == 'sign-up.php') ? "active" : ''; ?>" href="/minaguas-project/pages/sign-up.php">
                  <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                  <i class="ni ni-collection text-info text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Registrarse</span>
              </a>
          </li>
        </div>
      </ul>
      </div>
  </aside>