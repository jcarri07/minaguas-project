<script src="./assets/js/Chart.js"></script>
<!--script src="../../assets/js/date-fns.js"></script-->
<script src="./assets/js/date-fns.js"></script>
<script src="./assets/js/sweetalerts.js"></script>
<?php
include "./php/Conexion.php";
$re = mysqli_query($conn, "SELECT e.id_embalse, e.nombre_embalse, (SELECT YEAR(d.fecha) AS fech FROM datos_embalse d WHERE d.id_embalse = e.id_embalse and d.estatus = 'activo' AND d.cota_actual <> 0 ORDER BY YEAR(fecha) ASC limit 1) AS fecha FROM embalses e WHERE e.estatus = 'activo' ORDER BY e.nombre_embalse ASC;");
$count = mysqli_num_rows($re);
$array = mysqli_fetch_all($re, MYSQLI_ASSOC);
?>
<!-- <script src="../../assets/js/jquery/jquery.min.js"></script> -->
<script src="./assets/js/html2canvas.min.js"></script>
<link href="./assets/css/style-spinner.css" rel="stylesheet" />

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 card">
            <div class="card-header pb-0">
                <h4>MONITOREO DE CARGA DE DATOS</h4>
            </div>
            <div class="row card-body">
                <div id="prin" class="col-12">
                    <div class="row mb-2">
                        <div class="col-6 col-sm-2" style="padding: 0px auto;margin-bottom:5px;">
                            <label class="form-label">Embalse:</label>
                            <select name=0 id="embalses" class="form-select" required>
                                <!--option value="0">Todas los Embalses</option-->
                                <?php
                                $i = 0;
                                while ($i < $count) {
                                ?>
                                    <option value="<?php echo $array[$i]['id_embalse']; ?>"><?php echo $array[$i]['nombre_embalse']; ?></option>
                                <?php
                                    $i++;
                                };
                                closeConection($conn);
                                ?>
                            </select>
                        </div>

                        <div class="col-6 col-sm-2" style="padding: 0px auto;margin-bottom:5px;">
                            <label id="lab" class="form-label">Tipo de Gráfica:</label>
                            <select id="tipo" class="form-select" required>
                                <option value="line">Línea</option>
                                <option value="bar">Barra</option>
                            </select>
                        </div>
                        <div class="col-6 col-sm-2" style="padding: 0px auto;margin-bottom:5px;">
                            <label id="" class="form-label">Ver por:</label>
                            <select id="ver" class="form-select" required>
                                <option value="volumen">Volumen</option>
                                <option value="cota">Cota</option>
                            </select>
                        </div>
                        <div class="col-6 col-sm-2">
                            <label class="form-label">Período:</label>
                            <select id="fe" class="form-select " style="padding: 0px auto; margin-bottom:5px;">
                                <option value="Grafica_anio.php">Año</option>
                                <option value="Grafica_mes.php">Mes</option>
                                <option Value="Grafica_perso.php">Personalizado</option>
                            </select>

                        </div>
                        <div id="formato" class="row col-md-4" style="padding: 0px; margin-left: 0px;">
                            <div class="col-md-6">
                                <label class="form-label">Año:</label>
                                <select id="anio" class="form-select " style="padding: 0px auto; margin-bottom:5px;">
                                    <option value="2024" selected>2024</option>

                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hasta:</label>
                                <select id="periodo" class="form-select " style="padding: 0px auto; margin-bottom:5px;">
                                    <option value="2024" selected>2024</option>

                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="table-responsive col-12" style="height:610px;">
                    <div id="contenedor" class="" style="height:600px;min-width:1000px"><canvas id="chart" class="border border-radius-lg"></canvas></div>
                </div>
                <div class="col-12 pt-1">
                    <div id="" class="row align-items-center">


                        <div class="col-md-2">
                            <div class="row justify-content-end">
                                <div class="col-lg-12 col-md-7">

                                    <button type="button" id="grafica" title="Generar pdf de grafica de este embalse" class=" btn btn-outline-secondary btn-block w-100 mb-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                            <path fill="#d42b34" d="M64 464l48 0 0 48-48 0c-35.3 0-64-28.7-64-64L0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 304l-48 0 0-144-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z" />
                                        </svg>&nbsp;&nbsp;Reporte
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    $(document).ready(function() {
        let anios = [
            <?php

            $i = 0;
            while ($i < $count) {
                echo $array[$i]['fecha'];
                $i++;
                if ($i < ($count - 1)) {
                    echo ",";
                }
            };
            ?>

        ];
        let embalse = [
            <?php

            $i = 0;
            while ($i < $count) {
                echo $array[$i]['id_embalse'];
                $i++;
                if ($i < ($count - 1)) {
                    echo ",";
                }
            };
            ?>

        ];
        let menor = 1960;
        i = 0;
        while (i < embalse.length) {

            if (embalse[i] == $("#embalses option:selected").val()) {
                menor = anios[i];

            }

            i++;
        };
        anio();
        select($("#anio option:selected").val())
            .then(() => {
                ajax(); // Esto se ejecutará después de que select() termine.
            });

        $("#embalses").change(function() {
            i = 0;
            while (i < embalse.length) {

                if (embalse[i] == $("#embalses option:selected").val()) {
                    menor = anios[i];
                    if (!menor) {
                        menor = "<?php echo date('Y') ?>";
                    }
                    //console.log(menor);
                }

                i++;
            };
            a = 0;
            switch ($("#fe option:selected").val()) {

                case "Grafica_anio.php":
                    anio();
                    a = $("#anio option:selected").val();
                    break;

                case "Grafica_mes.php":
                    var fechaActual = new Date();
                    //console.log(fechaActual);

                    // Obtener el año y mes actual como cadenas de texto
                    var anioActual = fechaActual.getFullYear().toString();
                    var mesActual = (fechaActual.getMonth() + 1).toString().padStart(2, '0');

                    // Establecer el valor del input de tipo month
                    var valorMesActual = anioActual + '-' + mesActual;
                    $("#formato").html('<div class="col-md-6"><label class="form-label">Fecha:</label><input type="Month" id="mes" min="' + menor + '-01" max="<?php echo date('Y-m') ?>" class="form-control" style="padding: 0px auto; margin-bottom:5px;"></div><div class="col-md-6"><label class="form-label">Hasta:</label><select id="periodo" class="form-select " style="padding: 0px auto; margin-bottom:5px;"><option selected value="<?php echo date('Y-m') ?>"><?php echo date('Y') - 1 ?></option></select></div>');
                    $('#mes').val(valorMesActual);
                    a = anioActual;

                    $("#mes").on('change', function() {
                        const mes = $("#mes").val();
                        const year = new Date(mes + "-01").getFullYear();
                        console.log(year);
                        select(year + 1)
                            .then(() => {
                                ajax(); // Esto se ejecutará después de que select() termine.
                            });
                    });
                    $("#periodo").change(function() {
                        ajax();
                    });

                    console.log("a: " + a);
                    break;

            }


            select(a)
                .then(() => {
                    ajax(); // Esto se ejecutará después de que select() termine.
                });
        });
        $("#tipo").change(function() {
            ajax();
        });
        $("#periodo").change(function() {
            ajax();
        });
        $("#anio").change(function() {
            select($("#anio option:selected").val())
                .then(() => {
                    ajax(); // Esto se ejecutará después de que select() termine.
                });
        });
        $("#ver").change(function() {
            ajax();
        });
        $("#grafica").click(function() {

            const x = document.querySelector("#chart");
            html2canvas(x).then(function(canvas) { //PROBLEMAS
                //$("#ca").append(canvas);
                canvas.willReadFrequently = true,
                    dataURL = canvas.toDataURL("image/jpeg", 0.9);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', './php/guardar-imagen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('imagen=' + dataURL + '&nombre=grafica&numero=' + 0);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        //let response = JSON.parse(this.responseText);
                        if (this.responseText == "si") {
                            console.log(this.responseText);
                            // window.open(
                            //     "./pages/reports/print_monitoreo_simple.php?id=" +
                            //     $("#embalses option:selected").val() +
                            //     "&name=" + $("#embalses option:selected").html() +
                            //     "&fecha1=" + $("#fecha1").val() +
                            //     "&fecha2=" + $("#fecha2").val() +
                            //     "&anio=" + $("#anio option:selected").val() +
                            //     "&mes=" + $("#mes").val(),
                            //     '_blank'
                            // );
                        } else {
                            if (this.responseText == "no") {
                                console.log("Error al guardar la imagen: " + this.responseText);
                            } else {
                                console.log(this.responseText);
                            }
                        };
                    }
                }
            });
        });

        $("#fe").change(function() {

            switch ($("#fe option:selected").val()) {

                case "Grafica_anio.php":
                    var fechaActual = new Date();
                    $("#formato").html('<div class="col-md-6"><label class="form-label">Año:</label><select id="anio" class="form-control " style="padding: 0px auto; margin-bottom:5px;"><option value="<?php echo date('Y') ?>" selected><?php echo date('Y') ?></option></select></div><div class="col-md-6"><label class="form-label">Hasta:</label><select id="periodo" class="form-select " style="padding: 0px auto; margin-bottom:5px;"><option selected value="<?php echo date('Y') - 1 ?>"><?php echo date('Y') - 1 ?></option></select></div>');

                    $("#anio").change(function() {
                        select($("#anio option:selected").val())
                            .then(() => {
                                ajax(); // Esto se ejecutará después de que select() termine.
                            });
                    });
                    $("#periodo").change(function() {
                        ajax();
                    });

                    select($("#anio option:selected").val())
                        .then(() => {
                            ajax(); // Esto se ejecutará después de que select() termine.
                        });
                    break;

                case "Grafica_mes.php":
                    var fechaActual = new Date();
                    //console.log(fechaActual);

                    // Obtener el año y mes actual como cadenas de texto
                    var anioActual = fechaActual.getFullYear().toString();
                    var mesActual = (fechaActual.getMonth() + 1).toString().padStart(2, '0');

                    // Establecer el valor del input de tipo month
                    var valorMesActual = anioActual + '-' + mesActual;


                    $("#formato").html('<div class="col-md-6"><label class="form-label">Fecha:</label><input type="Month" id="mes" min="' + menor + '-01" max="<?php echo date('Y-m') ?>" class="form-control" style="padding: 0px auto; margin-bottom:5px;"></div><div class="col-md-6"><label class="form-label">Hasta:</label><select id="periodo" class="form-select " style="padding: 0px auto; margin-bottom:5px;"><option selected value="<?php echo date('Y-m') ?>"><?php echo date('Y') - 1 ?></option></select></div>');
                    $('#mes').val(valorMesActual);
                    select(anioActual)
                        .then(() => {
                            ajax(); // Esto se ejecutará después de que select() termine.
                        });

                    $("#mes").on('change', function() {
                        const mes = $("#mes").val();
                        const year = new Date(mes + "-01").getFullYear();
                        console.log(year);
                        select(year + 1)
                            .then(() => {
                                ajax(); // Esto se ejecutará después de que select() termine.
                            });
                    });
                    $("#periodo").change(function() {
                        ajax();
                    });

                    break;

                case "Grafica_perso.php":
                    var fechaActual = new Date();

                    $("#formato").html('<div class="col-md-6"><label class="form-label">Desde:</label><input type="date" id="fecha1" value="<?php echo date('Y-m-d', strtotime('-1 months', strtotime(date('Y-m-d')))); ?>" min="1988-01-01" max="<?php echo date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))) ?>" class="form-control" style="padding: 0px auto; margin-bottom:5px;"></div><div class="col-md-6"><label class="form-label">Hasta:</label><input type="date" id="fecha2" value="<?php echo date('Y-m-d') ?>" min="1988-01-01" max="<?php echo date('Y-m-d') ?>" class="form-control" style="padding: 0px auto; margin-bottom:5px;"></div>');


                    //var anioActual = fechaActual.getFullYear().toString();
                    //var mesActual = String(fechaActual.getMonth() + 1).padStart(2, '0');

                    $("#fecha1").on('change', function() {
                        ajax();
                    });
                    $("#fecha2").on('change', function() {
                        ajax();
                    });
                    ajax();
                    break;
                default:
                    $("#formato").html("");
                    break;
            }
        });

        function select(x) {

            return new Promise((resolve) => {
                if ($("#periodo option:selected").val() > x) {
                    const $select = $("#periodo");
                    $select.html("");
                    for (i = (x); i >= menor; i--) {
                        $select.append($("<option>", {
                            value: i,
                            text: i
                        }));
                        if (i == (x - 1)) {
                            $("#periodo > option[value=" + i + "]").attr("selected", true);
                        }


                    }
                };
                if ((x - $("#periodo option:selected").val()) > 1 || $("#periodo option:selected").val() <= x) {
                    const l = $("#periodo option:selected").val();
                    const $select = $("#periodo");
                    $select.html("");
                    for (i = (x); i >= menor; i--) {
                        $select.append($("<option>", {
                            value: i,
                            text: i
                        }));
                        if (i == l) {
                            $("#periodo > option[value=" + i + "]").attr("selected", true);
                        }


                    }
                };
                resolve();
            });


        };

        function anio() {
            const $anio = $("#anio");
            const k = $("#anio option:selected").val();
            if (menor) {
                $anio.html("");
                for (i = <?php echo date('Y') ?>; i >= menor; i--) {
                    $anio.append($("<option>", {
                        value: i,
                        text: i
                    }));
                    if (i == <?php echo (date('Y')) ?>) {
                        $("#anio > option[value=" + i + "]").attr("selected", true);
                    }


                };
            };
        }

        function ajax() {

            $("#contenedor").html('<div class="loaderPDF"><div class="lds-dual-ring"></div></div>');
            var values = new FormData();
            values.append("id_embalse", $("#embalses option:selected").val());
            values.append("tipo", $("#tipo option:selected").val());
            values.append("anio", $("#anio option:selected").val());
            values.append("periodo", $("#periodo option:selected").val());
            values.append("ver", $("#ver option:selected").val());
            //values.append("id_unidad", "");
            values.append("t", $("#tipo option:selected").val());
            values.append("mes", $("#mes").val());
            values.append("fecha1", $("#fecha1").val());
            values.append("fecha2", $("#fecha2").val());
            //console.log($("#fecha1").val());
            values.append("semana", $("#semana option:selected").val());

            $.ajax({
                url: 'php/Graficas/' + $("#fe option:selected").val(),
                type: 'POST',
                data: values,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    //$("#contenedor").html("");
                    $("#contenedor").html(response);

                },
                error: function(response) {

                    alertify.error("Error inesperado.");

                }
            });
        };
        setInterval(ajax, 1800000);
    });
</script>