<?php

class Batimetria
{
    private $id_embalse;
    private $conn;
    private $batimetria;
    private $años;
    private $cota_min;
    private $cota_nor;
    private $cota_max;
    private $sup_min;
    private $sup_nor;
    private $sup_max;
    private $vol_min;
    private $vol_nor;
    private $vol_max;
    private $ultima_carga;
    private $embalse;


    public function __construct($id_embalse, $conn)
    {
        $this->id_embalse = $id_embalse;
        $this->conn = $conn;

        $this->loadBatimetria();
    }

    private function loadBatimetria()
    {

        $id_embalse = mysqli_real_escape_string($this->conn, $this->id_embalse);
        $query = "SELECT * FROM embalses WHERE id_embalse = $id_embalse";
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Error en la consulta: " . mysqli_error($this->conn));
        }

        $embalse = mysqli_fetch_assoc($result);
        $this->embalse = $embalse;
        mysqli_free_result($result);

        if ($embalse['batimetria'] != "") {
            $this->batimetria = json_decode($embalse['batimetria'], true);
        } else {
            $this->batimetria = "";
        }
        $this->cota_min = $embalse['cota_min'];
        $this->cota_nor = $embalse['cota_nor'];
        $this->cota_max = $embalse['cota_max'];
        $this->sup_min = $embalse['sup_min'];
        $this->sup_nor = $embalse['sup_nor'];
        $this->sup_max = $embalse['sup_max'];
        $this->vol_min = $embalse['vol_min'];
        $this->vol_nor = $embalse['vol_nor'];
        $this->vol_max = $embalse['vol_max'];

        $query = "SELECT fecha, hora, cota_actual FROM datos_embalse WHERE id_embalse = 1 ORDER BY fecha DESC, hora DESC LIMIT 1";
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Error en la consulta: " . mysqli_error($this->conn));
        }

        $datos = mysqli_fetch_assoc($result);
        if (mysqli_num_rows($result) < 1) {
            $this->ultima_carga = "";
        } else {
            $fecha = date($datos['fecha']);
            $this->ultima_carga = array(date("Y", strtotime($fecha)), $datos['cota_actual']);
        }
        mysqli_free_result($result);

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
        if ($cota == null) {
            return 0;
        }
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

    public function getCloseYear($año_recibido = null)
    {

        if ($año_recibido == null) {
            return reset($this->años);
        }

        $año_recibido = intval($año_recibido);

        foreach ($this->años as $año) {
            if ($año <= $año_recibido) {
                return $año;
            }
        }

        return $this->años[count($this->años) - 1];
    }

    public function getYears()
    {
        return $this->años;
    }


    public function interpolacionLineallll($x, $tabla)
    {
        $n = count($tabla);
        // Ordenar la tabla por el valor de x
        usort($tabla, function ($a, $b) {
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

    public function interpolacionLineal($x, $tabla)
    {
        $x_values = array_keys($tabla);
        sort($x_values);

        if ($x < min($x_values)) {
            return explode("-", reset($tabla));
        } elseif ($x > max($x_values)) {
            return explode("-", end($tabla));
        }

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
        $Sup_min = explode("-", $tabla[$puntoAnterior])[0];
        $Sup_max = explode("-", $tabla[$puntoSiguiente])[0];
        $Sup = $Sup_min + (($Sup_max - $Sup_min) / ($puntoSiguiente - $puntoAnterior)) * ($x - $puntoAnterior);

        $Vol_min = explode("-", $tabla[$puntoAnterior])[1];
        $Vol_max = explode("-", $tabla[$puntoSiguiente])[1];
        $Vol = $Vol_min + (($Vol_max - $Vol_min) / ($puntoSiguiente - $puntoAnterior)) * ($x - $puntoAnterior);

        return array($Sup, $Vol);
    }

    public function cotaMinima()
    {
        return number_format(floatval($this->cota_min), 3, ".", "");
    }
    public function cotaNormal()
    {
        return number_format(floatval($this->cota_nor), 3, ".", "");
    }
    public function cotaMaxima()
    {
        return number_format(floatval($this->cota_max), 3, ".", "");
    }

    public function superficieMinima()
    {
        if ($this->batimetria != "") {
            return $this->getByCota($this->getCloseYear(), $this->cotaMinima())[0];
        } else {
            return $this->sup_min;
        }
    }
    public function superficieNormal()
    {
        if ($this->batimetria != "") {
            return $this->getByCota($this->getCloseYear(), $this->cotaNormal())[0];
        } else {
            return $this->sup_nor;
        }
    }
    public function superficieMaxima()
    {
        if ($this->batimetria != "") {
            return $this->getByCota($this->getCloseYear(), $this->cotaMaxima())[0];
        } else {
            return $this->sup_max;
        }
    }

    public function volumenMinimo()
    {
        if ($this->batimetria != "") {
            return $this->getByCota($this->getCloseYear(), $this->cotaMinima())[1];
        } else {
            return $this->vol_min;
        }
    }
    public function volumenNormal()
    {
        if ($this->batimetria != "") {
            return $this->getByCota($this->getCloseYear(), $this->cotaNormal())[1];
        } else {
            return $this->vol_nor;
        }
    }
    public function volumenMaximo()
    {
        if ($this->batimetria != "") {
            return $this->getByCota($this->getCloseYear(), $this->cotaMaxima())[1];
        } else {
            return $this->vol_max;
        }
    }

    public function volumenDisponible()
    {
        if ($this->batimetria != "") {
            return $this->volumenNormal() - $this->volumenMinimo();
        } else {
            return $this->volumenDisponibleOriginal();
        }
    }

    public function volumenDisponibleOriginal()
    {

        if ($this->vol_nor == "" || $this->vol_min == "") {
            return 0;
        } else {
            return $this->vol_nor - $this->vol_min;
        }
    }

    public function volumenDisponibleByCota($año, $cota)
    {
        if ($cota == null) {
            return 0;
        }
        if ($this->batimetria != "") {
            return $this->getByCota($año, $cota)[1] - $this->volumenMinimo();
        } else {
            return 0;
        }
    }

    public function volumenActualDisponible()
    {
        if ($this->ultima_carga != "" && $this->batimetria != "") {
            return $this->getByCota($this->ultima_carga[0], $this->ultima_carga[1])[1] - $this->volumenMinimo();
        } else {
            // return $this->volumenDisponible();
            return 0;
        }
    }

    public function getEmbalse()
    {
        return $this->embalse;
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
