<?php

class Batimetria
{
    private $id_embalse;
    private $conn;
    private $batimetria;

    public function __construct($id_embalse, $conn)
    {
        $this->id_embalse = $id_embalse;
        $this->conn = $conn;

        $this->loadBatimetria();
    }

    private function loadBatimetria()
    {

        $id_embalse = mysqli_real_escape_string($this->conn, $this->id_embalse);
        $query = "SELECT batimetria  FROM embalses WHERE id_embalse = $id_embalse";
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Error en la consulta: " . mysqli_error($this->conn));
        }

        $batimetria = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        $this->batimetria = json_decode($batimetria['batimetria'],true);
    }

    public function getBatimetria(){
        // $batimetria = array();
        $batimetria = $this->batimetria;
        return $batimetria;
    }

    public function getByCota($año, $cota)
    {
        $cota = number_format(floatval($cota),3,".","");
        return (array_key_exists((string)$cota, $this->batimetria[$año])) ? $this->batimetria[$año][(string)$cota] : $this->getCloseCota($this->batimetria[$año], $cota, 0.001);
    }

    public function getCloseCota($batimetria, $cota, $step){
        $cota = $cota + $step;
        $cota = number_format(floatval($cota),3,".","");
        $step = ($step > 0) ? (($step + 0.001) * -1) : (($step * -1) + 0.001);

        if(array_key_exists((string)$cota,$batimetria)){
            return $batimetria[(string)$cota];
        }else{
            return $this->getCloseCota($batimetria, $cota, $step);
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


}
