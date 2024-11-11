<?php

require_once 'php/Conexion.php';
$ced = $_SESSION["Cedula"];
$tipo = $_SESSION["Tipo"];
$res = mysqli_query($conn, "SELECT * FROM usuarios WHERE Cedula = '$ced'");
$data = mysqli_fetch_array($res);
$pass = $data["Contrasena"];
closeConection($conn);
$aux = "disabled";
if ($_SESSION["Tipo"] == "Admin" || $_SESSION["Tipo"] == "SuperAdmin") {
  $aux = "";
}
?>
<div class="container-fluid py-4">


  <div class="row">
    <div class=col-md-3></div>
    <div class="card col-md-6">
      <div class="card-header pb-0">
        <div class="d-flex">
          <p class="mb-0 mt-1">EDITAR PERFIL</p>
          <button type="button" id="edit" class="btn btn-primary btn-sm ms-auto">Guardar</button>

        </div>
      </div>
      <div class="card-body">
        <p class="text-uppercase text-sm">User Information</p>
        <form id="form" role="form">
          <div class="mb-3">
            <input type="text" class="form-control" placeholder="Nombres" aria-label="nombres" name="nombres" value="<?php echo $_SESSION["P_Nombre"] . ' ' . $_SESSION["S_Nombre"] ?>" required <?php echo $aux; ?>>
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" placeholder="Apellidos" aria-label="apellidos" name="apellidos" value="<?php echo $_SESSION["P_Apellido"] . ' ' . $_SESSION["S_Apellido"] ?>" required <?php echo $aux; ?>>
          </div>
          <div class="mb-3">
            <input type="email" class="form-control" placeholder="usuario@correo.com" aria-label="Email" name="email" value="<?php echo $_SESSION["Correo"] ?>" required <?php echo $aux; ?>>
          </div>
          <div class="row ">
            <div class="mb-3 col-6">
              <input type="text" class="form-control" placeholder="00001234567" aria-label="telefono" pattern="[0-9]{1,11}" id="telefono" name="telefono" value="<?php echo $_SESSION["Telefono"] ?>" required>
            </div>
            <div class="mb-3 col-6">
              <input type="text" class="form-control" placeholder="12345678" aria-label="cedula" pattern="[0-9]{5,8}" name="cedula" value="<?php echo $_SESSION["Cedula"] ?>" required <?php echo $aux; ?>>
            </div>
          </div>
          <div class="">
            <input type="text" class="" placeholder="Cedula" aria-label="cedula" pattern="[0-9]{5,8}" name="cedula2" value="<?php echo $_SESSION["Cedula"] ?>" hidden>
          </div>
          <!--div class="mb-3">
                        <input type="text" class="form-control" placeholder="usuario" aria-label="usuario" name="usuario" required>
                      </div-->
          <div id="con" class="row">
            <div class="mb-3 col-6">
              <input type="password" class="form-control" placeholder="Contraseña Anterior" aria-label="Password" name="confirmar" Value="" required>
            </div>
            <div class="mb-3 col-6">
              <input type="password" class="form-control" placeholder="Nueva Contraseña" aria-label="confirmar" name="password" Value="" required>
            </div>

          </div>

          <!--div class="form-check form-check-info text-start">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
                  <label class="form-check-label" for="flexCheckDefault">
                    I agree the <a href="javascript:;" class="text-dark font-weight-bolder">Terms and Conditions</a>
                  </label>
                </div-->


        </form>
      </div>
    </div>
    <div class=col-md-3></div>
  </div>
  <!--div class="col-md-4">
          <div class="card card-profile">
            <img src="assets/img/bg-profile.jpg" alt="Image placeholder" class="card-img-top">
            <div class="row justify-content-center">
              <div class="col-4 col-lg-4 order-lg-2">
                <div class="mt-n4 mt-lg-n6 mb-4 mb-lg-0">
                  <a href="javascript:;">
                    <img src="assets/img/team-2.jpg" class="rounded-circle img-fluid border border-2 border-white">
                  </a>
                </div>
              </div>
            </div>
            <div class="card-header text-center border-0 pt-0 pt-lg-2 pb-4 pb-lg-3">
              <div class="d-flex justify-content-between">
                <a href="javascript:;" class="btn btn-sm btn-info mb-0 d-none d-lg-block">Connect</a>
                <a href="javascript:;" class="btn btn-sm btn-info mb-0 d-block d-lg-none"><i class="ni ni-collection"></i></a>
                <a href="javascript:;" class="btn btn-sm btn-dark float-right mb-0 d-none d-lg-block">Message</a>
                <a href="javascript:;" class="btn btn-sm btn-dark float-right mb-0 d-block d-lg-none"><i class="ni ni-email-83"></i></a>
              </div>
            </div>
            <div class="card-body pt-0">
              <div class="row">
                <div class="col">
                  <div class="d-flex justify-content-center">
                    <div class="d-grid text-center">
                      <span class="text-lg font-weight-bolder">22</span>
                      <span class="text-sm opacity-8">Friends</span>
                    </div>
                    <div class="d-grid text-center mx-4">
                      <span class="text-lg font-weight-bolder">10</span>
                      <span class="text-sm opacity-8">Photos</span>
                    </div>
                    <div class="d-grid text-center">
                      <span class="text-lg font-weight-bolder">89</span>
                      <span class="text-sm opacity-8">Comments</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="text-center mt-4">
                <h5>
                  Mark Davis<span class="font-weight-light">, 35</span>
                </h5>
                <div class="h6 font-weight-300">
                  <i class="ni location_pin mr-2"></i>Bucharest, Romania
                </div>
                <div class="h6 mt-4">
                  <i class="ni business_briefcase-24 mr-2"></i>Solution Manager - Creative Tim Officer
                </div>
                <div>
                  <i class="ni education_hat mr-2"></i>University of Computer Science
                </div>
              </div>
            </div>
          </div>
        </div-->

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

<script>
  $(Document).ready(function() {

    $("#edit").click(function() {


      var values = new FormData();

      values.append("nombre", $("[name='nombres']").prop("value"));
      values.append("apellido", $("[name='apellidos']").prop("value"));
      values.append("telefono", $("[name='telefono']").prop("value"));
      values.append("cedula", $("[name='cedula']").prop("value"));
      values.append("cedula2", $("[name='cedula2']").prop("value"));
      values.append("email", $("[name='email']").prop("value"));
      values.append("viejo", $("[name='confirmar']").prop("value"));
      values.append("pass", $("[name='password']").prop("value"));
      values.append("ident", 'editar');
      values.append("tipo", '<?php echo $_SESSION["Tipo"]; ?>');


      $.ajax({
        url: 'php/Usuario/editar_usuario.php',
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
                title: 'Usuario Editado',
                showConfirmButton: false,
                timer: 1500
              }); //CUANDO REGISTRA EXITOSAMENTE
              setTimeout(function() {
                window.location.reload();
              }, 1500);
              break;
            case "no":

              Swal.fire({
                icon: 'error',
                title: 'Usuario no Editado',
                text: 'Problema de comunicación con el servidor, intente más tarde',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#01a9ac',
              }); //ERROR AL REGISTRAR
              console.log("no");
              break;
            case "existe_cedula":

              Swal.fire({
                icon: 'warning',
                title: 'Usuario no Editado',
                text: 'El número de cédula ya existe',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#01a9ac',
              }); //CEDULA EXISTENTE
              console.log("no ced");
              break;
            case "existe_usuario":

              Swal.fire({
                icon: 'warning',
                title: 'Usuario no Editado',
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

    });
  });
</script>