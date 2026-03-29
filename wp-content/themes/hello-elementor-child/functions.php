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


add_filter('login_redirect', 'redirigir_admin_edu_login', 10, 3);

function redirigir_admin_edu_login($redirect_to, $request, $user) {
    if (isset($user->user_login) && $user->user_login === 'admin-edu') {
        return 'https://signa.psoevinaros.com/registres/';
    }
    return $redirect_to;
}


get_template_part( 'components/shortcodes/gravity/1/index'); 
get_template_part( 'components/functions/gravity/index' ); 
get_template_part( 'components/hooks/gravity/1/index'); 
