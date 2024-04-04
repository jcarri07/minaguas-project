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

$queryEmbalses = mysqli_query($conn, "SELECT id_embalse, nombre_embalse, norte, este, huso FROM embalses WHERE estatus = 'activo';");

while ($row = mysqli_fetch_array($queryEmbalses)) {
    //Saco la ubicacion de los embalses.
    $array = array($row["norte"], $row["este"], $row["huso"]);

    //Calculo del porcentaje.
    $bat = new Batimetria($row["id_embalse"], $conn);
    $porcentaje = ($bat->volumenActualDisponible() * 100) / $bat->volumenDisponible();
    array_push($array, $porcentaje);
    // echo $row["nombre_embalse"]." Vol: ".$bat->cargaActual()." -- ";

    // Dependiendo del porcentaje, se asigna su icono, y se cuenta para su categoria.
    if ($porcentaje < 30) {
        array_push($array, "i_rojo");
        $cantidades_p[0] += 1;
    } else if ($porcentaje >= 30 && $porcentaje < 60) {
        array_push($array, "i_azulclaro");
        $cantidades_p[1] += 1;
    } else if ($porcentaje >= 60 && $porcentaje < 90) {
        array_push($array, "i_azul");
        $cantidades_p[2] += 1;
    } else if ($porcentaje >= 90 && $porcentaje <= 100) {
        array_push($array, "i_verde");
        $cantidades_p[3] += 1;
    } else {
        array_push($array, "i_verdeclaro");
        $cantidades_p[4] += 1;
    }

    // Guardo el nombre del embalse
    array_push($array, $row["nombre_embalse"]);
    array_push($embalses_porcentaje, $array);
}

$condiciones_actuales1 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, e.norte, e.este, e.huso, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha_sequia' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = MAX(d.fecha) AND d.fecha <= '$fecha_sequia' AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha_sequia' AND id_embalse = d.id_embalse) LIMIT 1) AS cota_actual 
    FROM embalses e
    LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha_sequia'
    WHERE e.estatus = 'activo' 
    GROUP BY id_embalse;");

$variacion_sequia = [];
$cantidades_sequia = [0, 0, 0];

while ($row = mysqli_fetch_array($condiciones_actuales1)) {
    $array = array($row["norte"], $row["este"], $row["huso"]);

    $bat = new Batimetria($row["id_embalse"], $conn);
    $fecha = date($row['fecha']);
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
    array_push($variacion_sequia, $array);
}

$condiciones_actuales2 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, e.norte, e.este, e.huso, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha_lluvia' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = MAX(d.fecha) AND d.fecha <= '$fecha_lluvia' AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha_lluvia' AND id_embalse = d.id_embalse) LIMIT 1) AS cota_actual 
    FROM embalses e
    LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha_lluvia'
    WHERE e.estatus = 'activo' 
    GROUP BY id_embalse;");

$variacion_lluvia = [];
$cantidades_lluvia = [0, 0, 0];

while ($row = mysqli_fetch_array($condiciones_actuales2)) {
    $array = array($row["norte"], $row["este"], $row["huso"]);

    $bat = new Batimetria($row["id_embalse"], $conn);
    $fecha = date($row['fecha']);
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
    array_push($variacion_lluvia, $array);
}

$valores = array($cantidades_p, $cantidades_sequia, $cantidades_lluvia);
$valores = json_encode($valores, true);
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.7.5/proj4.js"></script>
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

    .leaflet-top.leaflet-left {
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
        /* bottom: -23px; */
        /* opacity: 1; */
        margin-bottom: 5.5px;
    }
</style>

<body>

    <!-- Cantidades de embalse por porcentaje de volumen -->
    <div id="mapa-portada"></div>
    <br>
    <!-- Variacion de volumen respecto a la fecha de sequia -->
    <div id="mapa-periodo-uno"></div>
    <br>
    <!-- Variacion de volumen respecto a la fecha de lluvia -->
    <div id="mapa-periodo-dos"></div>

</body>

<script>
    // Creación de íconos
    var PointIcon = L.Icon.extend({
        options: {
            shadowUrl: '../../assets/icons/i-sombra.png',
            iconSize: [15, 15],
            shadowSize: [15, 15],
            shadowAnchor: [8, 8],
        }
    });

    var ArrowIcon = L.Icon.extend({
        options: {
            iconSize: [20, 15],
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

    //Añadiendo los marcadores al mapa de la portada.
    var ubicacion;

    <?php
    foreach ($embalses_porcentaje as $emb) {
        if ($emb[0] != "" && $emb[1] != "" && $emb[2] != "") { ?>
            // console.log("Prueba");
            ubicacion = geoToUtm(<?php echo $emb[0] . "," . $emb[1] . "," . $emb[2] ?>)

            var marker = L.marker([ubicacion[0], ubicacion[1]], {
                icon: <?php echo $emb[4] ?>
            }).addTo(mapa_portada).bindPopup("<b><?php echo $emb[5] ?></b>", {
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

    //Añadiendo los marcadores al mapa de la variacion de sequia.

    <?php
    foreach ($variacion_sequia as $emb) {
        if ($emb[0] != "" && $emb[1] != "" && $emb[2] != "") { ?>

            ubicacion = geoToUtm(<?php echo $emb[0] . "," . $emb[1] . "," . $emb[2] ?>)

            var marker = L.marker([ubicacion[0], ubicacion[1]], {
                icon: <?php echo $emb[3] ?>
            }).addTo(mapa_per_uno).bindPopup("<b><?php echo $emb[4] ?></b>", {
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

    <?php
    foreach ($variacion_lluvia as $emb) {
        if ($emb[0] != "" && $emb[1] != "" && $emb[2] != "") { ?>

            ubicacion = geoToUtm(<?php echo $emb[0] . "," . $emb[1] . "," . $emb[2] ?>)

            var marker = L.marker([ubicacion[0], ubicacion[1]], {
                icon: <?php echo $emb[3] ?>
            }).addTo(mapa_per_dos).bindPopup("<b><?php echo $emb[4] ?></b>", {
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
        }, 0);
    });
    // });
</script>

</html>