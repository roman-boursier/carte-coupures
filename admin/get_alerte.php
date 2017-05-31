<?php

/* - Fonction permettant de connaître le statut d'une implantation facilement--- */

function get_alerte($term) {
    $alert_info = array();
    
    $t_id = $term->term_id;
    $term_count = $term->count;

    $args = array(
        'post_type' => 'alertes',
        'numberposts' => 1,
        'orderby' => 'date',
        'order' => 'DESC',
        'tax_query' => array(
            array(
                'taxonomy' => 'implantations',
                'field' => 'id',
                'terms' => $t_id, // Where term_id of Term 1 is "1".
            )
    ));
    $alertes = new WP_Query($args);

    if ($term_count == 0) { /* Si l'implantation n'a pas encore d'alerte */
        $alert_info['statut'] = 'fonctionnement';
        $alert_info['description'] = '';

    } else {

        /* Sinon on recupère la dernière alert en date */
        if ($alertes->have_posts()) {
            $alertes->the_post();
            $alerte_id = get_the_ID();
            $statut = get_post_meta($alerte_id, '_statut', true);
            $alert_info['statut'] = $statut;
            $alert_info['date_parution'] = 'Le' . get_the_date(' d  F Y \à G:i');
            
            $alert_info['description'] = ($statut == 'fonctionnement')? '' : get_the_content();
           
        } 
    }
    
     return $alert_info;
}
