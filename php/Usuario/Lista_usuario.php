<?php
include "php/Conexion.php";
$res = mysqli_query($conn, "SELECT * FROM Usuarios WHERE NOT Tipo = 'Admin';");
$count = mysqli_num_rows($res);
if ($count >= 1) {


  echo '<table id="table-user" class="table align-items-center mb-0">
                  <thead>
                    <tr>
                    <!--th class="text-uppercase text-xxs font-weight-bolder  ps-2">Foto</th-->
                      <th class="text-uppercase text-xxs font-weight-bolder  ps-2">Nombre(s)</th>
                      <th class="text-uppercase text-xxs font-weight-bolder  ps-2">Apellido(s)</th>
                      <th class="text-uppercase text-xxs font-weight-bolder  ps-2">Email</th>
                      <th class="text-uppercase text-xxs font-weight-bolder  ps-2">Ultima Entrada</th>
                      <th class="text-uppercase text-xxs font-weight-bolder  text-center">Opciones</th>
                    </tr>
                  </thead>
                  <tbody>';
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
      <td class="align-middle text-sm">
        <div class="d-flex flex-column justify-content-center">
          <h6 class="mb-0 text-secondary text-sm"><?php echo $val["Correo"]; ?></h6>
        </div>
      </td>
      <td class="align-middle">
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
        <a type='button' onclick="Modaledit('<?php echo $val['P_Nombre'];?>','<?php echo$val['S_Nombre'];?>','<?php echo$val['P_Apellido'];?>','<?php echo$val['S_Apellido'];?>','<?php echo$val['Contrasena'];?>','<?php echo$val['Cedula'];?>','<?php echo$val['Correo'];?>','<?php echo$val['Telefono']?>')" class="text-secondary font-weight-bold text-xs pe-1" data-toggle="tooltip" data-original-title="Edit user">
          Editar
        </a>
        <a type='button' onclick="Modaldelete( <?php echo $val['Id_usuario']; ?>)"  class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
          Borrar
        </a>
      </td>
    </tr>
<?php


  }
  echo '</tbody></table>';
} else {
  echo '<p class="letra">Ningun Usuario Registrado</p>';
}
closeConection($conn);
?>