<?php
/*
Template Name: Registres
*/
get_header();
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .signature-img {
        max-width: 100px;
        max-height: 35px;
    }
    #signaturesTable {
        margin-top: 30px;
        border-collapse: collapse !important;
    }
    .dataTables_wrapper {
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(208, 72, 64, 0.1);
    }
    
    /* Header de la tabla */
    #signaturesTable thead th {
        background: linear-gradient(135deg, #D04840 0%, #B83B35 100%);
        color: white !important;
        font-weight: 600;
        padding: 12px 8px;
        border-bottom: 3px solid #A03530;
    }
    
    /* Filas alternadas */
    #signaturesTable tbody tr {
        border-bottom: 1px solid #f0dede;
    }
    
    #signaturesTable tbody tr:nth-child(even) {
        background-color: #fef5f5;
    }
    
    #signaturesTable tbody tr:hover {
        background-color: #ffe5e5 !important;
    }
    
    #signaturesTable tbody td {
        padding: 10px 8px;
        color: #333;
    }
    
    /* Controles de DataTables */
    .dataTables_filter {
        margin-bottom: 20px;
    }
    
    .dataTables_filter input {
        border: 2px solid #D04840;
        border-radius: 4px;
        padding: 5px 10px;
    }
    
    .dataTables_filter input:focus {
        outline: none;
        border-color: #B83B35;
        box-shadow: 0 0 5px rgba(208, 72, 64, 0.3);
    }
    
    .dataTables_length select {
        border: 2px solid #D04840;
        border-radius: 4px;
        padding: 5px;
    }
    
    /* Paginación */
    .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #D04840 0%, #B83B35 100%) !important;
        color: white !important;
        border: 1px solid #D04840 !important;
        border-radius: 4px;
    }
    
    .dataTables_paginate .paginate_button:hover {
        background: #ffe5e5 !important;
        color: #D04840 !important;
        border: 1px solid #D04840 !important;
        border-radius: 4px;
    }
    
    .dataTables_paginate .paginate_button {
        border-radius: 4px;
    }
    
    /* Info y labels */
    .dataTables_info {
        color: #666;
    }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        color: #333;
    }
</style>

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
           //pre($data_form['records']);

           /*
             [nom] => Sebastián
            [Primer Cognom] => Esteller
            [Segon Cognom] => Cumba
            [Dni] => 73392886R
            [Signatura] => 69c7d6e220fbf7.19261523.png
            [fecha] => 2026-03-28 13:26:05
            */


        
            
           
            $records = $data_form['records'] ?? [];
            $last_record = end($records);
        
            if (!empty($last_record['fecha'])) {
                $timestamp = strtotime($last_record['fecha']);
                if ($timestamp !== false) {
                    $timestamp += 2 * 3600; // Sumar 2 horas en segundos
                    $last_record['fecha'] = date('d/m/y H:i:s', $timestamp);
                }
            }
            if (empty($records)) {
                echo '<p>No hi ha signatures.</p>';
            } else {
                // Botón para descargar PDF centrado
                echo '<div style="display: flex; justify-content: center; align-items: center; margin: 40px auto; max-width: 600px; text-align: center;">
                    <div>
                        <a target="_blank" href="/wp-content/themes/hello-elementor-child/generar-pdf/"  style="display: inline-block; background-color: #D04840; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s; box-shadow: 0 2px 8px rgba(208, 72, 132, 0.3);">
                            📄 Crear PDF de signatures
                        </a>
                        <p style="margin-top: 15px; color: #000; font-size: 14px;">Total de signatures: ' . $data_form['total_entries'] . '</p>  
                        <p style="margin-top: 15px; color: #000; font-size: 14px;">Darrera signatura: ' . ($last_record['fecha'] ?? '') . '</p> 
                    </div>
                </div>';
                
                // Preparar datos para DataTables
                $tableData = [];
                foreach ($records as $record) {
                    $nom = ucfirst(mb_strtolower(trim($record['nom'] ?? '')));
                    $primerCognom = ucfirst(mb_strtolower(trim($record['Primer Cognom'] ?? '')));
                    $segonCognom = ucfirst(mb_strtolower(trim($record['Segon Cognom'] ?? '')));
                    $dni = strtoupper(trim($record['Dni'] ?? ''));
                    $signatura = trim($record['Signatura'] ?? '');
                    $fecha = trim($record['fecha'] ?? '');  //sumar 2 horas a la fecha original para mostrarla en la tabla

                    
                    // Formatear fecha a dd/mm/yy H:i:s
                    $fechaFormateada = '';
                    if (!empty($fecha)) {
                        $timestamp = strtotime($fecha);
                        if ($timestamp !== false) {
                            $timestamp += 2 * 3600; // Sumar 2 horas en segundos
                            $fechaFormateada = date('d/m/y H:i:s', $timestamp);
                        }
                    }
                    
                    $signaturaUrl = !empty($signatura) ? 'https://signa.psoevinaros.com//wp-content/uploads/gravity_forms/signatures/' . $signatura : '';
                    
                    $tableData[] = [
                        'nom' => $nom,
                        'primerCognom' => $primerCognom,
                        'segonCognom' => $segonCognom,
                        'dni' => $dni,
                        'signatura' => $signatura,
                        'signaturaUrl' => $signaturaUrl,
                        'fecha' => $fechaFormateada
                    ];
                }
                ?>
                <script>
                    var signaturesData = <?php echo json_encode($tableData); ?>;
                </script>
                <?php
                
                // Tabla de DataTables (vacía, se llenará con JavaScript)
                echo '<table id="signaturesTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Primer Cognom</th>
                            <th>Segon Cognom</th>
                            <th>DNI</th>
                            <th>Signatura</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>';
               
            }
                        
            ?>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
jQuery(document).ready(function($) {
    $('#signaturesTable').DataTable({
        "data": signaturesData,
        "deferRender": true, // Renderizar solo cuando sea necesario
        "columns": [
            { "data": "nom" },
            { "data": "primerCognom" },
            { "data": "segonCognom" },
            { "data": "dni" },
            { 
                "data": "signaturaUrl",
                "render": function(data, type, row) {
                    if (data && data !== '') {
                        return '<img src="' + data + '" class="signature-img" alt="Signatura">';
                    }
                    return '-';
                },
                "orderable": false
            },
            { "data": "fecha" }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/ca.json",
            "emptyTable": "No hi ha signatures disponibles",
            "zeroRecords": "No s'han trobat resultats"
        },
        "order": [[5, "asc"]], // Ordenar por fecha ascendente
        "pageLength": 25,
        "responsive": true
    });
});
</script>

<?php get_footer(); ?>