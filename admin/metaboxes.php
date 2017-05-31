<?php
/* ------------------------------------------------------------------------------------ */
/* -----------------Création des metaboxes--------------------------------------------- */
/* ------------------------------------------------------------------------------------ */

add_action('add_meta_boxes', 'init_metabox');

function init_metabox() {
    add_meta_box('infos_implantation', 'Statut de l\'alert', 'infos_implantation', 'alertes', 'side');
}

/* -------Construction des metaboxes---------- */

/* Les informations */

function infos_implantation($post) {
    $statut = get_post_meta($post->ID, '_statut', true);
    ?>
    <select name="statut" id="statut">
        <option value="fonctionnement" <?php echo ($statut == 'fonctionnement') ? 'selected' : ''; ?> >En fonctionnement</option>
        <option value="travaux" <?php echo ($statut == 'travaux') ? 'selected' : ''; ?>>Travaux en cours</option>
        <option value="panne" <?php echo ($statut == 'panne') ? 'selected' : ''; ?>>En panne</option>
    </select>
    <?php
}

/* ----Sauvegarde des metaboxes---- */
add_action('save_post', 'save_metabox');

function save_metabox($post_id) {
    /* informations implantations */
    if (isset($_POST['statut'])) {
        update_post_meta($post_id, '_statut', esc_html($_POST['statut']));
    }
}



/* ------------------------------------------------------------------------------------ */
/* -----------------On modifie l'affichage de la taxonomie implantation---------------- */
/* ------------------------------------------------------------------------------------ */

function custom_meta_box() {
    remove_meta_box('tagsdiv-implantations', 'alertes', 'side');
    add_meta_box('tagsdiv-implantations', 'Implantation', 'implantations_meta_box', 'alertes', 'side');
}

add_action('add_meta_boxes', 'custom_meta_box');



function implantations_meta_box($post) {

    $tax_name = 'implantations';
    $taxonomy = get_taxonomy($tax_name);
    ?>
    <div class="tagsdiv" id="<?php echo $tax_name; ?>">
        <div class="jaxtag">
            <?php
            // Use nonce for verification
            wp_nonce_field(plugin_basename(__FILE__), 'implantations_noncename');
            $type_IDs = wp_get_object_terms($post->ID, 'implantations', array('fields' => 'ids'));
            wp_dropdown_categories('taxonomy=implantations&hide_empty=0&orderby=name&name=implantations&show_option_none=Select type&selected=' . $type_IDs[0]);
            ?>
        </div>
    </div>
    <?php
}

/* When the post is saved, saves our custom taxonomy */

function implantations_save_postdata($post_id) {
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if (( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || wp_is_post_revision($post_id))
        return;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times

    if (!wp_verify_nonce($_POST['implantations_noncename'], plugin_basename(__FILE__)))
        return;


    // Check permissions
    if ('alerts' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return;
    }
    else {
        if (!current_user_can('edit_post', $post_id))
            return;
    }

    // OK, we're authenticated: we need to find and save the data

    $type_ID = $_POST['implantations'];

    $type = ( $type_ID > 0 ) ? get_term($type_ID, 'implantations')->slug : NULL;

    wp_set_object_terms($post_id, $type, 'implantations');
}

/* Do something with the data entered */
add_action('save_post', 'implantations_save_postdata');



