<?php
    require_once '../../Conexion.php';
    date_default_timezone_set("America/Caracas");
    session_start();
    
    $opc = $_POST['opc']; 

    if($opc == "add"){
        $cota = $_POST['cota'];
        $id_encargado = $_SESSION["Id_usuario"];
        $id_embalse = $_POST["id_embalse"];

        $tipo_extraccion = json_decode($_POST['tipo_extraccion']);
        $valor_extraccion = json_decode($_POST['valor_extraccion']);

        if($_SESSION["Tipo"] == "Admin"){
            $fecha = $_POST["fecha"];
            $hora = $_POST["hora"];
        }
        else{
            $fecha = date("Y-m-d");
            $hora = date("H:i") . ":00";
        }

        $res = mysqli_query($conn, "INSERT INTO datos_embalse (id_embalse, fecha, hora, cota_actual, id_encargado, estatus) VALUES ('$id_embalse', '$fecha', '$hora', '$cota', '$id_encargado', 'activo');");
        sleep(0.3);

        if($res == 1){
            $sql = "SELECT id_registro FROM datos_embalse WHERE id_embalse = '$id_embalse' AND id_encargado = '$id_encargado' ORDER BY id_registro DESC LIMIT 1;";
            $query = mysqli_query($conn, $sql);
            $id_registro = mysqli_fetch_array($query)['id_registro'];

            for($i = 0 ; $i < count($tipo_extraccion) ; $i++){
                $tipo_extraccion_aux = $tipo_extraccion[$i];
                $valor_extraccion_aux = $valor_extraccion[$i];

                $sql = "INSERT INTO detalles_extraccion (id_codigo_extraccion, extraccion, id_registro, estatus) VALUES ('$tipo_extraccion_aux', '$valor_extraccion_aux', '$id_registro', 'activo');";

                mysqli_query($conn, $sql);
            }

            echo 'si';
        }

        


    }
    if($opc == "delete"){
        $id_registro = $_POST['id_registro'];

        $sql = "UPDATE datos_embalse SET estatus = 'inactivo' WHERE id_registro = '$id_registro';";
        $res = mysqli_query($conn, $sql);

        if($res == 1)
            echo 'si';
    }
    /*if($opc == "edit"){
        $fecha_inicio = $_POST['fecha_inicio']; 
        $fecha_fin = $_POST['fecha_fin'];
        $costo_inscripcion = $_POST['costo_inscripcion'];
        $costo_mensualidad = $_POST['costo_mensualidad'];
        $cant_mensualidades = $_POST['cant_mensualidades'];
        $id_sucursal = $_POST['id_sucursal'];
        $id_curso = $_POST['id_curso'];
        $id_profesor = $_POST['id_profesor'];

        $query = mysqli_query($db, "SELECT * FROM periodo WHERE id_periodo = '$id' ;");
        $row = mysqli_fetch_array($query);

        if( ($fecha_inicio == $row['fecha_inicio']) &&
            ($fecha_fin == $row['fecha_fin']) && 
            ($costo_inscripcion == $row['costo_inscripcion']) &&
            ($costo_mensualidad == $row['costo_mensualidad']) &&
            ($cant_mensualidades == $row['cant_mensualidades']) &&
            ($id_sucursal == $row['id_sucursal']) && 
            ($id_curso == $row['id_curso']) &&
            ($id_profesor == $row['id_profesor'])
        ){
            echo 'vacio';
        }
        else{
            $res = "";

            if($fecha_inicio != $row['fecha_inicio']){
                $sql = "UPDATE periodo SET fecha_inicio = '$fecha_inicio' WHERE id_periodo = '$id';";
                $res = mysqli_query($db, $sql);
            }
            if($fecha_fin != $row['fecha_fin']){
                $sql = "UPDATE periodo SET fecha_fin = '$fecha_fin' WHERE id_periodo = '$id';";
                $res = mysqli_query($db, $sql);
            }
            if($costo_inscripcion != $row['costo_inscripcion']){
                $sql = "UPDATE periodo SET costo_inscripcion = '$costo_inscripcion' WHERE id_periodo = '$id';";
                $res = mysqli_query($db, $sql);
            }
            if($costo_mensualidad != $row['costo_mensualidad']){
                $sql = "UPDATE periodo SET costo_mensualidad = '$costo_mensualidad' WHERE id_periodo = '$id';";
                $res = mysqli_query($db, $sql);
            }
            if($cant_mensualidades != $row['cant_mensualidades']){
                $sql = "UPDATE periodo SET cant_mensualidades = '$cant_mensualidades' WHERE id_periodo = '$id';";
                $res = mysqli_query($db, $sql);
            }
            if($id_sucursal != $row['id_sucursal']){
                $sql = "UPDATE periodo SET id_sucursal = '$id_sucursal' WHERE id_periodo = '$id';";
                $res = mysqli_query($db, $sql);
            }
            if($id_curso != $row['id_curso']){
                $sql = "UPDATE periodo SET id_curso = '$id_curso' WHERE id_periodo = '$id';";
                $res = mysqli_query($db, $sql);
            }
            if($id_profesor != $row['id_profesor']){
                $sql = "UPDATE periodo SET id_profesor = '$id_profesor' WHERE id_periodo = '$id';";
                $res = mysqli_query($db, $sql);
            }

            if($res == 1){
                echo 'si';
            }
        }

    }

    if($opc == 'delete'){//Eliminar
        $sql = "UPDATE periodo SET estatus = 'Terminado' WHERE id_periodo = '$id';";
        $res = mysqli_query($db, $sql);
        if($res == 1)
            echo 'si';
    }*/
    closeConection($conn);
?>