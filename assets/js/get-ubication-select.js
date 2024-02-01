$(document).on('change', "#estado, #municipio, #parroquia", function() {
    // console.log("Faro")
    console.log("id: "+this.id);
    if (this.id == "estado" || this.id == "municipio") {
        var destino = "";
        var id = "";
        if (this.id == "estado") {
            destino = "municipio";
            id = this.value;
            $('#municipio').empty();
            $("#municipio").append("<option value=''>Cargando...</option>");
            $('#municipio').empty();
            $("#municipio").append("<option value=''>Seleccione</option>");
            $('#parroquia').empty();
            $("#parroquia").append("<option value=''>Seleccione</option>");
            // $("#parroquia").append("<option value=''>Seleccionesss</option>");
        }
        if (this.id == "municipio") {
            destino = "parroquia";
            id = this.value;

            // $('#municipio').empty();
            // $("#municipio").append("<option value=''>Cargando...</option>");
            $('#parroquia').empty();
            $("#parroquia").append("<option value=''>Seleccione</option>");
        }

        $.ajax({
            url: 'php/get-ubication-select.php',
            type: 'POST',
            data: {
                cat: this.id,
                id: id
            },
            success: function(response) {
                console.log(response);
                $('select#' + destino).html(response).fadeIn();
            },
            error: function(response) {
                console.log(response.error);
            }
        });
    }
});
