<?php
require_once '/home/u937561055/domains/psoevinaros.com/public_html/signa.psoevinaros.com/wp-load.php';

$form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 1;

$search_criteria = [];
$sorting = [
    'key'       => 'id',
    'direction' => 'ASC'
];

$all_entries = [];
$offset = 0;
$page_size = 200;
$total_count = 0;

/**
 * Obtener todas las entradas
 */
do {
    $paging = [
        'offset'    => $offset,
        'page_size' => $page_size
    ];

    $entries = GFAPI::get_entries($form_id, $search_criteria, $sorting, $paging, $total_count);

    if (is_wp_error($entries)) {
        die($entries->get_error_message());
    }

    if (!empty($entries)) {
        $all_entries = array_merge($all_entries, $entries);
        $offset += $page_size;
    }

} while (!empty($entries) && count($all_entries) < $total_count);

/**
 * Obtener formulario para mapear etiquetas
 */
$form = GFAPI::get_form($form_id);

if (is_wp_error($form) || empty($form)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status'  => 'error',
        'message' => 'No se pudo cargar el formulario.'
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Genera un array con:
 * - id del campo
 * - id de subcampo
 * usando adminLabel si existe, y si no label
 */
$field_labels = [];

foreach ($form['fields'] as $field) {
    $field_id = (string) $field->id;

    // Etiqueta principal del campo
    $label = !empty($field->adminLabel) ? $field->adminLabel : $field->label;
    $field_labels[$field_id] = $label;

    // Subcampos (name, address, etc.)
    if (!empty($field->inputs) && is_array($field->inputs)) {
        foreach ($field->inputs as $input) {
            $input_id = (string) $input['id'];

            $sub_label = !empty($input['label']) ? $input['label'] : $label;

            // Si el campo tiene adminLabel, lo usamos como prefijo
            if (!empty($field->adminLabel)) {
                $field_labels[$input_id] = $field->adminLabel . ' - ' . $sub_label;
            } else {
                $field_labels[$input_id] = $sub_label;
            }
        }
    }
}

/**
 * Campos internos/sistema de Gravity Forms que también puedes querer devolver
 */
$system_fields = [
    'id'                 => 'entry_id',
    'date_created'       => 'fecha',
    'ip'                 => 'ip',
    'source_url'         => 'source_url',
    'created_by'         => 'created_by',
    'transaction_id'     => 'transaction_id',
    'payment_amount'     => 'payment_amount',
    'payment_date'       => 'payment_date',
    'payment_status'     => 'payment_status',
    'is_read'            => 'is_read',
    'is_starred'         => 'is_starred',
    'currency'           => 'currency',
    'user_agent'         => 'user_agent',
    'status'             => 'status',
];

/**
 * Transformar entradas usando adminLabel
 */
$records = [];

foreach ($all_entries as $entry) {
    $record = [];

    foreach ($entry as $key => $value) {
        // Omitir arrays/objetos internos
        if (is_array($value) || is_object($value)) {
            continue;
        }

        // Campos del sistema
        if (isset($system_fields[$key])) {
            $record[$system_fields[$key]] = $value;
            continue;
        }

        // Campos del formulario
        if (isset($field_labels[$key])) {
            $record[$field_labels[$key]] = $value;
        }
    }

    $records[] = $record;
}

$out = [
    'status'        => 'success',
    'form_id'       => $form_id,
    'total_entries' => count($records),
    'records'       => $records,
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>