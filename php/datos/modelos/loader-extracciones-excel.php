<link href="../../../assets/css/style-spinner.css" rel="stylesheet" />
<div class="loaderPDF">
    <div style="height: 90% !important; display: flex; align-items:center !important;">
        <div class="lds-dual-ring">
        </div>
    </div>
</div>

<?php
    //print "<script>window.location='exportar-extracciones-excel.php?anio=$_GET[anio]';</script>";
?>

<script>

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

            window.close();

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
