<?php
include "php/Conexion.php";
if($_SESSION["Tipo"] == "SuperAdmin"){
  $res = mysqli_query($conn, "SELECT * FROM usuarios WHERE NOT Tipo = 'SuperAdmin' AND estatus = 'activo';");
}else{
$res = mysqli_query($conn, "SELECT * FROM usuarios WHERE NOT Tipo = 'Admin' AND NOT Tipo = 'SuperAdmin'AND estatus = 'activo';");
}
$count = mysqli_num_rows($res);
if ($count >= 1) {


?>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">

        <div class="card h-auto pb-3">
          <div class="card-header pb-0">
            <h6>Usuarios Registrados</h6>
          </div>
          <div class="card-body p-3 pb-0">

            <div class="text-center">

              <button type="button" onclick="$('#new-user').modal('show');" class="btn btn-primary btn-block">
                Nuevo
              </button>

            </div>

            <div class="dt-responsive table-responsive p-0 pb-3">
              <table id="table-user" class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <!--th class="text-uppercase text-xxs font-weight-bolder  ps-2">Foto</th-->
                    <th class="text-uppercase text-xxs font-weight-bolder  ps-2">Nombre(s)</th>
                    <th class="text-uppercase text-xxs font-weight-bolder  ps-2">Apellido(s)</th>
                    <th class="text-uppercase text-xxs font-weight-bolder  ps-2 hide-cell">Email</th>
                    <th class="text-uppercase text-xxs font-weight-bolder  ps-2 hide-cell">Tipo</th>
                    <th class="text-uppercase text-xxs font-weight-bolder  ps-2 hide-cell">Ultima Entrada</th>
                    <th class="text-uppercase text-xxs font-weight-bolder  text-center">Opciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  while ($val = mysqli_fetch_array($res)) {
                  ?>
                    <tr>
                      <!--td>
    <div>
          <img src="assets/img/team-2.jpg" class="avatar avatar-xl me-2" alt="user1">
        </div>
    </td-->
                      <td>


                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-secondary text-sm"><?php echo $val["P_Nombre"] . " " . $val["S_Nombre"]; ?></h6>
                        </div>

                      </td>
                      <td>
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-secondary text-sm"><?php echo $val["P_Apellido"] . " " . $val["S_Apellido"]; ?></h6>
                        </div>
                      </td>
                      <td class="align-middle text-sm hide-cell">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-secondary text-sm"><?php echo $val["Correo"]; ?></h6>
                        </div>
                      </td>
                      <td class=" hide-cell">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-secondary text-sm"><?php if($val["Tipo"] == "Admin"){echo "Administrador";}else{if($val["Tipo"] == "User"){echo "Usuario";}}?></h6>
                          </div>
                        </td>
                      <td class="align-middle hide-cell">
                        <?php
                        $aux = $val["Id_usuario"];
                        $re = mysqli_query($conn, "SELECT Fecha FROM datos_embalse WHERE Id_encargado = $aux");
                        if (mysqli_num_rows($re)) {

                          $v = mysqli_fetch_array($re);
                          echo '<span class="text-secondary text-xs font-weight-bold">' . $v["Fecha"] . '</span>';
                        } else {
                          echo '<span class="text-secondary text-xs font-weight-bold">Sin Registro</span>';
                        }

                        ?>

                      </td>
                      <td class="align-middle text-center justify-content-center">
                      <a type='button' onclick="openModalHistory('<?php echo $val['Id_usuario']; ?>')" class="text-secondary font-weight-bold text-xs me-3" data-toggle="tooltip" data-original-title="Edit user">
                      <i class="fas fa-clipboard-list text-dark me-1" aria-hidden="true"></i>
                          Historial
                        </a>
                        <a type='button' onclick="Modaledit('<?php echo $val['P_Nombre']; ?>','<?php echo $val['S_Nombre']; ?>','<?php echo $val['P_Apellido']; ?>','<?php echo $val['S_Apellido']; ?>','<?php echo $val['Contrasena']; ?>','<?php echo $val['Cedula']; ?>','<?php echo $val['Correo']; ?>','<?php echo $val['Telefono'] ?>','<?php echo $val['Tipo'] ?>')" class="text-secondary font-weight-bold text-xs me-3" data-toggle="tooltip" data-original-title="Edit user">
                          <i class="fas fa-pencil-alt text-dark me-1" aria-hidden="true"></i>
                          Editar
                        </a>



                        <a type='button' onclick="Modaldelete( <?php echo $val['Id_usuario']; ?>)" class="text-secondary font-weight-bold text-xs mx-2" data-toggle="tooltip" data-original-title="Edit user">
                          <i class="fas fa-trash text-dark me-1" aria-hidden="true"></i>
                          Eliminar
                        </a>

                      </td>
                    </tr>
                  <?php


                  };
                  ?>
                </tbody>
              </table>
            </div>

          </div>

          <?php lista($conn); ?>
        </div>
      </div>






    </div>
  <?php
} else {
  ?> <div class="container-fluid py-3">
      <div class="row">
        <div class="col-12 pb-4">

          <div class="card h-auto">
            <div class="card-header pb-0">
              <h6>Usuarios Registrados</h6>
            </div>
            <div class="card-body p-3 pb-0">

              <div class="text-center">

                <button type="button" onclick="$('#new-user').modal('show');" class="btn btn-primary btn-block">
                  Nuevo
                </button>

              </div>

              <div class="dt-responsive table-responsive pb-3">
                <h2 class="mb-1 text-dark font-weight-bold text-center mt-4">Ningun Usuario Registrado</h2>
              </div>

            </div>
          </div>
        </div>

        <?php lista($conn); ?>
      </div>
    <?php
  }

    ?>
    <?php
    function lista($conn)
    {
      if($_SESSION["Tipo"] == "SuperAdmin"){
        $res = mysqli_query($conn, "SELECT * FROM usuarios WHERE NOT Tipo = 'SuperAdmin' AND estatus = 'inactivo';");
      }else{
      $res = mysqli_query($conn, "SELECT * FROM usuarios WHERE NOT Tipo = 'Admin' AND NOT Tipo = 'Admin' AND estatus = 'inactivo';");
      }
      $count = mysqli_num_rows($res);

      if ($count >= 1) {
    ?>

        <div class="col-12">

          <div class=" h-auto">
            <div class="card-header pb-0">
              <h6>Usuarios Eliminados</h6>
            </div>
            <div class="card-body p-3 pb-0">
              <div class="dt-responsive table-responsive p-0 pb-3">
                <table id="table-user2" class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <!--th class="text-uppercase text-xxs font-weight-bolder  ps-2">Foto</th-->
                      <th class="text-uppercase text-xxs font-weight-bolder  ps-2">Nombre(s)</th>
                      <th class="text-uppercase text-xxs font-weight-bolder  ps-2">Apellido(s)</th>
                      <th class="text-uppercase text-xxs font-weight-bolder  ps-2 hide-cell">Email</th>
                      <th class="text-uppercase text-xxs font-weight-bolder  ps-2 hide-cell">Tipo</th>
                      <th class="text-uppercase text-xxs font-weight-bolder  ps-2 hide-cell">Ultima Entrada</th>
                      <th class="text-uppercase text-xxs font-weight-bolder  text-center">Opciones</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php
                    while ($val = mysqli_fetch_array($res)) { ?>



                      <tr>
                        <!--td>
    <div>
          <img src="assets/img/team-2.jpg" class="avatar avatar-xl me-2" alt="user1">
        </div>
    </td-->
                        <td>


                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-secondary text-sm"><?php echo $val["P_Nombre"] . " " . $val["S_Nombre"]; ?></h6>
                          </div>

                        </td>
                        <td>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-secondary text-sm"><?php echo $val["P_Apellido"] . " " . $val["S_Apellido"]; ?></h6>
                          </div>
                        </td>
                        <td class="align-middle text-sm hide-cell">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-secondary text-sm"><?php echo $val["Correo"]; ?></h6>
                          </div>
                        </td>
                        <td class=" hide-cell">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-secondary text-sm"><?php if($val["Tipo"] == "Admin"){echo "Administrador";}else{if($val["Tipo"] == "User"){echo "Usuario";}}?></h6>
                          </div>
                        </td>
                        <td class="align-middle hide-cell">
                          <?php
                          $aux = $val["Id_usuario"];
                          $re = mysqli_query($conn, "SELECT Fecha FROM datos_embalse WHERE Id_encargado = $aux");
                          if (mysqli_num_rows($re)) {

                            $v = mysqli_fetch_array($re);
                            echo '<span class="text-secondary text-xs font-weight-bold">' . $v["Fecha"] . '</span>';
                          } else {
                            echo '<span class="text-secondary text-xs font-weight-bold">Sin Registro</span>';
                          }

                          ?>

                        </td>
                        <td class="align-middle text-center">
                            <a type='button' onclick="recuperar( <?php echo $val['Id_usuario']; ?>)" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                              <i class="fas fa-redo text-dark me-1"></i>
                              Restaurar
                            </a>
                          
                        </td>
                      </tr>
                    <?php
                    } ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div> <?php
              }


                ?>





    <?php
    };
    //closeConection($conn);
    ?>