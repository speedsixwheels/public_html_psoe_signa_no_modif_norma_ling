<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '/home/u937561055/domains/psoevinaros.com/public_html/signa.psoevinaros.com/wp-load.php';
require_once ABSPATH . 'wp-content/themes/hello-elementor-child/assets/fpdf/fpdf.php';

$url = 'https://signa.psoevinaros.com/api/get-data/form/';
$post_fields = [
    'form_id' => 1,
];

$data_form = get_data_curl($url, $post_fields);
$records = $data_form['records'] ?? [];


if (empty($records)) {
    die('No se encontraron registros para generar el PDF.');
}

function pdf_text($text) {
    return iconv('UTF-8', 'windows-1252//TRANSLIT', (string)$text);
}

$logo_path = ABSPATH . 'wp-content/uploads/2026/03/logo_psoevinaros_red_180x.png';
$logo_path_2 = ABSPATH . 'wp-content/uploads/2026/03/logo_compromis.png'; 
$total_firmas = count($records);

class MiPDF extends FPDF
{
    public $logo_path;
    public $logo_path_2;
    public $total_firmas = 0;

    function Header()
    {
        $this->SetFillColor(255, 255, 255);
        $this->Rect(0, 0, 210, 297, 'F');

        if (!empty($this->logo_path) && file_exists($this->logo_path)) {
            $this->Image($this->logo_path, 12, 12, 32);
        }

        if (!empty($this->logo_path_2) && file_exists($this->logo_path_2)) {
            $this->Image($this->logo_path_2, 12, 28, 32);
        }

        $this->SetDrawColor(120, 120, 120);
        $this->SetLineWidth(0.4);
        $this->Line(50, 12, 50, 46);

        $this->SetXY(55, 10);
        $this->SetTextColor(210, 0, 0);
        $this->SetFont('Arial', 'B', 19);
        $this->MultiCell(
            140,
            10,
            pdf_text("NO VOLEM LA MODIFICACIÓ DEL\nREGLAMENT DE NORMALITZACIÓ\nLINGÜÍSTICA"),
            0,
            'C'
        );

        $this->Ln(2);
        $this->SetTextColor(110, 110, 110);
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 6, pdf_text('Recollida de firmes ciutadanes · PSPV Vinaròs - Compromís'), 0, 1, 'C');

        // Solo en la primera página: total de firmas
        if ($this->PageNo() == 1) {
            $this->Ln(2);
            $this->SetFont('Arial', 'B', 11);
            $this->SetTextColor(70, 70, 70);
            $this->Cell(
                0,
                7,
                pdf_text('Total de firmes recollides: ' . $this->total_firmas),
                0,
                1,
                'L'
            );
            $this->Ln(3);
        } else {
            $this->Ln(6);
        }

        $this->SetX(10);
        $this->SetFillColor(220, 0, 0);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(180, 180, 180);
        $this->SetFont('Arial', 'B', 11);

        $this->Cell(12, 14, 'N', 1, 0, 'C', true);
        $this->Cell(38, 14, pdf_text('Nom'), 1, 0, 'C', true);
        $this->Cell(42, 14, pdf_text('Cognoms'), 1, 0, 'C', true);
        $this->Cell(32, 14, 'DNI', 1, 0, 'C', true);
        $this->Cell(66, 14, pdf_text('Firma'), 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-24);

        $this->SetTextColor(130, 130, 130);
        $this->SetFont('Arial', 'I', 8.5);
        $this->MultiCell(
            0,
            4.5,
            pdf_text("Una iniciativa del PSPV Vinaròs i Compromís."),
            0,
            'L'
        );

        $this->Ln(1);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell(
            0,
            5,
            pdf_text('Pàgina ' . $this->PageNo() . ' de {nb}'),
            0,
            0,
            'R'
        );
    }
}

$pdf = new MiPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->logo_path = $logo_path;
$pdf->logo_path_2 = $logo_path_2;
$pdf->total_firmas = $total_firmas;
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 28);
$pdf->AddPage();

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(70, 70, 70);
$pdf->SetDrawColor(190, 190, 190);

$rowHeight = 11.5;
$num = 0;
$signature_errors = [];

foreach ($records as $record) {
    $num++;

    $nom             = ucfirst(strtolower(trim($record['nom'] ?? '')));
    $cognoms         = trim(ucfirst(strtolower($record['Primer Cognom'] ?? '')) . ' ' . ucfirst(strtolower($record['Segon Cognom'] ?? '')));
    $dni             = strtoupper(trim($record['Dni'] ?? ''));
    $signature_value = trim($record['Signatura'] ?? '');

    $signature_img = '';
    if (!empty($signature_value)) {
        $signature_img = ABSPATH . 'wp-content/uploads/gravity_forms/signatures/' . $signature_value;
    }

    $x = 10;
    $y = $pdf->GetY();

    if ($y + $rowHeight > ($pdf->GetPageHeight() - 28)) {
        $pdf->AddPage();
        $y = $pdf->GetY();
    }

    $pdf->SetX($x);

    $pdf->Cell(12, $rowHeight, $num, 1, 0, 'C');
    $pdf->Cell(38, $rowHeight, pdf_text($nom), 1, 0, 'L');
    $pdf->Cell(42, $rowHeight, pdf_text($cognoms), 1, 0, 'L');
    $pdf->Cell(32, $rowHeight, pdf_text($dni), 1, 0, 'C');

    $firma_x = $pdf->GetX();
    $firma_y = $pdf->GetY();
    $cell_w  = 66;
    $cell_h  = $rowHeight;

    $pdf->Cell($cell_w, $cell_h, '', 1, 0, 'C');

    if (!empty($signature_img) && file_exists($signature_img)) {
        try {
            $img_info = @getimagesize($signature_img);

            if ($img_info !== false) {
                $img_w_orig = $img_info[0];
                $img_h_orig = $img_info[1];

                if ($img_w_orig > 0 && $img_h_orig > 0) {
                    $max_h = $cell_h - 3;
                    $max_w = $cell_w - 4;

                    $ratio = $img_w_orig / $img_h_orig;

                    $img_w = $max_w;
                    $img_h = $img_w / $ratio;

                    if ($img_h > $max_h) {
                        $img_h = $max_h;
                        $img_w = $img_h * $ratio;
                    }

                    $x_img = $firma_x + (($cell_w - $img_w) / 2);
                    $y_img = $firma_y + (($cell_h - $img_h) / 2);

                    $pdf->Image($signature_img, $x_img, $y_img, $img_w, $img_h);
                } else {
                    $signature_errors[] = [
                        'entry_id' => $record['entry_id'] ?? null,
                        'num'      => $num,
                        'file'     => $signature_value,
                        'path'     => $signature_img,
                        'reason'   => 'Dimensiones de imagen no válidas',
                    ];
                }
            } else {
                $signature_errors[] = [
                    'entry_id' => $record['entry_id'] ?? null,
                    'num'      => $num,
                    'file'     => $signature_value,
                    'path'     => $signature_img,
                    'reason'   => 'No se pudo leer el tamaño de la imagen',
                ];
            }
        } catch (Exception $e) {
            $signature_errors[] = [
                'entry_id' => $record['entry_id'] ?? null,
                'num'      => $num,
                'file'     => $signature_value,
                'path'     => $signature_img,
                'error'    => $e->getMessage(),
            ];
        }
    } else {
        $signature_errors[] = [
            'entry_id' => $record['entry_id'] ?? null,
            'num'      => $num,
            'file'     => $signature_value,
            'path'     => $signature_img,
            'exists'   => !empty($signature_img) ? file_exists($signature_img) : false,
            'reason'   => empty($signature_value) ? 'Campo Signatura vacío' : 'Archivo no encontrado',
        ];
    }

    $pdf->Ln($rowHeight);
}

if (!empty($signature_errors)) {
    $log_file = ABSPATH . 'wp-content/uploads/signature_errors_' . date('Y-m-d_His') . '.json';
    file_put_contents($log_file, json_encode($signature_errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$filename = 'recollida_firmes_' . date('Y-m-d_His') . '.pdf';
$pdf->Output('I', $filename);
exit;