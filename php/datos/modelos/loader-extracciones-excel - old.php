<?php
    require_once '../../Conexion.php';
    $sql = "SELECT DISTINCT id_embalse, nombre_embalse
            FROM embalses em
            WHERE em.estatus = 'activo';";
    $query = mysqli_query($conn, $sql);

    closeConection($conn);


    $array_embalses = array();
    while($row = mysqli_fetch_array($query)){
        $array_aux = [];
        $array_aux['id_embalse'] = $row['id_embalse'];
        $array_aux['nombre_embalse'] = $row['nombre_embalse'];
        array_push($array_embalses, $array_aux);
    }
?>

<link href="../../../assets/css/style-spinner.css" rel="stylesheet" />
<link id="pagestyle" href="../../../assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />


<div class="loaderPDF" id="box-loader">
    <div class="text-center">
        <h3 id="wait-text"></h3>
        <div> <!--style="height: 90% !important; display: flex; align-items:center !important;"-->
            <div class="lds-dual-ring">
            </div>
        </div>
    </div>
</div>

<div id="box-success" style="display: none;">
    <div class="text-center"  style="height: 100%; display: flex; align-items: center;">
        <div class='w-100'>
            <h3>Archivo Generado Correctamente</h3>
            <button class="btn btn-primary" onclick="window.close();">Cerrar</button>
        </div>
    </div>
</div>

<?php
    //print "<script>window.location='exportar-extracciones-excel.php?anio=$_GET[anio]';</script>";
?>

<script>

    var array_embalses = <?php echo json_encode($array_embalses); ?>;

    let i = 0;

    setInterval(() => {
        if(i < array_embalses.length) {
            document.getElementById('wait-text').textContent = 'Generando ' + array_embalses[i].nombre_embalse + '...';
            i++;
        }
        else {
            document.getElementById('wait-text').textContent = 'Generando Archivo...';
        }
    }, 1700); // Cambia el texto cada 3 segundos (3000 ms)

    var xhr = new XMLHttpRequest();
    xhr.responseType = 'blob'; // Se espera una respuesta binaria (archivo)

    // Mostrar el spinner al iniciar la solicitud
    //document.querySelector('.spinner').style.display = 'block';

    xhr.open('GET', "exportar-extracciones-excel.php?anio=" + <?php echo $_GET['anio'];?>, true);

    xhr.onload = function() {
        if (xhr.status == 200) {

            var blob = xhr.response;
            var url = URL.createObjectURL(blob);

            // Crear un enlace <a> para descargar el archivo con su nombre original
            var a = document.createElement('a');
            a.href = url;
            a.download = xhr.getResponseHeader('Content-Disposition').match(/filename="([^"]+)"/)[1]; // Obtener el nombre del archivo desde las cabeceras de respuesta
            document.body.appendChild(a);
            a.click();

            // Limpiar y revocar el objeto URL después de la descarga
            URL.revokeObjectURL(url);
            document.body.removeChild(a);

            //window.close();
            document.getElementById('box-loader').style.display = 'none';
            document.getElementById('box-success').style.display = 'block';

            // Ocultar el spinner al completar la solicitud
            //document.querySelector('.spinner').style.display = 'none';
            //alert('Archivo generado correctamente.');
        } else {
            document.querySelector('.loaderPDF').style.display = 'none';
            alert('Error al generar el archivo.');
            //window.close();
        }
    };

    xhr.send();
</script>
