<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.2.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.2.0/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@4.2.0/main.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.2.0/main.min.css" rel="stylesheet"/>


<?php
    $meses = array(
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    );
?>


<div class="modal fade" id="modal-morosos" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card card-plain">
                    <div class="card-header pb-0 text-left">
                        <h3 class="font-weight-bolder text-primary text-gradient text-title">Morosos en Reportar</h3>
                        <button type="button" class="btn bg-gradient-primary close-modal btn-rounded mb-0" data-bs-dismiss="modal">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div class="card-body pb-3">
                        <div class="row">
                            <div class="col">
                                <label>Año</label>
                                <div class="input-group mb-4">
                                    <select class="form-select" name="anio_morosos" id="anio_morosos">
        <?php
                                $anio = date("Y");
                                $limit = 2022;
                                $selected = 'selected';
                                while($anio >= $limit) {
        ?>
                                        <option value="<?php echo $anio;?>" <?php echo $selected;?>><?php echo $anio;?></option>
        <?php
                                    $selected = '';
                                    $anio--;
                                }
        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col">
                                <label>Mes</label>
                                <div class="input-group mb-4">
                                    <select class="form-select" name="mes_morosos" id="mes_morosos">
                                        <option value="">Todos</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="content">

                        </div>
                    
                    </div>
                    <div class="card-footer text-center pt-0 px-sm-4 px-1">
                        <!--<p class="mb-4 mx-auto">
                        Already have an account?
                        <a href="javascrpt:;" class="text-primary text-gradient font-weight-bold">Guardar</a>
                        </p>--->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    var meses = <?php echo json_encode($meses);?>;
    var meses = Object.values(meses);

    function ajustar_meses_morosos(mes_select, anio_change) { 
        var fecha_hoy = new Date("<?php echo date("Y");?>", "<?php echo date("m");?>", "<?php echo date("d");?>"); //hoy
        //var f2 = new Date($("#anio_morosos").val(), $("#mes_morosos").val(), 01); //Fecha selecionada

        $("#mes_morosos").html('<option value="">Todos</option>');
        //$("#dia_morosos").html('<option value="">Todos</option>');
        for(var i = 1 ; i <= meses.length ; i++) {
            mes = i;
            if(i < 10)
                mes = "0" + i;
                var fecha_seleccionada = new Date($("#anio_morosos").val(), mes, 0o1);
                if(fecha_seleccionada <= fecha_hoy) {
                    $("#mes_morosos").append('<option value="' + mes + '">' + meses[i-1] + '</option>');
                }
                else {
                    break;
                }
        }
        $("#mes_morosos").val(mes_select);
        if(anio_change)
            $("#mes_morosos").val("");
    }

    ajustar_meses_morosos("", true);

    function morosos(detalles, id_embalse){

        $("#modal-morosos .card-body .content").html("<h3 class='text-center'>Cargando...</h3>");
        $("#modal-morosos .card-header .text-title").text("Morosos en Reportar");
        $("#modal-morosos").modal("show");

        var datos = new FormData();
        //datos.append('id_embalse', id_embalse);
        datos.append('anio', $("#anio_morosos").val());
        datos.append('mes', $("#mes_morosos").val());
        datos.append('detalles_mes_morosos', detalles);
        datos.append('id_embalse', id_embalse);

        $.ajax({
            url: 			'php/datos/vistas/list-morosos.php',
            type:			'POST',
            data:			datos,
            cache:          false,
            contentType:    false,
            processData:    false,
            success: function(response){
                $("#modal-morosos .card-body .content").html(response);
                iniciarTabla('table-morosos');
            }
            ,
            error: function(response){
            }
        });
    }

    $("#anio_morosos, #mes_morosos").on("change", function() {
        /*var mes = "";
        if(this.id == "mes_morosos")
            mes = $("").value;*/

        var anio_change = false;
        if(this.id == "anio_morosos")
            anio_change = true;
        var mes = $("#mes_morosos").val();
        //var dia = $("#dia_morosos").val();
        //ajustar_meses_morosos(mes, anio_change, dia);
        ajustar_meses_morosos(mes, anio_change);
        morosos('no', '');
    });

    function detalles_morosos_mes(id_embalse) {
        morosos('si', id_embalse);
    }
</script>

