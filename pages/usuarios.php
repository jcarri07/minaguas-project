<?php include "php/Usuario/Lista_usuario.php";
$options_extraccion = '<option value="">Seleccione</option>';
$options_extraccion .= '<option value="Riego">Riego</option>';
$options_extraccion .= '<option value="Hidroelectricidad">Hidroelectricidad</option>';
$options_extraccion .= '<option value="Consumo Humano">Consumo Humano</option>';
$options_extraccion .= '<option value="Control de Inundaciones (Aliviadero)">Control de Inundaciones (Aliviadero)</option>';
$options_extraccion .= '<option value="Recreación">Recreación</option>';
?>



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
              <?php if($_SESSION["Tipo"] == "SuperAdmin"){?>
              
                <label id="" class="form-label">Ver por</label>
                            <select name="tipo" class="form-select" required>
                                <option value="Admin">Administrador</option>
                                <option value="User">Usuario</option>
                            </select>

              <?php }?>


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

              <?php if($_SESSION["Tipo"] == "SuperAdmin"){?>
              
              <label id="" class="form-label">Ver por</label>
                          <select name="Etipo" class="form-select" required>
                              <option value="Admin">Administrador</option>
                              <option value="User">Usuario</option>
                          </select>

            <?php }?>
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
                <a href="sign-in.php"><button type="submit" class="btn btn-primary w-100 my-4 mb-2">Guardar cambios</button></a>
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
<!-- Modal-historial -->
<div class="modal fade" id="modal-details" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <h3 class="font-weight-bolder text-primary text-gradient">Historial de Reportes</h3>
            <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">X</button>
          </div>
          <div class="card-body pb-3" id="body-details">

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
<!-- Modal-historial-detalles -->
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <h3 class="font-weight-bolder text-primary text-gradient title"></h3>
            <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">X</button>
            <!--<p class="mb-0">Enter your email and password to register</p>-->
          </div>
          <div class="card-body pb-3">
            <form role="form text-left" id="form3">
              <?php
              if (date("H:i") . ":00" > "10:00:00") {
              ?>
                <h6 class="text-red text-center text-retraso">Estás retrasado al enviar el reporte</h6>
              <?php
              }
              ?>
              <div class="row">
                <div class="col">
                  <label>Fecha</label>
                  <div class="input-group mb-3">
                    <input type="date" class="form-control" name="fecha" id="fecha" value="<?php echo date("Y-m-d"); ?>" disabled required>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <label>Hora</label>
                  <div class="input-group mb-3">
                    <input type="time" class="form-control" name="hora" id="hora" value="<?php echo date("H:i") . ":00"; ?>" disabled required>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <label>Cota</label>
                  <div class="input-group mb-3">
                    <input type="number" step="0.00001" class="form-control" name="valor_cota" id="valor_cota" placeholder="Cota" aria-label="Cota" aria-describedby="name-addon" required>
                  </div>
                </div>
              </div>

              <h6 class="mt-2">Extracción</h6>
              <div id="box-extraccion">
                <div class="row">
                  <div class="col">
                    <label>Tipo</label>
                    <div class="input-group mb-3">
                      <select class="form-select" name="tipo_extraccion[]" id="tipo_extraccion_1" required>
                        <?php echo $options_extraccion; ?>
                      </select>
                    </div>
                  </div>
                  <div class="col">
                    <label>Valor</label>
                    <div class="input-group mb-3">
                      <input type="number" step="0.00001" class="form-control" name="valor_extraccion[]" id="valor_extraccion_1" placeholder="Valor de la Extracción" required>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 text-center" style="margin-top: 12px;">
                  <button class="btn btn-success btn-add-extraccion" id="addRows" type="button">Añadir Otra Extracción</button>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 loaderParent">
                  <div class="loader">
                  </div>
                  Por favor, espere
                </div>
              </div>

              <div class="text-center">
                <button type="submit" class="btn bg-gradient-primary btn-lg btn-rounded w-100 mt-4 mb-0 btn-submit">Guardar</button>
                <button type="button" class="btn btn-secondary btn-rounded mt-4 mb-0 btn-edit" data-bs-dismiss="modal" style="display: none;">Cerrar</button>
                <!--<button type="button" class="btn bg-gradient-primary btn-rounded mt-4 mb-0 btn-edit" style="display: none;">Editar</button>-->
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


  function Modaledit(p_nom, s_nom, p_ape, s_ape, pass, ced, correo, telf,tipo) {
    $("[name='Enombres']").val(p_nom + ' ' + s_nom);
    $("[name='Eapellidos']").val(p_ape + ' ' + s_ape);
    $("[name='Etelefono']").val(telf);
    $("[name='Ecedula']").val(ced);
    $("[name='Ecedula2']").val(ced);
    $("[name='Eemail']").val(correo);
    $("[name='Etipo'] option[value="+tipo+"]").prop('selected', true);
    // $("[name='Epassword']").val(pass);
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
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        var values = new FormData();
        values.append("id", id);
        values.append("ident", 'borrar');

        $.ajax({
          url: 'php/Usuario/editar_usuario.php',
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

  function recuperar(id) {

    var values = new FormData();
    values.append("id", id);
    values.append("ident", 'recuperar');

    $.ajax({
      url: 'php/Usuario/editar_usuario.php',
      type: 'POST',
      data: values,
      cache: false,
      contentType: false,
      processData: false,
      success: function(response) {
        switch (response) {
          case "recuperado":
            Swal.fire({
              icon: 'success',
              title: 'Usuario Restaurado',
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
              title: 'Error en recuperar',
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

  };

  function openModalHistory(id_usuario) {

    var values = new FormData();
    values.append("id_encargado", id_usuario);
    $("#id_embalse_aux").text(id_usuario);

    $("#body-details").html("<h3 class='text-center'>Cargando...</h3>");
    $("#modal-details").modal("show");

    $.ajax({
      url: 'php/Usuario/historial.php',
      type: 'POST',
      data: values,
      cache: false,
      contentType: false,
      processData: false,
      success: function(response) {
        $("#body-details").html(response);
        iniciarTabla('table-history');
      },
      error: function(response) {}
    });
  }


  function openModalDetalles(id_registro, fecha, hora, cota, extraccion){
      $("#id_aux").text(id_registro);
      //$("#opc_aux").text("edit");

      $(".removeRow").attr("disabled", false);
      $("#btn-open-modal-import-data").hide();

      $("#add .title").text("Detalles del Reporte de la Extracción");
      $(".removeRow").each(function( index ) {
        $(this).trigger("click");
      });

      $("#fecha").val(fecha);
      $("#hora").val(hora);
      $("#valor_cota").val(cota);
      var extraccion_array = extraccion.split(";");
      if(extraccion_array.length > 1){
        for(var i = 0 ; i < extraccion_array.length - 1 ; i++){
          $("#addRows").trigger("click");
        }
      }

      $(".removeRow").attr("disabled", true);

      //var ids_rows_extracciones = [];
      $("select[name='tipo_extraccion[]']").each(function(i) {
        var extraccion_aux = extraccion_array[i].split("&");

        this.value = extraccion_aux[0];
        $(this).trigger("change");
        var row = this.id.replace("tipo_extraccion_", "");

        var valor_extraccion = extraccion_aux[1];
        if(this.value == "30") {
          if($.isNumeric(extraccion_aux[1])) {
            valor_extraccion = extraccion_aux[1] + "%";
            if(extraccion_aux[1] < 1) {
              valor_extraccion = (extraccion_aux[1] * 100) + "%";
            }
          }
        }
        if(this.value == "31") {
          if($.isNumeric(extraccion_aux[1])) 
            valor_extraccion = Number(extraccion_aux[1]).toFixed(4);
        }
        if( this.value != "30" && this.value != "31" && $.isNumeric(extraccion_aux[1]) )
          valor_extraccion = Number(extraccion_aux[1]).toFixed(3);
        $("#valor_extraccion_" + row).val(valor_extraccion);

        $(this).attr("disabled", true);
        $("#valor_extraccion_" + row).attr("disabled", true);

        //En este atributo se guarda el id del detalle de la extraccion en caso de editar
        //$(this).attr("id_detalle_edit", extraccion_aux[2]);
      });

      $("#valor_cota").attr("disabled", true);

      $("#add .text-retraso").hide();
      $("#add .btn-submit").hide();
      $("#add .btn-add-extraccion").hide();
      $("#add .btn-edit").show();
      $("#add .btn-edit").attr("onclick", "$('#modal-details').modal('show')");
      $("#add .btn-edit").text("Atrás");
      $('#add').modal('show');

    }

  var count = 1;
  $(document).on('click', '#addRows', function() {
    count++;
    var htmlRows = '';
    htmlRows += '<div class="row">';
    htmlRows += '   <div class="col">';
    htmlRows += '       <label>Tipo</label>';
    htmlRows += '       <div class="input-group mb-3">';
    htmlRows += '           <select class="form-select" name="tipo_extraccion[]" id="tipo_extraccion_' + count + '" required>';
    htmlRows += '               <?php echo $options_extraccion; ?>';
    htmlRows += '           </select>';
    htmlRows += '       </div>';
    htmlRows += '   </div>';
    htmlRows += '   <div class="col">';
    htmlRows += '       <label>Valor</label>';
    htmlRows += '       <div class="input-group mb-3">';
    htmlRows += '           <input type="number" step="0.00001" class="form-control" name="valor_extraccion[]" id="valor_extraccion_' + count + '" placeholder="Valor de la Extracción" required>';
    htmlRows += '       </div>';
    htmlRows += '   </div>';
    htmlRows += '</div>';

    $('#box-extraccion').append(htmlRows);
  });

  $(document).ready(function() {

    $("#form").submit(function(e) {
      e.preventDefault();

      
        var values = new FormData();

        values.append("nombre", $("[name='nombres']").prop("value"));
        values.append("apellido", $("[name='apellidos']").prop("value"));
        values.append("telefono", $("[name='telefono']").prop("value"));
        values.append("cedula", $("[name='cedula']").prop("value"));
        values.append("email", $("[name='email']").prop("value"));
        values.append("ident", 'editarU');
        values.append("tipo", <?php if($_SESSION["Tipo"] == "SuperAdmin"){ ?>$("[name='Etipo'] option:selected").val()<?php }else{ ?>"User"<?php } ?>);
        
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
      values.append("ident", 'editarU');
      
      values.append("tipo", <?php if($_SESSION["Tipo"] == "SuperAdmin"){ ?>$("[name='Etipo'] option:selected").val()<?php }else{ ?>"User"<?php } ?>);
      
      /*console.log($("[name='nombres']").prop("value").split(' ').filter(function(n) {
        return n != ''
      }).length);*/

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