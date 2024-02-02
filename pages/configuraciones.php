<?php
require_once 'php/Conexion.php';

$queryPropositos = mysqli_query($conn, "SELECT * FROM propositos WHERE estatus = 'activo';");
$queryPropositosInactivos = mysqli_query($conn, "SELECT * FROM propositos WHERE estatus = 'inactivo';");
$queryEmbalses = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo';");

$stringPrioritarios = "0";
$prioritarios = [];
$queryPrioritarios = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'prioritarios';");
if (mysqli_num_rows($queryPrioritarios) > 0) {
  $stringPrioritarios = mysqli_fetch_assoc($queryPrioritarios)['configuracion'];
  var_dump($stringPrioritarios);
  $prioritarios = explode(",", $stringPrioritarios);
}

$embalsesPriotitarios = mysqli_query($conn, "SELECT * FROM embalses WHERE id_embalse IN ($stringPrioritarios);");

closeConection($conn);
?>

<style>
  .config-container {
    display: grid;
    place-content: center;
    grid-template-columns: repeat(auto-fit,
        minmax(400px, 1fr));
    column-gap: 5%;
    /* row-gap: 4%; */
    padding-left: 5%;
    padding-right: 5%;
    /* padding: 5%; */
  }

  .config-container-hijo {
    display: grid;
    grid-template-columns: repeat(auto-fill,
        minmax(180px, 1fr));
    gap: 10px;
    padding: 10px;
  }

  .config-container-prioritarios {
    display: grid;
    place-content: center;
    grid-template-columns: repeat(auto-fill,
        minmax(90px, 1fr));
    column-gap: 10px;
  }

  .finded {
    display: inline-block;
    position: relative;
  }

  .finded::after {
    content: "";
    position: absolute;
    width: 100%;
    height: 4px;
    bottom: 0;
    left: 0;
    background-color: lightcoral;
    animation-name: slidein;
    animation-duration: 0.4s;
    animation-iteration-count: 3;
    /* transform: scaleX(0);
    transform-origin: bottom right; */
    transition: transform 0.4s ease-out;
  }

  @keyframes slidein {
    from {
      transform: scaleX(0);
      /* transform-origin: bottom right; */
    }

    to {
      transform: scaleX(1);
      /* transform-origin: bottom left; */
    }
  }

  .label-embalse {
    font-size: 12px;
  }
</style>
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-12">
      <div class="card h-100">
        <div class="card-header pb-4">
          <!-- <div class="row"> -->
          <!-- <div class="col-6 d-flex align-items-center"> -->
          <h6 class="">Configuraciones</h6>
          <!-- </div> -->
          <!--<div class="col-6 text-end">
                  <button class="btn btn-outline-primary btn-sm mb-0">View All</button>
                </div>-->
          <!-- </div> -->
        </div>
        <div class="card-body  pb-0">
          <!-- <div class="text-center">
            <a href="?page=crear_embalse">
              <button type="button" class="btn btn-primary btn-block">
                Nuevo
              </button>
            </a>
          </div> -->

          <div class="config-container">

            <!-- Configuraciones de propositos -->
            <div class="mb-5">
              <h3 class="mb-3 text-center">Propósitos/Usos de embalse:</h3>
              <div class="d-flex flex-wrap justify-content-center align-items-center mb-2 gap-3 m-5">
                <div class="">
                  <div class="">Nuevo Propósito/Uso</div>
                </div>
                <div class="d-flex justify-content-center align-items-center mb-2 gap-3">
                  <div class=""><input class="form-control" type="text" name="" id="prop-uso-text"></div>
                  <div id="prop-uso" class="prop-uso cursor-pointer border rounded"><i class="fas fa-plus text-lg p-2"></i></div>
                </div>
              </div>
              <hr>
              <div id="config-propositos" class="config-container-hijo">
                <?php
                while ($row = mysqli_fetch_array($queryPropositos)) {
                ?>

                  <div class="">
                    <div class=" d-flex align-items-center">
                      <a data-id="<?php echo $row['id_proposito']; ?>" class="editar-pro btn btn-link text-dark px-0 mb-0"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i></a>
                      <a data-id="<?php echo $row['id_proposito']; ?>" class="eliminar-pro btn btn-link text-dark px-0 mb-0"><i class="fas fa-trash text-dark me-2" aria-hidden="true"></i></a>
                      <span id="<?php echo $row['id_proposito']; ?>-span" class="label-proposito old align-self-center text-sm" for="">
                        <?php echo $row['proposito']; ?>
                      </span>
                    </div>
                  </div>

                <?php
                }
                ?>
              </div>

              <?php
              if (mysqli_num_rows($queryPropositosInactivos) > 0) {
              ?>
                <div class="mt-4 text-sm text-dark">Eliminados</div>
              <?php
              }
              ?>

              <div id="config-propositos" class="config-container-hijo">
                <?php
                while ($row = mysqli_fetch_array($queryPropositosInactivos)) {
                ?>

                  <div class="">
                    <div class=" d-flex align-items-center">
                      <!-- <a data-id="<?php echo $row['id_proposito']; ?>" class="editar-pro btn btn-link text-dark px-0 mb-0"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i></a> -->
                      <a data-id="<?php echo $row['id_proposito']; ?>" class="restaurar-pro btn btn-link text-dark px-0 mb-0"><i class="fas fa-redo text-dark me-2" aria-hidden="true"></i></a>
                      <span id="<?php echo $row['id_proposito']; ?>-span" class="label-proposito old align-self-center text-sm" for="">
                        <?php echo $row['proposito']; ?>
                      </span>
                    </div>
                  </div>

                <?php
                }
                ?>
              </div>

              <div class="text-center mt-3"><button id="save-propositos" class="btn btn-primary">Guardar</button></div>
            </div>

            <!-- configuraciones de embalses prioritarios -->
            <div class="">
              <h3 class="mb-4 text-center">Embalses Prioritarios:</h3>
              <div class="config-container-prioritarios">

                <?php
                while ($embalse = mysqli_fetch_array($queryEmbalses)) {
                ?>
                  <div class="d-flex">
                    <div class="form-check">
                      <input <?php if (in_array($embalse["id_embalse"], $prioritarios)) echo "checked" ?> class="form-check-input check-prioritario" type="checkbox" value="<?php echo $embalse["id_embalse"]; ?>" id="<?php echo $embalse["id_embalse"]; ?>">
                      <label class="label-embalse form-check-label " for="<?php echo $i; ?>">
                        <?php echo $embalse["nombre_embalse"]; ?>
                      </label>
                    </div>
                  </div>
                <?php
                }
                ?>
              </div>
              <div class="text-center mt-3"><button id="save-prioritarios" class="btn btn-primary">Guardar</button></div>
            </div>

          </div>
          <br><br><br>
        </div>
      </div>
    </div>
  </div>

  <!-- <template id="template-propositoo">
    <div class="d-flex">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="">
        <label class="label-proposito form-check-label " for="">

        </label>
      </div>
    </div>
  </template> -->

  <template id="template-proposito">
    <div class="">
      <div class=" d-flex align-items-center">
        <a id="" class="editar-pro-new btn btn-link text-dark px-0 mb-0"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i></a>
        <a id="" class="eliminar-pro-new btn btn-link text-dark px-0 mb-0"><i class="fas fa-trash text-dark me-2" aria-hidden="true"></i></a>
        <span class="label-proposito label-proposito-new align-self-center text-sm text-dark" for="">

        </span>
      </div>
    </div>
  </template>


  <!--   Core JS Files   -->

  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

    $(".prop-uso").on("click", function(e) {
      let proposito = $("#" + this.id + "-text")[0];
      let propositos = $(".label-proposito");
      let find = false;

      removerClase($(".finded"), "finded");

      for (let index = 0; index < propositos.length; index++) {
        if (propositos[index].innerText.trim().toLocaleLowerCase() == proposito.value.trim().toLocaleLowerCase()) {
          find = true;
          agregarClase($(propositos[index]), "finded");
          break;
        }
      }

      if (proposito.value != "" && find != true) {
        let t = document.querySelector('#template-proposito');
        var clone = document.importNode(t.content, true);
        clone.querySelector('span').innerHTML = proposito.value;
        // clone.querySelector('span').htmlFor = proposito.value;
        // clone.querySelector('input').id = proposito.value;
        let bodyConfig = document.querySelector('#config-propositos');
        bodyConfig.appendChild(clone);
        proposito.value = "";
      }
      find = false;
    });

    function agregarClase(elemento, clase) {
      if (!elemento.hasClass(clase)) {
        elemento.addClass(clase);
      }
    }

    function removerClase(elemento, clase) {
      if (elemento.hasClass(clase)) {
        elemento.removeClass(clase);
      }
    }

    $("#save-propositos").on("click", function(e) {
      let propositosLabel = $(".label-proposito-new");
      let propositos = [];

      for (let index = 0; index < propositosLabel.length; index++) {
        propositos.push(propositosLabel[index].innerText);
      }

      let jsonPropositos = JSON.stringify(propositos);

      console.log(jsonPropositos);

      $.ajax({
        type: "POST",
        url: "php/proces_config.php",
        // contentType: 'application/json',
        data: {
          config: "add-proposito",
          propositos: jsonPropositos,
        },
        success: function(response) {
          console.log(response)
          window.location.href = "?page=configuraciones";

        }
      });

    });

    $(".eliminar-pro").on("click", function(e) {
      // Realizar la consulta AJAX al servidor
      // console.log("Eliminar");
      e.preventDefault();
      var id_prop = $(this).data("id");
      console.log(id_prop)

      $("#embalseTitulo").text("¿Eliminar?")
      $("#embalseNombre").text($("#" + id_prop + "-span").text());
      $("#embalseIdInput")[0].value = id_prop;
      $("#buttom-form")[0].name = "eliminar";
      $('#modal-prop').modal('show');

    });

    $(".restaurar-pro").on("click", function(e) {
      // Realizar la consulta AJAX al servidor
      // console.log("Eliminar");
      e.preventDefault();
      var id_prop = $(this).data("id");
      console.log(id_prop)

      $("#embalseTitulo").text("¿Restaurar?")
      $("#embalseNombre").text($("#" + id_prop + "-span").text());
      $("#embalseIdInput")[0].value = id_prop;
      $("#buttom-form")[0].name = "restaurar";
      $('#modal-prop').modal('show');

    });

    $(".editar-pro").on("click", function(e) {
      // Realizar la consulta AJAX al servidor
      // console.log("Eliminar");
      e.preventDefault();
      var id_prop = $(this).data("id");
      console.log($("#" + id_prop + "-span"))

      $("#embalseEditTitulo").text("¿Restaurar?")
      // $("#embalseNombre").text($("#" + id_prop + "-span").text());
      $("#embalseNameInput")[0].value = $("#" + id_prop + "-span")[0].innerText;
      $("#embalseEditInput")[0].value = id_prop;
      $("#buttom-form-edit")[0].name = "editar";
      $('#modal-prop-edit').modal('show');

    });

    $("#save-prioritarios").on("click", function(e) {
      var inputs_checkeados = $('input[type="checkbox"]:checked.check-prioritario');

      var valores_inputs = [];
      let embalses_prioritarios = "0";

      $.each(inputs_checkeados, function(index, input) {
        valores_inputs.push($(input).val());
      });

      if (valores_inputs.length > 0) {
        embalses_prioritarios = valores_inputs.join(",");
      }

      $.ajax({
        type: "POST",
        url: "php/proces_config.php",
        data: {
          config: "prioritarios",
          embalses_prioritarios: embalses_prioritarios,
        },
        success: function(response) {
          console.log(response)
          window.location.href = "?page=configuraciones";

        }
      });
    });
  </script>



  <div class="modal fade" id="modal-prop" tabindex="-1" role="dialog" aria-labelledby="modal-prop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content  w-300">
        <div class="modal-body p-0">
          <button type="button" class="btn btn-secondary close-modal btn-rounded mb-0" data-bs-dismiss="modal">X</button>
          <div class="card card-plain">
            <div class="card-body">

              <div class="">
                <h5 style="text-align:center;" id="embalseTitulo" class="mb-0"></h5>
                <h6 style="text-align:center;" id="embalseNombre" class="mt-3 text-dark"></h6>
              </div>
              <form method="POST" action="php/proces_config.php" enctype="multipart/form-data">

                <div class="input-group mb-2">
                  <input style="display: none;" id="embalseIdInput" type="text" class="form-control" name="id_prop" value="">
                </div>

                <div class="text-center d-flex flex-col-6 justify-content-center">
                  <button type="submit" id="buttom-form" name="delete" class="btn btn-round btn-primary btn-md  mt-3 mb-0">Confirmar</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal-prop-edit" tabindex="-1" role="dialog" aria-labelledby="modal-prop-edit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content  w-300">
        <div class="modal-body p-0">
          <button type="button" class="btn btn-secondary close-modal btn-rounded mb-0" data-bs-dismiss="modal">X</button>
          <div class="card card-plain">
            <div class="card-body">

              <div class="">
                <h5 style="text-align:center;" id="embalseEditTitulo" class="mb-0"></h5>
                <!-- <h6 style="text-align:center;" id="embalseNombre" class="mt-3 text-dark"></h6> -->
              </div>
              <form method="POST" action="php/proces_config.php" enctype="multipart/form-data">

                <div class=" mt-4 mb-2">
                  <input id="embalseNameInput" type="text" class="form-control" name="name_prop" value="">
                  <input style="display: none;" id="embalseEditInput" type="text" class="form-control" name="id_prop" value="">
                </div>

                <div class="text-center d-flex flex-col-6 justify-content-center">
                  <button type="submit" id="buttom-form-edit" name="delete" class="btn btn-round btn-primary btn-md  mt-3 mb-0">Confirmar</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal -->
  <div class="modal fade" id="edit-embalse" tabindex="-1" role="dialog" aria-labelledby="edit-embalse" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-body p-0">
          <div class="card card-plain">
            <div class="card-header pb-0 text-left">
              <h3 class="font-weight-bolder text-primary text-gradient">Editar</h3>
              <!--<p class="mb-0">Enter your email and password to register</p>-->
            </div>
            <div class="card-body pb-3">
              <form role="form text-left">
                <div class="row">
                  <div class="col">
                    <label>Nombre</label>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" placeholder="Nombre" aria-label="Nombre" value="Nombre" aria-describedby="name-addon">
                    </div>
                  </div>
                  <div class="col">
                    <label>Capacidad</label>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" placeholder="Capacidad" aria-label="Capacidad" value="Capacidad" aria-describedby="email-addon">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <label>Dirección</label>
                    <div class="input-group mb-3">
                      <input type="password" class="form-control" placeholder="Dirección" aria-label="Direccion" value="Direccion" aria-describedby="password-addon">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    .
                    <br>
                    .
                    <br>
                    .
                    <br>
                    .
                    <br>
                    .
                    <br>
                  </div>
                  <!--<div class="form-check form-check-info text-left">
                      <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked="">
                      <label class="form-check-label" for="flexCheckDefault">
                        I agree the <a href="javascrpt:;" class="text-dark font-weight-bolder">Terms and Conditions</a>
                      </label>
                    </div>-->
                </div>
                <div class="text-center">
                  <button type="button" class="btn bg-gradient-primary btn-lg btn-rounded w-100 mt-4 mb-0">Guardar</button>
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