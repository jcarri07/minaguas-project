<?php
include '../../php/Conexion.php';

$id = $_GET['id'];

$sql = "SELECT * FROM embalses WHERE id_embalse = $id";

$res = mysqli_query($conn, $sql);
$data = array();

if (mysqli_num_rows($res) > 0) {

  while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
  }

  print_r($data);
} else {
  echo "No se encontraron resultados.";
}


$HOST = basename(getcwd());
$fullPath = getcwd();

function cutRoute($rutaCompleta)
{
  $publicHtmlPos = strpos($rutaCompleta, 'public_html');

  if ($publicHtmlPos !== false) {
    $rutaAntesDePublicHtml = substr($rutaCompleta, 0, $publicHtmlPos + strlen('public_html'));
    return $rutaAntesDePublicHtml;
  } else {
    return $rutaCompleta;
  }
}

$parts = explode(DIRECTORY_SEPARATOR, $fullPath);

if (count($parts) >= 4) {
  $projectName = $parts[3];
  echo "Nombre del proyecto: " . $projectName;
} else {
  echo "No se pudo obtener el nombre del proyecto desde la ruta.";
}

$srcLogo = "https://embalsesminaguas.000webhostapp.com/assets/img/logos/cropped-mminaguas.jpg";
$srcLogoLetters = "https://embalsesminaguas.000webhostapp.com/assets/img/logos/MinaguasLetters.png";

$srcMap = "https://embalsesminaguas.000webhostapp.com/pages/reports_images/Imagen_map.png";
$srcMapReport = "https://embalsesminaguas.000webhostapp.com/pages/reports_images/Imagens_map_report.png";
$srcFooter = "https://embalsesminaguas.000webhostapp.com/pages/reports_images/pie_de_pagina.png";

$image_logo =  "/" . cutRoute($fullPath) . "/assets/img/logos/cropped-mminaguas.jpg";
$logo_letters =  "/" . $projectName . "/assets/img/logos/MinaguasLetters.png";

$imagen_mapa_dibujo =  "/" . $projectName . "/pages/reports_images/Imagen_map.png";
$imagen_mapa = "/" . $projectName . "/pages/reports_images/Imagens_map_report.png";
$PIE_PAGINA =  "/" . $projectName . "/pages/reports_images/pie_de_pagina.png";

$ESTE = 385441;
$NORTE = 983161;
$HUSO = 19;

$NOMBRE_EMBALSE = "EMBALSE BOCONO TUCUPIDO";
$ESTADO = "Portuguesa";
$MUNICIPIO = "San Genaro de Boconoito y Guanare";
$PARROQUIA = "Boconoito";

$CUENCA = "Río Boconó y Río Tucupido";
$AFLUENTES = "Río Boconó y Río Tucupido";
$AREA_CUENCA = 202000;
$ESCURRIMIENTO = 2620;

$UBICACION = "Constituido por dos (2) embalses: uno sobre el río Boconó y el otro sobre el río Tucupido, que a determinada cota forman uno solo (Boconó-Tucupido), debido a que están comunicados por medio de un canal excavado en la fila que los separa. A 50 km del sur-oeste de Guanare";
$ORGANO_RECTOR = "Ministerio del Poder Popular de Atención de las Aguas";
$PERSONAL_ENCARGADO = "Dirección General de Manejo de Embalses                                      
(Telf. 0212 - 5649428)";
$OPERADOR = "HIDROSPORTUGUESA - CORPOELEC - UTAA PORTUGUESA";
$AUTORIDAD_RESPONSABLE = "Ing. Naika Nadedja
(Telf. 04245729126)";
$PROYECTISTA = "COMISION BOCONO-TUCUPIDO; MOP (DGRH)";
$CONSTRUCTOR = "VINCCLER (Boconó); BARSANTI (Tucupido)";
$AÑO_INICIO = "1975";
$DURACION_CONSTRUCCION = 13;
$INCIO_OPERACION = "1988";
$MONITOREO = "Lectura de manera convenional mediante la observación de las regletas limnimétricas (miras)";

$BATIMETRIA = "1988-2001";

$COTA = 237;
$COTA2 = 267;
$COTA3 = 269;

$FIRTS_VOLUMEN = 88983;
$SECOND_VOLUMEN = 8798;

$FIRTS_SUPERFICIE = 548305;
$SECOND_SUPERFICIE = 569794;

$FIRTS_VOLUMEN2 = 88983;
$SECOND_VOLUMEN2 = 8798;

$FIRTS_SUPERFICIE2 = 548305;
$SECOND_SUPERFICIE2 = 569794;

$FIRTS_VOLUMEN3 = 88983;
$SECOND_VOLUMEN3 = 8798;

$FIRTS_SUPERFICIE3 = 548305;
$SECOND_SUPERFICIE3 = 569794;

$CAPACIDAD_UTIL = 2595;
$CAPACIDAD_UTIL2 = 2052;

$VIDA_UTIL = 100;
$VIDA_UTIL_RESTANTE = 65;

$NUMERO_PRESAS = 2;
$TIPO = "Presa Boconó ubicada a 7 km. aguas arriba del puente Páez sobre la carretera Guanare-Barinas. Es de tierra, zonificada, con núcleo de arcilla de mediana plasticidad y espaldones de grava natural del río. Presa Tucupido sobre el río Tucupido, aproximadamente a 25 km. de la ciudad de Guanare. Es de tierra, zonificada, con espaldones de grava.";
$ALTURA = "80 m (Boconó) y 87 m (Tucupido)";
$TALUD_AGUAS_ARRIBA = "2,5:1";
$TALUD_AGUAS_ABAJO = "2:1";
$LONGITUD_CRESTA = "395 m (Boconó) y 290 m (Tucupido)";
$COTA_CRESTA = "272 m s.n.m. (Ambas)";
$ANCHO_CRESTA = "10 m (Boconó) y 12 m (Tucupido)";
$VOLUMEN_TERRAPLEN = "6.004.000 m3 (Boconó) y 2.850.000 m3 (Tucupido)";
$ANCHO_MAX_BASE = "400 (Tucupido)";

$UBICACION2 = "En el estribo izquierdo";
$TIPO2 = "De entrada frontal controlado por compuerta radial de (10 x 11,5) m, conectado a un túnel revestido en concreto de øinterior = 7,5 m y 288 m de longitud, por medio de una transición inclinada de 45 m de largo.";
$NUMERO_COMPUERTAS = 1;
$CARGA_VERTEDERO = 13;
$DESCARGA_MAXIMA = 950;
$LONGITUD = 10;

$UBICACION_OBRA = "En el estribo derecho";
$TIPO_OBRA = "Torre-toma de 50 m de altura con compuertas, dos (2) túneles que se bifurcan en dos (2) tuberías a presión, las cuales descargan por una válvula y una turbina cada una";
$NUMERO_COMPUERTAS_OBRA = 0;
$MECANISMO_EMERGENCIA = "Dos (2) válvulas mariposa, ø = 3,20 m, de protección";
$MECANISMO_REGULACION = "Dos (2) válvulas Howell Bunger, ø = 2,80 m, de regulación.";
$GASTO_MAXIMO = 320;
$DESCARGA_FONDO = "No tiene";

$UBICACION_ALIVIADERO = "En el estribo izquierdo";
$TIPO_ALIVIADERO = "De entrada frontal controlado por compuerta radial de (10 x 11,5) m, conectado a un túnel revestido en concreto de øinterior = 7,5 m y 288 m de longitud, por medio de una transición inclinada de 45 m de largo.";
$COMPUERTAS_ALIVIADERO = 1;
$CARGA_ALIVIADERO = 13;
$DESCARGA_ALIVIADERO = 950;
$LONGITUD_ALIVIADERO = 10;

$POSEE_OBRA = "S/I";
$TIPO_OBRA_2 = "S/I";
$ACCION_REQUERIDA = "S/I";

$PROPOSITO = "Riego, hidroelectricidad, consumo humano, control de inundaciones, recreación. ";
$USO_ACTUAL = "Riego, hidroelectricidad (80.000 KW), consumo humano, control de inundaciones y recreación.";
$SECTOR_BENEFICIADO = "Mun. San Genaro de Boconoito
Mun. Guanare";
$POBLACION = 211466;
$AREA_RIEGO = 2000;

$CARGO_FUNCIONARIO = "Apoyo Técnico I";
$NOMBRES_FUNCIONARIO = "Hector";
$TLF_FUNCIONARIO = "0414-5221503";
$CORREO_FUNCIONARIO = "hector8@hotmail.com";
$CEDULA_FUNCIONARIO = "V.-17.049.840";
$APELLIDOS_FUNCIONARIO = "Ledezma";
$FIRMA_FUNCIONARIO = "";


foreach ($data as $row) {
  //INFO GENERAL
  $NOMBRE_EMBALSE = $row['nombre_embalse'];
  $NOMBRE_CUENCA = $row['nombre_presa'];
  $idE = $row['id_estado'];
  $idM = $row['id_municipio'];
  $idP = $row['id_parroquia'];
  $ESTADO = mysqli_fetch_assoc(mysqli_query($conn, "SELECT estado FROM estados WHERE id_estado = $idE"))['estado'];
  $MUNICIPIO = mysqli_fetch_assoc(mysqli_query($conn, "SELECT municipio FROM municipios WHERE id_municipio = $idM"))['municipio'];
  $PARROQUIA = mysqli_fetch_assoc(mysqli_query($conn, "SELECT parroquia FROM parroquias WHERE id_parroquia = $idP"))['parroquia'];
  //INFO CUENCA
  $ESTE = $row['este'];
  $NORTE = $row['norte'];
  $HUSO = $row['huso'];
  $CUENCA = $row['cuenca_principal'];
  $AFLUENTES = $row['afluentes_principales'];
  $AREA_CUENCA = $row['area_cuenca'];
  $ESCURRIMIENTO = $row['escurrimiento_medio'];
  //INFO EMBALSE
  $UBICACION = $row['ubicacion_embalse'];
  $ORGANO_RECTOR = $row['organo_rector'];
  $PERSONAL_ENCARGADO = $row['personal_encargado'];
  $OPERADOR = $row['operador'];
  $AUTORIDAD_RESPONSABLE = $row['autoridad_responsable'];
  $PROYECTISTA = $row['proyectista'];
  $CONSTRUCTOR = $row['constructor'];
  $AÑO_INICIO = $row['inicio_construccion'];
  $DURACION_CONSTRUCCION = $row['duracion_de_construccion'];
  $INCIO_OPERACION = $row['inicio_de_operacion'];
  $MONITOREO = $row['monitoreo_del_embalse'];
  //CARACTERISTICAS EMBALSE
  $bat_embalse = json_decode($row['batimetria']);
  $BATIMETRIA = "";
  foreach ($bat_embalse as $key => $value) {
    $BATIMETRIA .= $key . "-";
  }

  $COTA = $row['cota_min'];
  $COTA2 = $row['cota_nor'];
  $COTA3 = $row['cota_max'];
  $FIRTS_VOLUMEN = $row['vol_min'];
  $SECOND_VOLUMEN = 8798;
  $FIRTS_SUPERFICIE = $row['sup_min'];
  $SECOND_SUPERFICIE = 569794;
  $FIRTS_VOLUMEN2 = $row['vol_nor'];
  $SECOND_VOLUMEN2 = 8798;
  $FIRTS_SUPERFICIE2 = $row['sup_nor'];
  $SECOND_SUPERFICIE2 = 569794;
  $FIRTS_VOLUMEN3 = $row['vol_max'];
  $SECOND_VOLUMEN3 = 8798;
  $FIRTS_SUPERFICIE3 = $row['sup_max'];
  $SECOND_SUPERFICIE3 = 569794;
  $CAPACIDAD_UTIL = 2595;
  $CAPACIDAD_UTIL2 = 2052;
  $VIDA_UTIL = $row['vida_util'];
  $VIDA_UTIL_RESTANTE = 65;
  //COMPONENTES EMBALSE
  $NUMERO_PRESAS = $row['numero_de_presas'];
  $TIPO = $row['tipo_de_presa'];
  $ALTURA = $row['altura'];
  $TALUD_AGUAS_ARRIBA = $row['talud_aguas_arriba'];
  $TALUD_AGUAS_ABAJO = $row['talud_aguas_abajo'];
  $LONGITUD_CRESTA = $row["longitud_cresta"];
  $COTA_CRESTA = $row['cota_cresta'];
  $ANCHO_CRESTA = $row['ancho_cresta'];
  $VOLUMEN_TERRAPLEN = $row['volumen_terraplen'];
  $ANCHO_MAX_BASE = $row['ancho_base'];
  //ALIVIADERO
  $UBICACION_ALIVIADERO = $row['ubicacion_aliviadero'];
  $TIPO_ALIVIADERO = $row['tipo_aliviadero'];
  $COMPUERTAS_ALIVIADERO = $row['numero_compuertas_aliviadero'];
  $CARGA_ALIVIADERO = $row['carga_vertedero'];
  $DESCARGA_ALIVIADERO = $row['descarga_maxima'];
  $LONGITUD_ALIVIADERO = $row['longitud_aliviadero'];
  //OBRA
  $UBICACION_OBRA = $row['ubicacion_toma'];
  $TIPO_OBRA = $row['tipo_toma'];
  $NUMERO_COMPUERTAS_OBRA = $row['numero_compuertas_toma'];
  $MECANISMO_EMERGENCIA = $row['mecanismos_de_emergencia'];
  $MECANISMO_REGULACION = $row['mecanismos_de_regulacion'];
  $GASTO_MAXIMO = $row['gasto_maximo'];
  $DESCARGA_FONDO = $row['descarga_de_fondo'];
  //OBRA HIDRAULICA
  $POSEE_OBRA = $row['posee_obra'];
  $TIPO_OBRA_2 = $row['tipo_de_obra'];
  $ACCION_REQUERIDA = $row['accion_requerida'];
  //BENEFICIOS
  $PROPOSITO = $row['proposito'];
  $USO_ACTUAL = $row['uso_actual'];
  $SECTOR_BENEFICIADO = $row['sectores_beneficiados'];
  $POBLACION = $row['poblacion_beneficiada'];
  $AREA_RIEGO = $row['area_de_riego_beneficiada'];
  //FUNCIONARIO RESPONSABLE
  $CARGO_FUNCIONARIO = $row['f_cargo'];
  $NOMBRES_FUNCIONARIO = $row['f_nombres'];
  $TLF_FUNCIONARIO = $row['f_telefono'];
  $CORREO_FUNCIONARIO = $row['f_correo'];
  $CEDULA_FUNCIONARIO = $row['f_cedula'];
  $APELLIDOS_FUNCIONARIO = $row['f_apellidos'];
  $FIRMA_FUNCIONARIO = "";
  //IMAGENES
  $IMAGEN_UNO = $row['imagen_uno'];
  $IMAGEN_DOS = $row['imagen_dos'];
  $imagen_mapa_dibujo =  "/" . $projectName . "/pages/reports_images/" . $IMAGEN_DOS;
  $imagen_mapa = "/" . $projectName . "/pages/reports_images/" . $IMAGEN_UNO;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <style>
    body {
      display: flex;
      margin: 0;
      box-sizing: border-box;
      text-decoration: none;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      border: 1px solid #dddddd;
      padding: 8px;
      width: fit-content;
    }

    .value {
      text-align: right;
    }

    th {
      text-align: left;
      background-color: #f2f2f2;
      width: fit-content;
    }

    .subtitle {
      text-align: left;
      font-weight: bold;
    }

    .image-logo {
      width: 50px;
      height: 50px;
      border-radius: 100%;
    }

    .letters-logo {
      padding-top: 10px;
      margin-left: 5px;
      width: 120px;
      height: 50px;
    }

    .header {
      float: left;
      width: 25%;
    }

    .header-title {
      position: absolute;
      left: 25%;
      text-align: center;
      width: 50%;
    }

    .header-info {
      position: absolute;
      left: 65%;
      text-align: center;
      width: 25%;
      top: 0;
      bottom: 86%;
    }

    .header-info-left {
      position: absolute;
      text-align: center;
      width: 25%;
      top: 0;
      bottom: 83%;
    }

    .info {
      text-align: right;
      font-size: 10px;
    }

    .title-info {
      text-align: left;
      font-weight: bold;
      font-size: 10px;
    }
  </style>

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>REPORTE</title>
  </head>

<body>
  <div class="header">
    <div>
      <img class="image-logo" src="<?php echo $srcLogo; ?>" />
      <img class="letters-logo" src="<?php echo $srcLogoLetters; ?>" />
    </div>
  </div>
  <div class="header-title">
    <h4><?php echo $NOMBRE_EMBALSE; ?></h4>
  </div>
  <div class="header-info">
    <table style="padding-top: 130px;">
      <tr>
        <td class="title-info">Estado </td>
        <td class="info" style="width: 165px;"><?php echo $ESTADO ?></td>
      </tr>
      <tr>
        <td class="title-info">Municipio</td>
        <td class="info" style="width: 165px;"><?php echo $MUNICIPIO ?></td>
      </tr>
      <tr>
        <td class="title-info">Parroquia</td>
        <td class="info" style="width: 165px;"><?php echo $PARROQUIA ?></td>
      </tr>
    </table>
  </div>
  <div class="header-info-left">
    <table>
      <tr>
        <th colspan="2" class="title-info">Coordenadas UTM</th>
      </tr>
      <tr>
        <td class="title-info">Este </td>
        <td class="info" style="width: 165px;"><?php echo number_format(floatval($ESTE), 2, ',', '.'); ?></td>
      </tr>
      <tr>
        <td class="title-info">Norte</td>
        <td class="info" style="width: 165px;"><?php echo number_format(floatval($NORTE), 2, ',', '.'); ?></td>
      </tr>
      <tr>
        <td class="title-info">Huso</td>
        <td class="info" style="width: 165px;"><?php echo number_format(floatval($HUSO), 2, ',', '.'); ?></td>
      </tr>
    </table>
  </div>
  <table style="padding-top: 130px;">
    <tr>
      <th style="width: 100%; text-align: center" colspan="4">1.- INFORMACIÓN GENERAL DE LA CUENCA</th>
    </tr>
    <tr>
      <td class="subtitle" colspan="2">1.1.- Cuenca principal</td>
      <td class="value" colspan="2"><?php echo $CUENCA ?></td>
    </tr>
    <tr>
      <td class="subtitle" colspan="2">1.2.- Afluente(s) principal(es)</td>
      <td class="value" colspan="2"><?php echo $AFLUENTES ?></td>
    </tr>
    <tr>
      <td class="subtitle">1.3.- Área de la cuenca (ha)</td>
      <td class="value"><?php echo number_format($AREA_CUENCA, 2, ',', '.'); ?></td>
      <td class="subtitle">1.4.- Escurrimiento medio (hm³)</td>
      <td class="value"><?php echo number_format($ESCURRIMIENTO, 2, ',', '.'); ?></td>
    </tr>
  </table>
  <table style="padding-top: 10px;">
    <tr>
      <th style="width: 100%; text-align: center" colspan="4">2.- INFORMACIÓN GENERAL DEL EMBALSE</th>
    </tr>
    <tr>
      <td class="subtitle" colspan="2">2.1.- Ubicación</td>
      <td class="value" style="width: 200px;" colspan="2"><?php echo $UBICACION ?></td>
    </tr>
    <tr>
      <td class="subtitle" colspan="2">2.2.- Organo rector</td>
      <td class="value" style="width: 200px;" colspan="2"><?php echo $ORGANO_RECTOR ?></td>
    </tr>
    <tr>
      <td class="subtitle" colspan="2">2.3.- Personal encargado a nivel central</td>
      <td class="value" style="width: 200px;" colspan="2"><?php echo $PERSONAL_ENCARGADO ?></td>
    </tr>
    <tr>
      <td class="subtitle" colspan="2">2.4. Operador</td>
      <td class="value" style="width: 200px;" colspan="2"><?php echo $OPERADOR ?></td>
    </tr>
    <tr>
      <td class="subtitle" colspan="2">2.5.- Autoridad responsable del embalse</td>
      <td class="value" style="width: 200px;" colspan="2"><?php echo $AUTORIDAD_RESPONSABLE ?></td>
    </tr>
    <tr>
      <td class="subtitle" colspan="2">2.6.- Proyectista</td>
      <td class="value" style="width: 200px;" colspan="2"><?php echo $PROYECTISTA ?></td>
    </tr>
    <tr>
      <td class="subtitle" colspan="2">2.7.- Constructor</td>
      <td class="value" style="width: 200px;" colspan="2"><?php echo $CONSTRUCTOR ?></td>
    </tr>
    <tr>
      <td class="subtitle">2.8.- Año de inicio de construcción</td>
      <td class="value"><?php echo $AÑO_INICIO ?></td>
      <td class="subtitle">2.9.- Duración de construcción</td>
      <td class="value"><?php echo $DURACION_CONSTRUCCION ?></td>
    </tr>
    <tr>
      <td class="subtitle" colspan="2">2.10.- Inicio de operación</td>
      <td class="value" style="width: 200px;" colspan="2"><?php echo $INCIO_OPERACION ?></td>
    </tr>
    <tr>
      <td class="subtitle" colspan="2">2.11.- Monitoreo de niveles del embalse</td>
      <td class="value" style="width: 200px;" colspan="2"><?php echo $MONITOREO ?></td>
    </tr>
  </table>
  <table style="padding-top: 10px;">
    <tr>
      <th style="width: 100%; text-align: center" colspan="4">3.- CARACTERISTICAS GENERALES DEL EMBALSE </th>
    </tr>
    <tr>
      <td class="subtitle">3.1.- Batimetría</td>
      <td class="subtitle" rowspan="2">3.2.- Característica</td>
      <td class="subtitle" rowspan="2">Diseño</td>
      <td class="subtitle" rowspan="2">Actual (última batimetría)</td>
    </tr>
    <tr>
      <td><?php echo $BATIMETRIA ?></td>
    </tr>
    <tr>
      <td class="subtitle" rowspan="3" style="text-align: center;">3.2.3.- Máximo</td>
      <td class="subtitle">3.2.3.1.- Cota (m s.n.m.)</td>
      <td colspan="2" style="text-align: center;"><?php echo number_format(floatval($COTA3), 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle">3.2.3.2.- Volumen (hm³)</td>
      <td style="width: 130px;"><?php echo number_format(floatval($FIRTS_VOLUMEN3), 2, ',', '.'); ?></td>
      <td><?php echo number_format($SECOND_VOLUMEN3, 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle">3.2.3.3.- Superficie (ha)</td>
      <td style="width: 130px;"><?php echo number_format(floatval($FIRTS_SUPERFICIE3), 2, ',', '.'); ?></td>
      <td><?php echo number_format($SECOND_SUPERFICIE3, 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle" rowspan="3" style="text-align: center;">3.2.2.- Normal</td>
      <td class="subtitle">3.2.2.1.- Cota (m s.n.m.)</td>
      <td colspan="2" style="text-align: center;"><?php echo number_format(floatval($COTA2), 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle">3.2.2.2.- Volumen (hm³)</td>
      <td style="width: 130px;"><?php echo number_format(floatval($FIRTS_VOLUMEN2), 2, ',', '.'); ?></td>
      <td><?php echo number_format($SECOND_VOLUMEN2, 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle">3.2.2.3.- Superficie (ha)</td>
      <td style="width: 130px;"><?php echo number_format(floatval($FIRTS_SUPERFICIE2), 2, ',', '.'); ?></td>
      <td><?php echo number_format($SECOND_SUPERFICIE2, 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle" rowspan="3" style="text-align: center;">3.2.1.- Mínimo</td>
      <td class="subtitle" class="subtitle">3.2.1.1.- Cota (m s.n.m.)</td>
      <td colspan="2" style="text-align: center;"><?php echo number_format(floatval($COTA), 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle">3.2.1.2.- Volumen (hm³)</td>
      <td style="width: 130px;"><?php echo number_format(floatval($FIRTS_VOLUMEN), 2, ',', '.'); ?></td>
      <td><?php echo number_format($SECOND_VOLUMEN, 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle">3.2.1.3.- Superficie (ha)</td>
      <td style="width: 130px;"><?php echo number_format(floatval($FIRTS_SUPERFICIE), 2, ',', '.'); ?></td>
      <td><?php echo number_format($SECOND_SUPERFICIE, 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle">3.2.4.- Capacidad Útil (hm³)</td>
      <td style="width: 180px;"><?php echo number_format(floatval($CAPACIDAD_UTIL), 2, ',', '.'); ?></td>
      <td colspan="2"><?php echo number_format($CAPACIDAD_UTIL2, 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle">3.2.5.- Vida útil (años)</td>
      <td style="width: 180px;"><?php echo $VIDA_UTIL ?></td>
      <td colspan="2"><?php echo $VIDA_UTIL_RESTANTE ?></td>
    </tr>
  </table>
  <table style="padding-top: 10px;">
    <tr>
      <th class="subtitle" style="width: 100%; text-align: center" colspan="3">4.- COMPONENTES DEL EMBALSE</th>
    </tr>
    <tr>
      <td class="subtitle" style="text-align: center;" rowspan="12">4.1.- Presa</td>
    </tr>
    <tr>
      <td class="subtitle">4.1.1.- N° de Presas</td>
      <td><?php echo $NUMERO_PRESAS ?></td>
    </tr>
    <tr>
      <td class=" subtitle">4.1.2.- Tipo:</td>
      <td style="width: 200px;"><?php echo $TIPO ?></td>
    </tr>
    <tr>
      <td style="text-align: center;" colspan="2" class="subtitle">Diseño</td>
    </tr>
    <tr>
      <td class="subtitle">4.1.3.- Altura (m):</td>
      <td><?php echo $ALTURA ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.1.4.- Talud aguas arriba</td>
      <td><?php echo $TALUD_AGUAS_ARRIBA ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.1.5.- Talud aguas abajo</td>
      <td><?php echo $TALUD_AGUAS_ABAJO ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.1.6.- Longitud de la cresta (m):</td>
      <td><?php echo $LONGITUD_CRESTA ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.1.7.- Cota de la cresta (m s.n.m)</td>
      <td><?php echo $COTA_CRESTA ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.1.8.- Ancho de Cresta (m)</td>
      <td><?php echo $ANCHO_CRESTA ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.1.9.- Volumen del terraplén (m³)</td>
      <td><?php echo $VOLUMEN_TERRAPLEN ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.1.10.- Ancho max de base (m)</td>
      <td><?php echo $ANCHO_MAX_BASE ?></td>
    </tr>
  </table>
  <!-- <table style="padding-top: 10px;">
    <tr>
      <th class="subtitle" style="width: 100%; text-align: center" colspan="3">4.- COMPONENTES DEL EMBALSE</th>
    </tr>
    <tr>
      <td class="subtitle" style="text-align: center;" rowspan="12">4.1.- Presa</td>
    </tr>
    <tr>
      <td class="subtitle">4.2.1.- Ubicación</td>
      <td><?php //echo $UBICACION2 
          ?></td>
    </tr>
    <tr>
      <td class=" subtitle">4.2.2.- Tipo</td>
      <td style="width: 200px;"><?php //echo $TIPO2 
                                ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.2.3.- N° de compuertas</td>
      <td><?php //echo number_format($NUMERO_COMPUERTAS, 2, ',', '.'); 
          ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.2.4.- Carga Sobre el Vertedero (m)</td>
      <td><?php // echo number_format($CARGA_VERTEDERO, 2, ',', '.'); 
          ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.2.5.- Descarga máxima (m³/s)</td>
      <td><?php //echo number_format($DESCARGA_MAXIMA, 2, ',', '.'); 
          ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.2.5.- Descarga máxima (m³/s)</td>
      <td><?php //echo number_format($LONGITUD, 2, ',', '.'); 
          ?></td>
    </tr>
  </table> -->
  <table>
    <tr>
      <td style="width: 100%; text-align: center" colspan="3"></td>
    </tr>
    <tr>
      <td class="subtitle" style="text-align: center;" rowspan="12">4.2.- Aliviadero</td>
    </tr>
    <tr>
      <td class="subtitle">4.2.1.- Ubicación</td>
      <td><?php echo $UBICACION_ALIVIADERO ?></td>
    </tr>
    <tr>
      <td class=" subtitle">4.2.2.- Tipo</td>
      <td style="width: 200px;"><?php echo $TIPO_ALIVIADERO ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.2.3.- N° de compuertas</td>
      <td><?php echo number_format($COMPUERTAS_ALIVIADERO, 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.2.4.- Carga Sobre el Vertedero (m)</td>
      <td><?php echo $CARGA_ALIVIADERO ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.2.5.- Descarga máxima (m³/s)</td>
      <td><?php echo $DESCARGA_ALIVIADERO ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.2.6.- Longitud (m)</td>
      <td><?php echo number_format($LONGITUD_ALIVIADERO, 2, ',', '.'); ?></td>
    </tr>
  </table>
  <table>
    <tr>
      <td style="width: 100%; text-align: center" colspan="3"></td>
    </tr>
    <tr>
      <td class="subtitle" style="text-align: center;" rowspan="12">4.3.- Obra de toma</td>
    </tr>
    <tr>
      <td class="subtitle">4.3.1.- Ubicación</td>
      <td><?php echo $UBICACION_OBRA ?></td>
    </tr>
    <tr>
      <td class=" subtitle">4.3.2.- Tipo</td>
      <td style="width: 200px;"><?php echo $TIPO_OBRA ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.3.3.- N° de compuertas</td>
      <td><?php echo number_format($NUMERO_COMPUERTAS_OBRA, 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.3.4.- Mecanismos de emergencia</td>
      <td><?php echo $MECANISMO_EMERGENCIA ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.3.5.- Mecanismos de regulación</td>
      <td><?php echo $MECANISMO_REGULACION ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.3.6.- Gasto máximo (m3/s)</td>
      <td><?php echo number_format($GASTO_MAXIMO, 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.3.7.- Descarga de fondo</td>
      <td><?php echo $DESCARGA_FONDO ?></td>
    </tr>
  </table>
  <table style="padding-top: 10px;">
    <tr>
      <td class="subtitle" style="width: 25%;" rowspan="3">4.4.- Obra hidráulica aguas arriba del embalse</td>
      <td class="subtitle">4.4.1.- Posee obra</td>
      <td style="width: 16%;"><?php echo $POSEE_OBRA ?></td>
    </tr>
    <tr>
      <td class="subtitle" style="width: 59%;">4.4.2.- Tipo de obra</td>
      <td style="width: 16%;"><?php echo $TIPO_OBRA_2 ?></td>
    </tr>
    <tr>
      <td class="subtitle">4.4.3.- Acción requerida</td>
      <td style="width: 16%;"><?php echo $ACCION_REQUERIDA ?></td>
    </tr>
  </table>
  <table style="padding-top: 40px;">
    <tr>
      <th style=" text-align: center" colspan="4">5.- BENEFICIOS</th>
    </tr>
    <tr>
      <td class="subtitle">5.1.- Propósito</td>
      <td style="width: 70px;"><?php echo $PROPOSITO ?></td>
      <td class="subtitle">5.2.- Uso actual</td>
      <td style="width: 70px;"><?php echo $USO_ACTUAL ?></td>
    </tr>
    <tr>
      <td class="subtitle">5.3.- Parroquia(s) y/o sector(es) beneficiado(s)</td>
      <td colspan="3"><?php echo $SECTOR_BENEFICIADO ?></td>
    </tr>
    <tr>
      <td class="subtitle">5.4.- Población beneficiada (hab.)</td>
      <td style="width: 70px;"><?php echo $POBLACION ?></td>
      <td class="subtitle">5.5.- Área de riego beneficiada (ha)</td>
      <td style="width: 70px;"><?php echo $AREA_RIEGO ?></td>
    </tr>
  </table>
  <table style="padding-top: 10px;">
    <tr>
      <th class="subtitle" colspan="4" style="width: 100%; text-align: center;">6.- FUNCIONARIO RESPONSABLE DE MANIOBRAS</th>
    </tr>
    <tr>
      <td class="subtitle">6.1.- Cargo</td>
      <td><?php echo $CARGO_FUNCIONARIO ?></td>
      <td class="subitle" style="font-weight: bold;">6.2.- Cédula de identidad</td>
      <td><?php echo $CEDULA_FUNCIONARIO ?></td>
    </tr>
    <tr>
      <td class="subtitle">6.3- Nombres</td>
      <td><?php echo $NOMBRES_FUNCIONARIO ?></td>
      <td class="subitle" style="font-weight: bold;">6.4.- Apellidos</td>
      <td><?php echo $APELLIDOS_FUNCIONARIO ?></td>
    </tr>
    <tr>
      <td class="subtitle">6.5.- Teléfono</td>
      <td><?php echo $TLF_FUNCIONARIO ?></td>
      <td rowspan="2" class="subitle" style="font-weight: bold;">6.7.- Firma</td>
      <td rowspan="2"><?php echo $FIRMA_FUNCIONARIO ?></td>
    </tr>
    <tr>
      <td class="subtitle">6.6.- Correo electrónico</td>
      <td><?php echo $CORREO_FUNCIONARIO ?></td>
    </tr>
  </table>
  <table style="padding-top: 10px;">
    <tr>
      <th style="width: 100%;" class="subtitle" colspan="2">7.- IMÁGENES</th>
    </tr>
    <tr>
      <td style="font-weight: bold; font-size: 12px">7.1.- Ubicación relativa Estado/Municipio/Región hidrográfica</td>
      <td style="font-weight: bold; font-size: 12px">7.2.- Ubicación relativa de los componentes del embalse</td>
    </tr>
    <tr style="text-align: center;">
      <td>
        <?php if ($srcMap != "" && $srcMap != null) { ?>
          <img style="width: 280px; height: 200px;" src="<?php echo $srcMap; ?>" />
        <?php } ?>
      </td>
      <td>
        <?php if ($srcMapReport != "" &&  $srcMapReport != null) { ?>
          <img style="width: 280px; height: 200px;" src="<?php echo $srcMapReport; ?>" />
        <?php } ?>
      </td>
    </tr>
  </table>
</body>

</html>