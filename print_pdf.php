<?php
    require __DIR__.'/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf();
$html2pdf->writeHTML('<h1>Project Minaguas</h1> - El Carri, El Manaure, La Manolo, El Principe, El Gringo');
$html2pdf->output();
?>