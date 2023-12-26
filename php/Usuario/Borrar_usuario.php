<?php
    require_once '../conexion.php';


    $id = $_POST['id'];

    $res = mysqli_query($conn, "DELETE FROM usuarios WHERE Id_usuario = '$id';");
    
    if($res){

        echo'borrado';
        
    }else{

        echo 'usuario no existe';

    }

    closeConection($conn);

?>