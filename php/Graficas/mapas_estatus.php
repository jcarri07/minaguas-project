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

$queryEmbalses = mysqli_query($conn, "SELECT id_embalse, nombre_embalse, norte, este, huso, operador FROM embalses WHERE estatus = 'activo';");

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

$condiciones_actuales1 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, e.norte, e.este, e.huso, e.operador, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fecha_sequia' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = MAX(d.fecha) AND d.fecha <= '$fecha_sequia' AND h.estatus = 'activo' AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fecha_sequia' AND id_embalse = d.id_embalse) LIMIT 1) AS cota_actual 
    FROM embalses e
    LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha_sequia'
    WHERE e.estatus = 'activo' 
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

$condiciones_actuales2 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, e.norte, e.este, e.huso, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fecha_lluvia' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.estatus = 'activo' AND h.fecha = MAX(d.fecha) AND d.fecha <= '$fecha_lluvia' AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha_lluvia' AND id_embalse = d.id_embalse) LIMIT 1) AS cota_actual 
    FROM embalses e
    LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha_lluvia'
    WHERE e.estatus = 'activo' 
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
    array_push($variacion_lluvia, $array);
}

$positions_query = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'marks_posiciones'");
$positions_markers = mysqli_fetch_assoc($positions_query);
$positions_markers = json_decode($positions_markers["configuracion"], true);

$valores = array($cantidades_p, $cantidades_sequia, $cantidades_lluvia);
$valores = json_encode($valores, true);
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
        height: 600px;
    }

    #mapa-periodo-uno {
        width: 1200px;
        height: 600px;
    }

    #mapa-periodo-dos {
        width: 1200px;
        height: 600px;
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
</style>

<body style="height:800px">

    <!-- Cantidades de embalse por porcentaje de volumen -->
    <div id="mapa-portada" style="position:absolute; top:-100%;"></div>
    <br>
    <!-- Variacion de volumen respecto a la fecha de sequia -->
    <div id="mapa-periodo-uno" style="position:absolute; top:-100%;"></div>
    <br>
    <!-- Variacion de volumen respecto a la fecha de lluvia -->
    <div id="mapa-periodo-dos" style="position:absolute; top:-100%;"></div>
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

    // Creación de los mapas

    var mapa_portada = L.map('mapa-portada').setView([9, -67], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
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

    var mapa_per_uno = L.map('mapa-periodo-uno').setView([9, -67], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
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

    var mapa_per_dos = L.map('mapa-periodo-dos').setView([9, -67], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapa_per_dos);

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

                        console.log("listo");
                        location.href = "graficas_estatus.php?valores=<?php echo $valores; ?>";

                    } else {

                    }
                }
            });
        }, 5000);
    });
    // });
</script>
<?php closeConection($conn); ?>

</html>