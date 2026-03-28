<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'hello-elementor','hello-elementor-theme-style','hello-elementor-header-footer' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION


function pre($data){
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}


/**
 * Obté dades d'una URL mitjançant CURL
 * @param string $url
 * @param array|null $post_fields (opcional) Si es proporcionan, se realizará
 */
function get_data_curl($url,$post_fields = null){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Evita que el script se cuelgue si el servidor no responde
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Seguridad: verifica el certificado SSL
    
    if($post_fields !== null){
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
    }
    
    $response = curl_exec($ch);
    $error_msg = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);



    // 1. Validar si hubo error en la conexión (CURL)
    if ($response === false) {
        header('Content-Type: application/json', true, 500);
        echo json_encode(["status" => "error", "message" => "Error de conexión: " . $error_msg . "al obtener datos de " . $url]);
        exit;
    }

    // 2. Validar el código de estado HTTP
    if ($http_code !== 200) {
        header('Content-Type: application/json', true, $http_code);
        echo json_encode(["status" => "error", "message" => "Servidor respondió con código: " . $http_code]);
        exit;
    }

    // 3. Validar si el JSON es válido
    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        header('Content-Type: application/json', true, 500);
        echo json_encode(["status" => "error", "message" => "El servidor no devolvió un JSON válido"]);
        exit;
    }
    return $data;

}


add_action('template_redirect', 'bloquear_pagina_registres');

function bloquear_pagina_registres() {
    if (is_page('registres') && !is_user_logged_in()) {
        wp_die('Debes iniciar sesión para acceder a esta página.', 'Acceso restringido', array('response' => 403));
    }
}


get_template_part( 'components/shortcodes/gravity/1/index'); 
get_template_part( 'components/functions/gravity/index' ); 
get_template_part( 'components/hooks/gravity/1/index'); 


/**
 * Inclou TCPDF si no està ja carregada
 */
function load_tcpdf() {
    if (!class_exists('TCPDF')) {
        require_once(ABSPATH . 'wp-includes/certificates/ca-bundle.crt');
        
        // WordPress no incluye TCPDF por defecto, así que usaremos una versión simple con FPDF
        // O podemos usar la biblioteca TCPDF si está disponible
        $tcpdf_path = WP_PLUGIN_DIR . '/gravity-pdf/vendor/tecnickcom/tcpdf/tcpdf.php';
        
        if (file_exists($tcpdf_path)) {
            require_once($tcpdf_path);
        }
    }
}

/**
 * Genera el PDF con los registros de firmas
 */
function generar_pdf_registros() {
    // Verificar permisos
    if (!is_user_logged_in()) {
        wp_die('Debes iniciar sesión para descargar el PDF.');
    }
    
    // Obtener los registros
    $url = 'https://signa.psoevinaros.com/api/get-data/form/';
    $post_fields = [
        'form_id' => 1,
        'meses' => 12,
    ];
    $data_form = get_data_curl($url, $post_fields);
    $records = $data_form['records'] ?? [];
    
    if (empty($records)) {
        wp_die('No se encontraron registros para generar el PDF.');
    }
    
    // Intentar cargar TCPDF
    load_tcpdf();
    
    if (class_exists('TCPDF')) {
        generar_pdf_con_tcpdf($records);
    } else {
        // Fallback: generar PDF simple sin librería externa
        generar_pdf_simple($records);
    }
}

/**
 * Genera PDF usando TCPDF
 */
function generar_pdf_con_tcpdf($records) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Información del documento
    $pdf->SetCreator('PSPV Vinaròs');
    $pdf->SetAuthor('PSPV Vinaròs');
    $pdf->SetTitle('Recollida de Firmes');
    $pdf->SetSubject('Registres de Firmes');
    
    // Configuración
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
    // Añadir página
    $pdf->AddPage();
    
    // Establecer fuente
    $pdf->SetFont('helvetica', 'B', 16);
    
    // Título
    $pdf->Cell(0, 10, 'RECOLLIDA DE FIRMES PSPV VINARÒS', 0, 1, 'C');
    $pdf->Ln(5);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Data de generació: ' . date('d/m/Y H:i'), 0, 1, 'R');
    $pdf->Cell(0, 6, 'Total de registres: ' . count($records), 0, 1, 'R');
    $pdf->Ln(5);
    
    // Tabla con los registros
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(41, 128, 185);
    $pdf->SetTextColor(255, 255, 255);
    
    // Cabecera de la tabla
    $pdf->Cell(12, 8, 'Núm', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Fecha', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Nom', 1, 0, 'C', true);
    $pdf->Cell(35, 8, '1r Cognom', 1, 0, 'C', true);
    $pdf->Cell(35, 8, '2n Cognom', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'DNI', 1, 1, 'C', true);
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Contenido de la tabla
    $num = 1;
    foreach ($records as $record) {
        $pdf->Cell(12, 7, $num++, 1, 0, 'C');
        $pdf->Cell(35, 7, date('d/m/Y H:i', strtotime($record['fecha'])), 1, 0, 'C');
        $pdf->Cell(40, 7, $record['nom'], 1, 0, 'L');
        $pdf->Cell(35, 7, $record['Primer Cognom'], 1, 0, 'L');
        $pdf->Cell(35, 7, $record['Segon Cognom'], 1, 0, 'L');
        $pdf->Cell(25, 7, $record['Dni'], 1, 1, 'C');
    }
    
    // Nueva página para las firmas
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'SIGNATURES', 0, 1, 'C');
    $pdf->Ln(5);
    
    $pdf->SetFont('helvetica', '', 9);
    
    foreach ($records as $record) {
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 6, $record['nom'] . ' ' . $record['Primer Cognom'] . ' ' . $record['Segon Cognom'] . ' - DNI: ' . $record['Dni'], 0, 1);
        
        // Intentar incluir la firma si existe
        if (!empty($record['Signatura'])) {
            $signature_url = 'https://signa.psoevinaros.com/wp-content/uploads/gravity_forms/1-' . $record['entry_id'] . '/' . $record['Signatura'];
            
            // Descargar la imagen temporalmente
            $temp_file = download_url($signature_url);
            
            if (!is_wp_error($temp_file)) {
                $pdf->Image($temp_file, $pdf->GetX(), $pdf->GetY(), 60, 20, '', '', '', true, 150, '', false, false, 1);
                @unlink($temp_file);
            }
        }
        
        $pdf->Ln(25);
    }
    
    // Salida del PDF
    $pdf->Output('registres_firmes_' . date('Y-m-d') . '.pdf', 'D');
    exit;
}

/**
 * Genera PDF simple sin TCPDF (fallback)
 */
function generar_pdf_simple($records) {
    // Headers para forzar descarga
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="registres_firmes_' . date('Y-m-d') . '.html"');
    
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recollida de Firmes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; color: #2980b9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #2980b9; color: white; padding: 10px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .signature { max-width: 200px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>RECOLLIDA DE FIRMES PSPV VINARÒS</h1>
    <p><strong>Data de generació:</strong> ' . date('d/m/Y H:i') . '</p>
    <p><strong>Total de registres:</strong> ' . count($records) . '</p>
    
    <table>
        <thead>
            <tr>
                <th>Núm</th>
                <th>Fecha</th>
                <th>Nom</th>
                <th>Primer Cognom</th>
                <th>Segon Cognom</th>
                <th>DNI</th>
            </tr>
        </thead>
        <tbody>';
    
    $num = 1;
    foreach ($records as $record) {
        echo '<tr>
            <td>' . $num++ . '</td>
            <td>' . date('d/m/Y H:i', strtotime($record['fecha'])) . '</td>
            <td>' . htmlspecialchars($record['nom']) . '</td>
            <td>' . htmlspecialchars($record['Primer Cognom']) . '</td>
            <td>' . htmlspecialchars($record['Segon Cognom']) . '</td>
            <td>' . htmlspecialchars($record['Dni']) . '</td>
        </tr>';
    }
    
    echo '</tbody>
    </table>
    
    <h2 style="margin-top: 40px;">Signatures</h2>';
    
    foreach ($records as $record) {
        echo '<div style="margin: 20px 0; border-bottom: 1px solid #ddd; padding-bottom: 20px;">
            <p><strong>' . htmlspecialchars($record['nom'] . ' ' . $record['Primer Cognom'] . ' ' . $record['Segon Cognom']) . '</strong> - DNI: ' . htmlspecialchars($record['Dni']) . '</p>';
        
        if (!empty($record['Signatura'])) {
            $signature_url = 'https://signa.psoevinaros.com/wp-content/uploads/gravity_forms/1-' . $record['entry_id'] . '/' . $record['Signatura'];
            echo '<img src="' . esc_url($signature_url) . '" class="signature" alt="Signatura">';
        }
        
        echo '</div>';
    }
    
    echo '</body></html>';
    exit;
}

// Hook para manejar la descarga del PDF
add_action('init', 'manejar_descarga_pdf_registros');

function manejar_descarga_pdf_registros() {
    if (isset($_GET['descargar_pdf_registros']) && $_GET['descargar_pdf_registros'] == '1') {
        generar_pdf_registros();
    }
} 
