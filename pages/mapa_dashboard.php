<?php

require_once 'php/Conexion.php';
require_once 'php/batimetria.php';

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
    $porcentaje = ($bat->volumenActualDisponible() * 100) / $bat->volumenDisponible();
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
    array_push($embalses_porcentaje, $array);
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../../assets/img/logos/cropped-mminaguas.webp">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.7.5/proj4.js"></script>
    <script src="./assets/js/jquery/jquery.min.js"></script>
</head>

<style>
    #mapa-portada {
        width: 1500px;
        height: 500px;
    }

    .leaflet-top.leaflet-left {
        display: none;
    }

    .leaflet-popup-content-wrapper {
        text-align: center !important;
        background-color: rgba(255, 255, 255, 1) !important;
        color: black !important;
        font-size: 8px !important;
        padding: 0 !important;
        box-shadow: 0 3px 14px rgba(0, 0, 0, 0.1) !important;
    }


    .leaflet-popup-content {
        background-color: rgba(0, 0, 0, 0) !important;
        margin: 8px 10px !important;
    }

    .leaflet-popup-close-button {
        display: none !important;
    }

    .leaflet-popup-tip-container {
        margin-top: -8px !important;
    }

    .leaflet-popup-tip {
        background-color: rgba(0, 0, 0, 0) !important;
        color: rgba(0, 0, 0, 0) !important;
        box-shadow: 0 3px 14px rgba(0, 0, 0, 0.1) !important;
    }

    .leaflet-popup.leaflet-zoom-animated {
        margin-bottom: 5.5px !important;
    }
    
</style>

<body style="height:1500px">

    <!-- Cantidades de embalse por porcentaje de volumen -->
    <div id="mapa-portada"></div>

</body>

<script>
    // Creación de íconos
    var PointIcon = L.Icon.extend({
        options: {
            shadowUrl: 'assets/icons/i-sombra.png',
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
        iconUrl: 'assets/icons/i-rojo.png'
    })
    var i_azulclaro = new PointIcon({
        iconUrl: 'assets/icons/i-azulclaro.png'
    })
    var i_azul = new PointIcon({
        iconUrl: 'assets/icons/i-azul.png'
    })
    var i_verde = new PointIcon({
        iconUrl: 'assets/icons/i-verde.png'
    })
    var i_verdeclaro = new PointIcon({
        iconUrl: 'assets/icons/i-verdeclaro.png'
    })
    var f_arriba = new ArrowIcon({
        iconUrl: 'assets/icons/f-arriba.png'
    })
    var f_abajo = new ArrowIcon({
        iconUrl: 'assets/icons/f-abajo.png'
    })
    var f_igual = new ArrowIcon({
        iconUrl: 'assets/icons/f-igual.png'
    })

    // Creación de los mapas
    var mapa_portada = L.map('mapa-portada').setView([9.5, -68], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapa_portada);

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
    // L.tileLayer('https://{s}.tile.thunderforest.com/atlas/{z}/{x}/{y}.png?apikey={apikey}', {
    //     maxZoom: 19,
    //     attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors, Tiles courtesy of <a href="https://www.thunderforest.com/transport/">Andy Allan</a>',
    //     apikey: '38db809be13a400c8c5061e304ba99cd' // Reemplaza esto con tu clave de API de Thunderforest
    // }).addTo(mapa_portada);

    //Añadiendo los marcadores al mapa de la portada.
    <?php
    // echo json_encode($embalses_porcentaje);
    ?>
</script>
<?php closeConection($conn); ?>