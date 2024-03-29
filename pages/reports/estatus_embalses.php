<?php
require_once '../../php/Conexion.php';
$fullPath = getcwd();
$parts = explode(DIRECTORY_SEPARATOR, $fullPath);
date_default_timezone_set('America/Caracas');
require_once '../../php/batimetria.php';

//Consultar los embalses.
//por cada embalse se llama a NewBatimetria en cada objeto (array de objetos)
//por cada objeto se consulta su ultima medicion, y se llama al metodo getByCota con el año y la cota de esa medicion.
//A su vez necesitas la cota minima de ese objeto
        //Eso te va a dar volumen actual [1] y el volumen  minimo [1]
        //Luego se restan y se obtiene el didponible



function getMonthName()
{
    $fecha_actual = getdate();

    $numero_mes = $fecha_actual['mon'];

    $nombres_meses = array(
        1 => "ENERO", 2 => "FEBRERO", 3 => "MARZO", 4 => "ABRIL",
        5 => "MAYO", 6 => "JUNIO", 7 => "JULIO", 8 => "AGOSTO",
        9 => "SEPTIEMBRE", 10 => "OCTUBRE", 11 => "NOVIEMBRE", 12 => "DICIEMBRE"
    );

    $nombre_mes = $nombres_meses[$numero_mes];

    return  $nombre_mes;
}

function getShortName()
{
    $fecha_actual = getdate();

    $numero_mes = $fecha_actual['mon'];

    $nombres_meses_abreviados = array(
        1 => "Ene", 2 => "Feb", 3 => "Mar", 4 => "Abr",
        5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Ago",
        9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dic"
    );

    $nombre_mes_abreviado = $nombres_meses_abreviados[$numero_mes];

    return $nombre_mes_abreviado;
}

function getYear()
{
    $fecha_actual = getdate();

    $year = $fecha_actual['year'];

    return $year;
}

// $stringPrioritarios = "0";
// $queryPrioritarios = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'prioritarios'");
// if (mysqli_num_rows($queryPrioritarios) > 0) {
//     $stringPrioritarios = mysqli_fetch_assoc($queryPrioritarios)['configuracion'];
// }

$result = mysqli_query($conn, "SELECT nombre_embalse, operador FROM embalses");

$mes_actual = date('m');
$dia_actual = date('d');

$año_actual = date('Y');
$año_pasado = date('Y', strtotime('-1 year'));

/*$image_logo =  "/" . $projectName . "/assets/img/logos/cropped-mminaguas.jpg";
$logo_letters =  "/" . $projectName . "/assets/img/logos/MinaguasLetters.png";*/
// $area =  "/" . $projectName . "/pages/reports_images/Area_cuenca.png";

$image_logo = "https://embalsesminaguas.000webhostapp.com/assets/img/logos/cropped-mminaguas.jpg";
$logo_letters =  "https://embalsesminaguas.000webhostapp.com/assets/img/logos/MinaguasLetters.png";
$area =  "https://embalsesminaguas.000webhostapp.com/pages/reports_images/Area_cuenca.png";
$logo_combinado = "../../assets/img/logos/logo_combinado.jpg";
$mapa = "../../assets/img/estatus_embalses.png";


// $codigo = "08RHL0101";
// $titulo = "EMBALSE CAMATAGUA - ESTADO ARAGUA";
// $cota = 289.87;
// $mes = "Noviembre";
// $area_cuenta = 636.49;
// $variacion_semanal = "VARIACION SEMANAL";
// $fecha = "02";
// $fecha2 = "08";
// $variacion_mensual = getMonthName();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatus Embalses</title>

</head>

<style>
    hr {
        background-color: #2E86C1;
        height: 2px;
        width: 80%;
        top: 65px;
        color: #2E86C1;
        position: absolute;
    }

    .square {
        width: 60px;
        height: 60px;
        background-color: #2E86C1;
        border-radius: 10;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
        vertical-align: middle;
        line-height: 100px;

        color: #fff;
        font-size: 40px;
        font-weight: bold;
    }

    .code {
        height: 40px;
        left: 100px;
        color: #2E86C1;
        font-size: 20px;
        font-weight: bold;
    }

    .code-container {
        left: 75px;
        bottom: 10px;
        width: 500px;
        background-color: red;
    }

    .box-title {

        font-size: 18px; color:#FFFFFF; 
        background-color:  #0070C0;
        /* box-shadow: 50px 50px 50px grey;  */
        width: 95%; height: 30px; 
        text-align: center;
        position: absolute; 
        vertical-align: middle;
        margin-top: 650px; 
        margin-left: 35px; 
    }

    .box-note {
        font-size: 14px;; 
        width: 95%; height: 25px; 
        text-align: left;
        /* position: absolute;  */
        vertical-align: middle;
         margin-top: 60px;  
        /* margin-left: 20px;  */
        border: 0.5px solid red;

    }

    .img-logo {
        float: left;
        width: 50px;
        margin-bottom: 50px;
    }

    .img-letters {
        float: right;
        width: 100px;
        background-color: red;
    }

    .container-letters {
        left: 950px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        text-align: center;
        padding: 5px;
        border: 1px solid #707273;
        /*width: fit-content;*/
        font-size: 10px;
    }

    th {
        text-align: center;
        background-color: #0070C0;
        color: #FFFFFF;
    }

    .text-celd {
        vertical-align: middle; 
        width: 150px;
        text-align: center;
        font-size: 16px;
        border: 1px solid #707273;

       
    }

    .text-celdas {
        vertical-align: middle; 
        width: 130px;
        text-align: center;
        font-size: 16px;
        border: 1px solid #707273;

       
    }

    .celd-table {
        vertical-align: middle; 
        width: 80px;
        text-align: center;
        font-size: 14px;
        border: 1px solid #707273;
    }

    .text-big{
        height: 100px;
        font-size: 16px;
        vertical-align: middle; 
    }

    .total{

        font-size: 16px; 
        background-color: #DAE3F3;
        border: 1px solid #707273;

    }

    .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 50px;
           /* background-color: lightgray;*/
            text-align: center;
        }

    .tablaDos{

        vertical-align: middle; 
        text-align: center; 
        font-size: 12px;

    }
    .spazio{
        background-color: #FFFFFF;
        color: #FFFFFF;
        border: none;
    }
     
</style>

<body>

<!-- PAGINA 1 -->
    <div class="header">
        <hr style="top: 55px; color:#1B569D">
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
    </div>

    <div>
        <h1 style="position: absolute; top: 70px; left: 50px; text-align:center; text-justify:; color:#2E86C1; font-size: 23px;">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
        <h2 style="position: absolute; top: 100px; text-align: center; text-justify: center; color:#021568">Estatus de Fuentes Hídricas para Consumo Humano</h2>
        <div style="width: 1000px; height: 480px; background-color: lightgray; margin: 10px, 0, 0, 35px;">
        <!-- Mapa --> <img style="width:1000px ; height: 480px;" src="<?php echo $mapa ?>" />
        </div>
        <div style="position: absolute; height: 160px; width: 350px; left: 38px; top: 485px; border: gray 1px solid; background-color: #FFFFFF">
        <h5 style="text-align:center; letter-spacing: 5px; width: 100%;">LEYENDA</h5>
        <p style="position: absolute; top: 25px;
        text-align: left; padding-left: 40px; font-size: 12px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: red;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición baja (< 30%) <b> # Embalses</b></p>
    

        <p style="position: absolute; top: 45px;
        text-align: left; padding-left: 40px; font-size: 12px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: #44BEF0;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición Normal Bajo (30% < A > 60%) <b> # Embalses</b></p>

         
        <p style="position: absolute; top: 65px;
        text-align: left; padding-left: 40px; font-size: 12px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: blue;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición Normal Alto (60% < A > 90%) <b> # Embalses</b></p>

         
        <p style="position: absolute; top: 85px;
        text-align: left; padding-left: 40px; font-size: 12px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: green;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición Buena (> 90%) <b> # Embalses</b></p>

         
        <p style="position: absolute; top: 105px;
        text-align: left; padding-left: 40px; font-size: 12px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: #58F558;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición de Alivio <b> # Embalses</b></p>

         
        <p style="position: absolute; top: 125px;
        text-align: left; padding-left: 40px; font-size: 12px;">
        <div style="position: absolute; left: 20px; top: 2px; width: 0; height: 0;
        border-left: 5px solid transparent; border-right: 5px solid transparent; border-bottom: 10px solid black;"></div> EDC (Embalse de Compensación)</p>
       
      </div>

        <h4 style="position: absolute; top: 720px; text-align: center; text-justify: center;"><?php echo "$dia_actual DE " . getMonthName() . " $año_actual" ?></h4>
    </div>

    <!-- PAGINA 2 -->

    <div class="header" >    
        <hr style="top: 55px; color:#1B569D">
        <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
    </div>
    
    <div style="position: absolute; top: 70px; left: 20px; font-size: 18px; color:#000000;"><b>Bajo (< 30 %)</b>

    <table>
        <tr>
          <th class="text-celd">EMBALSE</th>
          <th class="text-celd">VOL. DISP. (HM3)</th>
          <th class="text-celd">HIDROLÓGICA</th>
        </tr>
       
        
          <?php while ($row = mysqli_fetch_array($result)) { ?>
            <tr>
             <td class="text-celd" style="font-size: 12px;"><?php echo $row['nombre_embalse']; ?> </td>
             <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
             <td class="text-celd" style="font-size: 12px;"><?php echo $row['operador']; ?> </td>
            </tr>
        <?php } ?>
      
        <tr>
        <td class="text-celd total" ><b> TOTAL </b></td>
        <td class="text-celd total" colspan="2"><b></b> Embalses</td>
        </tr>
      </table>
        

      <div style="font-size: 18px; color:#000000;  margin-top: 40px;"><b>Normal Bajo (30 % <  A  < 60%)</b></div>

      <table>
        <tr>
          <th class="text-celd">EMBALSE</th>
          <th class="text-celd">VOL. DISP. (HM3)</th>
          <th class="text-celd">HIDROLÓGICA</th>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
        <td class="text-celd total" ><b> TOTAL </b></td>
        <td class="text-celd total" colspan="2"><b></b> Embalses</td>
        </tr>
      </table>

      <div class="box-note"> Nota:</div>

    </div>



     <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 560px;"><b>Normal Alto (60 % <  A  < 90 %) </b>

      <table>
        <tr>
          <th class="text-celd">EMBALSE</th>
          <th class="text-celd">VOL. DISP. (HM3)</th>
          <th class="text-celd">HIDROLÓGICA</th>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
        <td class="text-celd total" ><b> TOTAL </b></td>
        <td class="text-celd total" colspan="2"><b></b> Embalses</td>
        </tr>
      </table>

      </div>


      <!-- PAGINA 3 -->

      <div style="page-break-before: always;"></div>
      <div class="header" >    
        <hr style="top: 55px; color:#1B569D">
        <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
    </div> 

      <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 20px;"><b>Buena ( 90 % <  A  < 100 %) </b>

        <table>
        <tr>
            <th class="text-celd">EMBALSE</th>
            <th class="text-celd">VOL. DISP. (HM3)</th>
            <th class="text-celd">HIDROLÓGICA</th>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
        <td class="text-celd total" ><b> TOTAL </b></td>
        <td class="text-celd total" colspan="2"><b></b> Embalses</td>
        </tr>
        </table>
        </div>

      <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 560px;"><b>Condición de Alivio </b>

        <table>
        <tr>
            <th class="text-celd">EMBALSE</th>
            <th class="text-celd">VOL. DISP. (HM3)</th>
            <th class="text-celd">HIDROLÓGICA</th>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
        <td class="text-celd total" ><b> TOTAL </b></td>
        <td class="text-celd total" colspan="2"><b></b> Embalses</td>
        </tr>
        </table>
        </div> 

        <!-- PAGINA 4 -->

        <div style="page-break-before: always;"></div>
      <div class="header" >    
        <hr style="top: 55px; color:#1B569D">
        <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
    </div> 
        <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>CONDICIONES ACTUALES DE ALMACENAMIENTO DE EMBALSES POR HIDROLÓGICA </b>
        
            <table style="margin-top: 20px;">
            <tr>
                <th class="tablaDos" rowspan="2">HIDROLÓGICA</th>
                <th class="tablaDos">BAJA</th>
                <th class="tablaDos" rowspan="2">% TOTAL</th>
                <th class="tablaDos">NORMAL- <br> BAJO</th>
                <th class="tablaDos" rowspan="2">% TOTAL</th>
                <th class="tablaDos">NORMAL- <br> ALTO</th>
                <th class="tablaDos" rowspan="2">% TOTAL</th>
            </tr>

            <tr>
                <th class="tablaDos">< 30 %</th>
                <th class="tablaDos">30% < A < 60% </th>
                <th class="tablaDos">60% < A < 90% </th>
            </tr>

            <tr>
            <td class="tablaDos" style="font-size: 12px;">PRUEBA</td>
            <td class="tablaDos" style="font-size: 12px;">PRUEBA</td>
            <td class="tablaDos" style="font-size: 12px;">PRUEBA</td>
            <td class="tablaDos" style="font-size: 12px;">PRUEBA</td>
            <td class="tablaDos" style="font-size: 12px;">PRUEBA</td>
            <td class="tablaDos" style="font-size: 12px;">PRUEBA</td>
            <td class="tablaDos" style="font-size: 12px;">PRUEBA</td>
            </tr>

            <tr>
                <th class="spazio" colspan="7"></th>
            </tr>

            <tr>
            <th class="spazio" style="border-bottom: 1px solid #707273; border-right: 1px solid #707273;" ><b></b></th>  
            <th class="tablaDos" colspan="2"><b> BAJA </b></th>
            <th class="tablaDos" colspan="2"><b></b> NORMAL-BAJO</th>
            <th class="tablaDos" colspan="2"><b></b> NORMAL-BAJO</th>
            </tr>

            <tr>
            <td class="text-celdas total" style="font-size: 12px;"><b>TOTAL</b></td>
            <td class="tablaDos" style="font-size: 12px;" colspan="2"><b>PRUEBA</b></td>
            <td class="tablaDos" style="font-size: 12px;" colspan="2"><b>PRUEBA</b></td>
            <td class="tablaDos" style="font-size: 12px;" colspan="2"><b>PRUEBA</b></td>
            </tr>

            <tr>
            <td class="text-celdas total" style="font-size: 12px;"><b>%</b></td>
            <td class="tablaDos" style="font-size: 12px;" colspan="2"><b>PRUEBA</b></td>
            <td class="tablaDos" style="font-size: 12px;" colspan="2"><b>PRUEBA</b></td>
            <td class="tablaDos" style="font-size: 12px;" colspan="2"><b>PRUEBA</b></td>
            </tr>

            </table>

            <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 40px; margin-left: 620px;"><b> </b>

            <table>
            <tr>
                <th class="tablaDos" rowspan="2">HIDROLÓGICA</th>
                <th class="tablaDos" >BUENA</th>
                <th class="tablaDos">ALIVIANDO</th>
                <th class="tablaDos"  rowspan="2"> TOTAL</th>
                <th class="tablaDos" rowspan="2">% TOTAL</th>
            </tr>

            <tr>
                <th class="tablaDos">90% < B < 100% </th>
                <th class="tablaDos">< 100 %</th>                 
            </tr>

            <tr>
            <td class="tablaDos" style="font-size: 12px;">PRUEBA</td>
            <td class="tablaDos" style="font-size: 12px;">PRUEBA</td>
            <td class="tablaDos" style="font-size: 12px;">PRUEBA</td>
            <td class="tablaDos" style="font-size: 12px;">PRUEBA</td>
            <td class="tablaDos" style="font-size: 12px;">PRUEBA</td>
            </tr>

            <tr>
                <th class="spazio" colspan="7"></th>
            </tr>

            <tr>
            <th class="spazio" style="border-bottom: 1px solid #707273; border-right: 1px solid #707273;" ><b></b></th>  
            <th class="tablaDos" ><b> BUENA </b></th>
            <th class="tablaDos" ><b></b> ALIVIANDO</th>
            <th class="tablaDos" ><b></b> TOTAL</th>
            <th class="tablaDos" ><b></b> % </th>
            </tr>

    <tr>
        <td class="text-celdas total" style="font-size: 12px; height: 20px;"><b>TOTAL</b></td>
        <td class="tablaDos" style="font-size: 12px;" ><b>PRUEBA</b></td>
        <td class="tablaDos" style="font-size: 12px;" ><b>PRUEBA</b></td>
        <td class="tablaDos" style="font-size: 12px;" ><b>PRUEBA</b></td>
        <td class="tablaDos" style="font-size: 12px;" ><b>PRUEBA</b></td>
    </tr>


            </table>
            </div>

            </div>

<!-- PAGINA 5 -->

            <div style="page-break-before: always;"></div>
      <div class="header" >    
        <hr style="top: 55px; color:#1B569D">
        <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
    </div> 
        <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>CONDICIONES ACTUALES DE ALMACENAMIENTO DE EMBALSES</b>
        </div>

        <div style="width: 550px; height: 450px; background-color: lightgray; margin-top: 50px; margin-left: 35px;">
        </div>

        <div style="font-size: 15px; color:#000000; position: absolute;  margin-top: 200px; margin-left: 640px;"><b> <u># EMBALSES</u> EN CONDICIONES BUENAS Y MUY BUENAS <br> ( > 90% Y ALIVIANDO )</b></div>

        <div style="font-size: 15px; color:#000000; position: absolute;  margin-top: 300px; margin-left: 640px;"><b> <u># EMBALSES</u> EN CONDICIONES NORMALES ALTO <br> ( 60 % < A < 90 % )</b></div>

        <div style="font-size: 15px; color:#000000; position: absolute;  margin-top: 400px; margin-left: 640px;"><b> <u># EMBALSES</u> EN CONDICIONES NORMALES BAJO <br> ( 30 % <  A  < 60% )</b></div>

        <div style="font-size: 15px; color:#000000; position: absolute;  margin-top: 500px; margin-left: 640px;"><b> <u># EMBALSES</u> EN CONDICIONES BAJAS ( < 30 %)</b></div>
         
        <div class="box-title"><b> # DE LOS EMBALSES SE ENCUENTRAN EN CONDICIONES NORMALES A MUY BUENAS</b></div>

<!-- PAGINA 6 -->

        <div style="page-break-before: always;"></div>
      <div class="header" >    
        <hr style="top: 55px; color:#1B569D">
        <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
    </div> 
        <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>CONDICIONES ACTUALES DE ALMACENAMIENTO DE EMBALSES</b>
        </div>

        <div style="font-size: 17px; color: #0070C0; position: absolute;  margin-top: 120px; margin-left: 7px;"><b>DESDE EL fecha HASTA HOY</b>
        </div>

        <div style="width: 450px; height: 450px; background-color: lightgray; margin-top: 80px; margin-left: 35px;">
        </div>

        <div style="position: absolute; width: 0.5px; height: 600px; background-color: #7F7F7F; margin-top: 110px; margin-left: 525px;"></div>

        <div style="font-size: 17px; color: #0070C0; position: absolute;  margin-top: 120px; margin-left: 550px;"><b>DESDE EL fecha HASTA HOY</b>
        </div>

        <div style="width: 450px; height: 450px; background-color: lightgray; position: absolute; margin-top: 180px; margin-left: 570px;">
        </div>
        
        <div style="position: absolute; margin-top: 670px; margin-left: 50px; width: 95%; height: 100px;">
        <div style="position: absolute; font-size: 18px; color:red; text-align: center;"> <b> (Varió #% comparado con la semana pasada y <br> #% con respecto a hace 15 días)</b></div>
        <div style="position: absolute; margin-left: 550px; font-size: 18px; color:red; text-align: center;"><b> (Varió #% comparado con la semana pasada y <br> #% con respecto a hace 15 días)</b></div>  
        </div>

<!-- PAGINA 7 -->
        <div style="page-break-before: always;"></div>
        <div class="header">
        <hr style="top: 55px; color:#1B569D">
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
        </div>

      <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>VARIACIÓN DE VOLUMEN DE ALMACENAMIENTO NACIONAL</b>
      </div>

     <div>
        <div style="width: 1000px; height: 535px; background-color: lightgray; margin: 10px, 0, 0, 35px;">
        </div>
      </div>

      <div style="position: absolute; height: 160px; width: 350px; left: 38px; top: 485px; border: gray 1px solid; background-color: #FFFFFF">
        <h5 style="text-align:center; letter-spacing: 5px; width: 100%;">LEYENDA</h5>
        <p style="position: absolute; top: 35px;
        text-align: left; padding-left: 40px; font-size: 15px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: blue;
         border-radius: 5; height: 10px; width: 10px;"></div> Embalses</p>
    

        <p style="position: absolute; top: 60px;
        text-align: left; padding-left: 40px; font-size: 15px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: green;
         border-radius: 5; height: 10px; width: 10px;"></div>Aumento de Volumen <b> # Embalses</b></p>

         
        <p style="position: absolute; top: 85px;
        text-align: left; padding-left: 40px; font-size: 15px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: red;
         border-radius: 5; height: 10px; width: 10px;"></div>Disminución de Volumen <b> # Embalses</b></p>

         
        <p style="position: absolute; top: 110px;
        text-align: left; padding-left: 40px; font-size: 15px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: black;
         border-radius: 5; height: 10px; width: 10px;"></div>Sin Cambioss <b> # Embalses</b></p>

      </div>

      <h4 style="position: absolute; top: 640px; text-align: right; text-justify: right;">DESDE EL <?php echo "$dia_actual DE " . getMonthName()?></h4>

<!-- PAGINA 8 -->
<div style="page-break-before: always;"></div>
        <div class="header">
        <hr style="top: 55px; color:#1B569D">
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
        </div>

      <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>VARIACIÓN DE VOLUMEN DE ALMACENAMIENTO NACIONAL</b>
      </div>

     <div>
        <div style="width: 1000px; height: 535px; background-color: lightgray; margin: 10px, 0, 0, 35px;">
        </div>
      </div>

      <div style="position: absolute; height: 160px; width: 350px; left: 38px; top: 485px; border: gray 1px solid; background-color: #FFFFFF">
        <h5 style="text-align:center; letter-spacing: 5px; width: 100%;">LEYENDA</h5>
        <p style="position: absolute; top: 35px;
        text-align: left; padding-left: 40px; font-size: 15px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: blue;
         border-radius: 5; height: 10px; width: 10px;"></div> Embalses</p>
    

        <p style="position: absolute; top: 70px;
        text-align: left; padding-left: 40px; font-size: 15px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: green;
         border-radius: 5; height: 10px; width: 10px;"></div>Aumento de Volumen <b> # Embalses</b></p>

         
        <p style="position: absolute; top: 105px;
        text-align: left; padding-left: 40px; font-size: 15px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: red;
         border-radius: 5; height: 10px; width: 10px;"></div>Disminución de Volumen <b> # Embalses</b></p>

      </div>

      <h4 style="position: absolute; top: 640px; text-align: right; text-justify: right;"> DESDE EL <?php echo "$dia_actual DE " . getMonthName()?></h4>
<!-- PAGINA 9 -->

        <div style="page-break-before: always;"></div>
        <div class="header">
        <hr style="top: 55px; color:#1B569D">
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
        </div>

      <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 50px; margin-left: 500px;"><b>HIDROCAPITAL</b>
      </div>

      <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 85px; margin-left: 5px;"><b>VARIACIONES DE VOLUMEN DE LOS EMBALSES HASTA HOY</b>
      </div>

        <div style="width: 520px; height: 320px; background-color: lightgray; margin-top: 20px; margin-left: 10px;">
        </div>

        <div style="width: 520px; height: 320px; background-color: lightgray; position: absolute; margin-top: 120px; margin-left: 560px;">
        </div>
        
        <div style="position: absolute; margin-top: 430px; margin-left: 10px; width: 95%; height: 100px;">
        <div style="position: absolute; font-size: 18px; text-align: right;"> <b>FECHA</b>
        <table>
        <tr>
          <th class="text-celd">EMBALSE</th>
          <th class="text-celd">VAR. VOL. <br>(HM3)</th>
          <th class="text-celd">% VAR. VOL.</th>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 16px;"><b>TOTAL</b></td>
        <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        </tr>
      </table>
             
        </div>

        <div style="position: absolute; margin-left: 550px; font-size: 18px; text-align: right;"><b>FECHA</b>
        <table>
        <tr>
          <th class="text-celd">EMBALSE</th>
          <th class="text-celd">VAR. VOL. <br>(HM3)</th>
          <th class="text-celd">% VAR. VOL.</th>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 16px;"><b>TOTAL</b></td>
        <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        </tr>
      </table>

        </div>  
        </div>
<!-- PAGINA 10 -->

<div style="page-break-before: always;"></div>
        <div class="header">
        <hr style="top: 55px; color:#1B569D">
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
        </div>

      <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 55px; margin-left: 5px;"><b>VARIACIONES DE VOLUMEN DE LOS EMBALSES HASTA HOY</b>
      </div>

        <div style="position: absolute; margin-top: 100px; margin-left: 10px; width: 95%; height: 100px;">

        <div style="position: absolute; font-size: 18px; text-align: right;"> <b> HIDROBOLÍVAR  DESDE EL FECHA</b></div>
        <div style="width: 520px; height: 520px; background-color: lightgray; margin-top: 50px; margin-left: 10px;">
        </div>

    
        <div style="position: absolute; margin-left: 550px; font-size: 18px; text-align: right; margin-top: 50px;">
        <table>
        <tr>
          <th class="text-celd text-big">EMBALSE</th>
          <th class="text-celd text-big">VAR. VOL. <br><br><br> (HM3)</th>
          <th class="text-celd text-big">% VAR. VOL.</th>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 12px; height: 50px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>

        <tr>
        <td class="text-celd" style="font-size: 12px; height: 50px;"><b>TOTAL</b></td>
        <td class="text-celd" style="font-size: 12px;"><b>PRUEBA</b></td>
        <td class="text-celd" style="font-size: 12px;"><b>PRUEBA</b></td>
        </tr>

      </table>

    
        </div>  
        </div>

   <!-- PAGINA 11 -->
   
        <div style="page-break-before: always;"></div>
        <div class="header">
        <hr style="top: 55px; color:#1B569D">
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
        </div>

      <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 50px; margin-left: 500px;"><b>HIDROCENTRO</b>
      </div>

      <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 85px; margin-left: 5px;"><b>VARIACIONES DE VOLUMEN DE LOS EMBALSES HASTA HOY</b>
      </div>

        <div style="width: 520px; height: 320px; background-color: lightgray; margin-top: 20px; margin-left: 10px;">
        </div>

        <div style="width: 520px; height: 320px; background-color: lightgray; position: absolute; margin-top: 120px; margin-left: 560px;">
        </div>
        
        <div style="position: absolute; margin-top: 430px; margin-left: 10px; width: 95%; height: 100px;">
        <div style="position: absolute; font-size: 18px; text-align: right;"> <b>FECHA</b>
        <table>
        <tr>
          <th class="text-celd" rowspan="2">EMBALSE</th>
          <th class="text-celd" colspan="2">DESDE fecha</th>
        </tr>
        <tr>
          <th class="text-celd" style="height: 20px;">VAR. VOL. <br>(HM3)</th>
          <th class="text-celd" rowspan="">% VAR. <br> VOL.</th>
        </tr>
 <tr>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 16px;"><b>TOTAL</b></td>
        <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        </tr>

      </table>
             
        </div>

        <div style="position: absolute; margin-left: 550px; font-size: 18px; text-align: right;"><b>FECHA</b>
        <table>
        <tr>
          <th class="text-celd" rowspan="2">EMBALSE</th>
          <th class="text-celd" colspan="2">DESDE fecha</th>
        </tr>
        <tr>
          <th class="text-celd" style="height: 20px;">VAR. VOL. <br>(HM3)</th>
          <th class="text-celd" rowspan="">% VAR. <br> VOL.</th>
        </tr>

        <tr>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>

        <tr>
        <td class="text-celd" style="font-size: 16px;"><b>TOTAL</b></td>
        <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        </tr>

       
      </table>

        </div>  
        </div>
<!-- PAGINA 12 -->

<div style="page-break-before: always;"></div>
        <div class="header">
        <hr style="top: 55px; color:#1B569D">
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
        </div>

      <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 50px; margin-left: 500px;"><b>HIDROPÁEZ</b>
      </div>

      <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 85px; margin-left: 5px;"><b>VARIACIONES DE VOLUMEN DE LOS EMBALSES HASTA HOY</b>
      </div>

        <div style="width: 520px; height: 320px; background-color: lightgray; margin-top: 20px; margin-left: 10px; text-align: right; font-size: 18px;"><b>FECHA</b>
        </div>

        <div style="width: 520px; height: 320px; background-color: lightgray; position: absolute; margin-top: 120px; margin-left: 560px; text-align: right; font-size: 18px;"><b>FECHA</b>
        </div>
        
        <div style="position: absolute; margin-top: 430px; margin-left: 10px; width: 95%; height: 100px;">
        <div style="position: absolute; margin-top: 30px;">
        <table>
        <tr>
          <th class="text-celd">EMBALSE</th>
          <th class="text-celd">VAR. VOL. <br> (HM3)</th>
          <th class="text-celd">% VAR. VOL.</th>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 16px;"><b>TOTAL</b></td>
        <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        </tr>
      </table>
             
        </div>

        <div style="position: absolute; margin-left: 550px; margin-top: 30px;">
        <table>
        <tr>
          <th class="text-celd">EMBALSE</th>
          <th class="text-celd">VAR. VOL. <br> (HM3)</th>
          <th class="text-celd">% VAR. VOL.</th>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
        <td class="text-celd" style="font-size: 16px;"><b>TOTAL</b></td>
        <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        </tr>
      </table>

        </div>  
        </div>
   <!-- PAGINA 11 -->
   
   <div style="page-break-before: always;"></div>
        <div class="header">
        <hr style="top: 55px; color:#1B569D">
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
        </div>

        <div style="width: 520px; height: 320px; background-color: lightgray;  position: absolute; margin-top: 80px; margin-left: 10px; text-align: right; font-size: 18px;"><b>FECHA</b>
        </div>

        <div style="width: 520px; height: 320px; background-color: lightgray; position: absolute; margin-top: 420px; margin-left: 10px; text-align: right; font-size: 18px;"><b>FECHA</b>
        </div>
        
        <div style="width: 520px; height: 320px; position: absolute; margin-top: 80px; margin-left: 550px;">
    <table>
        <tr>
        <th class="celd-table" colspan="5">HIDROLAGO</th>
        </tr>
        <tr>
        <th class="celd-table" rowspan="2">EMBALSE</th>
        <th style="font-size: 12px" colspan="2">DESDE 05/ENE/2023</th>
        <th style="font-size: 12px" colspan="2">DESDE 01/JUN/2023</th>
        </tr>
        <tr>
        <th class="celd-table" >VAR. VOL. <br>(HM3)</th>
        <th class="celd-table" >% VAR. VOL.</th>
        <th class="celd-table" >VAR. VOL. <br>(HM3)</th>
        <th class="celd-table" >% VAR. VOL.</th>
        </tr>
        <tr>
        <td class="" style="font-size: 12px; width= 10px">PRUEBA</td>
        <td class="" style="font-size: 12px;">PRUEBA</td>
        <td class="" style="font-size: 12px;">PRUEBA</td>
        <td class="" style="font-size: 12px;">PRUEBA</td>
        <td class="" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
        <td class="" style="font-size: 12px;"><b>TOTAL</b></td>
        <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
        <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
        <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
        <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
        </tr>
      </table>
             
        </div>
   
        <div style="position: absolute; margin-top: 420px; margin-left: 10px; width: 95%; height: 100px;">

        <div style="position: absolute; margin-left: 540px; font-size: 18px; text-align: right;">
        <table>
        <tr>
        <th class="celd-table" colspan="5">FALCÓN</th>
        </tr>
        <tr>
        <th class="celd-table" rowspan="2">EMBALSE</th>
        </tr>
        <tr>
        <th class="celd-table" >VAR. VOL. <br>(HM3)</th>
        <th class="celd-table" >% VAR. VOL.</th>
        <th class="celd-table" >VAR. VOL. <br>(HM3)</th>
        <th class="celd-table" >% VAR. VOL.</th>
        </tr>
        <tr>
        <td class="" style="font-size: 12px; width= 10px">PRUEBA</td>
        <td class="" style="font-size: 12px;">PRUEBA</td>
        <td class="" style="font-size: 12px;">PRUEBA</td>
        <td class="" style="font-size: 12px;">PRUEBA</td>
        <td class="" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
        <td class="" style="font-size: 12px;"><b>TOTAL</b></td>
        <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
        <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
        <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
        <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
        </tr>
      </table>
        </div>  
        </div>

    </body>

</html>