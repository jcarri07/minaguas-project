<?php
<<<<<<< HEAD
  // print "<script>window.location='maintenance.php';</script>";
=======
 // print "<script>window.location='maintenance.php';</script>";
>>>>>>> 7264a0afbdc806b1af4a657dc836227f1b7fbae7
  if (!isset($_SESSION)) {
    session_start();
  };
  if(isset($_SESSION["Id_usuario"])){
    
    print "<script>window.location='main.php';</script>";
  }

  date_default_timezone_set("America/Caracas");
?>
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

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./assets/img/logos/cropped-mminaguas.webp">
  <title>
    Minaguas
  </title> 
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="./assets/js/fontawesome/42d5adcbca.js"></script>
  <script src="./assets//js/sweetalerts.js"></script>
  <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="./assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />
</head>

<body class="" style="background-image: linear-gradient(rgb(20,28,49,0.8),rgb(20,28,49,0.8)),url(./assets/img/world-map-wallpaper.jpg);overflow-x:hidden;overflow-y:auto">


  <!-- Navbar -->
  <!--nav class="navbar navbar-expand-lg blur border-radius-lg top-0 z-index-3 shadow position-absolute mt-4 py-2 start-0 end-0 mx-4" >
          <div class="container-fluid">
            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 " href="../pages/dashboard.html">
              Argon Dashboard 2
            </a>
            <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon mt-2">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </span>
            </button>
            <div class="collapse navbar-collapse" id="navigation">
              <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                  <a class="nav-link d-flex align-items-center me-2 active" aria-current="page" href="../pages/dashboard.html">
                    <i class="fa fa-chart-pie opacity-6 text-dark me-1"></i>
                    Dashboard
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link me-2" href="../pages/profile.html">
                    <i class="fa fa-user opacity-6 text-dark me-1"></i>
                    Profile
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link me-2" href="../pages/sign-up.html">
                    <i class="fas fa-user-circle opacity-6 text-dark me-1"></i>
                    Sign Up
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link me-2" href="../pages/sign-in.html">
                    <i class="fas fa-key opacity-6 text-dark me-1"></i>
                    Sign In
                  </a>
                </li>
              </ul>
              <ul class="navbar-nav d-lg-block d-none">
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/product/argon-dashboard" class="btn btn-sm mb-0 me-1 btn-primary">Free Download</a>
                </li>
              </ul>
            </div>
          </div>
        </nav-->
  <!-- End Navbar -->


  <main class="main-content mt-0 h-100">

    <section class="row">
      <div class="col-12 d-flex justify-content-center d-none d-sm-flex" style="height: 120px; background-color:#0081d5;">
        <img src="./assets/img/mina.png" class="col-xxl-8 col-xl-9 col-lg-10 col-md-12 col-sm-12 col-xs-12" style="background-position: center;object-fit:cover;">
      </div>
      <div class="col-12 justify-content-center">
        <div class="container ">


          <div class="row my-7 justify-content-center">
            <div class="col-xl-6 col-lg-6 col-md-9 col-sm-8 col-xs-5 d-flex flex-column mx-lg-0 justify-content-center">
              <div class="px-xl-6 px-md-4">
                <div class="card card-plain" style="background-color: white;">
                  <div class="card-header pb-0 text-start">
                    <h4 class="font-weight-bolder">Inicie Sesion</h4>
                    <!--p class="mb-0">Enter your email and password to sign in</p-->
                  </div>
                  <div class="card-body">
                    <form role="form" id="form">
                      <div class="mb-3">
                        <input type="email" class="form-control form-control-lg" placeholder="Email" aria-label="Email" id="email">
                      </div>
                      <div class="mb-3">
                        <input type="password" class="form-control form-control-lg" placeholder="Contraseña" aria-label="Contraseña" id="pass">
                      </div>
                      <!--div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="rememberMe">
                      <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div-->
                      <div class="text-center">
                        <button type="submit" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">Iniciar Sesion</button>
                      </div>
                    </form>
                  </div>
                  <div class="card-footer text-center pt-0 px-lg-2 px-1">
                    <p class="mb-4 text-sm mx-auto">
                      Usuario nuevo?
                      <a href="./pages/sign-up.php" class="text-primary text-gradient font-weight-bold">Nuevo Usuario</a>
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-9 col-sm-8 col-sx-5 d-flex flex-column mx-lg-0 justify-content-center">
              <div class="m-3 border-radius-lg d-flex flex-column justify-content-center px-lg-2">
                <!--span class="mask bg-gradient-primary opacity-6" style="background-size: cover;"></span-->
                <img src="./assets/img/minaguas.png" class="px-lg-4 px-xl-6" style="object-fit:cover;">
                <p class="text-white position-relative text-center px-lg-3 d-none d-sm-block">“Ejercer como Autoridad Nacional una gestión integral de las aguas, elemento indispensable para la vida, el bienestar humano y el desarrollo sustentable del país.”</p>
              </div>

            </div>
          </div>

        </div>
      </div>

    </section>
  </main>
  <footer class="footer">
    <div class="container pb-4">
      <!--div class="row">
        <div class="col-lg-8 mb-4 mx-auto text-center">
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
            Company
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
            About Us
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
            Team
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
            Products
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
            Blog
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
            Pricing
          </a>
        </div>
        <div class="col-lg-8 mx-auto text-center mb-4 mt-2">
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
            <span class="text-lg fab fa-dribbble"></span>
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
            <span class="text-lg fab fa-twitter"></span>
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
            <span class="text-lg fab fa-instagram"></span>
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
            <span class="text-lg fab fa-pinterest"></span>
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
            <span class="text-lg fab fa-github"></span>
          </a>
        </div>
      </div-->
      <div class="row">
        <div class="col-8 mx-auto text-center mt-1">
          <p class="mb-0" style="color: white;">
            Copyright © <script>
              document.write(new Date().getFullYear())
            </script> Desarrollado por la Dirección de Investigación e Innovación Espacial - ABAE.
          </p>
        </div>
      </div>
    </div>
  </footer>
  </div>
  <!--   Core JS Files   -->
  <script src="./assets/js/core/popper.min.js"></script>
  <script src="./assets/js/core/bootstrap.min.js"></script>
  <!--script src="../assets/js/plugins/perfect-scrollbar.min.js"></script-->
  <!--script src="../assets/js/plugins/smooth-scrollbar.min.js"></script-->
  <script type="text/javascript" src="./assets/js/jquery/jquery.min.js"></script>
  <script type="text/javascript" src="./assets/js/jquery-ui/jquery-ui.min.js"></script>
  <!--script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script-->
  <script>
    $(document).ready(function() {
          $("#form").submit(function(e) {
            e.preventDefault();
            var values = new FormData();

            values.append("email", $("#email").val());
            values.append("pass", $("#pass").val());

            $.ajax({
              url: 'php/login/comp-usuario.php',
              type: 'POST',
              data: values,
              cache: false,
              contentType: false,
              processData: false,
              success: function(response) {
                switch (response) {
                  case "si":
                    Swal.fire({
                      icon: 'success',
                      title: 'Sesión Iniciada',
                      showConfirmButton: false,
                      timer: 1500
                    }); //CUANDO INICIA SESION
                    setTimeout(function() {
                      window.location = "index.php";
                    }, 1500);
                    break;
                  case "no":
                    Swal.fire({
                      icon: 'warning',
                      title: 'Error en verificación',
                      text: 'Nombre de usuario o contraseña incorrecto',
                      confirmButtonText: 'Aceptar',
                      confirmButtonColor: '#01a9ac',
                    }); //CONTRASEÑA O USUARIO INCORRECTOS
                    break;
                  default:
                    console.log(response);
                    break;
                }

              },
              error: function(response) {

              }
            });

          });
        });
  </script>
  <!-- Github buttons >
  <script async defer src="https://buttons.github.io/buttons.js"></script-->
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <!--script src="./assets/js/argon-dashboard.min.js?v=2.0.4"></script-->

</body>

</html>