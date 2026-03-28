<?php

add_shortcode('confirmation_message_form_1', 'confirmation_message_form_1_shortcode');
function confirmation_message_form_1_shortcode($atts){
    ?>
        <style>
            .confirmation-message {
                background-color: #D04840 !important;
                border-radius: 10px !important;
                color: white !important;
                padding: 10px 20px !important;
            }
        </style>
    <?php
    $message = '<div class="confirmation-message">';  
    $message .= '¡Gràcies per la seua col·laboració!<br /><br />';
    $message .= 'El seu suport a la iniciativa <strong>«No volem la modificació del Reglament de Normalització Lingüística»</strong> ha sigut registrat correctament.';
    $message .= '<br>Agraïm la seua participació en aquesta recollida de signatures ciutadanes promoguda pel grup municipal del PSOE de Vinaròs- Compromís.';
    $message .= '</div>';
    return $message;
}

?>