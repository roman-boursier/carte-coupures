<?php


/* ---------------------------------------------------- */
/* -----------Ajout des custom meta taxonomies-------- */
/* ---------------------------------------------------- */


/* Ajout du champs dans la liste des implantations */

function mj_taxonomy_add_custom_meta_field() {
    ?>
    <div class="form-field">
        <label for="term_meta[lat]"><?php _e('Latitude', 'pippin'); ?></label>
        <input type="text" name="term_meta[lat]" id="term_meta[lat]" value="">
        <p class="description"><?php _e('Exemple : 14.671821', 'pippin'); ?></p>
    </div>
    <div class="form-field">
        <label for="term_meta[lng]"><?php _e('Longitude', 'pippin'); ?></label>
        <input type="text" name="term_meta[lng]" id="term_meta[lat]" value="">
        <p class="description"><?php _e('Exemple :-61.1603466', 'pippin'); ?></p>
    </div>
    <?php
}

add_action('implantations_add_form_fields', 'mj_taxonomy_add_custom_meta_field', 10, 2);


/* Ajout du champs dans la l'edition d'une implantations */

function mj_taxonomy_edit_custom_meta_field($term) {
    $t_id = $term->term_id;
    $term_meta = get_option("taxonomy_$t_id");
    ?>

    <tr class="form-field">  
        <th scope="row" valign="top"><label for="term_meta[lat]"><?php _e('Latitude', 'MJ'); ?></label></th>
        <td>
            <input type="text" name="term_meta[lat]" id="term_meta[lat]" value="<?php echo esc_attr($term_meta['lat']) ? esc_attr($term_meta['lat']) : ''; ?>">
            <p class="description"><?php _e('Exemple : 14.671821', 'MJ'); ?></p>
        </td>
    </tr>

    <tr class="form-field">  
        <th scope="row" valign="top"><label for="term_meta[lng]"><?php _e('Longitude', 'MJ'); ?></label></th>
        <td>
            <input type="text" name="term_meta[lng]" id="term_meta[lng]" value="<?php echo esc_attr($term_meta['lng']) ? esc_attr($term_meta['lng']) : ''; ?>">
            <p class="description"><?php _e('Exemple :-61.1603466', 'MJ'); ?></p>
        </td>
    </tr>

    <?php
}

add_action('implantations_edit_form_fields', 'mj_taxonomy_edit_custom_meta_field', 10, 2);


/* Sauvegarde de l'implantation au niveau de la liste et de l'edition */

function mj_save_taxonomy_custom_meta_field($term_id) {
    if (isset($_POST['term_meta'])) {

        $t_id = $term_id;
        $term_meta = get_option("taxonomy_$t_id");
        $cat_keys = array_keys($_POST['term_meta']);
        foreach ($cat_keys as $key) {
            if (isset($_POST['term_meta'][$key])) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
// Save the option array.
        update_option("taxonomy_$t_id", $term_meta);
    }
}

add_action('edited_implantations', 'mj_save_taxonomy_custom_meta_field', 10, 2);
add_action('create_implantations', 'mj_save_taxonomy_custom_meta_field', 10, 2);




/* ---------------------------------------------------- */
/* -----------Customisation des colonnes------------- */
/* ---------------------------------------------------- */

function add_implantations_columns($columns) {
    $columns['statut'] = 'Statut';
    return $columns;
}

add_filter('manage_edit-implantations_columns', 'add_implantations_columns');

function add_implantations_column_content($content, $column_name, $term_id) {
    $term = get_term($term_id, 'implantations');
    $statut = get_alerte($term)['statut'];
    switch ($column_name) {
        case 'statut':
            //do your stuff here with $term or $term_id
            $content =  '<div class="voyant '.$statut.'"></div>';  ;
            break;
        
        default:
            break;
    }
    return $content;
}

add_filter('manage_implantations_custom_column', 'add_implantations_column_content', 10, 3);


/*On desactive la colonne description*/
add_filter('manage_edit-implantations_columns', function ( $columns ) 
{
    if( isset( $columns['description'] ) )
        unset( $columns['description'] );   

    return $columns;
} );