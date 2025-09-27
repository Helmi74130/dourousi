<?php
/**
 * Enregistrement du CPT "cours" + taxonomies
 * avec slug dynamique depuis les options.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Sécurité

/**
 * Récupération du slug personnalisé depuis les options
 */
function dourousi_get_cpt_slug() {
    $options = get_option('dourousi_options');
    return isset($options['custom_slug']) && !empty($options['custom_slug']) 
        ? sanitize_title($options['custom_slug']) 
        : 'cours';
}

/**
 * Register Custom Post Type : cours
 */
function dourousi_register_cpt() {
    $slug = dourousi_get_cpt_slug();

    $labels = array(
        'name'               => 'Cours',
        'singular_name'      => 'Cours',
        'menu_name'          => 'DOUROUSI Cours',
        'name_admin_bar'     => 'Cours',
        'add_new'            => 'Ajouter',
        'add_new_item'       => 'Ajouter un cours',
        'edit_item'          => 'Éditer le cours',
        'new_item'           => 'Nouveau cours',
        'view_item'          => 'Voir le cours',
        'search_items'       => 'Rechercher un cours',
        'not_found'          => 'Aucun cours trouvé',
        'not_found_in_trash' => 'Aucun cours dans la corbeille',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_menu'       => true,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-welcome-learn-more',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions' ),
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => $slug ),
        'show_in_rest'       => true,
    );

    register_post_type( 'cours', $args );
}
add_action( 'init', 'dourousi_register_cpt' );

/**
 * Register Taxonomies : difficulté, catégorie, savant
 */
function dourousi_register_taxonomies() {
    $slug = dourousi_get_cpt_slug(); // pour cohérence avec CPT

    // Difficulté
    register_taxonomy( 'difficulte', array( 'cours' ), array(
        'hierarchical' => true,
        'labels'       => array(
            'name' => 'Difficultés',
            'singular_name' => 'Difficulté',
            'search_items' => 'Rechercher des difficultés',
            'all_items' => 'Toutes les difficultés',
            'edit_item' => 'Éditer la difficulté',
            'update_item' => 'Mettre à jour',
            'add_new_item' => 'Ajouter une difficulté',
        ),
        'show_ui'      => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => $slug . '-difficulte' ),
    ) );

    // Catégorie du cours
    register_taxonomy( 'categorie_cours', array( 'cours' ), array(
        'hierarchical' => true,
        'labels'       => array(
            'name' => 'Catégories de cours',
            'singular_name' => 'Catégorie',
            'search_items' => 'Rechercher des catégories',
            'all_items' => 'Toutes les catégories',
            'edit_item' => 'Éditer la catégorie',
            'update_item' => 'Mettre à jour',
            'add_new_item' => 'Ajouter une catégorie',
        ),
        'show_ui'      => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => $slug . '-categorie' ),
    ) );

    // Savants
    register_taxonomy( 'savant', array( 'cours' ), array(
        'hierarchical' => true,
        'labels'       => array(
            'name' => 'Savants',
            'singular_name' => 'Savant',
            'search_items' => 'Rechercher un savant',
            'all_items' => 'Tous les savants',
            'edit_item' => 'Éditer le savant',
            'update_item' => 'Mettre à jour',
            'add_new_item' => 'Ajouter un savant',
            'menu_name' => 'Savants',
        ),
        'show_ui'      => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => $slug . '-savant' ),
    ) );
}
add_action( 'init', 'dourousi_register_taxonomies' );

/**
 * Flush rewrite rules à l’activation
 */
function dourousi_activate() {
    dourousi_register_cpt();
    dourousi_register_taxonomies();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'dourousi_activate' );

/**
 * Flush rewrite rules à la désactivation
 */
function dourousi_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'dourousi_deactivate' );

/**
 * Flush rewrite rules quand on change le slug dans les options
 */
add_action('update_option_dourousi_options', function($old_value, $value){
    $old_slug = isset($old_value['custom_slug']) ? $old_value['custom_slug'] : '';
    $new_slug = isset($value['custom_slug']) ? $value['custom_slug'] : '';

    if ($old_slug !== $new_slug) {
        // On force le réenregistrement du CPT/taxonomies
        dourousi_register_cpt();
        dourousi_register_taxonomies();

        // Puis on flush correctement
        flush_rewrite_rules();
    }
}, 10, 2);

