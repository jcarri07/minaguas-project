<?php
include '../php/Conexion.php';
header('Content-Type: text/html; charset=UTF-8');

$cat = $_POST['cat'];
$id = $_POST['id'];

$query;
$id_item_name = "";
$item_name = "";

if ($cat == 'estado' || $cat == 'SectoresEstado') {
    // $sql = "SELECT * FROM municipios WHERE id_estado LIKE '$id' ORDER BY municipio ASC;";
    $sql = "SELECT * FROM municipios WHERE id_estado IN ($id);";
    $query = mysqli_query($conn, $sql);
    $id_item_name = "id_municipio";
    $item_name = "municipio";
}
if ($cat == 'municipio' || $cat == 'SectoresMunicipio') {
    // $sql = "SELECT * FROM parroquias WHERE id_municipio LIKE '$id' ORDER BY parroquia ASC;";
    $sql = "SELECT * FROM parroquias WHERE id_municipio IN ($id);";
    $query = mysqli_query($conn, $sql);
    $id_item_name = "id_parroquia";
    $item_name = "parroquia";
}
if (mysqli_num_rows($query) > 0) {
    echo "<option value=''></option>";
    while ($row = mysqli_fetch_array($query)) {
        $id_item = $row[$id_item_name];
        $item = $row[$item_name];
?>
        <option value='<?php echo $id_item; ?>'><?php echo $item; ?></option>
<?php
    }
}
?>