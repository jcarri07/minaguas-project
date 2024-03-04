$(document).on('change', "#estado, #municipio, #parroquia", function () {
    // console.log("Faro")
    console.log("id: " + this.id);
    if (this.id == "estado" || this.id == "municipio") {
        var destino = "";
        var id = "";
        if (this.id == "estado") {
            destino = "municipio";
            id = this.value;

            console.log($(this)[0].selectedOptions)
            opciones = $(this)[0].selectedOptions;
            var valores = [];
            var textos = [];
            for (let i = 0; i < opciones.length; i++) {
                valores.push(opciones[i].value);
                textos.push(opciones[i].innerText);
            }
            var id = valores.join(',');
            $('.label-estados').text(textos.join(', ')).fadeIn();
            // console.log(valores, textos);

            $('#municipio').empty();
            // $("#municipio").append("<option value=''>Cargando...</option>");
            $('#municipio').empty();
            $('.label-municipios').empty();
            // $("#municipio").append("<option value=''>Seleccione</option>");
            $('#parroquia').empty();
            $('.label-parroquias').empty();
            // $("#parroquia").append("<option value=''>Seleccione</option>");
            // $("#parroquia").append("<option value=''>Seleccionesss</option>");
        }
        if (this.id == "municipio") {
            destino = "parroquia";
            id = this.value;

            opciones = $(this)[0].selectedOptions;
            var valores = [];
            var textos = [];
            for (let i = 0; i < opciones.length; i++) {
                valores.push(opciones[i].value);
                textos.push(opciones[i].innerText);
            }
            var id = valores.join(',');
            $('.label-municipios').text(textos.join(', ')).fadeIn();
            // $('#municipio').empty();
            // $("#municipio").append("<option value=''>Cargando...</option>");
            $('#parroquia').empty();
            $('.label-parroquias').empty();
            // $("#parroquia").append("<option value=''>Seleccione</option>");
        }

        $.ajax({
            url: 'php/get-ubication-select.php',
            type: 'POST',
            data: {
                cat: this.id,
                id: id
            },
            success: function (response) {
                // console.log(response);
                $('select#' + destino).html(response).fadeIn();
                if (destino == "municipio") { MunicipioSelect.update(); ParroquiaSelect.update(); };
                if (destino == "parroquia") { ParroquiaSelect.update() };
            },
            error: function (response) {
                console.log(response.error);
            }
        });


    }

    if (this.id == "parroquia") {
        console.log("hola")
        opciones = $(this)[0].selectedOptions;
        var valores = [];
        var textos = [];
        for (let i = 0; i < opciones.length; i++) {
            valores.push(opciones[i].value);
            textos.push(opciones[i].innerText);
        }
        var id = valores.join(',');
        $('.label-parroquias').text(textos.join(', ')).fadeIn();
    }
});
