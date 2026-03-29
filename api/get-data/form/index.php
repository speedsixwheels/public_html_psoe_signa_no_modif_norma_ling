<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
/**
 * Retorna les dades de les entrades d'un formulari de Gravity Forms en format JSON
 * i permet filtrar per estat i per mesos.
 * Adaptat per recuperar tots els camps del formulari, inclosos subcamps.
 */

require_once '/home/u937561055/domains/psoevinaros.com/public_html/signa.psoevinaros.com/wp-load.php';



$form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 1;
$meses   = isset($_POST['meses']) ? intval($_POST['meses']) : 12;




if (empty($form_id)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Faltan parámetros obligatorios: form_id.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($meses <= 0) {
    $meses = 1;
}

// 1. Obtener formulario
$form = GFAPI::get_form($form_id);

if (is_wp_error($form) || empty($form)) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Error al obtener el formulario ' . $form_id . '.',
        'details' => is_wp_error($form) ? $form->get_error_message() : 'Formulario vacío o no encontrado.',
        'form_id' => $form_id,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. Calcular rango de fechas
$fecha_hoy    = date('Y-m-d 23:59:59');
$fecha_inicio = date('Y-m-d 00:00:00', strtotime("-{$meses} months"));

// 3. Criterios de búsqueda
$search_criteria = [
    'status'        => 'active',
    'start_date'    => $fecha_inicio,
    'end_date'      => $fecha_hoy,
    'field_filters' => [
        'mode' => 'all',
    ],
];

// 4. Ordenación y paginación
$sorting = [
    'key'       => 'date_created',
    'direction' => 'DESC',
];

$page_size = 200; // Tamaño de página para cada petición
$offset = 0;
$total_count = 0;
$all_entries = [];

// 6. Obtener todas las entradas mediante paginación
do {
    $paging = [
        'offset'    => $offset,
        'page_size' => $page_size,
    ];

    $entries = GFAPI::get_entries($form_id, $search_criteria, $sorting, $paging, $total_count);

    if (is_wp_error($entries)) {
        http_response_code(500);

        $out = [
            'status'  => 'error',
            'message' => 'Error al obtener las entradas: ' . $entries->get_error_message(),
            'form_id' => $form_id,
        ];

        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!empty($entries)) {
        $all_entries = array_merge($all_entries, $entries);
        $offset += $page_size;
    }

    // Continuar mientras haya más entradas por obtener
} while (!empty($entries) && count($all_entries) < $total_count);

// Usar todas las entradas obtenidas
$entries = $all_entries;

if (empty($entries)) {
    $out = [
        'status'          => 'error',
        'message'         => 'No se encontraron entradas que coincidan con los criterios en el formulario.',
        'form_id'         => $form_id,
        'meses'           => $meses,
        'search_criteria' => $search_criteria,
    ];

    echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}



// 7. Crear mapeos generales de campos
$field_ids = [];
$fields_labels_id = [];

foreach ($form['fields'] as $field) {
    $field_id    = (string) $field->id;
    $field_label = !empty($field->adminLabel) ? $field->adminLabel : $field->label;

    $field_ids[$field_id] = $field_label;
    $fields_labels_id[$field_label] = $field_id;
}

// 8. Función auxiliar para normalizar valores
function normalizar_valor_gf($value) {
    $decoded = json_decode($value, true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        return $decoded;
    }

    return $value;
}

// 9. Construir respuesta de entradas
$array_entries = [];

foreach ($entries as $i => $entry) {
    $data = [];
    $data['fecha'] = rgar($entry, 'date_created');
    $data['entry_id'] = rgar($entry, 'id');

    foreach ($form['fields'] as $field) {
        $field_id    = (string) $field->id;
        $field_label = !empty($field->adminLabel) ? $field->adminLabel : $field->label;

        // Campos compuestos: name, address, checkbox, etc.
        if (!empty($field->inputs) && is_array($field->inputs)) {
            $subvalues = [];

            foreach ($field->inputs as $input) {
                $input_id = (string) $input['id'];
                $input_label = isset($input['label']) && $input['label'] !== ''
                    ? $input['label']
                    : $input_id;

                $value = rgar($entry, $input_id);

                if ($value !== '' && $value !== null) {
                    $subvalues[$input_label] = normalizar_valor_gf($value);
                }
            }

            $data[$field_label] = $subvalues;
        } else {
            // Campos simples
            $value = rgar($entry, $field_id);
            $data[$field_label] = normalizar_valor_gf($value);
        }
    }


    // Mapeos útiles
    $data['fields_labels_id'] = $fields_labels_id;
    $data['field_ids'] = $field_ids;

    // Metadatos básicos de la entry
    $data['meta'] = [
        'id'             => rgar($entry, 'id'),
        'form_id'        => rgar($entry, 'form_id'),
        'date_created'   => rgar($entry, 'date_created'),
        'date_updated'   => rgar($entry, 'date_updated'),
        'is_starred'     => rgar($entry, 'is_starred'),
        'is_read'        => rgar($entry, 'is_read'),
        'ip'             => rgar($entry, 'ip'),
        'source_url'     => rgar($entry, 'source_url'),
        'user_agent'     => rgar($entry, 'user_agent'),
        'currency'       => rgar($entry, 'currency'),
        'payment_status' => rgar($entry, 'payment_status'),
        'payment_date'   => rgar($entry, 'payment_date'),
        'payment_amount' => rgar($entry, 'payment_amount'),
        'transaction_id' => rgar($entry, 'transaction_id'),
        'created_by'     => rgar($entry, 'created_by'),
        'status'         => rgar($entry, 'status'),
    ];

    $array_entries[$i] = $data;
}

if (empty($array_entries)) {
    $out = [
        'status'  => 'error',
        'message' => 'No se han podido generar entradas en $array_entries.',
        'form_id' => $form_id,
        'meses'   => $meses,
    ];

    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

// 10. Respuesta final
$out = [
    'status'           => 'success',
    'form_id'          => $form_id,
    'meses'            => $meses,
    'total_entries'    => count($array_entries),
    'total_found'      => $total_count,
    'search_criteria'  => $search_criteria,
    'field_ids'        => $field_ids,
    'fields_labels_id' => $fields_labels_id,
    'records'          => $array_entries,
];
header('Content-Type: application/json; charset=utf-8');
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>