<?php
/*
Template Name: Registres
*/
get_header();
?>

<main class="pagina-personalizada">
    <div class="container">
    

        <div class="contenido-personalizado">
            <h1 style="text-align: center;">Signatures</h1>
            

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
                echo '<p>No hi ha signatures.</p>';
            } else {
                // Botón para descargar PDF centrado
                echo '<div style="display: flex; justify-content: center; align-items: center; margin: 40px auto; max-width: 600px; text-align: center;">
                    <div>
                        <a target="_blank" href="/wp-content/themes/hello-elementor-child/generar-pdf/"  style="display: inline-block; background-color: #D04840; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s; box-shadow: 0 2px 8px rgba(208, 72, 132, 0.3);">
                            📄 Crear PDF de signatures
                        </a>
                        <p style="margin-top: 15px; color: #000; font-size: 14px;">Total de signatures: ' . $data_form['total_found'] . '</p>  
                    </div>
                </div>';
                
               
            }
                        
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>