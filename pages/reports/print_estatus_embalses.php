<?php
require __DIR__ . '../../../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

// $miVariable = $_GET['id'];
ob_start();
require_once './estatus_embalses.php';

$template = ob_get_clean();
$html2pdf = new Html2Pdf('L', 'A4', 'es', 'true', 'UTF-8');
$html2pdf->writeHTML($template);
$template = ob_end_clean();
$html2pdf->output('Estatus Embalses.pdf');