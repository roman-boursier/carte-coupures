<?php

/* --------------------------------------------------------------------------------------- */
/* ----------------------------Inclusion des js/css---------------------------------------- */
/* ---------------------------------------------------------------------------------------- */

/* head */
add_action('admin_enqueue_scripts', 'add_cc_admin_scripts');

function add_cc_admin_scripts() {
    wp_enqueue_style('cc_admin_css', plugins_url() . '/carte-coupures-V2/admin/admin-css.css');
}



/* --------------------------------------------------------------------------------------- */
/* -------------------------------Création du custom post type----------------------------- */
/* ---------------------------------------------------------------------------------------- */
add_action('init', 'create_post_type');

function create_post_type() {
    register_post_type('alertes', array(
        'labels' => array(
            'name' => __('Alerts'),
            'singular_name' => __('Alert'),
            'add_new' => __('Ajouter'),
            'add_new_item' => __('Ajouter une nouvelle alerte'),
            'edit_item' => __('Editer l\'alerte'),
            'new_item' => __('Nouvelle alerte'),
            'all_items' => __('Toutes les alertes'),
            'view_item' => __('Voir l\'alerte'),
            'search_items' => __('rechercher une alerte'),
            'not_found' => __('Aucune alerte trouvée'),
            'not_found_in_trash' => __('Aucune alerte dans la corbeille'),
            'menu_name' => __('Carte '),
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'alertes'),
        'exclude_from_search' => true,
        'menu_icon' => 'dashicons-location',
        'supports' => array('title', 'editor'),
            )
    );

    register_taxonomy(
            'implantations', 'alertes', array(
        'label' => __('Implantations'),
        'rewrite' => array('slug' => 'implantations'),
        'hierarchical' => false,
        'query_var' => true,
        'show_admin_column' => true,
        'labels' => array(
            'add_new_item'      => __( 'Ajouter une implantation', 'textdomain' ),
            'edit_item'         => __( 'Editer l\'implantation', 'textdomain' ),
        )
            )
    );
}

/* --------------------------------------------------------------------------------------- */
/* -------------------------------Métas boxes------------------------------------------- */
/* ---------------------------------------------------------------------------------------- */
require_once 'metaboxes.php';


/* --------------------------------------------------------------------------------------- */
/* -----------------Modif  listing en admin------------------------------ */
/* --------------------------------------------------------------------------------------- */
require_once 'liste-admin.php';


/* --------------------------------------------------------------------------------------- */
/* -----------------Ajout des implantation------------------------------------------------ */
/* --------------------------------------------------------------------------------------- */
require_once 'taxonomy-implantations.php';
