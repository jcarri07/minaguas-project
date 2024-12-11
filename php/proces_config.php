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
        if (mysqli_num_rows($prioritarios) > 0) {
            mysqli_query($conn, "UPDATE configuraciones SET configuracion = '$embalses_prioritarios' WHERE nombre_config = 'prioritarios'");
        } else {
            mysqli_query($conn, "INSERT INTO configuraciones (nombre_config, configuracion) VALUES ('prioritarios', '$embalses_prioritarios')");
        }
    }

    if ($_POST['config'] == "consumo-humano") {

        $embalses_consumo_humano = $_POST['embalses_consumo_humano'];

        $consumo_humano = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'consumo_humano'");
        if (mysqli_num_rows($consumo_humano) > 0) {
            mysqli_query($conn, "UPDATE configuraciones SET configuracion = '$embalses_consumo_humano' WHERE nombre_config = 'consumo_humano'");
        } else {
            mysqli_query($conn, "INSERT INTO configuraciones (nombre_config, configuracion) VALUES ('consumo_humano', '$embalses_consumo_humano')");
        }
    }

    date_default_timezone_set('America/Caracas');


    if ($_POST['config'] == "evap-filt") {

        $datos = $_POST['datos'];
        $new_datos = json_decode($datos, true);

        $evap_filt = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'evap_filt'");
        if (mysqli_num_rows($evap_filt) > 0) {
            $config = mysqli_fetch_assoc($evap_filt)['configuracion'];
            $pre_datos = json_decode($config, true);

            foreach ($new_datos as $key => &$fila) {
                if (array_key_exists($key, $pre_datos)) {
                    if ($pre_datos[$key]['evaporacion'] != $fila['evaporacion'] || $pre_datos[$key]['filtracion'] != $fila['filtracion']) {
                        $pre_datos[$key]['fecha'] = date("Y-m-d");
                        $pre_datos[$key]['evaporacion'] = $fila['evaporacion'];
                        $pre_datos[$key]['filtracion'] = $fila['filtracion'];
                    }
                } else {
                    $fila['fecha'] = date("Y-m-d");
                    $pre_datos[$key] = $fila;
                }
            }

            $datos = json_encode($pre_datos);

            mysqli_query($conn, "UPDATE configuraciones SET configuracion = '$datos' WHERE nombre_config = 'evap_filt'");
        } else {
            foreach ($new_datos as &$fila) {
                $fila['fecha'] = date("Y-m-d");
            }
            $datos = json_encode($new_datos);
            mysqli_query($conn, "INSERT INTO configuraciones (nombre_config, configuracion) VALUES ('evap_filt', '$datos')");
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


// if (isset($_POST['fecha_inameh'])) {

//     $inicio_periodo = $_POST["fecha_inameh"];


//     $fecha = "UPDATE configuraciones 
//     SET fecha_inameh = '$inicio_periodo'";

//     $resultado = mysqli_query($conn, $fecha);
//     if ($resultado) {

//         // echo "Los datos se actualizaron correctamente en la base de datos.";
//         // header("Location: ../main.php?page=embalses");
//         echo "<script>window.location='../main.php?page=configuraciones';</script>";
//     } else {
//         echo "Ocurrió un error al actualizar los datos: " . mysqli_error($conn);
//     }
// }



if (isset($_POST["fecha_seca"]) && isset($_POST["fecha_lluvia"])) {
    $nueva_fecha_seca = $_POST["fecha_seca"];
    $nueva_fecha_lluvia = $_POST["fecha_lluvia"];

    $nueva_fecha_seca = date("Y-m-d", strtotime($nueva_fecha_seca));
    $nueva_fecha_lluvia = date("Y-m-d", strtotime($nueva_fecha_lluvia));


    $peri_seco = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'fecha_sequia'");
    if (mysqli_num_rows($peri_seco) > 0) {
        mysqli_query($conn, "UPDATE configuraciones SET configuracion = '$nueva_fecha_seca' WHERE nombre_config = 'fecha_sequia';");
    } else {
        mysqli_query($conn, "INSERT INTO configuraciones (nombre_config, configuracion) VALUES ('fecha_sequia', '$nueva_fecha_seca')");
    }

    $peri_lluvia = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'fecha_lluvia'");
    if (mysqli_num_rows($peri_lluvia) > 0) {
        mysqli_query($conn, "UPDATE configuraciones SET configuracion = '$nueva_fecha_lluvia' WHERE nombre_config = 'fecha_lluvia';");
    } else {
        mysqli_query($conn, "INSERT INTO configuraciones (nombre_config, configuracion) VALUES ('fecha_lluvia', '$nueva_fecha_lluvia')");
    }

    // if ($resultado) {
    //     // header("Location: ../main.php?page=configuraciones");
    echo "<script>window.location='../main.php?page=configuraciones';</script>";
    // } else {
    //     echo "Error al actualizar las fechas en la base de datos.";
    // }
}
