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
    private $sup_nor = "";
    private $sup_max;
    private $vol_min;
    private $vol_nor;
    private $vol_max;
    private $ultima_carga;
    private $embalse;
    private $area_cuenca;
    private $disenio;

    public function __construct($id_embalse, $conn)
    {
        $this->id_embalse = $id_embalse;
        $this->conn = $conn;

        $this->loadBatimetria();
    }

    private function loadBatimetria()
    {

        // $id_embalse = mysqli_real_escape_string($this->conn, $this->id_embalse);
        $id_embalse = $this->id_embalse;
        $query = "SELECT * FROM embalses WHERE id_embalse = '$id_embalse'";
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Error en la consulta: " . mysqli_error($this->conn));
        }

        $embalse = mysqli_fetch_array($result);
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
        $this->area_cuenca = $embalse['area_cuenca'];
        $this->disenio = $embalse['inicio_de_operacion'];

        $numero = str_replace(['.', ','], ['', '.'], $this->area_cuenca);
        $numero = (float) $numero;
        $this->area_cuenca = $numero;

        // $query = "SELECT fecha, hora, cota_actual FROM datos_embalse WHERE id_embalse = $id_embalse ORDER BY fecha DESC, hora DESC LIMIT 1";
        // $query = " SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(SELECT MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
        // FROM datos_embalse h 
        // WHERE h.id_embalse = d.id_embalse AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND estatus = 'activo' AND id_embalse = d.id_embalse) AND h.estatus = 'activo' AND cota_actual <> 0 LIMIT 1) AS cota_actual 
        // FROM embalses e
        // LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
        // WHERE e.estatus = 'activo' AND e.id_embalse = '$id_embalse'
        // GROUP BY id_embalse;";
        $query = "SELECT id_embalse, CONCAT(fecha, ' ', hora) AS fecha, cota_actual FROM datos_embalse WHERE id_embalse = '$id_embalse' AND cota_actual <> 0 AND estatus = 'activo' ORDER BY fecha DESC LIMIT 1";
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Error en la consulta: " . mysqli_error($this->conn));
        }

        $datos = mysqli_fetch_assoc($result);
        if (mysqli_num_rows($result) < 1) {
            $this->ultima_carga = "";
        } else {
            if ($datos['fecha'] == null || $datos['cota_actual'] == null) {
                $this->ultima_carga = "";
            } else {
                $fecha = date($datos['fecha']);
                $this->ultima_carga = array(date("Y", strtotime($fecha)), $datos['cota_actual']);
            }
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
            return array(0, 0);
        }
        $año = $this->getCloseYear($año);
        if (!array_key_exists($año, $this->batimetria)) {
            return array(0, 0);
        }
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
            // return explode("-", $batimetria[(string)$cota]);
            return $this->explodeBat($batimetria[(string)$cota]);
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
        if ($tabla == null) {
            return array(0, 0);
        }
        $x_values = array_keys($tabla);
        sort($x_values);

        if ($x < min($x_values)) {
            // return explode("-", reset($tabla));
            return $this->explodeBat(reset($tabla));
        } elseif ($x > max($x_values)) {
            // return explode("-", end($tabla));
            return $this->explodeBat(end($tabla));
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

        //VERSION VIEJA DA ERRORES CUANDO HAY VALORES NEGATIVOS.
        // Realizar la interpolación lineal
        // $Sup_min = explode("-", $tabla[$puntoAnterior])[0];
        // $Sup_max = explode("-", $tabla[$puntoSiguiente])[0];
        // $Sup = $Sup_min + (($Sup_max - $Sup_min) / ($puntoSiguiente - $puntoAnterior)) * ($x - $puntoAnterior);

        // $Vol_min = explode("-", $tabla[$puntoAnterior])[1];
        // $Vol_max = explode("-", $tabla[$puntoSiguiente])[1];
        // $Vol = $Vol_min + (($Vol_max - $Vol_min) / ($puntoSiguiente - $puntoAnterior)) * ($x - $puntoAnterior);

        //VERSION NUEVA USANDO UNA FUNCION CON EXPRESION REGULAR.
        // Realizar la interpolación lineal
        // var_dump( $tabla[$puntoAnterior]);
        // var_dump("PA:".$tabla[$puntoAnterior]);
        if (array_key_exists($puntoAnterior, $tabla)) {
            $Sup_min = $this->explodeBat($tabla[$puntoAnterior], 0);
            $Vol_min = $this->explodeBat($tabla[$puntoAnterior], 1);
        } else {
            $Sup_min = 0;
            $Vol_min = 0;
        }

        if (array_key_exists($puntoSiguiente, $tabla)) {
            $Sup_max = $this->explodeBat($tabla[$puntoSiguiente], 0);
            $Vol_max = $this->explodeBat($tabla[$puntoSiguiente], 1);
        } else {
            $Sup_max = 0;
            $Vol_max = 0;
        }

        $Sup = $Sup_min + (($Sup_max - $Sup_min) / ($puntoSiguiente - $puntoAnterior)) * ($x - $puntoAnterior);
        $Vol = $Vol_min + (($Vol_max - $Vol_min) / ($puntoSiguiente - $puntoAnterior)) * ($x - $puntoAnterior);

        $Sup < 0 ? $Sup = 0 : $Sup = $Sup;
        $Vol < 0 ? $Vol = 0 : $Vol = $Vol;

        return array($Sup, $Vol);
    }

    public function cotaMinima()
    {
        return floatval($this->cota_min);
        return number_format(floatval($this->cota_min), 3, ",", ".");
    }
    public function cotaNormal()
    {
        return floatval($this->cota_nor);
        return number_format(floatval($this->cota_nor), 3, ",", ".");
    }
    public function cotaMaxima()
    {
        return floatval($this->cota_max);
        return number_format(floatval($this->cota_max), 3, ",", ".");
    }

    public function superficieMinima()
    {
        if ($this->batimetria != "") {
            return $this->getByCota($this->getCloseYear(), $this->cotaMinima())[0];
        } else {
            return $this->sup_min != "" ? $this->sup_min : 0;
        }
    }
    public function superficieNormal()
    {
        if ($this->batimetria != "") {
            return $this->getByCota($this->getCloseYear(), $this->cotaNormal())[0];
        } else {
            return $this->sup_nor != "" ? $this->sup_nor : 0;
        }
    }
    public function superficieMaxima()
    {
        if ($this->batimetria != "") {
            return $this->getByCota($this->getCloseYear(), $this->cotaMaxima())[0];
        } else {
            return $this->sup_max != "" ? $this->sup_max : 0;
        }
    }

    public function volumenMinimo()
    {
        if ($this->batimetria != "") {
            return $this->getByCota($this->getCloseYear(), $this->cotaMinima())[1];
        } else {
            return $this->vol_min != "" ? $this->vol_min : 0;
        }
    }
    public function volumenNormal()
    {
        if ($this->batimetria != "") {
            // return floatval($this->cotaNormal());
            return $this->getByCota($this->getCloseYear(), $this->cotaNormal())[1];
        } else {
            return $this->vol_nor != "" ? $this->vol_nor : 0;
        }
    }
    public function volumenMaximo()
    {
        if ($this->batimetria != "") {
            return $this->getByCota($this->getCloseYear(), $this->cotaMaxima())[1];
        } else {
            return $this->vol_max != "" ? $this->vol_max : 0;
        }
    }

    public function volumenDisponible()
    {
        if ($this->batimetria != "") {
            $resultado = $this->volumenNormal() - $this->volumenMinimo();
            return $resultado >= 0 ? $resultado : 0;
        } else {
            return $this->volumenDisponibleOriginal();
        }
    }

    public function volumenDisponibleOriginal()
    {
        if ($this->vol_nor == "" || $this->vol_min == "") {
            return 0;
        } else {
            $resultado = floatval($this->vol_nor) - floatval($this->vol_min);
            return $resultado >= 0 ? $resultado : 0;
        }
    }

    public function volumenDisponibleByCota($año, $cota)
    {
        if ($cota == null) {
            return 0;
        }
        if ($this->batimetria != "") {
            $resultado = $this->getByCota($año, $cota)[1] - $this->volumenMinimo();
            return $resultado >= 0 ? $resultado : 0;
        } else {
            return 0;
        }
    }

    public function volumenActualDisponible()
    {
        // return $this->ultima_carga[1];
        if ($this->ultima_carga != "" && $this->batimetria != "") {
            $resultado = $this->getByCota($this->ultima_carga[0], $this->ultima_carga[1])[1] - $this->volumenMinimo();
            return $resultado >= 0 ? $resultado : 0;
        } else {
            // return $this->volumenDisponible();
            return 0;
        }
    }

    public function getDisenio()
    {
        return $this->disenio;
    }

    public function getEmbalse()
    {
        return $this->embalse;
    }

    public function cargaActual()
    {
        return $this->ultima_carga;
    }

    public function explodeBat($value, $i = null)
    {
        $value = strval($value);
        $pattern = "/^(-?[\d,.]+)-(-?[\d,.]+)$/";

        if (preg_match($pattern, $value, $matches)) {
            $valores = [$matches[1], $matches[2]]; // Valores capturados

            if ($i !== null) {
                return $valores[$i];
            } else {
                return $valores;
            }
        } else {
            $valores = [0, 0]; // Valores predeterminados en caso de no coincidencia

            if ($i !== null) {
                return $valores[$i];
            } else {
                return $valores;
            }
        }
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

    public function abastecimiento($extraccion = 0, $evaporacion = 0, $filtracion = 0)
    {
        $extraccion_litros = $extraccion * 1000 * 1000 / 86400;
        $total_extraccion = $extraccion_litros * (1 / 1000) * (1 / 1000000) * (86400 / 1); // tentativa con 1 / 1000
        // $total_extraccion = $extraccion * (1 / 1000000) * (86400 / 1); // sin 1 / 1000
        $total_salidas = $total_extraccion + $this->evaporacion($evaporacion) + $this->filtracion($filtracion);
        $vol_actual_disp = $this->volumenActualDisponible();
        $vol_normal = $this->volumenNormal();

        if ($vol_actual_disp > $vol_normal) {
            $vol_actual_disp = $vol_normal;
        }

        $abastecimiento = ($vol_actual_disp / $total_salidas) / 30.5;
        return $abastecimiento;
    }

    public function evaporacion($evap = 0)
    {

        $area = 0;
        if ($this->ultima_carga !== "") {
            $area = $this->getByCota($this->ultima_carga[0], $this->ultima_carga[1])[0];
        } else {
            $area = $this->superficieNormal();
        }

        $evap = str_replace(['.', ','], ['', '.'], $evap);
        $evap = floatval($evap);
        $evaporacion = ($area * ($evap / 1000) * 0.8 * (10000 / 30.5)) / 1000000;
        return $evaporacion;
    }

    public function filtracion($porcentaje = 0)
    {
        $porcentaje = str_replace(['.', ','], ['', '.'], $porcentaje);
        $porcentaje = floatval($porcentaje);
        $vol_actual = $this->getByCota($this->ultima_carga[0], $this->ultima_carga[1])[1];
        $filtracion = (($vol_actual * $porcentaje) / 100) / 35.5;
        return $filtracion;
    }
}
