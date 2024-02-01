<?php
require __DIR__ . '../../../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

$id = $_GET['id'];
$name = $_GET['name'];

ob_start();

require_once './ficha_tecnica.php';

$template = ob_get_clean();
$html2pdf = new Html2Pdf('P', 'A4', 'es', 'true', 'UTF-8');
$html2pdf->writeHTML($template);
$html2pdf->output('Ficha Tecnica (' . $name . ').pdf');
