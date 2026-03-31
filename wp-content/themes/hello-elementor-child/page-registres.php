<?php
/*
Template Name: Registres
*/
get_header();
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
<style>
    .pagina-personalizada,
    .pagina-personalizada .container,
    .pagina-personalizada .contenido-personalizado {
        min-width: 0;
    }

    .signature-img {
        max-width: 100px;
        max-height: 35px;
    }

    .stats-fancybox-trigger {
        display: inline-block;
        margin-top: 12px;
        background-color: #ffffff;
        color: #D04840;
        border: 2px solid #D04840;
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .stats-fancybox-trigger:hover {
        background-color: #D04840;
        color: #ffffff;
    }

    .stats-modal {
        display: none;
        max-width: 520px;
        width: calc(100vw - 32px);
        padding: 24px;
        border-radius: 12px;
    }

    .stats-modal h2 {
        margin: 0 0 20px;
        text-align: center;
        color: #D04840;
        font-size: 24px;
    }

    .stats-modal-list {
        display: grid;
        gap: 10px;
    }

    .stats-modal-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 12px 16px;
        background: #fef5f5;
        border: 1px solid #f3d6d3;
        border-radius: 8px;
        color: #333;
        font-size: 14px;
    }

    .stats-modal-item strong {
        color: #D04840;
    }

    #signaturesTable {
        margin-top: 30px;
        border-collapse: collapse !important;
        table-layout: auto;
    }

    #signaturesTable th,
    #signaturesTable td {
        white-space: nowrap;
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

    .signatures-table-wrapper {
        width: 100%;
        max-width: 100%;
    }

    .signatures-table-wrapper,
    .signatures-table-scroll-top {
        scrollbar-color: #b83d36 #f3d6d3;
        scrollbar-width: thin;
    }

    .signatures-table-wrapper::-webkit-scrollbar,
    .signatures-table-scroll-top::-webkit-scrollbar {
        height: 10px;
    }

    .signatures-table-wrapper::-webkit-scrollbar-track,
    .signatures-table-scroll-top::-webkit-scrollbar-track {
        background: #f3d6d3;
        border-radius: 999px;
    }

    .signatures-table-wrapper::-webkit-scrollbar-thumb,
    .signatures-table-scroll-top::-webkit-scrollbar-thumb {
        background: #b83d36;
        border-radius: 999px;
    }

    .signatures-table-wrapper::-webkit-scrollbar-thumb:hover,
    .signatures-table-scroll-top::-webkit-scrollbar-thumb:hover {
        background: #a1332d;
    }

    .signatures-table-scroll-top {
        display: none;
        overflow-x: auto;
        overflow-y: hidden;
        margin-bottom: 8px;
        -webkit-overflow-scrolling: touch;
    }

    .signatures-table-scroll-top-inner {
        height: 1px;
    }

    @media (max-width: 767px) {
        .signatures-table-scroll-top {
            display: block;
        }

        .signatures-table-wrapper {
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .signatures-table-wrapper .dataTables_wrapper {
            min-width: 720px;
        }

        #signaturesTable {
            width: 720px !important;
            min-width: 720px;
        }

        .dataTables_wrapper {
            padding: 16px;
        }

        .dataTables_wrapper .dataTables_filter {
            float: left;
            text-align: left;
            width: 100%;
            margin-bottom: 12px;
        }

        .dataTables_wrapper .dataTables_filter label {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .dataTables_wrapper .dataTables_length {
            float: left !important;
            text-align: left !important;
            width: 100%;
            margin-bottom: 12px;
            display: flex;
            justify-content: flex-start;
        }

        .dataTables_wrapper .dataTables_length label {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            text-align: left !important;
            width: 100%;
            margin: 0;
        }

        .dataTables_wrapper .dataTables_length select {
            margin-left: 8px;
            margin-right: 0;
        }
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
            } 
                
                // Preparar datos para DataTables
                $array_estats = array();
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
                            $fechaDia = date('d/m/Y', $timestamp);

                            if (!isset($array_estats[$fechaDia])) {
                                $array_estats[$fechaDia] = 0;
                            }

                            $array_estats[$fechaDia]++;
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

                ksort($array_estats);
                ?>


        <?php if(!empty($records)): ?>
               
                <div style="display: flex; justify-content: center; align-items: center; margin: 40px auto; max-width: 600px; text-align: center;">
                    <div>
                        <a target="_blank" href="/wp-content/themes/hello-elementor-child/generar-pdf/"  style="display: inline-block; background-color: #D04840; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s; box-shadow: 0 2px 8px rgba(208, 72, 132, 0.3);">
                            📄 Crear PDF de signatures
                        </a>
                        <p style="margin-top: 15px; color: #000; font-size: 14px;">Total de signatures: <?php echo $data_form['total_entries']; ?></p>  
                        <p style="margin-top: 15px; color: #000; font-size: 14px;">Darrera signatura: <?php echo $last_record['fecha'] ?? ''; ?></p> 
                        <?php if(!empty($array_estats)): ?>
                            <a href="#stats-modal" data-fancybox class="stats-fancybox-trigger">Estadístiques de signatures</a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if(!empty($array_estats)): ?>
                    <div id="stats-modal" class="stats-modal">
                        <h2>Estasdístiques de signatures</h2>
                        <div class="stats-modal-list">
                            <?php foreach($array_estats as $dia => $total): ?>
                                <div class="stats-modal-item">
                                    <span><?php echo esc_html($dia); ?></span>
                                    <strong><?php echo esc_html($total); ?></strong>
                                </div>
                            <?php endforeach; ?>
                            <div class="stats-modal-item" style="background: #ffe5e5; border-color: #D04840; font-weight: 600; margin-top: 8px;">
                                <span>TOTAL</span>
                                <strong style="font-size: 18px;"><?php echo esc_html(array_sum($array_estats)); ?></strong>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php endif; ?>


        
                <script>
                    var signaturesData = <?php echo json_encode($tableData); ?>;
                </script>
                <?php

                
                
                // Tabla de DataTables (vacía, se llenará con JavaScript)
                echo '<div class="signatures-table-scroll-top"><div class="signatures-table-scroll-top-inner"></div></div><div class="signatures-table-wrapper"><table id="signaturesTable" class="display" style="width:100%">
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
                </table></div>';
               
            
                        
            ?>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
jQuery(document).ready(function($) {
    var $table = $('#signaturesTable');
    var $bottomScroll = $('.signatures-table-wrapper');
    var $topScroll = $('.signatures-table-scroll-top');
    var $topScrollInner = $('.signatures-table-scroll-top-inner');
    var isSyncingTopScroll = false;
    var isSyncingBottomScroll = false;

    if (!$table.length) {
        return;
    }

    function syncTopScrollbarWidth() {
        var scrollWidth = $bottomScroll.get(0) ? $bottomScroll.get(0).scrollWidth : 0;
        var clientWidth = $bottomScroll.get(0) ? $bottomScroll.get(0).clientWidth : 0;

        $topScrollInner.width(scrollWidth);

        if (scrollWidth > clientWidth) {
            $topScroll.show();
        } else {
            $topScroll.hide();
        }
    }

    $topScroll.on('scroll', function() {
        if (isSyncingBottomScroll) {
            return;
        }

        isSyncingTopScroll = true;
        $bottomScroll.scrollLeft($topScroll.scrollLeft());
        isSyncingTopScroll = false;
    });

    $bottomScroll.on('scroll', function() {
        if (isSyncingTopScroll) {
            return;
        }

        isSyncingBottomScroll = true;
        $topScroll.scrollLeft($bottomScroll.scrollLeft());
        isSyncingBottomScroll = false;
    });

    var dataTable = $table.DataTable({
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
        "pageLength": 10,
        "lengthMenu": [[10], [10]],
        "responsive": false,
        "initComplete": function() {
            syncTopScrollbarWidth();
        },
        "drawCallback": function() {
            syncTopScrollbarWidth();
        }
    });

    $(window).on('load resize orientationchange', function() {
        dataTable.columns.adjust();
        syncTopScrollbarWidth();
    });

    if (typeof Fancybox !== 'undefined') {
        Fancybox.bind('[data-fancybox]', {
            dragToClose: false
        });
    }
});
</script>

<?php get_footer(); ?>