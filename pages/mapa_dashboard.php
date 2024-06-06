<?php

require_once 'php/Conexion.php';
require_once 'php/batimetria.php';

//EMBALSES - PORCENTAJE Y VARIACION

// $queryEmbalses = mysqli_query($conn, "SELECT id_embalse, nombre_embalse, norte, este, huso, operador FROM embalses WHERE estatus = 'activo';");
$almacenamiento_actual = mysqli_query($conn, "SELECT e.id_embalse,operador,region,nombre_embalse,norte,este,huso, MAX(d.fecha) AS fech,               (
    SELECT SUM(extraccion)
            FROM detalles_extraccion dex, codigo_extraccion ce
            WHERE ce.id = dex.id_codigo_extraccion AND dex.id_registro = (SELECT id_registro
               FROM datos_embalse h 
               WHERE h.id_embalse = d.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND estatus = 'activo' AND id_embalse = d.id_embalse) AND cota_actual <> 0) AND (ce.id_tipo_codigo_extraccion = '1' OR ce.id_tipo_codigo_extraccion = '2' OR ce.id_tipo_codigo_extraccion = '3' OR ce.id_tipo_codigo_extraccion = '4')
          ) AS 'extraccion',
          e.nombre_embalse, (SELECT cota_actual 
               FROM datos_embalse h 
               WHERE h.id_embalse = d.id_embalse AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND estatus = 'activo' AND id_embalse = d.id_embalse) AND h.estatus = 'activo' AND cota_actual <> 0 LIMIT 1) AS cota_actual
          FROM embalses e
    LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
    WHERE e.estatus = 'activo'
    GROUP BY id_embalse 
    ORDER BY id_embalse ASC;");


$datos_embalses = mysqli_fetch_all($almacenamiento_actual, MYSQLI_ASSOC);

$embalses_abast = [];
$cantidades_p = [0, 0, 0, 0, 0];
$condiciones = [];
$row = 0;

while ($row < count($datos_embalses)) {
    $array = array($datos_embalses[$row]["norte"], $datos_embalses[$row]["este"], $datos_embalses[$row]["huso"]);

    $emb = new Batimetria($datos_embalses[$row]["id_embalse"], $conn);

    $abastecimiento = 0;
    if ($datos_embalses[$row]["extraccion"] > 0) {
        $abastecimiento = (($emb->volumenActualDisponible() * 1000) / $datos_embalses[$row]["extraccion"]) / 30;
    }

    array_push($array, $datos_embalses[$row]["nombre_embalse"]);

    $porcentaje = $emb->volumenDisponible() != 0 ? (($emb->volumenActualDisponible() * 100) / $emb->volumenDisponible()) : 0 ;

    $icono = "i_";


    // Dependiendo del porcentaje, se asigna su icono, y se cuenta para su categoria.
    // $abastecimiento = intval($abastecimiento);
    if (intval($abastecimiento) < 5) {
        $icono .= "rojo_";
    } 
     if (intval($abastecimiento) > 4 && intval($abastecimiento) < 9) {
        $icono .= "naranja_";
    } 
     if (intval($abastecimiento) > 8 && intval($abastecimiento) < 13) {
        $icono .= "amarillo_";
    } 
    if (intval($abastecimiento) > 12) {
        $icono .= "verde_";
    }


    if ($porcentaje < 30) {
        $icono .= "30";
    } else if ($porcentaje >= 30 && $porcentaje < 60) {
        $icono .= "60";
    } else if ($porcentaje >= 60 && $porcentaje < 90) {
        $icono .= "90";
    } else if ($porcentaje >= 90 && $porcentaje <= 100) {
        $icono .= "100";
    } else {
        $icono .= "200";
    }

    array_push($array, $icono);
    array_push($array, number_format($porcentaje,2,",","."));
    // var_dump($array[3]);
    // Guardo el nombre del embalse
    // array_push($array, $row["nombre_embalse"]);
    array_push($embalses_abast, $array);
    // $embalses_abast[$row] = $array;
    $row++;
}

// var_dump($embalses_abast);
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
        height: 700px;
    }

    .leaflet-top.leaflet-left {
        /* display: none; */
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

<!-- <body style="height:1500px"> -->

<!-- Cantidades de embalse por porcentaje de volumen -->
<div id="mapa-portada"></div>

<!-- </body> -->

<script>
    // Creación de íconos
    var PointIcon = L.Icon.extend({
        options: {
            shadowUrl: 'assets/icons/i-sombra.png',
            iconSize: [18, 18],
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

    var i_rojo_30 = new PointIcon({
        iconUrl: 'assets/icons/i-rojo-30.png'
    })
    var i_rojo_60 = new PointIcon({
        iconUrl: 'assets/icons/i-rojo-60.png'
    })
    var i_rojo_90 = new PointIcon({
        iconUrl: 'assets/icons/i-rojo-90.png'
    })
    var i_rojo_100 = new PointIcon({
        iconUrl: 'assets/icons/i-rojo-100.png'
    })
    var i_rojo_200 = new PointIcon({
        iconUrl: 'assets/icons/i-rojo-200.png'
    })

    var i_naranja_30 = new PointIcon({
        iconUrl: 'assets/icons/i-naranja-30.png'
    })
    var i_naranja_60 = new PointIcon({
        iconUrl: 'assets/icons/i-naranja-60.png'
    })
    var i_naranja_90 = new PointIcon({
        iconUrl: 'assets/icons/i-naranja-90.png'
    })
    var i_naranja_100 = new PointIcon({
        iconUrl: 'assets/icons/i-naranja-100.png'
    })
    var i_naranja_200 = new PointIcon({
        iconUrl: 'assets/icons/i-naranja-200.png'
    })

    var i_amarillo_30 = new PointIcon({
        iconUrl: 'assets/icons/i-amarillo-30.png'
    })
    var i_amarillo_60 = new PointIcon({
        iconUrl: 'assets/icons/i-amarillo-60.png'
    })
    var i_amarillo_90 = new PointIcon({
        iconUrl: 'assets/icons/i-amarillo-90.png'
    })
    var i_amarillo_100 = new PointIcon({
        iconUrl: 'assets/icons/i-amarillo-100.png'
    })
    var i_amarillo_200 = new PointIcon({
        iconUrl: 'assets/icons/i-amarillo-200.png'
    })

    var i_verde_30 = new PointIcon({
        iconUrl: 'assets/icons/i-verde-30.png'
    })
    var i_verde_60 = new PointIcon({
        iconUrl: 'assets/icons/i-verde-60.png'
    })
    var i_verde_90 = new PointIcon({
        iconUrl: 'assets/icons/i-verde-90.png'
    })
    var i_verde_100 = new PointIcon({
        iconUrl: 'assets/icons/i-verde-100.png'
    })
    var i_verde_200 = new PointIcon({
        iconUrl: 'assets/icons/i-verde-200.png'
    })

    // Creación de los mapas
    var mapa_portada = L.map('mapa-portada').setView([9, -66.5], 7);
    mapa_portada.scrollWheelZoom.disable();

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapa_portada);

    // L.tileLayer('https://{s}.tile.thunderforest.com/atlas/{z}/{x}/{y}.png?apikey={apikey}', {
    //     attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors, Tiles courtesy of <a href="https://www.thunderforest.com/transport/">Andy Allan</a>',
    //     apikey: '38db809be13a400c8c5061e304ba99cd' // Reemplaza esto con tu clave de API de Thunderforest
    // }).addTo(mapa_portada);

    var ubicacion;

    <?php
    foreach ($embalses_abast as $emb) {
        if ($emb[0] != "" && $emb[1] != "" && $emb[2] != "") { ?>
            ubicacion = geoToUtm(<?php echo $emb[0] . "," . $emb[1] . "," . $emb[2] ?>)
            var marker = L.marker([ubicacion[0], ubicacion[1]], {
                icon: <?php echo $emb[4] ?>
            }).addTo(mapa_portada).bindPopup("<b><?php echo $emb[3] ?></b> <b><?php echo $emb[5] ?> %</b>", {
                autoClose: false,
                closeOnClick: false
            });
            // }).openPopup();
        <?php } else { ?>
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