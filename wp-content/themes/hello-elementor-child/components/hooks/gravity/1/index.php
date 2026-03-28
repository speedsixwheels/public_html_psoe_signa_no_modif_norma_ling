<?php

add_filter('gform_validation_1', 'validation_form_1');
function validation_form_1($validation_result){

    $input['cifnif'] = trim(rgpost('input_5', true));

    //Definició per defecte del plugin, es obligatori
    $form = $validation_result['form'];

    //Iterem els fiels del form per validar els errors personalitzats que vulguem
    foreach ($form['fields'] as &$field) {
        if ($field->id == 5) {
            $valid = validDniCifNie($input['cifnif']);
            if (!$valid) {
                $field->failed_validation = true;
                $field->validation_message = 'El CIF/NIF introduït no és vàlid';
                $validation_result['is_valid'] = false;
            }
        }
    }

    return $validation_result;
}




?>