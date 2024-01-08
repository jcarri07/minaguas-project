<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">

      <div class="card h-100">
        <div class="card-header pb-0">
          <h6>Usuarios Registrados</h6>
        </div>
        <div class="card-body p-3 pb-0">

          <div class="text-center">

            <button type="button" onclick="$('#new-user').modal('show');" class="btn btn-primary btn-block">
              Nuevo
            </button>

          </div>

          <div class="dt-responsive table-responsive p-0">
            <?php include "php/Usuario/Lista_usuario.php"; ?>

          </div>
          <br><br><br>
        </div>
      </div>
    </div>
  </div>

  <!--<footer class="footer pt-3">
    <div class="container-fluid">
      <div class="row align-items-center justify-content-lg-between">
        <div class="col-lg-6 mb-lg-0 mb-4">
          <div class="copyright text-center text-sm text-muted text-lg-start">
            © <script>
              document.write(new Date().getFullYear())
            </script>,
            desarrollado por
            <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Dirección de Investigación e Innovación - ABAE
            </a>
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
  </footer>-->

</div>
<!-- Modal-New -->
<div class="modal fade" id="new-user" tabindex="-1" role="dialog" aria-labelledby="edit-embalse" aria-hidden="true">
  <div class="modal-dialog modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <h3 class="font-weight-bolder text-primary text-gradient">Nuevo Usuario</h3>
            <!--<p class="mb-0">Enter your email and password to register</p>-->
          </div>
          <div class="card-body pb-3">
            <form id="form" role="form">
              <div class="mb-3">
                <label>Nombre(s)</label>
                <input type="text" class="form-control" placeholder="Nombre Completo" aria-label="nombres" name="nombres" required>
              </div>
              <div class="mb-3">
                <label>Apellido(s)</label>
                <input type="text" class="form-control" placeholder="Apellidos" aria-label="apellidos" name="apellidos" required>
              </div>
              <div class="mb-3">
                <label>Correo</label>
                <input type="email" class="form-control" placeholder="Email" aria-label="Email" name="email" required>
              </div>
              <div class="row ">
                <div class="mb-3 col-6">
                  <label>Telefono</label>
                  <input type="text" class="form-control" placeholder="Telefono" aria-label="telefono" pattern="[0-9]{1,11}" name="telefono" required>
                </div>
                <div class="mb-3 col-6">
                  <label>Cedula</label>
                  <input type="text" class="form-control" placeholder="Cedula" aria-label="cedula" pattern="[0-9]{8}" name="cedula" required>
                </div>
              </div>
              <label>Contraseña</label>
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
            </form>
          </div>
          <div class="card-footer text-center pt-0 px-sm-4 px-1">
            <!--<p class="mb-4 mx-auto">
                  Already have an account?
                  <a href="javascrpt:;" class="text-primary text-gradient font-weight-bold">Guardar</a>
                </p>--->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal-Edit -->
<div class="modal fade" id="edit-user" tabindex="-1" role="dialog" aria-labelledby="edit-user" aria-hidden="true">
  <div class="modal-dialog modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <h3 class="font-weight-bolder text-primary text-gradient">Editar</h3>
            <!--<p class="mb-0">Enter your email and password to register</p>-->
          </div>
          <div class="card-body pb-3">
            <form id="form2" role="form">
              <div class="mb-2">
                <label>Nombre(s)</label>
                <input type="text" class="form-control" placeholder="Nombre Completo" aria-label="nombres" name="Enombres" required>
              </div>
              <div class="mb-2">
                <label>Apellido(s)</label>
                <input type="text" class="form-control" placeholder="Apellidos" aria-label="apellidos" name="Eapellidos" required>
              </div>
              <div class="mb-2">
                <label>Correo</label>
                <input type="email" class="form-control" placeholder="Email" aria-label="Email" name="Eemail" required>
              </div>
              <div class="row ">
                <div class="mb-2 col-6">
                  <label>Telefono</label>
                  <input type="text" class="form-control" placeholder="Telefono" aria-label="telefono" pattern="[0-9]{1,11}" name="Etelefono" required>
                </div>
                <div class="mb-2 col-6">
                  <label>Cedula</label>
                  <input type="text" class="form-control" placeholder="Cedula" aria-label="cedula" pattern="[0-9]{8}" name="Ecedula" required>
                </div>

              </div>
              <div class="">
                <input type="text" class="" placeholder="Cedula" aria-label="cedula" pattern="[0-9]{8}" name="Ecedula2" hidden>
              </div>
              <!--div class="mb-3">
                        <input type="text" class="form-control" placeholder="usuario" aria-label="usuario" name="usuario" required>
                      </div-->

              <div class="mb-2">
                <label>Contraseña</label>
                <input type="password" class="form-control" placeholder="Contraseña" aria-label="Password" name="Epassword" required>
              </div>
              <!--div class="mb-3 col-6">
                  <input type="password" class="form-control" placeholder="Repetir Contraseña" aria-label="confirmar" name="Econfirmar" required>
                </div-->


              <!--div class="form-check form-check-info text-start">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
                  <label class="form-check-label" for="flexCheckDefault">
                    I agree the <a href="javascript:;" class="text-dark font-weight-bolder">Terms and Conditions</a>
                  </label>
                </div-->
              <div class="text-center">
                <a href="sign-in.php"><button type="submit" class="btn btn-primary w-100 my-4 mb-2">Editar Usuario</button></a>
              </div>
            </form>
          </div>
          <div class="card-footer text-center pt-0 px-sm-4 px-1">
            <!--<p class="mb-4 mx-auto">
                  Already have an account?
                  <a href="javascrpt:;" class="text-primary text-gradient font-weight-bold">Guardar</a>
                </p>--->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    iniciarTabla('table-user');
    /*   $('#table-user').DataTable({
         language: {
           "decimal": "",
           "emptyTable": "No hay información",
           "info": "Mostrando resultados _START_ a _END_ de _TOTAL_",
           "infoEmpty": "Mostrando 0 to_MENU_of_TOTAL_Entradas",
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
             "next": ">",
             "previous": "<"
           }
         },
       });*/
  });


  function Modaledit(p_nom, s_nom, p_ape, s_ape, pass, ced, correo, telf) {
    $("[name='Enombres']").val(p_nom + ' ' + s_nom);
    $("[name='Eapellidos']").val(p_ape + ' ' + s_ape);
    $("[name='Etelefono']").val(telf);
    $("[name='Ecedula']").val(ced);
    $("[name='Ecedula2']").val(ced);
    $("[name='Eemail']").val(correo);
    $("[name='Epassword']").val(pass);
    $("#edit-user").modal("show");

  };

  function Modaldelete(id) {
    Swal.fire({
      title: "Esta seguro?",
      text: "Una vez Eliminado no podra deshacer su elección",
      icon: "warning",
      showCancelButton: true,
      showConfirmButton: true,
      confirmButtonColor: "#5e72e4",
      cancelButtonColor: "#d33",
    }).then((result) => {
      if (result.isConfirmed) {
        var values = new FormData();
        values.append("id", id);

        $.ajax({
          url: 'php/Usuario/Borrar_usuario.php',
          type: 'POST',
          data: values,
          cache: false,
          contentType: false,
          processData: false,
          success: function(response) {
            switch (response) {
              case "borrado":
                Swal.fire({
                  icon: 'success',
                  title: 'Usuario Eliminado',
                  showConfirmButton: false,
                  timer: 1200
                });
                setTimeout(function() {
                  window.location.reload();
                }, 1200);
                break;
              case "usuario no existe":
                Swal.fire({
                  icon: 'warning',
                  title: 'Error en eliminacion',
                  confirmButtonText: 'Aceptar',
                  confirmButtonColor: '#01a9ac',
                });
                break;
              default:
                console.log(response);
                break;
            }

          },
        });
      } else {
        console.log("presiono cancelar");
      }
    });

  };

  $(document).ready(function() {

    $("#form").submit(function(e) {
      e.preventDefault();

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
          url: 'php/login/nuevo-usuario.php',
          type: 'POST',
          data: values,
          cache: false,
          contentType: false,
          processData: false,
          success: function(response) {

            switch (response) {
              case "si":
                $('#new-user').modal('hide');
                Swal.fire({
                  icon: 'success',
                  title: 'Usuario Registrado',
                  showConfirmButton: false,
                  timer: 1500
                }); //CUANDO REGISTRA EXITOSAMENTE
                setTimeout(function() {
                  window.location.reload();
                }, 1500);
                break;
              case "no":
                $('#new-user').modal('hide');
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
                $('#new-user').modal('hide');
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
                $('#new-user').modal('hide');
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
                $('#new-user').modal('hide');
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
            $('#new-user').modal('hide');
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
        $('#new-user').modal('hide');
        Swal.fire({
          icon: 'warning',
          text: 'Las contraseñas no coinciden',
          confirmButtonText: 'Aceptar',
          confirmButtonColor: '#01a9ac',
        });
      }
    });

    $("#form2").submit(function(e) {
      e.preventDefault();

      var values = new FormData();

      values.append("nombre", $("[name='Enombres']").prop("value"));
      values.append("apellido", $("[name='Eapellidos']").prop("value"));
      values.append("telefono", $("[name='Etelefono']").prop("value"));
      values.append("cedula", $("[name='Ecedula']").prop("value"));
      values.append("cedula2", $("[name='Ecedula2']").prop("value"));
      values.append("email", $("[name='Eemail']").prop("value"));
      //values.append("usuario", $("[name='usuario']").prop("value"));
      values.append("pass", $("[name='Epassword']").prop("value"));
      console.log($("[name='nombres']").prop("value").split(' ').filter(function(n) {
        return n != ''
      }).length);

      $.ajax({
        url: 'php/Usuario/editar-usuario.php',
        type: 'POST',
        data: values,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {

          switch (response) {
            case "si":
              $('#edit-user').modal('hide');
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
              $('#edit-user').modal('hide');
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
              $('#edit-user').modal('hide');
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
              $('#edit-user').modal('hide');
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
              $('#edit-user').modal('hide');
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
          $('#edit-user').modal('hide');
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