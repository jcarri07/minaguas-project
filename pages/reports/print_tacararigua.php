<?php
require __DIR__ . '../../../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

ob_start();

require_once './report_tacarigua.php';

$template = ob_get_clean();
$html2pdf = new Html2Pdf('L', 'A4', 'es', 'true', 'UTF-8');
$html2pdf->writeHTML($template);
$html2pdf->output('Reporte Lago Tacarigua.pdf');
