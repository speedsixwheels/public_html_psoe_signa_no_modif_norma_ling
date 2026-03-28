<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '/home/u937561055/domains/psoevinaros.com/public_html/signa.psoevinaros.com/wp-load.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/hello-elementor-child/assets/fpdf/fpdf.php';


 $url = 'https://signa.psoevinaros.com/api/get-data/form/';
        $post_fields = [
            'form_id' => 1,
            'meses' => 12,
        ];

        
        $data_form = get_data_curl($url,$post_fields);

        $records = $data_form['records'] ?? [];
        if (empty($records)) {
            die('No se encontraron registros para generar el PDF.');
        }



/**
 * Convierte UTF-8 a ISO-8859-1/Windows-1252 para FPDF clásico
 */
function pdf_text($text) {
    return iconv('UTF-8', 'windows-1252//TRANSLIT', $text);
}

       
/**
 * Rutas
 */
$logo_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads/2026/03/logo_psoevinaros_red_180x.png'; // tu logo
$signature_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads/gravity_forms/signatures/'; // ruta real de la firma

class MiPDF extends FPDF
{
    public $record;
    public $logo_path;

    function Header()
    {
        // Fondo claro opcional
        $this->SetFillColor(245, 245, 245);
        $this->Rect(0, 0, 210, 297, 'F');

        // Logo
        if (!empty($this->logo_path) && file_exists($this->logo_path)) {
            $this->Image($this->logo_path, 12, 12, 32);
        }

        // Línea vertical decorativa
        $this->SetDrawColor(120, 120, 120);
        $this->SetLineWidth(0.4);
        $this->Line(50, 12, 50, 46);

        // Título
        $this->SetXY(55, 10);
        $this->SetTextColor(210, 0, 0);
        $this->SetFont('Arial', 'B', 19);
        $this->MultiCell(140, 10, pdf_text("NO VOLEM LA MODIFICACIÓ DEL\nREGLAMENT DE NORMALITZACIÓ\nLINGÜÍSTICA"), 0, 'C');

        // Subtítulo
        $this->Ln(2);
        $this->SetTextColor(110, 110, 110);
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 6, pdf_text('Recollida de firmes ciutadanes · Vinaròs PSOE'), 0, 1, 'C');

        $this->Ln(6);

        // Cabecera tabla
        $this->SetX(10);
        $this->SetFillColor(220, 0, 0);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(180, 180, 180);
        $this->SetFont('Arial', 'B', 11);

        $this->Cell(12, 14, 'Nº', 1, 0, 'C', true);
        $this->Cell(38, 14, pdf_text('Nom'), 1, 0, 'C', true);
        $this->Cell(52, 14, pdf_text('Cognoms'), 1, 0, 'C', true);
        $this->Cell(32, 14, 'DNI', 1, 0, 'C', true);
        $this->Cell(56, 14, pdf_text('Firma'), 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-22);
        $this->SetTextColor(130, 130, 130);
        $this->SetFont('Arial', 'I', 8.5);
        $this->Cell(
            0,
            5,
            pdf_text("Les dades recollides seran tractades de conformitat amb el RGPD i s'utilitzaran exclusivament per als fins polítics del PSPV-PSOE de Vinaròs."),
            0,
            1,
            'L'
        );

       
    }
}

$pdf = new MiPDF('P', 'mm', 'A4');
$pdf->logo_path = $logo_path;
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 28);
$pdf->AddPage();

/**
 * Dibujar filas con todos los registros
 */
$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(70, 70, 70);
$pdf->SetDrawColor(190, 190, 190);

$rowHeight = 11.5;
$num = 0;

// Recorrer todos los registros
foreach ($records as $record) {
    $num++;
    $pdf->SetX(10);

    // Extraer datos del registro
    $nom = $record['nom'] ?? '';
    $cognoms = trim(($record['Primer Cognom'] ?? '') . ' ' . ($record['Segon Cognom'] ?? ''));
    $dni = $record['Dni'] ?? '';
    
    // Construir ruta completa de la firma
    $entry_id = $record['entry_id'] ?? '';
    $signature_file = $record['Signatura'] ?? '';
    $full_signature_path = '';

    pre($signature_file);
    die();
    
    if (!empty($signature_file) && !empty($entry_id)) {
        $full_signature_path = 'https://signa.psoevinaros.com/wp-content/uploads/gravity_forms/' . $signature_file;
    }

    // Guardamos posición Y de la fila
    $y = $pdf->GetY();

    // Celdas
    $pdf->Cell(12, $rowHeight, $num, 1, 0, 'C');
    $pdf->Cell(38, $rowHeight, pdf_text($nom), 1, 0, 'L');
    $pdf->Cell(52, $rowHeight, pdf_text($cognoms), 1, 0, 'L');
    $pdf->Cell(32, $rowHeight, pdf_text($dni), 1, 0, 'C');
    $pdf->Cell(56, $rowHeight, '', 1, 1, 'C');

    // Insertar firma dentro de la última celda si existe
     if (!empty($full_signature_path)) {
        // Intentar obtener la imagen
        $temp_file = download_url($full_signature_path);
        if (!is_wp_error($temp_file) && file_exists($temp_file)) {
            $pdf->Image($temp_file, 136, $y + 1.2, 48, 9);
            @unlink($temp_file); // Eliminar archivo temporal
        }
    } 
}

$filename = 'recollida_firmes_' . date('Y-m-d_His') . '.pdf';
$pdf->Output('I', $filename);
exit;