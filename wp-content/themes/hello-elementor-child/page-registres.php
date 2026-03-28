<?php
/*
Template Name: Registres
*/
get_header();
?>

<main class="pagina-personalizada">
    <div class="container">
        <h1><?php the_title(); ?></h1>

        <div class="contenido-personalizado">
            <p>Este es el contenido de mi página personalizada.</p>

            <?php
           //*   Obtenim els registres del formulari de manteniment
            $url = 'https://signa.psoevinaros.com/api/get-data/form/';
            $post_fields = [
                'form_id' => 1,
                'meses' => 12,
            ];
            $data_form = get_data_curl($url,$post_fields);
           
            $records = $data_form['records'] ?? [];
            if (empty($records)) {
                echo '<p>No se encontraron registros.</p>';
            } else {
                // Botón para descargar PDF
                echo '<div style="margin: 20px 0;">
                    <a href="/wp-content/themes/hello-elementor-child/generar-pdf/index.php?entry_id=' . $record['entry_id'] . '" class="btn-descargar-pdf" style="display: inline-block; background-color: #2980b9; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s;">
                        📄 Descargar PDF de Registros
                    </a>
                    <p style="margin-top: 10px; color: #666; font-size: 14px;">Total de registros: ' . count($records) . '</p>
                </div>';
                
               
            }
                        
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>