<?php
if (!isset($_SESSION)) {
  session_start();
};
if (isset($_SESSION["Id_usuario"])) {

  print "<script>window.location='../main.php';</script>";
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
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/logos/cropped-mminaguas.webp">
  <title>
    Minaguas
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="../assets/js/fontawesome/42d5adcbca.js"></script>
  <script src="../assets/js/sweetalerts.js"></script>
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />
</head>

<body style="background-image: linear-gradient(rgb(20,28,49,0.8),rgb(20,28,49,0.8)),url(../assets/img/world-map-wallpaper.jpg);overflow-x:hidden;overflow-y:auto">
  <!-- Navbar -->
  <!--nav class="navbar navbar-expand-lg position-absolute top-0 z-index-3 w-100 shadow-none my-3 navbar-transparent mt-4">
    <div class="container" style="text-align: left;">
      <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 text-white ni ni-bold-left" href="sign-in.php" >
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
              <i class="fa fa-chart-pie opacity-6  me-1"></i>
              Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link me-2" href="../pages/profile.html">
              <i class="fa fa-user opacity-6  me-1"></i>
              Profile
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link me-2" href="../pages/sign-up.html">
              <i class="fas fa-user-circle opacity-6  me-1"></i>
              Sign Up
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link me-2" href="../pages/sign-in.html">
              <i class="fas fa-key opacity-6  me-1"></i>
              Sign In
            </a>
          </li>
        </ul>
        <ul class="navbar-nav d-lg-block d-none">
          <li class="nav-item">
            <a href="https://www.creative-tim.com/product/argon-dashboard" class="btn btn-sm mb-0 me-1 bg-gradient-light">Free Download</a>
          </li>
        </ul>
      </div>
    </div>
  </nav-->
  <!-- End Navbar -->
  <main class="main-content mt-0 h-75">

    <section class="row">
      <div class="col-12 d-flex justify-content-center d-none d-sm-flex" style="height: 120px; background-color:#0081d5;">
        <img src="../assets/img/mina.png" class="col-xxl-8 col-xl-9 col-lg-10 col-md-12 col-sm-12 col-xs-12" style="background-position: center;object-fit:cover;">
      </div>
      <div class="col-12 justify-content-center">
        <div class="container ">


          <div class="row my-6 justify-content-center">
            <div class="col-xl-6 col-lg-6 col-md-9 col-sm-8 col-xs-5 d-flex flex-column mx-lg-0 justify-content-center">
              <div class="px-xl-6 px-md-4">
                <div class="card card-plain" style="background-color: white;">
                  <div class="card-header text-center pt-4">
                    <h5>Introduzca sus datos de registro en las casillas correspondientes</h5>
                  </div>
                  <div class="card-body">

                    <form id="form" role="form">
                      <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Nombre Completo" aria-label="nombres" name="nombres" required>
                      </div>
                      <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Apellidos" aria-label="apellidos" name="apellidos" required>
                      </div>
                      <div class="mb-3">
                        <input type="email" class="form-control" placeholder="Email" aria-label="Email" name="email" required>
                      </div>
                      <div class="row ">
                        <div class="mb-3 col-6">
                          <input type="text" class="form-control" placeholder="Telefono" aria-label="telefono" pattern="[0-9]{1,11}" id="telefono" name="telefono" required>
                        </div>
                        <div class="mb-3 col-6">
                          <input type="text" class="form-control" placeholder="Cedula" aria-label="cedula" pattern="[0-9]{8}" name="cedula" required>
                        </div>
                      </div>
                      <!--div class="mb-3">
                        <input type="text" class="form-control" placeholder="usuario" aria-label="usuario" name="usuario" required>
                      </div-->
                      <div class="row ">
                        <div class="mb-3 col-6">
                          <input type="password" class="form-control" placeholder="Contraseña" aria-label="Password" name="password" required>
                        </div>
                        <div class="mb-3 col-6">
                          <input type="password" class="form-control" placeholder="Repetir Contraseña" aria-label="confirmar" name="confirmar" required>
                        </div>

                      </div>

                      <!--div class="form-check form-check-info text-start">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
                  <label class="form-check-label" for="flexCheckDefault">
                    I agree the <a href="javascript:;" class="text-dark font-weight-bolder">Terms and Conditions</a>
                  </label>
                </div-->
                      <div class="text-center">
                        <a href="sign-in.php"><button type="submit" class="btn btn-primary w-100 my-4 mb-2">Crear Usuario</button></a>
                      </div>
                      <div class="text-center">
                        <p class="text-sm mt-3 mb-0">Ya tienes una Cuenta? <a href="../index.php" class="text-primary font-weight-bolder">Iniciar Sesion</a></p>
                      </div>
                    </form>
                  </div>

                </div>
              </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-9 col-sm-8 col-sx-5 d-flex flex-column mx-lg-0 justify-content-center">
              <div class="m-3 border-radius-lg d-flex flex-column justify-content-center px-lg-2">
                <!--span class="mask bg-gradient-primary opacity-6" style="background-size: cover;"></span-->

                <img src="../assets/img/computadora.png" class="px-lg-4 px-xl-6" style="object-fit:cover;">

                <p class="text-white position-relative text-left px-lg-3 mt-4">Es un proceso sencillo, solo debe llenar las casillas con sus datos y podrá disponer de una cuenta para ingresar al sistema.</p>
                <div class="row" style="padding-left: 27px;">
                  <div class="row col-12">
                    <div class="col-1" style="height: 20px;padding:1px;">
                      <img src="../assets/img/icons/seguridad.png" class="icon-xxs">
                    </div>
                    <p class="col-11" style="padding-left: 0;">Seguridad y Eficiencia</p>
                  </div>
                  <div class="row col-12">
                    <div class="col-1" style="height: 20px;padding:1px;">
                      <img src="../assets/img/icons/rapidez.png" class="icon-xxs">
                    </div>
                    <p class="col-11" style="padding-left: 0;">Rapidez y efectividad</p>
                  </div>
                </div>
              </div>

            </div>




          </div>

        </div>
      </div>

    </section>
  </main>

  <!-- -------- START FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
  <footer class="footer ">
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
            </script> Desarrolládo por la Dirección de Investigación eh Innovación - ABAE.
          </p>
        </div>
      </div>
    </div>
  </footer>
  <!-- -------- END FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <!--script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script-->
  <script type="text/javascript" src="../assets/js/jquery/jquery.min.js"></script>
  <script type="text/javascript" src="../assets/js/jquery-ui/jquery-ui.min.js"></script>
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

          /*if ($("[name='nombres']").prop("value").split(' ').filter(function(n) {
              return n != ''
            }).length != 2) {
            Swal.fire({
              icon: 'warning',
              title: 'Ingrese sus 2 Nombres',
              confirmButtonText: 'Aceptar',
              confirmButtonColor: '#01a9ac',
            });
          } else {
            if ($("[name='apellidos']").prop("value").split(' ').filter(function(n) {
                return n != ''
              }).length != 2) {
              Swal.fire({
                icon: 'warning',
                title: 'Ingrese sus 2 Apellidos',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#01a9ac',
              });
            } else {*/
          if ($("[name='password']").prop("value") == $("[name='confirmar']").prop("value")) {
            var values = new FormData();

            values.append("nombre", $("[name='nombres']").prop("value"));
            values.append("apellido", $("[name='apellidos']").prop("value"));
            values.append("telefono", $("[name='telefono']").prop("value"));
            values.append("cedula", $("[name='cedula']").prop("value"));
            values.append("email", $("[name='email']").prop("value"));
            //values.append("usuario", $("[name='usuario']").prop("value"));
            values.append("pass", $("[name='password']").prop("value"));
            console.log($("[name='nombres']").prop("value").split(' ').filter(function(n) {
              return n != ''
            }).length);

            $.ajax({
              url: '../php/login/nuevo-usuario.php',
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
                      title: 'Usuario Registrado',
                      showConfirmButton: false,
                      timer: 2000
                    }); //CUANDO REGISTRA EXITOSAMENTE
                    setTimeout(function() {
                      window.location = "../index.php";
                    }, 2000);
                    break;
                  case "no":
                    Swal.fire({
                      icon: 'error',
                      title: 'Usuario no Registrado',
                      text: 'Problema de comunicación con el servidor, intente más tarde',
                      confirmButtonText: 'Aceptar',
                      confirmButtonColor: '#01a9ac',
                    }); //ERROR AL REGISTRAR
                    console.log("no");
                    break;
                  case "existe_cedula":
                    Swal.fire({
                      icon: 'warning',
                      title: 'Usuario no Registrado',
                      text: 'El número de cédula ya existe',
                      confirmButtonText: 'Aceptar',
                      confirmButtonColor: '#01a9ac',
                    }); //CEDULA EXISTENTE
                    console.log("no ced");
                    break;
                  case "existe_usuario":
                    Swal.fire({
                      icon: 'warning',
                      title: 'Usuario no Registrado',
                      text: 'El nombre de usuario ya existe',
                      confirmButtonText: 'Aceptar',
                      confirmButtonColor: '#01a9ac',
                    }); //NOMBRE DE USUARIO EXISTENTE
                    console.log("no usu");
                    break;
                  default:
                    Swal.fire({
                      icon: 'error',
                      title: 'Error Inesperado',
                      text: toString(response),
                      confirmButtonText: 'Aceptar',
                      confirmButtonColor: '#01a9ac',
                    });
                    console.log(response);
                    break;
                }

              },
              error: function(response) {
                Swal.fire({
                  icon: 'error',
                  title: 'Error Inesperado',
                  text: toString(response),
                  confirmButtonText: 'Aceptar',
                  confirmButtonColor: '#01a9ac',
                });
                console.log("err2");
              }
            });
          } else {
            Swal.fire({
              icon: 'warning',
              text: 'Las contraseñas no coinciden',
              confirmButtonText: 'Aceptar',
              confirmButtonColor: '#01a9ac',
            });
          }
        }



        /*}}*/
      );
    });
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <!--script src="../assets/js/argon-dashboard.min.js?v=2.0.4"></script-->
</body>

</html>