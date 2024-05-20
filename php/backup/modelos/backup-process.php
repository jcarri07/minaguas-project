<?php
    require_once '../../Conexion.php';

    $nombre_archivo = 'respaldo_' . date('Y-m-d_H-i-s') . '.sql';
    $ruta_respaldo = '../' . $nombre_archivo;

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $consulta_tablas = "SHOW TABLES";
    $resultado_tablas = $conn->query($consulta_tablas);

    $sql_creates = "";
    $sql_foreign_keys = "";

    if ($resultado_tablas->num_rows > 0) {

        $sql = "";

        while ($fila_tabla = $resultado_tablas->fetch_row()) {

            $nombre_tabla = $fila_tabla[0];

            $consulta_estructura  = "SHOW CREATE TABLE $nombre_tabla";
            $resultado_estructura  = $conn->query($consulta_estructura );

            if ($resultado_estructura ->num_rows > 0) {
                // Obtener la consulta CREATE TABLE
                $fila_estructura  = $resultado_estructura ->fetch_assoc();
                $sql_creates = $fila_estructura ['Create Table'] . ";\n\n";
    
                // Extraer claves foráneas
                preg_match_all('/,\s*CONSTRAINT\s+\`?\w+\`?\s+FOREIGN KEY\s+\(\`?\w+\`?\)\s+REFERENCES\s+\`?\w+\`?\s+\(\`?\w+\`?\)/', $sql_creates, $matches);

                foreach ($matches[0] as $match) {
                    $sql_creates = str_replace($match, "", $sql_creates);
                    //$sql_creates = preg_replace('/,\s*(?=(?:CONSTRAINT|$))/', '', $sql_creates);

                    $match = preg_replace('/,\s*CONSTRAINT/', 'CONSTRAINT', $match);
                    $sql_foreign_keys .= "ALTER TABLE $nombre_tabla ADD $match;\n";
                    $match = str_replace(",", "", $match);
                }
            }

            $sql .= $sql_creates;


            $consulta_datos = "SELECT * FROM $nombre_tabla";
            $resultado_datos = $conn->query($consulta_datos);
            
            
            //Esta rutina guarda los datos en un insert into por fila
            /*if ($resultado_datos->num_rows > 0) {
                $i = 1;
                while ($fila_datos = $resultado_datos->fetch_assoc()) {

                    //if($i == 1) {
                        $sql .= "INSERT INTO $nombre_tabla (";
                        $columnas = implode(', ', array_keys($fila_datos));
                        $sql .= "$columnas)\nVALUES ";
                    //}

                    $valores = implode(', ', array_map(function($value) use ($conn) {
                        //return is_null($value) ? 'NULL' : "'" . $conn->real_escape_string($value) . "'";
                        if(is_numeric($value))
                            return $value;
                        else
                            if(is_null($value))
                                return 'NULL';
                            else
                                return "'" . $conn->real_escape_string($value) . "'";
                    }, array_values($fila_datos)));
                    //$sql .= "($valores)";
                    $sql .= "($valores);\n\n";

                    //if ($resultado_datos->num_rows == $i) 
                    //    $sql .= ";\n\n";
                    //else 
                    //    $sql .= ",\n";

                    $i++;

                }
                $sql .= "\n";
            }*/

            if ($resultado_datos->num_rows > 0) {
                $i = 1;
                $poner_coma = false;
                $consulta_actual = "";
                $membrete_insert = "";
                $max_length = 700000;
                while ($fila_datos = $resultado_datos->fetch_assoc()) {

                    if($i == 1){
                        $membrete_insert .= "INSERT INTO $nombre_tabla (";
                        $columnas = implode(', ', array_keys($fila_datos));
                        $membrete_insert .= "$columnas) VALUES \n";

                        $consulta_actual = $membrete_insert;
                        $poner_coma = false;
                    }

                    $valores = implode(', ', array_map(function($value) use ($conn, $nombre_tabla) {
                        //return is_null($value) ? 'NULL' : "'" . $conn->real_escape_string($value) . "'";
                        //if(is_numeric($value) && $nombre_tabla != "embalses")
                        
                        if(is_numeric($value))
                        if((str_contains($value,',')||str_contains($value,'.'))||(strlen($value)<8||strlen($value)>11)){
                            return $value;
                        }else
                            return "'" . $conn->real_escape_string($value) . "'";  
                        else
                            if(is_null($value))
                                return 'NULL';
                            else
                                return "'" . $conn->real_escape_string($value) . "'";
                            
                                
                    }, array_values($fila_datos)));

                    //$consulta_fila
                    $consulta_fila = "(" . $valores . ")";

                    if (strlen($consulta_actual) + strlen($consulta_fila) > $max_length) {
                        $sql .= $consulta_actual . ";\n\n";
                        $consulta_actual = $membrete_insert;
                        $poner_coma = false;
                    }
                    else{
                        if($poner_coma)
                            $consulta_actual .= ",\n";
                    }

                    
                    $consulta_actual .= $consulta_fila;
                    $poner_coma = true;
                    if ($resultado_datos->num_rows == $i) {
                        $sql .= $consulta_actual;
                        $sql .= ";\n\n";
                    }


                    $i++;

                }
                $sql .= "\n";
            }
        }

        $sql .= $sql_foreign_keys;

        file_put_contents($ruta_respaldo, $sql);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');

        readfile($ruta_respaldo);
        
        //echo "Respaldo generado exitosamente en la ruta: $ruta_respaldo";
    } else {
        //echo "No se encontraron tablas en la base de datos.";
    }

    closeConection($conn);
?>