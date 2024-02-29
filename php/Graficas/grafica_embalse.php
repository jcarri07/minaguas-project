<script src="./assets/js/Chart.js"></script>
<!--script src="../../assets/js/date-fns.js"></script-->
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<?php
include "./php/Conexion.php";
$re = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo';");
$count = mysqli_num_rows($re);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 card">
            <div class="row card-body">
                <div id="prin" class="col-12">
                    <div class="row">
                        <div class="col-6" style="padding: 0px auto;margin-bottom:5px;">
                            <label class="form-label">Embalse</label>
                            <select name=0 id="embalses" class="form-control" required>
                                <!--option value="0">Todas los Embalses</option-->
                                <?php
                                while ($row = mysqli_fetch_array($re)) {
                                ?>
                                    <option value="<?php echo $row['id_embalse']; ?>"><?php echo $row['nombre_embalse']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-6" style="padding: 0px auto;margin-bottom:5px;">
                            <label id="lab" class="form-label">Tipo de Grafica</label>
                            <select id="tipo" class="form-control" required>
                                <option value="bar">Barra</option>
                                <option value="line">Linea</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="contenedor" class="col-12" style="height:600px;"></div>
                <div class="col-12">
                    <div id="" class="row">
                        <div class="col-md-4">
                            <select id="fe" class="form-control " style="padding: 0px auto; margin-bottom:5px;">
                                <option value="Grafica_año.php">Año</option>
                                <option value="Grafica_mes.php">Mes</option>
                                <option Value="Grafica_perso.php">Personalizado</option>
                            </select>
                        </div>
                        <div id="formato" class="row col-md-8" style="padding: 0px; margin-left: 0px;">
                            <div class="col-md-6">
                                <select id="anio" class="form-control " style="padding: 0px auto; margin-bottom:5px;">
                                    <?php
                                    for ($i = 1980; $i <= date('Y'); $i++) {
                                        echo '<option value="' . $i . '"';
                                        if ($i == date('Y')) {
                                            echo 'selected';
                                        }
                                        echo '>' . $i . '</option>';
                                    }
                                    ?>

                                </select>
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



        ajax();

        $("#embalses").change(function() {

            ajax();


        });
        $("#tipo").change(function() {

            ajax();


        });
        $("#anio").change(function() {

            ajax();


        });



        $("#fe").change(function() {

            switch ($("#fe option:selected").val()) {


                case "Grafica_año.php":
                    var fechaActual = new Date();
                    $("#formato").html('<div class="col-md-6"><select id="anio" class="form-control " style="padding: 0px auto; margin-bottom:5px;"><?php
                                                                                                                                                    for ($i = 1980; $i <= date('Y'); $i++) {
                                                                                                                                                        echo '<option value="' . $i . '"';
                                                                                                                                                        if ($i == date('Y')) {
                                                                                                                                                            echo 'selected';
                                                                                                                                                        }
                                                                                                                                                        echo '>' . $i . '</option>';
                                                                                                                                                    } ?></select></div></div>');
                    $("#anio").change(function() {

                        ajax();


                    });

                    ajax();
                    break;

                case "Grafica_mes.php":
                    var fechaActual = new Date();
                    //console.log(fechaActual);
                    $("#formato").html('<div class="col-md-6"><input type="Month" id="mes" max="<?php echo date('Y-m') ?>" class="form-control" style="padding: 0px auto; margin-bottom:5px;"></div>');

                    // Obtener el año y mes actual como cadenas de texto
                    var anioActual = fechaActual.getFullYear().toString();
                    var mesActual = (fechaActual.getMonth() + 1).toString().padStart(2, '0');

                    // Establecer el valor del input de tipo month
                    var valorMesActual = anioActual + '-' + mesActual;
                    $('#mes').val(valorMesActual);
                    ajax();
                    $("#mes").on('change', function() {
                        ajax();


                    });




                    break;

                case "Grafica_perso.php":
                    var fechaActual = new Date();

                    $("#formato").html('<div class="col-md-6"><input type="date" id="fecha1" value="<?php echo date('Y-m-d',strtotime('-1 months',strtotime(date('Y-m-d'))));?>" class="form-control" style="padding: 0px auto; margin-bottom:5px;"></div><div class="col-md-6"><input type="date" id="fecha2" value="<?php echo date('Y-m-d') ?>" max="<?php echo date('Y-m-d') ?>" class="form-control" style="padding: 0px auto; margin-bottom:5px;"></div>');


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

        function ajax() {

            var values = new FormData();
            values.append("id_embalse", $("#embalses option:selected").val());
            values.append("tipo", $("#tipo option:selected").val());
            values.append("anio", $("#anio option:selected").val());
            //values.append("id_unidad", "");
            values.append("t", $("#tipo option:selected").val());
            values.append("mes", $("#mes").val());
            values.append("fecha1", $("#fecha1").val());
            values.append("fecha2", $("#fecha2").val());
            console.log($("#fecha1").val());
            values.append("semana", $("#semana option:selected").val());

            $.ajax({
                url: 'php/graficas/' + $("#fe option:selected").val(),
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
        setInterval(ajax, 60000);
    });
</script>