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
<script src="../../../assets/js/excel.js/exceljs.min.js"></script>


<div class="loaderPDF" id="box-loader">
    <div class="text-center">
        <h3 id="wait-text"></h3>
        <div> 
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
    var anio = "<?php echo $_GET['anio'];?>";

    setInterval(() => {
        if(i < array_embalses.length) {
            document.getElementById('wait-text').textContent = 'Generando ' + array_embalses[i].nombre_embalse + '...';
            i++;
        }
        else {
            document.getElementById('wait-text').textContent = 'Generando Archivo...';
        }
    }, 196); // 0.196s (196 ms)

    function columnLetterToNumber(letter) {
        let number = 0;
        for (let i = 0; i < letter.length; i++) {
            number = number * 26 + (letter.charCodeAt(i) - 64); // 'A' tiene valor ASCII 65
        }
        return number - 1; // Retorna índice base 0
    }

    function columnNumberToLetter(number) {
        let letter = '';
        while (number >= 0) {
            letter = String.fromCharCode((number % 26) + 65) + letter;
            number = Math.floor(number / 26) - 1;
        }
        return letter;
    }
    function buscarPosicion(array, valorABuscar, columna) {
        const posicion = array.findIndex(item => item[columna] === valorABuscar);
        return posicion !== -1 ? posicion : -1;
    }
    function isNumeric(value) {
        return !isNaN(value) && isFinite(value);
    }
    function convertirValor(valor) {
        valor = valor.replace(',', '.');  // Reemplaza la coma por punto

        if (!isNaN(valor)) {
            return parseFloat(valor);  // Convierte el valor a número flotante
        }
        
        return null;  // Si no es un número válido, retorna null
    }


    fetch("exportar-extracciones-excel.php?anio=" + anio, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al obtener los datos');
        }
        return response.json();
    })
    .then(async data => {
        //console.log(data.array_total);
        const tipo_extracciones = data.array_total.tipos_extraccion;
        const array_codigos = data.array_total.array_codigos;
        //const wb = XLSX.utils.book_new();

        const wb = new ExcelJS.Workbook();

        //data.array_total.array_embalses_all.forEach((sheetData, index) => {
        Object.values(data.array_total.array_embalses_all).forEach((item, index) => {
            //console.log(item.nombre_embalse.toUpperCase());
            // Suponemos que cada `sheetData` es un array de arrays (AOA) que representa las filas de la hoja
            //const ws = XLSX.utils.aoa_to_sheet('');

            // Nombre dinámico de la hoja (puedes personalizarlo según sea necesario)
            const sheetName = item.nombre_embalse.toUpperCase();
            const ws = wb.addWorksheet(sheetName);

            // Congelar la primera fila y la primera columna
            ws.views = [
                {
                    state: 'frozen',
                    xSplit: 3,
                    ySplit: 8
                }
            ];

            const columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V'];
            columns.forEach(col => {
                ws.getColumn(col).width = 12.56;
            });
            ws.getColumn('A').width = 11.11;
            ws.getColumn('B').width = 13.89;
            ws.getColumn('C').width = 11.11;
            ws.getColumn('D').width = 12.89;
            ws.getColumn('T').width = 48.89;
            ws.getColumn('U').width = 16.89;
            ws.getColumn('V').width = 32.22;

            ws.mergeCells('A1:B1');
            ws.mergeCells('A2:B2');
            ws.mergeCells('A3:B3');
            ws.mergeCells('A4:B4');
            ws.mergeCells('A7:B7');
            ws.mergeCells('C7:C8');
            ws.mergeCells('D7:D8');
            
            // Ajustar la altura de la fila 8
            ws.getRow(8).height = 53.40;


            var color = '5B9BD5';
            ws.getCell('A7').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: color } };
            ws.getCell('A8').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: color } };
            ws.getCell('B8').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: color } };
            ws.getCell('C7').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: color } };

            var color = 'DDEBF7';
            ws.getCell('D7').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: color } };


            // Establecer valores de las celdas
            ws.getCell('A1').value = '% De información faltante hasta la fecha:';
            ws.getCell('A2').value = 'Información Faltante (días):';
            ws.getCell('A3').value = 'Días Transcurridos:';
            ws.getCell('A4').value = 'Información Faltante del Año:';
            ws.getCell('A5').value = 'Embalse:'; // Aquí va el nombre del embalse
            ws.getCell('B5').value = sheetName;
            ws.getCell('A7').value = 'FECHA';
            ws.getCell('A8').value = 'DIA';
            ws.getCell('B8').value = 'FECHA';
            ws.getCell('C7').value = 'Cota Actual (msnm)';
            ws.getCell('D7').value = 'Dias de Reserva de Agua';

            // Aplicar estilos
            const styleCell = (cell) => {
                cell.font = { bold: true, color: { argb: 'FFFFFF' } };
                cell.alignment = { wrapText: true, horizontal: 'center', vertical: 'middle' };
            };

            // Aplicar estilos a las celdas
            styleCell(ws.getCell('A7'));
            styleCell(ws.getCell('A8'));
            styleCell(ws.getCell('B8'));
            styleCell(ws.getCell('C7'));
            styleCell(ws.getCell('D7'));
            ws.getCell('D7').font = { color: { argb: '000000' }, bold: true };

           








            //Cabecera
            let index_columna_inicio = columnLetterToNumber('E'); // Columna 'E'
            let columna_inicio = 'E';
            let index_columna_final = 0;
            let columna_final = '';
            let tipo_extraccion_actual = '';

            array_codigos.forEach(codigo => {
                codigo.sumatoria = 0;
                codigo.cantidad = 0;
            });

            tipo_extracciones.forEach(tipo_extraccion => {
                index_columna_final = Number(index_columna_inicio) + (Number(tipo_extraccion.cant) - 1);
                columna_final = columnNumberToLetter(index_columna_final);

                // Fusionar celdas
                //console.log(index_columna_final);
                //console.log("\n");
                //return false;
                //console.log(`${columna_inicio}7:${columna_final}7`);
                ws.mergeCells(`${columna_inicio}7:${columna_final}7`);

                // Determinar el color basado en el tipo de extracción
                let color = "FFFFFF";
                if (tipo_extraccion.id_tipo_extraccion === '1') color = "2F75B5";
                if (tipo_extraccion.id_tipo_extraccion === '2') color = "A9D08E";
                if (tipo_extraccion.id_tipo_extraccion === '3') color = "92D050";
                if (tipo_extraccion.id_tipo_extraccion === '4') color = "ED7D31";

                // Aplicar color de fondo
                ws.getCell(`${columna_inicio}7`).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: color } };

                // Asignar el texto a la celda
                let string = tipo_extraccion.nombre;
                if (tipo_extraccion.cantidad_primaria && tipo_extraccion.cantidad_primaria !== "0")
                    string += ` (${tipo_extraccion.cantidad_primaria} ${tipo_extraccion.unidad})`;

                ws.getCell(`${columna_inicio}7`).value = string;

                // Alineación
                ws.getCell(`${columna_inicio}7`).alignment = { horizontal: 'center', vertical: 'middle' };

                // Añadiendo códigos de las extracciones
                let index_column_aux = index_columna_inicio;
                array_codigos.forEach(codigo => {
                    if (codigo.id_tipo_extraccion === tipo_extraccion.id_tipo_extraccion) {
                        //const column_aux = ExcelJS.utils.columnToLetter(index_column_aux);
                        const column_aux = columnNumberToLetter(index_column_aux);
                        codigo.columna = column_aux;

                        // Ajustes de estilo y texto para las celdas de código
                        ws.getCell(`${column_aux}8`).value = `${codigo.name} (${codigo.codigo})`;
                        ws.getCell(`${column_aux}8`).alignment = { horizontal: 'center', vertical: 'middle' };

                        if (codigo.id_tipo_extraccion !== "5")
                            ws.getCell(`${column_aux}8`).font = { size: 9 };

                        index_column_aux += 1;
                    }
                });

                // Actualizar las columnas para la siguiente iteración
                index_columna_inicio = index_columna_final + 1;
                columna_inicio = columnNumberToLetter(index_columna_inicio);
            });

            // Columna "Reportado por"
            index_columna_final = index_columna_inicio + 1;
            columna_final = columnNumberToLetter(index_columna_final);
            const COLUMNA_FINAL_REPORTE = columna_final;

            ws.mergeCells(`${columna_inicio}7:${columna_final}7`);
            ws.getCell(`${columna_inicio}7`).value = "Reportado por";
            ws.getCell(`${columna_inicio}7`).alignment = { horizontal: 'center', vertical: 'middle' };

            ws.getCell(`${columna_inicio}8`).value = "Nombre";
            ws.getCell(`${columna_inicio}8`).alignment = { horizontal: 'center', vertical: 'middle' };

            ws.getCell(`${columna_final}8`).value = "Fecha";
            ws.getCell(`${columna_final}8`).alignment = { horizontal: 'center', vertical: 'middle' };


            //Bordes
            celdaInicio = 'A7';
            celdaFin = columna_final + '8';

            let startRow = 7;
            let endRow = 8;
            let startCol = 1;  // A es la columna 1
            let endCol = index_columna_final + 1;    // D es la columna 4

            // Iterar sobre el rango de celdas y aplicar bordes
            for (let row = startRow; row <= endRow; row++) {
                for (let col = startCol; col <= endCol; col++) {
                    let cell = ws.getCell(row, col);
                    cell.border = {
                        top: { style: 'thin', color: { argb: '000000' } },
                        left: { style: 'thin', color: { argb: '000000' } },
                        bottom: { style: 'thin', color: { argb: '000000' } },
                        right: { style: 'thin', color: { argb: '000000' } }
                    };
                }
            }





            //Datos

            const diasSemanaEspañol = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
            const array_extracciones = item.extracciones;  // Suponiendo que data.extracciones es el array de extracciones
            //const array_codigos = data.codigos; // Suponiendo que data.codigos es el array de codigos
            let dia_actual = anio + `-01-01`;
            let fila_actual = 9;
            let cant_registros = 0;

            const ultimoDiaDelAnio = anio + `-12-31`;
            const numberOfDays = (new Date(ultimoDiaDelAnio) - new Date(anio + `-01-01`)) / (1000 * 60 * 60 * 24) + 1;

            // Bucle que recorre todos los días del año
            for (let i = 0; i <= numberOfDays; i++) {
                // Dejar en blanco las celdas de la fila
                if (i > 0) {
                    ws.getCell(`C${fila_actual}`).value = "";  // Dejando en blanco la cota anterior
                    array_codigos.forEach(codigo => {
                        ws.getCell(`${codigo.columna}${fila_actual}`).value = "";
                    });
                }

                // Colocar el día de la semana
                const dayOfWeek = new Date(dia_actual).getDay();  // Obtener el día de la semana
                ws.getCell(`A${fila_actual}`).value = diasSemanaEspañol[dayOfWeek];
                ws.getCell(`A${fila_actual}`).alignment = { horizontal: 'right' };

                let fecha = new Date(dia_actual);
                let dia = ('0' + fecha.getDate()).slice(-2); // Asegura que el día tenga dos dígitos
                let mes = ('0' + (fecha.getMonth() + 1)).slice(-2); // Asegura que el mes tenga dos dígitos
                let anio = fecha.getFullYear(); // Obtiene el año con cuatro dígitos

                ws.getCell(`B${fila_actual}`).value = `${dia}/${mes}/${anio}`;

                // Colocar la fecha en formato dd/mm/yyyy
                //ws.getCell(`B${fila_actual}`).value = new Date(dia_actual).toLocaleDateString('es-ES').split('/').reverse().join('-');
                ws.getCell(`B${fila_actual}`).alignment = { horizontal: 'right' };

                // Buscar extracciones para la fecha actual
                const index_row = buscarPosicion(array_extracciones, dia_actual, 'fecha');
                if (index_row !== -1) {
                    const extraccion = array_extracciones[index_row];
                    cant_registros++;
                    ws.getCell(`C${fila_actual}`).value = parseFloat(extraccion.cota_actual.replace(',', '.'));
                    ws.getCell(`C${fila_actual}`).alignment = { horizontal: 'center' };
                    ws.getCell(`C${fila_actual}`).numFmt = '0.00';

                    if (extraccion.extraccion !== null) {
                        const extraccion_aux = extraccion.extraccion.split(";");
                        extraccion_aux.forEach((fila) => {
                            if (fila !== "") {
                                const datos_aux = fila.split("&");
                                const index_extraccion = buscarPosicion(array_codigos, datos_aux[0], 'id_codigo_extraccion');
                                const columna_extraccion = array_codigos[index_extraccion].columna;

                                // Comprobación para sumabilidad
                                let tiene_formato_para_sumarse = false;
                                //let valor_extraccion = datos_aux[1];

                                if (array_codigos[index_extraccion].sumable && 
                                    datos_aux[1] != "" && 
                                    datos_aux[1] != 0 && 
                                    datos_aux[1] != "0"
                                ) {
                                    if (isNumeric(datos_aux[1])) {
                                        tiene_formato_para_sumarse = true;
                                    } else {
                                        datos_aux[1] = convertirValor(datos_aux[1]);
                                        if (datos_aux[1] !== null) {
                                            tiene_formato_para_sumarse = true;
                                        }
                                    }

                                    if (tiene_formato_para_sumarse) {
                                        array_codigos[index_extraccion].sumatoria += Number(datos_aux[1]);
                                        array_codigos[index_extraccion].cantidad++;
                                    }
                                }

                                let valor_extraccion = datos_aux[1];
                                if (array_codigos[index_extraccion]['id_codigo_extraccion'] === "30") {
                                    if (!isNumeric(valor_extraccion)) {  // Si el valor es numérico
                                        valor_extraccion = valor_extraccion + "%"; 
                                        if (valor_extraccion < 1) {  // Si el valor es menor que 1, multiplicar por 100
                                            valor_extraccion = (valor_extraccion * 100) + "%";
                                        }
                                    }
                                }

                                //console.log(valor_extraccion); //Se imprime porque llego a dar error la funcion replace y se puso la funcion toString para que funcionara
                                valor_extraccion = isNumeric(valor_extraccion) ? parseFloat(valor_extraccion.toString().replace(',', '.')) : valor_extraccion;

                                // Aplicar valor de la extracción en la celda correspondiente
                                ws.getCell(`${columna_extraccion}${fila_actual}`).value = valor_extraccion;
                                ws.getCell(`${columna_extraccion}${fila_actual}`).alignment = { horizontal: 'center' };
                                ws.getCell(`${columna_extraccion}${fila_actual}`).numFmt = '0.00'; // #,##0.00 para aplicar separador de miles
                            }
                        });
                    }

                    // Colocar el encargado
                    var colAux = Number(columnLetterToNumber(COLUMNA_FINAL_REPORTE)) - 1;
                    var colReportadoPor = columnNumberToLetter(colAux);
                    ws.getCell(`${colReportadoPor}${fila_actual}`).value = extraccion.encargado;
                    ws.getCell(`${colReportadoPor}${fila_actual}`).alignment = { horizontal: 'center', vertical: 'middle' };
                }

                // Aplicar bordes al rango de celdas
                /*const celdaInicio = `A${fila_actual}`;
                const celdaFin = `Z${fila_actual}`;  // Suponiendo que la columna final es Z
                ws.getRange(`${celdaInicio}:${celdaFin}`).style = {
                borders: {
                    top: { style: 'thin', color: { argb: '000000' } },
                    left: { style: 'thin', color: { argb: '000000' } },
                    bottom: { style: 'thin', color: { argb: '000000' } },
                    right: { style: 'thin', color: { argb: '000000' } }
                }
                };*/

                // Avanzar al siguiente día
                dia_actual = new Date(new Date(dia_actual).setDate(new Date(dia_actual).getDate() + 1)).toISOString().split('T')[0];
                fila_actual++;
            }




            var diaDelAnioAux = ("<?php echo date("Y");?>" == anio) ? (Number("<?php echo date('z');?>") + 1) : numberOfDays;
            var porcentajeFaltanteHastaLafecha = (100 - (cant_registros * 100 / diaDelAnioAux)).toFixed(2);
            ws.getCell('C1').value = `${porcentajeFaltanteHastaLafecha}%`;
            ws.getCell('C2').value = numberOfDays - cant_registros;
            ws.getCell('C3').value = diaDelAnioAux;

            var porcentajeFaltanteEnElAnio = `${(100 - (cant_registros * 100 / numberOfDays)).toFixed(2)}%`;
            ws.getCell('C4').value = porcentajeFaltanteEnElAnio;

            ws.getCell(`C1`).alignment = { horizontal: 'center', vertical: 'middle' };
            ws.getCell(`C2`).alignment = { horizontal: 'center', vertical: 'middle' };
            ws.getCell(`C3`).alignment = { horizontal: 'center', vertical: 'middle' };
            ws.getCell(`C4`).alignment = { horizontal: 'center', vertical: 'middle' };





            //console.log(array_codigos);
            //Añadiendo los promedios a los codigos
            array_codigos.forEach(codigo => {
                if (codigo.sumable) {
                    // Calcular el divisor y el promedio
                    const divisor = codigo.cantidad > 0 ? codigo.cantidad : 1;
                    const promedio = (codigo.sumatoria / divisor).toFixed(2).replace('.', ',');

                    // Crear el contenido de la celda con salto de línea
                    const contenidoCelda = `${codigo.name} (${codigo.codigo})\n${promedio}`;

                    // Asignar el valor a la celda
                    ws.getCell(`${codigo.columna}8`).value = contenidoCelda;

                    // Habilitar el salto de línea en la celda
                    //ws.getCell(`${codigo.columna}8`).alignment = { wrapText: true };
                } else {
                    // Asignar solo el nombre y el código si no es sumable
                    ws.getCell(`${codigo.columna}8`).value = `${codigo.name} (${codigo.codigo})`;
                }
                ws.getCell(`${codigo.columna}8`).alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
            });

        

        // Descargar el archivo
        // XLSX.writeFile(wb, "EXTRACCIONES_" + <?php echo $_GET['anio'];?> + ".xlsx");

        });

        const buffer = await wb.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = "EXTRACCIONES_" + 2024 + ".xlsx";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        document.getElementById('box-loader').style.display = 'none';
        document.getElementById('box-success').style.display = 'block';

    })
    .catch(error => {
        console.error('Error:', error);
    });
</script>
