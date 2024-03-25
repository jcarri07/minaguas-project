<?php

class Batimetria
{
    private $id_embalse;
    private $conn;
    private $batimetria;
    private $años;

    public function __construct($id_embalse, $conn)
    {
        $this->id_embalse = $id_embalse;
        $this->conn = $conn;

        $this->loadBatimetria();
    }

    private function loadBatimetria()
    {

        $id_embalse = mysqli_real_escape_string($this->conn, $this->id_embalse);
        $query = "SELECT batimetria FROM embalses WHERE id_embalse = $id_embalse";
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Error en la consulta: " . mysqli_error($this->conn));
        }

        $batimetria = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        $this->batimetria = json_decode($batimetria['batimetria'], true);

        $array = array();
        foreach ($this->batimetria as $key => $value) {
            array_push($array, $key);
        }
        $this->años = $array;
        $this->años = array_map('intval', $this->años);
        rsort($this->años);
    }

    public function getBatimetria()
    {
        // $batimetria = array();
        $batimetria = $this->batimetria;
        return $batimetria;
    }

    public function getByCota($año, $cota)
    {
        $año = $this->getCloseYear($año);
        $cota = number_format(floatval($cota), 3, ".", "");
        // return (array_key_exists((string)$cota, $this->batimetria[$año])) ? explode("-", $this->batimetria[$año][(string)$cota]) : $this->getCloseCota($this->batimetria[$año], $cota, 0.001);
        return $this->interpolacionLineal($cota, $this->batimetria[$año]);
    }

    public function getCloseCota($batimetria, $cota, $step)
    {
        $cota = $cota + $step;
        $cota = number_format(floatval($cota), 3, ".", "");
        $step = ($step > 0) ? (($step + 0.001) * -1) : (($step * -1) + 0.001);

        if (array_key_exists((string)$cota, $batimetria)) {
            return explode("-", $batimetria[(string)$cota]);
        } else {
            return $this->getCloseCota($batimetria, $cota, $step);
        }
    }

    public function getCloseYear($año_recibido)
    {
        $año_recibido = intval($año_recibido);
        // Iterar sobre el vector de años
        foreach ($this->años as $año) {
            // Si el año actual es menor o igual al año recibido, lo retornamos
            if ($año <= $año_recibido) {
                return $año;
            }
        }

        // Si el año recibido es menor que todos los años en el vector, retornar el menor año
        return $this->años[count($this->años) - 1];
    }

    public function getYears()
    {
        return $this->años;
    }


    public function interpolacionLineallll($x, $tabla) {
        $n = count($tabla);
        // Ordenar la tabla por el valor de x
        usort($tabla, function($a, $b) {
            return $a['x'] <=> $b['x'];
        });
    
        // Comprobar si el valor de x está fuera del rango de la tabla
        if ($x < $tabla[0]['x']) {
            return $tabla[0]['y']; // Devolver el valor más bajo
        } elseif ($x > $tabla[$n - 1]['x']) {
            return $tabla[$n - 1]['y']; // Devolver el valor más alto
        }
    
        // Buscar los puntos más cercanos en la tabla
        $puntoAnterior = null;
        $puntoSiguiente = null;
        foreach ($tabla as $punto) {
            if ($punto['x'] <= $x) {
                $puntoAnterior = $punto;
            } else {
                $puntoSiguiente = $punto;
                break;
            }
        }
    
        // Realizar la interpolación lineal
        $x0 = $puntoAnterior['x'];
        $y0 = $puntoAnterior['y'];
        $x1 = $puntoSiguiente['x'];
        $y1 = $puntoSiguiente['y'];
    
        $y = $y0 + (($y1 - $y0) / ($x1 - $x0)) * ($x - $x0);
    
        return $y;
    }

    public function interpolacionLineal($x, $tabla) {
        // Convertir las claves del array a un array numérico y ordenarlas
        $x_values = array_keys($tabla);
        sort($x_values);
    
        // Comprobar si el valor de x está fuera del rango de la tabla
        if ($x < min($x_values)) {
            return reset($tabla); // Devolver el valor más bajo
        } elseif ($x > max($x_values)) {
            return end($tabla); // Devolver el valor más alto
        }
    
        // Buscar los puntos más cercanos en la tabla
        $puntoAnterior = null;
        $puntoSiguiente = null;
        foreach ($x_values as $x_value) {
            if ($x_value <= $x) {
                $puntoAnterior = $x_value;
            } else {
                $puntoSiguiente = $x_value;
                break;
            }
        }
    
        // Realizar la interpolación lineal
        $Sup_min = explode("-",$tabla[$puntoAnterior])[0];
        $Sup_max = explode("-",$tabla[$puntoSiguiente])[0];
        $Sup = $Sup_min + (($Sup_max - $Sup_min) / ($puntoSiguiente - $puntoAnterior)) * ($x - $puntoAnterior);

        $Vol_min = explode("-",$tabla[$puntoAnterior])[1];
        $Vol_max = explode("-",$tabla[$puntoSiguiente])[1];
        $Vol = $Vol_min + (($Vol_max - $Vol_min) / ($puntoSiguiente - $puntoAnterior)) * ($x - $puntoAnterior);
    
        return array($Sup,$Vol);
    }


    // public function AuxGetCloseCota($año, $cota)
    // {
    // $intervalo = 0.010;
    // $top = ceil($cota / $intervalo) * $intervalo;
    // $bottom = floor($cota / $intervalo) * $intervalo;
    // reset($this->batimetria[$año]);
    // $bottom = key($this->batimetria[$año]);
    // end($this->batimetria[$año]);
    // $top = key($this->batimetria[$año]);
    //     $step = 0.001;
    //     $cota_number = number_format(floatval($cota),3,".","");

    //     return $this->stepByStepCloseCota($this->batimetria[$año], $cota_number, $step);
    // }


}
