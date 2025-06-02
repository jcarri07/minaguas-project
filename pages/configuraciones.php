<?php
require_once 'php/Conexion.php';

$queryPropositos = mysqli_query($conn, "SELECT * FROM propositos WHERE estatus = 'activo' OR estatus = 'principal';");
$queryPropositosInactivos = mysqli_query($conn, "SELECT * FROM propositos WHERE estatus = 'inactivo';");
$queryEmbalses = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo' order by nombre_embalse;");
$stringPrioritarios = "0";
$prioritarios = [];
$queryPrioritarios = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'prioritarios';");
if (mysqli_num_rows($queryPrioritarios) > 0) {
  $stringPrioritarios = mysqli_fetch_assoc($queryPrioritarios)['configuracion'];
  $prioritarios = explode(",", $stringPrioritarios);
}

$stringConsumoHumano = "0";
$consumoHumano = [];
$queryConsumoHumano = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'consumo_humano';");
if (mysqli_num_rows($queryConsumoHumano) > 0) {
  $stringConsumoHumano = mysqli_fetch_assoc($queryConsumoHumano)['configuracion'];
  $consumoHumano = explode(",", $stringConsumoHumano);
}

$evaporacionFiltracion = [];
$queryEvaporacionFiltracion = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'evap_filt';");
if (mysqli_num_rows($queryEvaporacionFiltracion) > 0) {
  $evaporacionFiltracion = json_decode(mysqli_fetch_assoc($queryEvaporacionFiltracion)['configuracion'], true);
}

$embalsesPriotitarios = mysqli_query($conn, "SELECT * FROM embalses WHERE id_embalse IN ($stringPrioritarios);");

$queryInameh = mysqli_query($conn, "SELECT nombre_config, configuracion FROM configuraciones WHERE nombre_config = 'fecha_sequia' OR nombre_config = 'fecha_lluvia';");
$fechas = [];
// $resultado = mysqli_fetch_assoc($queryInameh);
while ($resultado = mysqli_fetch_array($queryInameh)) {
  array_push($fechas, $resultado['configuracion']);
}
// var_dump($fechas);

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
        minmax(140px, 1fr));
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

  .form-container {
    border: 1px solid lightgray;
    /* Borde claro */
    padding: 15px;
    /* Espaciado interno */
    margin-top: 15px;
    /* Espaciado superior */
    border-radius: 5px;
    /* Bordes redondeados */
    align-items: center;
    /* Centra horizontalmente */

  }

  .input-error {
    background: #ffe6e6;
    border-color: #ff8f8f;
    color: darkred;
  }
</style>
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-12">
      <div class="card h-100">
        <div class="card-header pb-4">
          <!-- <div class="row"> -->
          <!-- <div class="col-6 d-flex align-items-center"> -->
          <h4 class="">CONFIGURACIONES</h4>
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

          <div class="config-container----">
            <div class="d-flex gap-4">
              <!-- Configuraciones de propositos -->
              <div class=" w-50">
                <h3 class="mb-3 text-center">Propósitos/Usos de embalse</h3>
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
                <div id="config-propositos" class="config-container-hijo px-6">
                  <?php
                  while ($row = mysqli_fetch_array($queryPropositos)) {
                  ?>

                    <div class="">
                      <div class=" d-flex align-items-center">
                        <?php if ($row['estatus'] == 'activo') { ?>
                          <a data-id="<?php echo $row['id_proposito']; ?>" class="editar-pro btn btn-link text-dark px-0 mb-0"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i></a>
                          <a data-id="<?php echo $row['id_proposito']; ?>" class="eliminar-pro btn btn-link text-dark px-0 mb-0"><i class="fas fa-trash text-dark me-2" aria-hidden="true"></i></a>
                        <?php } else { ?>
                          <a class="btn btn-link text-dark px-0 mb-0"><i class="fas fa-check-double text-dark me-2" aria-hidden="true"></i></a>

                        <?php } ?>
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

              <!-- configuraciones fecha inameh -->
              <div class="w-50" style="display: flex; flex-direction: column; align-items: center;">
                <div class="mb-5">
                  <h3 class="mb-2 text-center" style="">Inicio de Temporadas (Según INAMEH)</h3>
                </div>
                <div class="d-flex gap-4">
                  <div style="text-align: center;">
                    <h5 class="mb-2">Fecha inicio época Seca:</h5>
                    <label>Fecha actual:</label>
                    <input type="text" style="text-align: center;" class="form-control" style="width: 100%;" value="<?php echo date("d-m-Y", strtotime($fechas[0])); ?>" id="fecha_actual_sequia" readonly>
                    <label>Nueva fecha: </label>
                    <input type="date" class="form-control" style="width: 100%;" id="fecha_inameh_seca">
                  </div>

                  <div style="text-align: center; margin-left: 80px; ">
                    <h5 class="mb-2">Fecha inicio época Lluvia:</h5>
                    <label>Fecha actual:</label>
                    <input type="text" style="text-align: center;" class="form-control" style="width: 100%;" value="<?php echo date("d-m-Y", strtotime($fechas[1])); ?>" id="fecha_actual_lluvia" readonly>
                    <label>Nueva fecha: </label>
                    <input type="date" class="form-control" style="width: 100%;" id="fecha_inameh_lluvia">
                  </div>
                </div>
                <div class="text-center mt-5"><button id="periodo" class="btn btn-primary">Guardar</button></div>
              </div>
            </div>

            <div class="px-6 mt-5">

              <?php
              $embalses = mysqli_fetch_all($queryEmbalses, MYSQLI_ASSOC);

              $embalses_consumo = array_filter($embalses, function ($value) {
                $uso_actual = isset($value['uso_actual']) && $value['uso_actual'] !== ""
                  ? explode(",", $value['uso_actual'])
                  : [];

                $uso_actual = array_filter($uso_actual, function ($item) {
                  return is_string($item) && trim($item) !== '';
                });

                return in_array(1, $uso_actual);
              });
              ?>
              <!-- configuraciones de embalses prioritarios -->
              <div>
                <h3 class="mb-2 text-center">Embalses Prioritarios</h3>
                <h5 class="mb-4 text-center">Seleccione aquellos embalses que desee que aparezcan en el reporte de embalses prioritarios.</h5>
                <div class="config-container-prioritarios">

                  <?php
                  // resetear el puntero de la consulta
                  mysqli_data_seek($queryEmbalses, 0);
                  while ($embalse = mysqli_fetch_array($queryEmbalses)) {
                  ?>
                    <div class="d-flex">
                      <div class="form-check">
                        <input <?php if (in_array($embalse["id_embalse"], $prioritarios)) echo "checked" ?> class="form-check-input check-prioritario" type="checkbox" value="<?php echo $embalse["id_embalse"]; ?>" id="<?php echo $embalse["id_embalse"]; ?>">
                        <label class="label-embalse form-check-label " for="">
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

              <div class="mt-5">
                <h3 class="mb-2 text-center">Embalses de Consumo Humano</h3>
                <h5 class="mb-4 text-center">Deseleccione los embalses de compensación</h5>
                <div class="config-container-prioritarios">
                  <?php
                  if (!empty($embalses_consumo)) {
                    foreach ($embalses_consumo as $embalse) {
                  ?>
                      <div class="d-flex">
                        <div class="form-check">
                          <input
                            class="form-check-input check-consumo-humano"
                            type="checkbox"
                            value="<?php echo htmlspecialchars($embalse["id_embalse"]); ?>"
                            id="embalse-<?php echo htmlspecialchars($embalse["id_embalse"]); ?>"
                            <?php echo (isset($consumoHumano) && is_array($consumoHumano) && !in_array($embalse["id_embalse"], $consumoHumano)) ? "checked" : ""; ?>>
                          <label class="label-embalse form-check-label" for="embalse-<?php echo htmlspecialchars($embalse["id_embalse"]); ?>">
                            <?php echo htmlspecialchars($embalse["nombre_embalse"]); ?>
                          </label>
                        </div>
                      </div>
                  <?php
                    }
                  } else {
                    echo "<p class='text-center'>No hay embalses disponibles.</p>";
                  }
                  ?>
                </div>
                <div class="text-center mt-3">
                  <button id="save-consumo-humano" class="btn btn-primary">Guardar</button>
                </div>
              </div>


            </div>

            <div class="text-center mt-5">
              <div class="mb-5">
                <h3 class="mb-2 text-center">Valores de Filtración y Evaporación por Embalses</h3>
              </div>
              <button class="btn btn-success" id="evap-filtracion" onclick="openModal()">Actualizar valores</button>
            </div>
          </div>

        </div>
        <br><br><br>
      </div>
    </div>
  </div>
</div>

<!-- <form action="php/guardar_embalses.php" method="POST" class="form-control mt-4 mb-4" enctype="multipart/form-data">
  <label for="excel">Cargar el archivo</label>
  <input type="file" name="excel" id="excel" class="form-control">
  <br>
  <button type="submit">Enviar</button>
</form> -->

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

<div class="modal fade" id="modal-evap" tabindex="-1" role="dialog" aria-labelledby="modal-evap" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content w-300">
      <div class="modal-body p-0">
        <button type="button" class="btn btn-secondary close-modal btn-rounded mb-0" data-bs-dismiss="modal">X</button>
        <div class="card card-plain">
          <div class="card-body">
            <div class="text-center">
              <h3 id="embalseTitulo" class="mb-2 text-center">Valores de Filtración y Evaporación</h3>
              <h6 id="embalseTitulo" class="mb-4 text-center">Ingrese los valores de cada embalse para actualizar según la fecha que corresponda</h6>

            </div>

            <div style="height: 60vh; position:relative;" class="table-responsive mb-3 px-5">
              <table class="table align-items-center text-lg text-center table-lg" id="table">
                <thead style="position: sticky; top:0;" class="bg-white">
                  <tr style="width: 100%;">
                    <th style="text-align: center; font-size:small; font-weight:semibold; width: 30%" class="hide-cell">
                      <div style="margin-top: 20px;">Embalse</div>
                    </th>
                    <th style="text-align: center; font-size:small; font-weight:semibold;  width: 25%" class="hide-cell">
                      <div
                        style="text-transform: lowercase;">(mm/mes)</div>
                      <div>Evaporación</div>
                    </th> <!--Ubicación-->
                    <th style="text-align: center; font-size:small; font-weight:semibold; width: 25% " class="hide-cell">
                      <div>(%)</div>
                      <div>Filtración</div>
                    </th>
                    <th style="text-align: center; font-size:small; font-weight:semibold;  width: 20%;" class="hide-cell">
                      <div style="margin-top: 20px;">Fecha</div>
                    </th> <!--Ubicación-->

                  </tr>
                </thead>
                <tbody style="overflow-y: scroll; height: 100%;" class="list">
                  <?php
                  $existe = false;
                  $actual = [];
                  if (!empty($embalses_consumo)) {
                    foreach ($embalses_consumo as $embalse) {
                      if (array_key_exists($embalse["id_embalse"], $evaporacionFiltracion)) {
                        $existe = true;
                        $actual = $evaporacionFiltracion[$embalse["id_embalse"]];
                      } else {
                        $existe = false;
                      }
                  ?>
                      <tr id="<?php echo $embalse["id_embalse"]; ?>" style="width: 100%; height: auto" class="py-0 fila-datos">
                        <td style="text-align: left; font-size:small; height: 10px;" class="hide-cell py-0">
                          <div class="">
                            <label for="evaporacion" class="col-form-label"><?php echo $embalse["nombre_embalse"]; ?></label>
                          </div>
                        </td>
                        <td style="text-align: center; font-size:small;" class="hide-cell py-0">
                          <input type="text" class="form-control py-1 evaporacion numero Vnumero" name="evaporacion" value="<?php if ($existe) {
                                                                                                                              echo $actual["evaporacion"];
                                                                                                                            } else {
                                                                                                                            } ?>">
                        </td>
                        <td style="text-align: center; font-size:small;" class="hide-cell py-0">
                          <input type="text" class="form-control py-1 filtracion numero Vnumero" name="filtracion" value="<?php if ($existe) {
                                                                                                                            echo $actual["filtracion"];
                                                                                                                          } ?>">
                        </td>
                        <td style="text-align: center; font-size:small;" class="hide-cell py-0">
                          <div class="">
                            <label for="fecha" class="col-form-label"><?php if ($existe) {
                                                                        echo $actual["fecha"];
                                                                      } else {
                                                                        echo "dd/mm/aaaa";
                                                                      } ?></label>
                          </div>
                        </td>
                      </tr>
                    <?php
                    }
                  } else { ?>
                    <tr>
                      No hay embalses disponibles.
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div>

            <div class="text-center">
              <Button type="button" id="guardar-evap-filt" class="btn btn-primary btn-block"">Guardar</Button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>



                <!-- <template id=" template-propositoo">
                <div class="d-flex">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="">
                    <label class="label-proposito form-check-label " for="">

                    </label>
                  </div>
                </div>
                </template> -->





                <!--   Core JS Files   -->

                <script>
                  var win = navigator.platform.indexOf('Win') > -1;
                  if (win && document.querySelector('#sidenav-scrollbar')) {
                    var options = {
                      damping: '0.5'
                    }
                    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
                  }
                  $(" .prop-uso").on("click", function(e) {
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
                      // clone.querySelector('span').htmlFor=proposito.value;
                      // clone.querySelector('input').id=proposito.value;
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

                    // console.log(jsonPropositos);

                    $.ajax({
                      type: "POST",
                      url: "php/proces_config.php",
                      // contentType: 'application/json' ,
                      data: {
                        config: "add-proposito",
                        propositos: jsonPropositos,
                      },
                      success: function(response) {
                        // console.log(response)
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
                    // console.log(id_prop)

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
                    // console.log($("#" + id_prop + "-span"))

                    $("#embalseEditTitulo").text("¿Restaurar?")
                    // $("#embalseNombre").text($("#" + id_prop + "-span" ).text());
                    $("#embalseNameInput")[0].value = $("#" + id_prop + "-span")[0].innerText;
                    $("#embalseEditInput")[0].value = id_prop;
                    $("#buttom-form-edit")[0].name = "editar";
                    $('#modal-prop-edit').modal('show');

                  });

                  $("#save-prioritarios").on("click", function(e) {
                    var inputs_checkeados = $('input[type="checkbox" ]:checked.check-prioritario');

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
                        // console.log(response)
                        window.location.href = "?page=configuraciones";

                      }
                    });
                  });

                  $("#save-consumo-humano").on("click", function(e) {
                    var inputs_checkeados = $('input[type="checkbox"]:not(:checked).check-consumo-humano');

                    var valores_inputs = [];
                    let embalses_consumo_humano = "0";

                    $.each(inputs_checkeados, function(index, input) {
                      valores_inputs.push($(input).val());
                    });

                    if (valores_inputs.length > 0) {
                      embalses_consumo_humano = valores_inputs.join(",");
                    }
                    // console.log(embalses_consumo_humano);
                    $.ajax({
                      type: "POST",
                      url: "php/proces_config.php",
                      data: {
                        config: "consumo-humano",
                        embalses_consumo_humano: embalses_consumo_humano,
                      },
                      success: function(response) {
                        // console.log(response)
                        window.location.href = "?page=configuraciones";

                      }
                    });
                  });

                  $("#periodo").on("click", function(e) {
                    var fecha_seca = $('#fecha_inameh_seca').val();
                    var fecha_lluvia = $('#fecha_inameh_lluvia').val();

                    $.ajax({
                      type: "POST",
                      url: "php/proces_config.php",
                      data: {
                        fecha_seca: fecha_seca,
                        fecha_lluvia: fecha_lluvia
                      },

                      success: function(response) {
                        // console.log(response);
                        window.location.href = "?page=configuraciones";
                      },
                      error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                      }
                    });
                  });

                  function openModal() {
                    $('#modal-evap').modal('show');
                  }

                  // document.getElementById('buscador').addEventListener('input', function() {
                  // const searchTerm = this.value.toLowerCase();
                  // const inputs = document.querySelectorAll('input[type="text"]');

                  // inputs.forEach(input => {
                  // if (input.value.toLowerCase().includes(searchTerm)) {
                  // input.parentElement.style.display = ''; // Mostrar el input si coincide
                  // } else {
                  // input.parentElement.style.display = 'none'; // Ocultar el input si no coincide
                  // }
                  // });
                  // });
                  $("#guardar-evap-filt").on("click", function(e) {

                    if (!validaciones()) {
                      const filas = $(".fila-datos");
                      let datos = {};
                      filas.each(function() {
                        const id = $(this).attr("id"); // Captura el ID de la fila si existe
                        const evaporacion = $(this).find("input[name='evaporacion']").val();
                        const filtracion = $(this).find("input[name='filtracion']").val();
                        if (evaporacion.trim() !== "" || filtracion.trim() !== "") {
                          datos[id] = {
                            evaporacion: evaporacion,
                            filtracion: filtracion
                          };
                        }
                      });
                      // console.log(datos)
                      $.ajax({
                        type: "POST",
                        url: "php/proces_config.php",
                        data: {
                          config: "evap-filt",
                          datos: JSON.stringify(datos),
                        },
                        success: function(response) {
                          // console.log(response)
                          window.location.href = "?page=configuraciones";

                        }
                      });
                    }
                  });

                  $(document).ready(function() {
                    const inputs = $(".numero");
                    var regex = /^(\d{1,3}(\.\d{3})*|\d+)(,\d+)?$/; //PERFECTA

                    for (let i = 0; i < inputs.length; i++) {
                      // Verificar si el evento ya fue agregado
                      if (!inputs[i].dataset.eventAdded) {
                        // Agregar eventos
                        inputs[i].addEventListener("keydown", function(event) {
                          validarNumero(event, inputs[i]);
                        });

                        inputs[i].addEventListener("blur", function(event) {
                          if (inputs[i].value.trim() === "") {
                            if (inputs[i].classList.contains('input-error')) {
                              inputs[i].classList.remove('input-error');
                            }
                          } else if (!regex.test(inputs[i].value)) {
                            if (!inputs[i].classList.contains('input-error')) {
                              inputs[i].classList.add('input-error');
                            }
                          } else {
                            if (inputs[i].classList.contains('input-error')) {
                              inputs[i].classList.remove('input-error');
                            }
                          }
                        });

                        // Marcar que el evento fue agregado
                        inputs[i].dataset.eventAdded = "true";
                      }
                    }
                  });



                  function validarNumero(event, input) {
                    let valorInput = input.value;
                    const codigoTecla = event.key;
                    // console.log(input.id)
                    if (input.id == "huso" && (codigoTecla < '0' || codigoTecla > '9') && codigoTecla !== 'Backspace') {
                      event.preventDefault();
                      // input.value = valorInput.slice(0, -1);
                    }
                    // Permitir solo números y una coma
                    if ((codigoTecla < '0' || codigoTecla > '9') && codigoTecla !== '.' && codigoTecla !== ',' && codigoTecla !== 'Backspace' && codigoTecla !== 'ArrowUp' && codigoTecla !== 'ArrowDown' && codigoTecla !== 'ArrowLeft' && codigoTecla !== 'ArrowRight') {
                      event.preventDefault();
                      // input.value = valorInput.slice(0, -1);
                    }

                    // Permitir solo una coma
                    if (codigoTecla === ',' && valorInput.includes(',')) {
                      event.preventDefault();
                      // const numeroDeComas = valorInput.split(",").length - 1;
                      // if (numeroDeComas > 1) {
                      // input.value = valorInput.slice(0, -1);
                      // }
                    }
                    if (codigoTecla === ',' && valorInput[valorInput.length - 1] === ".") {
                      event.preventDefault();
                      // input.value = valorInput.slice(0, -1);
                    }
                    if (codigoTecla === '.' && valorInput == "") {
                      event.preventDefault();
                      // input.value = valorInput.slice(0, -1);
                    }
                    if ((codigoTecla === '.' && valorInput.includes(','))) {
                      event.preventDefault();
                      // input.value = valorInput.slice(0, -1);
                    }

                    if ((codigoTecla === '.' && valorInput[valorInput.length - 1] === ".")) {
                      event.preventDefault();
                    }

                    if (codigoTecla === '.' || codigoTecla === ',') {
                      let text_split = valorInput.split(".")
                      // console.log(valorInput, text_split)
                      let newValorInput = "";
                      for (let i = 0; i < text_split.length; i++) {
                        // console.log(text_split[i].includes(','))
                        if (text_split[i].includes(',')) {
                          newValorInput += text_split[i];
                          break;
                        }
                        if (text_split[i].length > 3) {
                          let newSplit = [];
                          if (i == 0) {
                            newSplit = dividirEnTres(text_split[i])
                            // console.log(newSplit)
                          } else {
                            newSplit = dividirEnTresIzquierda(text_split[i])
                          }
                          // console.log(newSplit);
                          newValorInput += cifrasMayoresQueTres(newSplit, i, i == text_split.length - 1);
                        } else if (text_split[i].length == 3) {
                          newValorInput += text_split[i];
                          if (i != text_split.length - 1) {
                            newValorInput += ".";
                          }
                        } else if (text_split[i].length == 2) {
                          let aux = parseInt(text_split[i]);
                          if (i != 0) {
                            aux = aux * 10;
                          }
                          newValorInput += aux.toString();
                          if (i != text_split.length - 1) {
                            newValorInput += ".";
                          }
                        } else if (text_split[i].length == 1) {
                          let aux = parseInt(text_split[i]);
                          if (i != 0) {
                            aux = aux * 100;
                          }
                          newValorInput += aux.toString();
                          if (i != text_split.length - 1) {
                            newValorInput += ".";
                          }
                        }
                      }
                      // console.log("new:" + newValorInput);
                      // console.log(dividirEnTres("2222569874"));
                      // console.log(dividirEnTresIzquierda("2222569874"));
                      input.value = newValorInput;
                    }

                  }


                  function dividirEnTres(cifra) {
                    const str = cifra.toString(); // Convertir a string
                    const resultado = [];

                    for (let i = str.length; i > 0; i -= 3) {
                      resultado.unshift(str.slice(Math.max(i - 3, 0), i));
                    }

                    return resultado;
                  }

                  function dividirEnTresIzquierda(cifra) {
                    const str = cifra.toString(); // Convertimos la cifra a cadena
                    const resultado = [];

                    for (let i = 0; i < str.length; i += 3) {
                      resultado.push(str.slice(i, i + 3)); // Extraemos los grupos de tres
                    }

                    return resultado;
                  }

                  function cifrasMayoresQueTres(text_split, index, ultima) {
                    let newValorInput = "";
                    for (let i = 0; i < text_split.length; i++) {
                      // console.log(text_split[i], text_split[i].length);
                      if (text_split[i].length == 3) {
                        newValorInput += text_split[i];
                        // if (i < text_split.length) {
                        newValorInput += ".";
                        // }
                      } else if (text_split[i].length == 2) {
                        let aux = parseInt(text_split[i]);
                        if (index != 0) {
                          aux = aux * 10;
                        }
                        newValorInput += aux.toString();
                        // if (i < text_split.length) {
                        newValorInput += ".";
                        // }
                      } else if (text_split[i].length == 1) {
                        let aux = parseInt(text_split[i]);
                        if (index != 0) {
                          aux = aux * 100;
                        }
                        newValorInput += aux.toString();
                        // if (i < text_split.length) {
                        newValorInput += ".";
                        // }
                      }
                    }
                    if (ultima) {

                      newValorInput = newValorInput.slice(0, -1);
                    }
                    return newValorInput;
                  }


                  function validaciones() {
                    // event.preventDefault();
                    // var regex=/^-?\d{1,3}(?:([,.])\d{3})*(?:\1\d*)?$/
                    // var regex=/^\d{1,3}(\.\d{3})*(,\d+)?$/;
                    var regex = /^(\d{1,3}(\.\d{3})*|\d+)(,\d+)?$/; //PERFECTA
                    var campos = document.querySelectorAll('.Vnumero, .Vrequerido, .Varchivo, .Viguales');
                    var errorMessages = [];

                    campos.forEach(function(campo) {

                      // var label = campo.previousElementSibling.innerText;
                      var label = ""

                      if (campo.classList.contains('Vnumero')) {
                        // console.log(campo, campo.value, regex.test(campo.value), isNaN(campo.value));
                        if (campo.value.trim() === "") {
                          // errorMessages.push("El campo '<b>" + label + "</b>' no puede estar vacío.");
                          // if (!campo.classList.contains('input-error')) {
                          //   campo.className += " input-error";
                          // }
                        } else if ((!regex.test(campo.value))) {
                          errorMessages.push("El campo '<b>" + label + "</b>' debe respetar el formato numérico.");
                          if (!campo.classList.contains('input-error')) {
                            campo.className += " input-error";
                          }
                        }
                      }

                      if (campo.classList.contains('Vrequerido')) {
                        if (campo.value.trim() === "") {
                          errorMessages.push("El campo '<b>" + label + "</b>' no puede estar vacío.");
                          if (!campo.classList.contains('input-error')) {
                            campo.className += " input-error";
                          }
                        }
                      }

                      if (campo.classList.contains('Varchivo')) {
                        // console.log("archivooo")
                        if (campo.files.length === 0) {
                          errorMessages.push("Debe seleccionar un archivo para el campo '<b>" + label + "</b>' .");
                          if (!campo.classList.contains('input-error')) {
                            campo.className += " input-error";
                          }
                        }
                      }

                      if (campo.classList.contains('Viguales')) {
                        let nombre_input = $(campo).val().trim().toLocaleLowerCase();

                        let busqueda = nombresEmbalses.filter((nombre) => {
                          return nombre.trim().toLocaleLowerCase() == nombre_input.trim().toLocaleLowerCase()
                        });
                        if (busqueda.length > 0) {
                          errorMessages.push("El nombre del Embalse '<b>" + nombre_input.charAt(0).toUpperCase() + nombre_input.slice(1) + "</b>' ya está registrado.");
                          if (!campo.classList.contains('input-error')) {
                            campo.className += " input-error";
                          }
                        }
                      }
                    });

                    return errorMessages.length > 0;
                    // if (errorMessages.length > 0) {
                    //   event.preventDefault();
                    //   var errorContainer = document.getElementById("modal-body-validate");
                    //   console.log(errorContainer)
                    //   errorContainer.innerHTML = "<ul> <li > " + errorMessages.join(" < /li> <li > ") + " < /li> < /ul > ";
                    //   $('#modal-validate').modal('show');
                    // }
                  }
                </script>




                <div class=" modal fade" id="modal-prop" tabindex="-1" role="dialog" aria-labelledby="modal-prop" aria-hidden="true">
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