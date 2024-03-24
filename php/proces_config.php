<?php
include '../php/Conexion.php';

if (isset($_POST['config'])) {
    if ($_POST['config'] == "add-proposito") {
        $miArray = json_decode($_POST['propositos'], true);

        $query = "INSERT INTO propositos (proposito, estatus) VALUES ";

        foreach ($miArray as $key => $value) {
            $query .= "('$value','activo')";

            if ($value === end($miArray)) {
                $query .= "; ";
            } else {
                $query .= ", ";
            }
        }

        mysqli_query($conn, $query);
    }

    if ($_POST['config'] == "prioritarios") {

        $embalses_prioritarios = $_POST['embalses_prioritarios'];

        $prioritarios = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'prioritarios'");
        if(mysqli_num_rows($prioritarios)>0){
            mysqli_query($conn, "UPDATE configuraciones SET configuracion = '$embalses_prioritarios' WHERE nombre_config = 'prioritarios'");
        }else{
            mysqli_query($conn, "INSERT INTO configuraciones (nombre_config, configuracion) VALUES ('prioritarios', '$embalses_prioritarios')");
        }
    }
}

if (isset($_POST['eliminar'])) {
    $id_prop = $_POST["id_prop"];

    $consulta = "UPDATE propositos 
    SET estatus = 'inactivo'
    WHERE id_proposito = '$id_prop'";

    $resultado = mysqli_query($conn, $consulta);
    if ($resultado) {

        // echo "Los datos se actualizaron correctamente en la base de datos.";
        // header("Location: ../main.php?page=embalses");
        echo "<script>window.location='../main.php?page=configuraciones';</script>";
    } else {
        echo "Ocurrió un error al actualizar los datos: " . mysqli_error($conn);
    }
}

if (isset($_POST['restaurar'])) {
    $id_prop = $_POST["id_prop"];

    $consulta = "UPDATE propositos 
    SET estatus = 'activo'
    WHERE id_proposito = '$id_prop'";

    $resultado = mysqli_query($conn, $consulta);
    if ($resultado) {

        // echo "Los datos se actualizaron correctamente en la base de datos.";
        // header("Location: ../main.php?page=embalses");
        echo "<script>window.location='../main.php?page=configuraciones';</script>";
    } else {
        echo "Ocurrió un error al actualizar los datos: " . mysqli_error($conn);
    }
}

if (isset($_POST['editar'])) {
    $id_prop = $_POST["id_prop"];
    $name_prop = $_POST["name_prop"];

    $consulta = "UPDATE propositos 
    SET proposito = '$name_prop'
    WHERE id_proposito = '$id_prop'";

    $resultado = mysqli_query($conn, $consulta);
    if ($resultado) {

        // echo "Los datos se actualizaron correctamente en la base de datos.";
        // header("Location: ../main.php?page=embalses");
        echo "<script>window.location='../main.php?page=configuraciones';</script>";
    } else {
        echo "Ocurrió un error al actualizar los datos: " . mysqli_error($conn);
    }
}


if (isset($_POST['fecha_inameh'])) {

    $inicio_periodo = $_POST["fecha_inameh"];


    $fecha = "UPDATE configuraciones 
    SET fecha_inameh = '$inicio_periodo'";

    $resultado = mysqli_query($conn, $fecha);
    if ($resultado) {

        // echo "Los datos se actualizaron correctamente en la base de datos.";
        // header("Location: ../main.php?page=embalses");
        echo "<script>window.location='../main.php?page=configuraciones';</script>";
    } else {
        echo "Ocurrió un error al actualizar los datos: " . mysqli_error($conn);
    }
}



if (isset($_POST["fecha_seca"]) && isset($_POST["fecha_lluvia"])) {
    $nueva_fecha_seca = $_POST["fecha_seca"];
    $nueva_fecha_lluvia = $_POST["fecha_lluvia"];

    $nueva_fecha_seca = date("Y-m-d", strtotime($nueva_fecha_seca));
    $nueva_fecha_lluvia = date("Y-m-d", strtotime($nueva_fecha_lluvia));

    $consulta_sql = "UPDATE configuraciones 
                     SET fecha_sequia = '$nueva_fecha_seca', fecha_lluvia = '$nueva_fecha_lluvia'";
    
    $resultado = mysqli_query($conn, $consulta_sql);
    

    if ($resultado) {
       // header("Location: ../main.php?page=configuraciones");
        echo "<script>window.location='../main.php?page=embalses';</script>";

    } else {
        echo "Error al actualizar las fechas en la base de datos.";
    }
} 

?>