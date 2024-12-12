<?php

require_once '../Conexion.php';
require_once '../batimetria.php';

//PERIODOS INAMEH
$queryInameh = mysqli_query($conn, "SELECT nombre_config, configuracion FROM configuraciones WHERE nombre_config = 'fecha_sequia' OR nombre_config = 'fecha_lluvia' ORDER BY id_config ASC;");
$fechas = mysqli_fetch_all($queryInameh, MYSQLI_ASSOC);

$fecha_sequia = $fechas[0]['configuracion'];
$fecha_lluvia = $fechas[1]['configuracion'];

//EMBALSES - PORCENTAJE Y VARIACION
$embalses_porcentaje = [];
$cantidades_p = [0, 0, 0, 0, 0];
$condiciones = [];

$array_excluidos = [];
$embalses_excluidos = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'consumo_humano';");
if (mysqli_num_rows($embalses_excluidos) > 0) {
    $string_excluidos = mysqli_fetch_assoc($embalses_excluidos)['configuracion'];
    $array_excluidos = explode(",", $string_excluidos);
}


$queryEmbalses = mysqli_query($conn, "SELECT id_embalse, nombre_embalse, norte, este, huso, operador FROM embalses WHERE estatus = 'activo' AND FIND_IN_SET('1', uso_actual);");

while ($row = mysqli_fetch_array($queryEmbalses)) {
    //Saco la ubicacion de los embalses.
    $array = array($row["norte"], $row["este"], $row["huso"]);

    //Calculo del porcentaje.
    $bat = new Batimetria($row["id_embalse"], $conn);
    $porcentaje = $bat->volumenDisponible() != 0 ? (($bat->volumenActualDisponible() * 100) / $bat->volumenDisponible()) : 0;
    array_push($array, $porcentaje);
    // echo $row["nombre_embalse"]." Vol: ".$bat->cargaActual()." -- ";

    // Dependiendo del porcentaje, se asigna su icono, y se cuenta para su categoria.
    if ($porcentaje < 30) {
        array_push($array, "i_rojo");
        $cantidades_p[0] += 1;
        // agregarACondiciones($row["operador"], $condiciones, 1);
    } else if ($porcentaje >= 30 && $porcentaje < 60) {
        array_push($array, "i_azulclaro");
        $cantidades_p[1] += 1;
        // agregarACondiciones($row["operador"], $condiciones, 2);
    } else if ($porcentaje >= 60 && $porcentaje < 90) {
        array_push($array, "i_azul");
        $cantidades_p[2] += 1;
        // agregarACondiciones($row["operador"], $condiciones, 3);
    } else if ($porcentaje >= 90 && $porcentaje <= 100) {
        array_push($array, "i_verde");
        $cantidades_p[3] += 1;
        // agregarACondiciones($row["operador"], $condiciones, 4);
    } else {
        array_push($array, "i_verdeclaro");
        $cantidades_p[4] += 1;
        // agregarACondiciones($row["operador"], $condiciones, 5);
    }

    // Guardo el nombre del embalse
    array_push($array, $row["nombre_embalse"]);
    array_push($array, $row["id_embalse"]);
    array_push($embalses_porcentaje, $array);
}

// function agregarACondiciones($operador, &$array, $porcentaje){
//     if(array_key_exists($operador, $array)){
//         $v = explode("-",$array[$operador]);
//         $v[0] = intval($v[0]) + 1;
//         $v[$porcentaje] = intval($v[$porcentaje]) + 1;
//         $array[$operador] = implode("-", $v);
//         return;
//     }else{
//         $array[$operador] = "0-0-0-0-0-0";
//         agregarACondiciones($operador, $array, $porcentaje);
//     }
// }

// var_dump($condiciones);

$condiciones_actuales1 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, e.norte, e.este, e.huso, e.operador, MAX(d.fecha) AS fecha,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = MAX(d.fecha) AND d.fecha <= '$fecha_sequia' AND h.estatus = 'activo' AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fecha_sequia' AND id_embalse = d.id_embalse) LIMIT 1) AS cota_actual 
    FROM embalses e
    LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha_sequia'
    WHERE e.estatus = 'activo' AND FIND_IN_SET('1', e.uso_actual)
    GROUP BY id_embalse;");

$variacion_sequia = [];
$cantidades_sequia = [0, 0, 0];

// $embalse_variacion1 = [];

while ($row = mysqli_fetch_array($condiciones_actuales1)) {
    $array = array($row["norte"], $row["este"], $row["huso"]);

    $bat = new Batimetria($row["id_embalse"], $conn);
    $fecha = $row['fecha'] != null ? date($row['fecha']) : date("Y-m-d");
    $anio = date("Y", strtotime($fecha));
    $variacion = $bat->volumenActualDisponible() - $bat->getByCota($anio, $row["cota_actual"])[1];

    if ($variacion > 0) {
        array_push($array, "f_arriba");
        $cantidades_sequia[0] += 1;
    } else if ($variacion < 0) {
        array_push($array, "f_abajo");
        $cantidades_sequia[1] += 1;
    } else {
        array_push($array, "f_igual");
        $cantidades_sequia[2] += 1;
    }

    array_push($array, $row["nombre_embalse"]);
    array_push($array, $row["operador"]);
    array_push($array, $variacion);
    array_push($array, $row["id_embalse"]);
    array_push($variacion_sequia, $array);
}

$mapas_hidrologicas_sequia = array_reduce($variacion_sequia, function ($counts, $item) {
    $tipo = $item[5]; // Índice del tipo
    $counts[$tipo] = ($counts[$tipo] ?? 0) + 1; // Incrementar el conteo para este tipo
    return $counts;
}, []);



$condiciones_actuales2 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, e.norte, e.este, e.huso, e.operador, MAX(d.fecha) AS fecha,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.estatus = 'activo' AND h.fecha = MAX(d.fecha) AND d.fecha <= '$fecha_lluvia' AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha_lluvia' AND id_embalse = d.id_embalse) LIMIT 1) AS cota_actual 
    FROM embalses e
    LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha_lluvia'
    WHERE e.estatus = 'activo' AND FIND_IN_SET('1', e.uso_actual)
    GROUP BY id_embalse;");

$variacion_lluvia = [];
$cantidades_lluvia = [0, 0, 0];

while ($row = mysqli_fetch_array($condiciones_actuales2)) {
    $array = array($row["norte"], $row["este"], $row["huso"]);

    $bat = new Batimetria($row["id_embalse"], $conn);
    $fecha = $row['fecha'] != null ? date($row['fecha']) : date("Y-m-d");
    $anio = date("Y", strtotime($fecha));
    $variacion = $bat->volumenActualDisponible() - $bat->getByCota($anio, $row["cota_actual"])[1];

    if ($variacion > 0) {
        array_push($array, "f_arriba");
        $cantidades_lluvia[0] += 1;
    } else if ($variacion < 0) {
        array_push($array, "f_abajo");
        $cantidades_lluvia[1] += 1;
    } else {
        array_push($array, "f_igual");
        $cantidades_lluvia[2] += 1;
    }

    array_push($array, $row["nombre_embalse"]);
    array_push($array, $row["id_embalse"]);
    array_push($array, $row["operador"]);
    array_push($variacion_lluvia, $array);
}

$mapas_hidrologicas_lluvia = array_reduce($variacion_lluvia, function ($counts, $item) {
    $tipo = $item[6]; // Índice del tipo
    $counts[$tipo] = ($counts[$tipo] ?? 0) + 1; // Incrementar el conteo para este tipo
    return $counts;
}, []);

$positions_query = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'marks_posiciones_estatus'");
$positions_markers = mysqli_fetch_assoc($positions_query);
$positions_markers = json_decode($positions_markers["configuracion"], true);

$valores = array($cantidades_p, $cantidades_sequia, $cantidades_lluvia);
$valores = json_encode($valores, true);

$almacenamiento_actual = mysqli_query($conn, "SELECT e.id_embalse,operador,region,nombre_embalse,e.norte, e.este,e.huso,(SELECT da.fecha FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0 ORDER BY da.fecha DESC LIMIT 1) AS fech,               (
SELECT SUM(extraccion)
        FROM detalles_extraccion dex, codigo_extraccion ce
        WHERE ce.id = dex.id_codigo_extraccion AND dex.id_registro = (SELECT id_registro
           FROM datos_embalse h 
           WHERE h.id_embalse = d.id_embalse AND h.estatus = 'activo' AND h.fecha = fech AND cota_actual <> 0 ORDER BY h.hora DESC LIMIT 1) AND (ce.id_tipo_codigo_extraccion = '1' OR ce.id_tipo_codigo_extraccion = '2' OR ce.id_tipo_codigo_extraccion = '3' OR ce.id_tipo_codigo_extraccion = '4')
      ) AS 'extraccion',
      e.nombre_embalse, (SELECT cota_actual 
           FROM datos_embalse h 
           WHERE h.id_embalse = d.id_embalse AND h.estatus = 'activo' AND h.fecha = fech AND cota_actual <> 0 ORDER BY h.hora DESC LIMIT 1) AS cota_actual
      FROM embalses e
LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
WHERE e.estatus = 'activo' AND FIND_IN_SET('1', e.uso_actual)
GROUP BY id_embalse 
ORDER BY id_embalse ASC;");

$datos_embalses = mysqli_fetch_all($almacenamiento_actual, MYSQLI_ASSOC);

$queryReg =  mysqli_query($conn, "SELECT * FROM regiones WHERE estatus = 'activo'");
$totalreg = [];

$evaporacionFiltracion = [];
$queryEvaporacionFiltracion = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'evap_filt';");
if (mysqli_num_rows($queryEvaporacionFiltracion) > 0) {
    $evaporacionFiltracion = json_decode(mysqli_fetch_assoc($queryEvaporacionFiltracion)['configuracion'], true);
}

while ($reg = mysqli_fetch_array($queryReg)) {
    $totalreg[$reg["id_region"]] = $reg["region"];
}

$embalse_abast = [];
$regiones = [];
$countReg = [];
$row = 0;

while ($row < count($datos_embalses)) {
    $emb = new Batimetria($datos_embalses[$row]["id_embalse"], $conn);
    // CALCULO DE ABASTECIMIENTO!!

    if (!in_array($datos_embalses[$row]["id_embalse"], $array_excluidos)) {
        $abastecimiento = 0;
        if ($datos_embalses[$row]["extraccion"] > 0) {
            if (array_key_exists($datos_embalses[$row]["id_embalse"], $evaporacionFiltracion)) {
                $evaporacio = $evaporacionFiltracion[$datos_embalses[$row]["id_embalse"]]["evaporacion"];
                $filtracion = $evaporacionFiltracion[$datos_embalses[$row]["id_embalse"]]["filtracion"];
                $abastecimiento = $emb->abastecimiento($datos_embalses[$row]["extraccion"], $evaporacio, $filtracion);
            } else {
                $abastecimiento = $emb->abastecimiento($datos_embalses[$row]["extraccion"]);
            }
            // $abastecimiento = round((($emb->volumenActualDisponible() * 1000) / $datos_embalses[$row]["extraccion"]) / 30);
        }
        if ($datos_embalses[$row]["extraccion"] == NULL) {
            $abastecimiento = 0;
        }

        if (!in_array($totalreg[$datos_embalses[$row]["region"]], $regiones)) {
            array_push($regiones, $totalreg[$datos_embalses[$row]["region"]]);
            $countReg[$totalreg[$datos_embalses[$row]["region"]]] = 1;
        } else {
            $countReg[$totalreg[$datos_embalses[$row]["region"]]] += 1;
        }

        $icono = "f_igual";

        if (($abastecimiento) <= 4) {
            $icono = "rojo";
        } else if (($abastecimiento) > 4 && ($abastecimiento) <= 8) {
            $icono = "naranja";
        } else if (($abastecimiento) > 8 && ($abastecimiento) <= 12) {
            $icono = "amarillo";
        } else if (($abastecimiento) > 12) {
            $icono = "verde";
        }

        $array = [$datos_embalses[$row]["norte"], $datos_embalses[$row]["este"], $datos_embalses[$row]["huso"], $totalreg[$datos_embalses[$row]["region"]],  $datos_embalses[$row]["nombre_embalse"], $datos_embalses[$row]["id_embalse"], $abastecimiento, $icono];
        array_push($embalse_abast, $array);
    }

    $row++;
}

// var_dump(json_encode($condiciones));
// var_dump($embalses_porcentaje);
// var_dump(implode(" - ",$cantidades_p));
// var_dump(mysqli_fetch_all($condiciones_actuales1));
// var_dump($valores);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../../assets/img/logos/cropped-mminaguas.webp">
    <link rel="stylesheet" href="../../assets/css/leaflet.css" />
    <script src="../../assets/js/leaflet.js"></script>
    <script src="../../assets/js/proj4.js"></script>
    <script src="../../assets/js/html2canvas.min.js"></script>
    <link href="../../assets/css/style-spinner.css" rel="stylesheet" />
    <title>Mapas Estatus</title>
</head>

<style>
    #mapa-portada {
        width: 1200px;
        height: 642px;
    }

    #mapa-periodo-uno {
        width: 1200px;
        height: 642px;
    }

    #mapa-periodo-dos {
        width: 1200px;
        height: 642px;
    }

    #mapa-abastecimiento {
        width: 1200px;
        height: 642px;
    }

    .map-container-hidros {
        width: 900px;
        height: 504px;
        position: absolute;
        top: -100%;
    }

    /* .leaflet-top.leaflet-left {
        display: none;
    }

    .leaflet-popup-content-wrapper {
        text-align: center;
        background-color: rgba(255, 255, 255, 1);
        color: black;
        font-size: 8px;
        padding: 0;
        box-shadow: 0 3px 14px rgba(0, 0, 0, 0.1);
    }


    .leaflet-popup-content {
        background-color: rgba(0, 0, 0, 0);
        margin: 8px 10px;
    }

    .leaflet-popup-close-button {
        display: none;
    }

    .leaflet-popup-tip-container {
        margin-top: -8px;
    }

    .leaflet-popup-tip {
        background-color: rgba(0, 0, 0, 0);
        color: rgba(0, 0, 0, 0);
        box-shadow: 0 3px 14px rgba(0, 0, 0, 0.1);
    }

    .leaflet-popup.leaflet-zoom-animated {
        margin-bottom: 5.5px;
    } */

    .leaflet-popup {
        margin-bottom: 0px !important;
        /* bottom: 7px !important; */
    }

    .leaflet-popup-tip {
        pointer-events: none !important;
    }

    .leaflet-popup-content-wrapper {
        text-align: center !important;
        /* background-color: rgba(255, 255, 255, 1) !important; */
        background: rgba(255, 255, 255, 0) !important;
        color: black !important;
        font-size: 8px !important;
        padding: 0 !important;
        box-shadow: 0 3px 14px rgba(0, 0, 0, 0) !important;
    }


    .leaflet-popup-content {
        background-color: rgba(0, 0, 0, 0) !important;
        margin: 0px !important;
        width: 100% !important;
        position: relative !important;
        /* margin: 8px 10px !important; */
    }

    .leaflet-popup-close-button {
        display: none !important;
    }

    .leaflet-popup-tip-container {
        /* margin-top: -8px !important; */
    }

    .leaflet-popup-tip {
        background-color: rgba(0, 0, 0, 0) !important;
        color: rgba(0, 0, 0, 0) !important;
        box-shadow: 0 3px 14px rgba(0, 0, 0, 0) !important;
    }

    .leaflet-popup.leaflet-zoom-animated {
        /* margin-bottom: 5.5px !important; */
    }

    .markleaflet {
        position: absolute;
        display: flex;
        flex-direction: row;
        gap: 2px;
        text-wrap: nowrap;
        background-color: rgba(255, 255, 255, 0.5);
        padding-top: 0;
        padding-bottom: 0;
        height: 10px;
        /* width: 60px; */
    }

    .mark-t {
        transform: translateX(-50%);
        top: -22px
    }

    .mark-tr {
        top: -22px;
        /* left: 4px; */
    }

    .mark-r {
        left: 7px;
        top: -11px;
    }

    .mark-br {
        /* left: 4px; */
    }

    .mark-b {
        transform: translateX(-50%);
    }

    .mark-bl {
        transform: translateX(-100%);
        /* left: -4px; */
    }

    .mark-l {
        transform: translateX(-100%);
        left: -7px;
        top: -11px;
    }

    .mark-tl {
        transform: translateX(-100%);
        top: -22px;
        /* left: -4px; */
    }

    /* clases para los popups large */
    .mark-t-large {
        transform: translateX(-50%);
        top: -32px;
        font-size: 14px;
    }

    .mark-tr-large {
        top: -32px;
        font-size: 14px;
        /* left: 4px; */
    }

    .mark-r-large {
        left: 15px;
        top: -16px;
        font-size: 14px;
    }

    .mark-br-large {
        /* left: 4px; */
        font-size: 14px;
    }

    .mark-b-large {
        transform: translateX(-50%);
        font-size: 14px;
    }

    .mark-bl-large {
        transform: translateX(-100%);
        font-size: 14px;
        /* left: -4px; */
    }

    .mark-l-large {
        transform: translateX(-100%);
        left: -15px;
        top: -16px;
        font-size: 14px;
    }

    .mark-tl-large {
        transform: translateX(-100%);
        top: -32px;
        font-size: 14px;
        /* left: -4px; */
    }

    .nombre-estado {
        font-size: 8px;
        padding: 1px;
        background-color: transparent;
    }

    .leaflet-tooltip {
        background-color: transparent !important;
        box-shadow: none !important;
        border: none !important;
    }
</style>

<body id="body-mapas" style="height:800px">

    <!-- Cantidades de embalse por porcentaje de volumen -->
    <div id="mapa-portada" style="position:absolute; top:-100%;"></div>
    <br>
    <!-- Variacion de volumen respecto a la fecha de sequia -->
    <div id="mapa-periodo-uno" style="position:absolute; top:-100%;"></div>
    <br>
    <!-- Variacion de volumen respecto a la fecha de lluvia -->
    <div id="mapa-periodo-dos" style="position:absolute; top:-100%;"></div>
    <div id="mapa-abastecimiento" style="position:absolute; top:-100%;"></div>
    <div class="loaderPDF">
        <div class="lds-dual-ring"></div>
    </div>
</body>

<script>
    // Creación de íconos
    var PointIcon = L.Icon.extend({
        options: {
            shadowUrl: '../../assets/icons/i-sombra.png',
            iconSize: [12, 12],
            shadowSize: [0, 0],
            shadowAnchor: [8, 8],
        }
    });

    var ArrowIcon = L.Icon.extend({
        options: {
            iconSize: [15, 10],
        }
    });

    var PointIconLarge = L.Icon.extend({
        options: {
            shadowUrl: '../../assets/icons/i-sombra.png',
            iconSize: [24, 24],
            shadowSize: [0, 0],
            shadowAnchor: [8, 8],
        }
    });

    var ArrowIconLarge = L.Icon.extend({
        options: {
            iconSize: [30, 20],
        }
    });

    var i_rojo = new PointIcon({
        iconUrl: '../../assets/icons/i-rojo.png'
    })
    var i_azulclaro = new PointIcon({
        iconUrl: '../../assets/icons/i-azulclaro.png'
    })
    var i_azul = new PointIcon({
        iconUrl: '../../assets/icons/i-azul.png'
    })
    var i_verde = new PointIcon({
        iconUrl: '../../assets/icons/i-verde.png'
    })
    var i_verdeclaro = new PointIcon({
        iconUrl: '../../assets/icons/i-verdeclaro.png'
    })
    var f_arriba = new ArrowIcon({
        iconUrl: '../../assets/icons/f-arriba.png'
    })
    var f_abajo = new ArrowIcon({
        iconUrl: '../../assets/icons/f-abajo.png'
    })
    var f_igual = new ArrowIcon({
        iconUrl: '../../assets/icons/f-igual.png'
    })

    var i_rojo_large = new PointIconLarge({
        iconUrl: '../../assets/icons/i-rojo.png'
    })
    var i_azulclaro_large = new PointIconLarge({
        iconUrl: '../../assets/icons/i-azulclaro.png'
    })
    var i_azul_large = new PointIconLarge({
        iconUrl: '../../assets/icons/i-azul.png'
    })
    var i_verde_large = new PointIconLarge({
        iconUrl: '../../assets/icons/i-verde.png'
    })
    var i_verdeclaro_large = new PointIconLarge({
        iconUrl: '../../assets/icons/i-verdeclaro.png'
    })
    var f_arriba_large = new ArrowIconLarge({
        iconUrl: '../../assets/icons/f-arriba.png'
    })
    var f_abajo_large = new ArrowIconLarge({
        iconUrl: '../../assets/icons/f-abajo.png'
    })
    var f_igual_large = new ArrowIconLarge({
        iconUrl: '../../assets/icons/f-igual.png'
    })

    var rojo = L.divIcon({
        className: '', // Evitar estilos predeterminados
        html: '<div style="width: 15px; height: 15px; background-color: #ff0000; border-radius: 50%; border: 0.5px solid black;"></div>',
        iconSize: [12, 12], // Tamaño del ícono (coincide con el tamaño del div)
    });
    var naranja = L.divIcon({
        className: '', // Evitar estilos predeterminados
        html: '<div style="width: 15px; height: 15px; background-color: #ffaa00; border-radius: 50%; border: 0.5px solid black;"></div>',
        iconSize: [12, 12], // Tamaño del ícono (coincide con el tamaño del div)
    });
    var amarillo = L.divIcon({
        className: '', // Evitar estilos predeterminados
        html: '<div style="width: 15px; height: 15px; background-color: #ffff00; border-radius: 50%; border: 0.5px solid black;"></div>',
        iconSize: [12, 12], // Tamaño del ícono (coincide con el tamaño del div)
    });
    var verde = L.divIcon({
        className: '', // Evitar estilos predeterminados
        html: '<div style="width: 15px; height: 15px; background-color: #70ad47; border-radius: 50%; border: 0.5px solid black;"></div>',
        iconSize: [12, 12], // Tamaño del ícono (coincide con el tamaño del div)
    });

    var rojo_small = L.divIcon({
        className: '', // Evitar estilos predeterminados
        html: '<div style="width: 10px; height: 10px; background-color: #ff0000; border-radius: 50%; border: 0.5px solid black;"></div>',
        iconSize: [12, 12], // Tamaño del ícono (coincide con el tamaño del div)
    });
    var naranja_small = L.divIcon({
        className: '', // Evitar estilos predeterminados
        html: '<div style="width: 10px; height: 10px; background-color: #ffaa00; border-radius: 50%; border: 0.5px solid black;"></div>',
        iconSize: [12, 12], // Tamaño del ícono (coincide con el tamaño del div)
    });
    var amarillo_small = L.divIcon({
        className: '', // Evitar estilos predeterminados
        html: '<div style="width: 10px; height: 10px; background-color: #ffff00; border-radius: 50%; border: 0.5px solid black;"></div>',
        iconSize: [12, 12], // Tamaño del ícono (coincide con el tamaño del div)
    });
    var verde_small = L.divIcon({
        className: '', // Evitar estilos predeterminados
        html: '<div style="width: 10px; height: 10px; background-color: #70ad47; border-radius: 50%; border: 0.5px solid black;"></div>',
        iconSize: [12, 12], // Tamaño del ícono (coincide con el tamaño del div)
    });
    // Cargar el archivo GeoJSON usando fetch

    var highlightStyle = {
        "color": "#9c9c9c", //Color de delineado
        "weight": 2, //Ancho de delineado
        "opacity": 0.6, //Opacidad del delineado
        "fillColor": "#ffd700", // Color de relleno
        "fillOpacity": 0 //Opacidad de relleno
    };

    //Funcion para mostrar etiquetas con los nombres de los Estados
    function onEachFeature(feature, layer) {
        if (feature.properties && feature.properties.ESTADO) {
            layer.bindPopup(feature.properties.ESTADO); // Muestra el nombre en un popup
            layer.bindTooltip(feature.properties.ESTADO, {
                permanent: true,
                className: "nombre-estado",
                direction: "center",
                interactive: true
            }); // Muestra el nombre como una etiqueta
        }
    }




    // Creación de los mapas

    var mapa_portada = L.map('mapa-portada', {
        zoomControl: false,
    }).setView([9, -67], 7);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapa_portada);

    // L.tileLayer('https://{s}.tile.thunderforest.com/atlas/{z}/{x}/{y}.png?apikey={apikey}', {
    //     maxZoom: 19,
    //     attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors, Tiles courtesy of <a href="https://www.thunderforest.com/transport/">Andy Allan</a>',
    //     apikey: '38db809be13a400c8c5061e304ba99cd' // Reemplaza esto con tu clave de API de Thunderforest
    // }).addTo(mapa_portada);

    //Añadiendo los marcadores al mapa de la portada.
    var ubicacion;

    <?php
    foreach ($embalses_porcentaje as $emb) {
        if ($emb[0] != "" && $emb[1] != "" && $emb[2] != "") {

            $posicion = "t";
            if ($positions_markers[$emb[6]]) {
                $posicion = $positions_markers[$emb[6]];
            }
    ?>
            // console.log("Prueba");
            ubicacion = geoToUtm(<?php echo $emb[0] . "," . $emb[1] . "," . $emb[2] ?>)

            var marker = L.marker([ubicacion[0], ubicacion[1]], {
                icon: <?php echo $emb[4] ?>
            }).addTo(mapa_portada).bindPopup('<div class="markleaflet mark-<?php echo $posicion ?>"><b><?php echo $emb[5] ?></b></div>', {
                autoClose: false,
                closeOnClick: false
            }).openPopup();
    <?php }
    }
    ?>


    // L.marker([8, -66], {
    //     icon: i_azulclaro
    // }).addTo(mapa_portada).bindPopup("<b>Camatagua</b>", {
    //     autoClose: false,
    //     closeOnClick: false
    // }).openPopup();

    var mapa_per_uno = L.map('mapa-periodo-uno', {
        zoomControl: false,
    }).setView([9, -67], 7);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapa_per_uno);

    // L.tileLayer('https://{s}.tile.thunderforest.com/atlas/{z}/{x}/{y}.png?apikey={apikey}', {
    //     maxZoom: 19,
    //     attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors, Tiles courtesy of <a href="https://www.thunderforest.com/transport/">Andy Allan</a>',
    //     apikey: '38db809be13a400c8c5061e304ba99cd' // Reemplaza esto con tu clave de API de Thunderforest
    // }).addTo(mapa_per_uno);

    //Añadiendo los marcadores al mapa de la variacion de sequia.

    <?php
    foreach ($variacion_sequia as $emb) {
        if ($emb[0] != "" && $emb[1] != "" && $emb[2] != "") {
            $posicion = "t";
            if ($positions_markers[$emb[7]]) {
                $posicion = $positions_markers[$emb[7]];
            }
    ?>

            ubicacion = geoToUtm(<?php echo $emb[0] . "," . $emb[1] . "," . $emb[2] ?>)

            var marker = L.marker([ubicacion[0], ubicacion[1]], {
                icon: <?php echo $emb[3] ?>
            }).addTo(mapa_per_uno).bindPopup('<div class="markleaflet mark-<?php echo $posicion ?>"><b><?php echo $emb[4] ?></b></div>', {
                autoClose: false,
                closeOnClick: false
            }).openPopup();
    <?php }
    }
    ?>

    var mapa_per_dos = L.map('mapa-periodo-dos', {
        zoomControl: false,
    }).setView([9, -67], 7);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapa_per_dos);

    var mapa_abastecimiento = L.map('mapa-abastecimiento', {
        zoomControl: false,
    }).setView([9, -67], 7);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapa_abastecimiento);


    fetch('../../pages/venezuela.geojson')
        .then(response => response.json())
        .then(data => {

            // Aplicar offset manual a las coordenadas del GeoJSON
            const offsetLat = -1.4; // Offset en latitud
            const offsetLng = 2.65; // Offset en longitud

            const applyOffset = (coordinates) => {
                // Recursivamente ajusta las coordenadas dependiendo del nivel de anidación
                if (typeof coordinates[0] === 'number') {
                    // Es un punto [lng, lat]
                    return [coordinates[0] + offsetLng, coordinates[1] + offsetLat];
                } else {
                    // Es una colección de puntos o polígonos
                    return coordinates.map(applyOffset);
                }
            };

            const geoJsonWithOffset = {
                ...data,
                features: data.features.map(feature => ({
                    ...feature,
                    geometry: {
                        ...feature.geometry,
                        coordinates: applyOffset(feature.geometry.coordinates),
                    },
                })),
            };

            // Crear el layer GeoJSON y añadirlo al mapa
            L.geoJSON(geoJsonWithOffset, {
                style: highlightStyle,
                // onEachFeature: onEachFeature,
            }).addTo(mapa_portada);

            L.geoJSON(geoJsonWithOffset, {
                style: highlightStyle,
                // onEachFeature: onEachFeature,
            }).addTo(mapa_per_uno);

            L.geoJSON(geoJsonWithOffset, {
                style: highlightStyle,
                // onEachFeature: onEachFeature,
            }).addTo(mapa_per_dos);

            L.geoJSON(geoJsonWithOffset, {
                style: highlightStyle,
                // onEachFeature: onEachFeature,
            }).addTo(mapa_abastecimiento);

            L.geoJSON(data, {
                style: {
                    ...highlightStyle,
                    opacity: 0,
                },
                onEachFeature: onEachFeature,
            }).addTo(mapa_portada);

            L.geoJSON(data, {
                style: {
                    ...highlightStyle,
                    opacity: 0,
                },
                onEachFeature: onEachFeature,
            }).addTo(mapa_per_uno);

            L.geoJSON(data, {
                style: {
                    ...highlightStyle,
                    opacity: 0,
                },
                onEachFeature: onEachFeature,
            }).addTo(mapa_per_dos);

            L.geoJSON(data, {
                style: {
                    ...highlightStyle,
                    opacity: 0,
                },
                onEachFeature: onEachFeature,
            }).addTo(mapa_abastecimiento);
        })
        .catch(err => console.error('Error al cargar el archivo GeoJSON:', err));

    // L.tileLayer('https://{s}.tile.thunderforest.com/atlas/{z}/{x}/{y}.png?apikey={apikey}', {
    //     maxZoom: 19,
    //     attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors, Tiles courtesy of <a href="https://www.thunderforest.com/transport/">Andy Allan</a>',
    //     apikey: '38db809be13a400c8c5061e304ba99cd' // Reemplaza esto con tu clave de API de Thunderforest
    // }).addTo(mapa_per_dos);

    <?php
    foreach ($variacion_lluvia as $emb) {
        if ($emb[0] != "" && $emb[1] != "" && $emb[2] != "") {
            $posicion = "t";
            if ($positions_markers[$emb[5]]) {
                $posicion = $positions_markers[$emb[5]];
            }
    ?>

            ubicacion = geoToUtm(<?php echo $emb[0] . "," . $emb[1] . "," . $emb[2] ?>)

            var marker = L.marker([ubicacion[0], ubicacion[1]], {
                icon: <?php echo $emb[3] ?>
            }).addTo(mapa_per_dos).bindPopup('<div class="markleaflet mark-<?php echo $posicion ?>"><b><?php echo $emb[4] ?></b></div>', {
                autoClose: false,
                closeOnClick: false
            }).openPopup();
    <?php }
    }
    ?>
    <?php
    foreach ($embalse_abast as $emb) {
        if ($emb[0] != "" && $emb[1] != "" && $emb[2] != "" && $emb[6] <= 12) {
            $posicion = "t";
            if ($positions_markers[$emb[5]]) {
                $posicion = $positions_markers[$emb[5]];
            }
    ?>

            ubicacion = geoToUtm(<?php echo $emb[0] . "," . $emb[1] . "," . $emb[2] ?>)

            var marker = L.marker([ubicacion[0], ubicacion[1]], {
                icon: <?php echo $emb[7] . "_small" ?>
            }).addTo(mapa_abastecimiento).bindPopup('<div class="markleaflet mark-<?php echo $posicion ?>"><b><?php echo $emb[4] ?></b></div>', {
                autoClose: false,
                closeOnClick: false
            }).openPopup();
    <?php }
    } ?>

    const mapsContainer = document.getElementById('body-mapas');
    let mapDiv = null;
    let map = null;
    let marker_embalses = [];
    let bounds = null;
    <?php
    foreach ($mapas_hidrologicas_sequia as $key => $value) { ?>
        mapDiv = document.createElement('div');
        mapDiv.id = "<?php echo $key . "-sequia" ?>";
        mapDiv.className = 'map-container-hidros';
        mapsContainer.appendChild(mapDiv);
        marker_embalses = [];
        // Inicializar el mapa en el div creado
        map = L.map(mapDiv.id, {
            zoomControl: false,
        }).setView([9, -67], 8);



        // Agregar un tile layer
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {

        }).addTo(map);

        <?php

        $filter_array = array_filter($variacion_sequia, function ($k) use ($key) {
            return $k[5] == $key;
        });

        foreach ($filter_array as $emb) {
            if ($emb[0] != "" && $emb[1] != "" && $emb[2] != "") {
                $posicion = "t";
                if ($positions_markers[$emb[7]]) {
                    $posicion = $positions_markers[$emb[7]];
                }
        ?>

                ubicacion = geoToUtm(<?php echo $emb[0] . "," . $emb[1] . "," . $emb[2] ?>)
                marker_embalses.push(ubicacion);

                var marker = L.marker([ubicacion[0], ubicacion[1]], {
                    icon: <?php echo $emb[3] . "_large" ?>
                }).addTo(map).bindPopup('<div class="markleaflet mark-<?php echo $posicion . "-large" ?>"><b><?php echo $emb[4] ?></b></div>', {
                    autoClose: false,
                    closeOnClick: false
                }).openPopup();
        <?php }
        } ?>

        bounds = L.latLngBounds(marker_embalses);
        map.fitBounds(bounds);
        if (marker_embalses.length == 1) {
            map.setZoom(8);
        }

    <?php
    }
    ?>

    <?php
    foreach ($mapas_hidrologicas_lluvia as $key => $value) { ?>
        mapDiv = document.createElement('div');
        mapDiv.id = "<?php echo $key . "-lluvia" ?>";
        mapDiv.className = 'map-container-hidros';
        mapsContainer.appendChild(mapDiv);
        marker_embalses = [];

        // Inicializar el mapa en el div creado
        map = L.map(mapDiv.id, {
            zoomControl: false,
        }).setView([9, -67], 8);

        // Agregar un tile layer
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {

        }).addTo(map);

        <?php

        $filter_array = array_filter($variacion_lluvia, function ($k) use ($key) {
            return $k[6] == $key;
        });

        foreach ($filter_array as $emb) {
            if ($emb[0] != "" && $emb[1] != "" && $emb[2] != "") {
                $posicion = "t";
                if ($positions_markers[$emb[5]]) {
                    $posicion = $positions_markers[$emb[5]];
                }
        ?>

                ubicacion = geoToUtm(<?php echo $emb[0] . "," . $emb[1] . "," . $emb[2] ?>)
                marker_embalses.push(ubicacion);

                var marker = L.marker([ubicacion[0], ubicacion[1]], {
                    icon: <?php echo $emb[3] . "_large" ?>
                }).addTo(map).bindPopup('<div class="markleaflet mark-<?php echo $posicion . "-large" ?>"><b><?php echo $emb[4] ?></b></div>', {
                    autoClose: false,
                    closeOnClick: false
                }).openPopup();
        <?php }
        } ?>

        bounds = L.latLngBounds(marker_embalses);
        map.fitBounds(bounds);
        if (marker_embalses.length == 1) {
            map.setZoom(8);
        }

    <?php
    }


    foreach ($regiones as $region) { ?>
        mapDiv = document.createElement('div');
        mapDiv.id = "<?php echo $region ?>";
        mapDiv.className = 'map-container-hidros';
        mapsContainer.appendChild(mapDiv);
        marker_embalses = [];
        // Inicializar el mapa en el div creado
        map = L.map(mapDiv.id, {
            zoomControl: false,
        }).setView([9, -67], 8);

        // Agregar un tile layer
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {

        }).addTo(map);

        <?php

        $filter_array = array_filter($embalse_abast, function ($k) use ($region) {
            return $k[3] == $region;
        });

        foreach ($filter_array as $emb) {
            if ($emb[0] != "" && $emb[1] != "" && $emb[2] != "") {
                $posicion = "t";
                if ($positions_markers[$emb[5]]) {
                    $posicion = $positions_markers[$emb[5]];
                }
        ?>

                ubicacion = geoToUtm(<?php echo $emb[0] . "," . $emb[1] . "," . $emb[2] ?>)
                marker_embalses.push(ubicacion);

                var marker = L.marker([ubicacion[0], ubicacion[1]], {
                    icon: <?php echo $emb[7] ?>
                }).addTo(map).bindPopup('<div class="markleaflet mark-<?php echo $posicion . "-large" ?>"><b><?php echo $emb[4] ?></b></div>', {
                    autoClose: false,
                    closeOnClick: false
                }).openPopup();
        <?php }
        } ?>

        bounds = L.latLngBounds(marker_embalses);
        map.fitBounds(bounds);
        if (marker_embalses.length == 1) {
            map.setZoom(8);
        }

    <?php

    }


    ?>

    //funcion para pasar de coordenadas geograficas a coordenadas UTM
    function geoToUtm(norte, este, huso) {
        norte = parseFloat(norte);
        este = parseFloat(este);
        huso = parseInt(huso)

        proj4.defs("EPSG:326" + huso, "+proj=utm +zone=" + huso + " +datum=WGS84 +units=m +no_defs");

        proj4.defs("EPSG:4326", "+proj=longlat +datum=WGS84 +no_defs");
        var coordenadasGeograficas = proj4("EPSG:326" + huso, "EPSG:4326", [este, norte]);

        var latitud = coordenadasGeograficas[1];
        var longitud = coordenadasGeograficas[0];

        return [latitud, longitud];
    }

    // window.addEventListener('load', function() {
    // setTimeout(function() {
    window.addEventListener('load', function() {
        setTimeout(function() {
            const x = document.querySelector("#mapa-portada");
            html2canvas(x, {
                useCORS: true,
                width: x.offsetWidth,
                height: x.offsetHeight,
            }).then(function(canvas) { //PROBLEMAS
                //$("#ca").append(canvas);
                canvas.willReadFrequently = true,
                    dataURL = canvas.toDataURL("image/jpeg", 0.9);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../guardar-imagen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'estatus-mapa'; ?>&numero=' + 1);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {

                        console.log("listo");

                    } else {

                    }
                }
            });

            const y = document.querySelector("#mapa-periodo-uno");
            html2canvas(y, {
                useCORS: true,
                width: y.offsetWidth,
                height: y.offsetHeight,
            }).then(function(canvas) { //PROBLEMAS
                //$("#ca").append(canvas);
                canvas.willReadFrequently = true,

                    dataURL = canvas.toDataURL("image/jpeg", 0.9);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../guardar-imagen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'estatus-mapa'; ?>&numero=' + 2);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {

                        console.log("listo");

                    } else {

                    }
                }
            });

            const q = document.querySelector("#mapa-abastecimiento");
            html2canvas(q, {
                useCORS: true,
                width: q.offsetWidth,
                height: q.offsetHeight,
            }).then(function(canvas) { //PROBLEMAS
                //$("#ca").append(canvas);
                canvas.willReadFrequently = true,
                    dataURL = canvas.toDataURL("image/jpeg", 0.9);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../guardar-imagen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'estatus-mapa'; ?>&numero=' + 4);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {

                        console.log("listo");

                    } else {

                    }
                }
            });

            const z = document.querySelector("#mapa-periodo-dos");
            html2canvas(z, {
                useCORS: true,
                width: z.offsetWidth,
                height: z.offsetHeight,
            }).then(function(canvas) { //PROBLEMAS
                //$("#ca").append(canvas);
                canvas.willReadFrequently = true,
                    dataURL = canvas.toDataURL("image/jpeg", 0.9);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../guardar-imagen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'estatus-mapa'; ?>&numero=' + 3);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if (this.responseText == "si") {
                            console.log("listo");
                            location.href = "graficas_estatus.php?valores=<?php echo $valores; ?>";
                        } else {
                            console.log(this.responseText);
                        }
                    }
                }
            });



            //CICLOS DE MAPAS POR HIDROLOGICA

            const mapContainers = document.querySelectorAll('.map-container-hidros'); // Asegúrate de que tus mapas tengan esta clase

            mapContainers.forEach((mapContainer, index) => {
                // Capturar cada mapa con html2canvas
                console.log(mapContainer);
                html2canvas(mapContainer, {
                    useCORS: true,
                    width: mapContainer.offsetWidth,
                    height: mapContainer.offsetHeight,
                }).then(function(canvas) {
                    // Convertir a dataURL
                    const dataURL = canvas.toDataURL("image/jpeg", 0.9);

                    // Enviar la imagen al servidor
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '../guardar-imagen.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send(`imagen=${dataURL}&nombre=hidro-mapa&numero=${mapContainer.id}`);
                    xhr.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            console.log(`Mapa ${mapContainer.id} listo`);
                        } else {
                            console.error(`Error al procesar el mapa ${mapContainer.id}`);
                        }
                    }
                }).catch(err => {
                    console.error(`Error capturando el mapa ${mapContainer.id}:`, err);
                });
            });

        }, 5000);
    });
    // });
</script>
<?php closeConection($conn); ?>

</html>