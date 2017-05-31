<?php
/* ---------------------------------------------------- */
/* ----------------INCLUSION JS----------------------- */
/* ---------------------------------------------------- */
add_action('wp_enqueue_scripts', 'cc_scripts');

function cc_scripts($content) {
    global $post;
    if (has_shortcode($post->post_content, 'carte_coupures')) {
        wp_enqueue_style('cc_css', plugins_url() . '/carte-coupures-V2/front/front-css.css',188);
        wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAKSS4SHnjvG8vp6qVEyRiprmHB20hEifc');
        wp_enqueue_script('script', plugins_url() . '/carte-coupures-V2/front/generateur-map.js', array(), '1.0.', true);
    }
}

/* ---------------------------------------------------- */
/* -----------------AJAX------------------------------- */
/* ---------------------------------------------------- */

function get_implantations() {

    /* Tableau que va récupérer le JS */
    $json_response = array();
    $json_response['plugin_url'] = plugins_url('/carte-coupures-V2');
    $json_response['points'] = array();

    /* On récupère les implantations */
    $taxonomy = array('implantations');
    $args = array(
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => false,
        'exclude' => array(),
        'exclude_tree' => array(),
        'include' => array(),
        'number' => '',
        'fields' => 'all',
    );

    /* On parcours les implantation (terms) */
    $terms = get_terms($taxonomy, $args);
    if (!empty($terms)) {
        foreach ($terms as $term) {
            $term_id = $term->term_id;
     
            $term_meta = get_option("taxonomy_$term_id");
            $row_array['name'] = $term->name;
            $row_array['lat'] = $term_meta['lat'];
            $row_array['lng'] = $term_meta['lng'];

            /* On récupère le statut et la description de la dernière alert */
            $alert = get_alerte($term);
           
            $row_array['statut'] = $alert['statut'];
            $row_array['date_parution'] = $alert['date_parution'];
            $row_array['description'] = $alert['description'];

            array_push($json_response['points'], $row_array);
        }
        echo json_encode($json_response);
        
        die();
    }
}

add_action('wp_ajax_get_implantations', 'get_implantations');
add_action('wp_ajax_nopriv_get_implantations', 'get_implantations');


/* ---------------------------------------------------- */
/* -----------------Shortcode------------------------- */
/* ---------------------------------------------------- */

function carte_coupures_shortcode($post) {
    ?>
    <!-- Affichages de la map -->
    <div id="map-canvas" lat="14.6500000" long="-61.0297823" style="width:100%;"></div>
    <?php
    /* Liste */
    $args = array(
        'posts_per_page' => 20,
        'post_type' => 'alertes'
    );

    $query = new WP_Query($args);
    ob_start();
    ?>


    <?php if ($query->have_posts()): ?>
        <div class="cc-alert-container">

            <!----------Titre principal----------->
            <div class="cc-alert-titre">
                <h3>CARTE DES COUPURES</h3>
                <p><i>La SME vous informe en direct</i><p>
            </div>
            
            <!----------Legende----------->
            <div class="cc-alert-legende">
                <span><img class="cc-alert-pin" src="<?php echo plugins_url('carte-coupures-V2/front/assets/fonctionnement.png'); ?>"/> Service normal</span>
                <span><img class="cc-alert-pin" src="<?php echo plugins_url('carte-coupures-V2/front/assets/travaux.png'); ?>"/> Travaux en cours</span>
                <span><img class="cc-alert-pin" src="<?php echo plugins_url('carte-coupures-V2/front/assets/panne.png'); ?>"/> Panne en cours</span>
            </div>
            
            <!----------Tableau----------->
            <table class="cc-alert-tableau">
                <thead>
                    <tr>
                        <th><strong>Statut<strong></th>
                        <th><strong>Commune<strong></th>
                        <th><strong>Date de parution<strong></th>
                        <th><strong>Message<strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($query->have_posts()):
                        $post = $query->the_post();
                        $post_id = get_the_ID();
                        $commune = get_the_terms($post_id, 'implantations')[0];
                        ?>
                        <tr>
                            <td><img class="cc-alert-pin" src="<?php echo plugins_url('carte-coupures-V2/front/assets/' . get_post_meta($post_id, '_statut', true) . '.png'); ?>"/></td>
                            <td><strong><?php echo $commune->name ?></strong></td>
                            <td><?php echo 'Le' . get_the_date(' d  F Y \à G:i'); ?></td>
                            <td><?php echo get_the_content(); ?></td>
                        </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
             </div>
         <?php else:?>
        <?php endif ?> 
   
    <?php
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
    wp_reset_postdata();
}

add_shortcode('carte_coupures', 'carte_coupures_shortcode');

