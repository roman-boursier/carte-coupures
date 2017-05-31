<?php
/* ----Ajout du statut dans les colonnes ----- */
add_filter('manage_alertes_posts_columns', 'gppm_implantation_table_head');

function gppm_implantation_table_head($defaults) {
    $defaults['statut'] = 'Statut';
    return $defaults;
}

/* On récupère la valeur de statut dans chaques colonnes */
add_action('manage_alertes_posts_custom_column', 'custom_alertes_column', 10, 2);

function custom_alertes_column($column, $post_id) {

    /* Voyant de l'état */
    $statut = get_post_meta($post_id, '_statut', true);

    /* On insère dans les colonnes */
    switch ($column) {
        case 'statut':
            echo '<div class="voyant ' . $statut . '"></div>';
            break;
    }
}
