<?php
/**
 * Register Custom Post Type : 'cours'
 */
function dourousi_register_cpt() {
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
        'menu_icon'          => 'dashicons-media-audio',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions' ),
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'cours' ),
        'show_in_rest'       => true,
    );

    register_post_type( 'cours', $args );
}
add_action( 'init', 'dourousi_register_cpt' );

/**
 * Register taxonomies : difficulty + category + savant
 */
function dourousi_register_taxonomies() {
    // Difficulté (ex: débutant / intermédiaire / avancé)
    $labels = array(
        'name' => 'Difficultés',
        'singular_name' => 'Difficulté',
        'search_items' => 'Rechercher des difficultés',
        'all_items' => 'Toutes les difficultés',
        'edit_item' => 'Éditer la difficulté',
        'update_item' => 'Mettre à jour',
        'add_new_item' => 'Ajouter une difficulté',
    );

    register_taxonomy( 'difficulte', array( 'cours' ), array(
        'hierarchical' => true,
        'labels'       => $labels,
        'show_ui'      => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'difficulte' ),
    ) );

    // Catégorie du cours
    $labels2 = array(
        'name' => 'Catégories de cours',
        'singular_name' => 'Catégorie',
        'search_items' => 'Rechercher des catégories',
        'all_items' => 'Toutes les catégories',
        'edit_item' => 'Éditer la catégorie',
        'update_item' => 'Mettre à jour',
        'add_new_item' => 'Ajouter une catégorie',
    );

    register_taxonomy( 'categorie_cours', array( 'cours' ), array(
        'hierarchical' => true,
        'labels'       => $labels2,
        'show_ui'      => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'categorie-cours' ),
    ) );

    // Savants
    $labels3 = array(
        'name' => 'Savants',
        'singular_name' => 'Savant',
        'search_items' => 'Rechercher un savant',
        'all_items' => 'Tous les savants',
        'edit_item' => 'Éditer le savant',
        'update_item' => 'Mettre à jour',
        'add_new_item' => 'Ajouter un savant',
        'menu_name' => 'Savants',
    );

    register_taxonomy( 'savant', array( 'cours' ), array(
        'hierarchical' => true, // on peut mettre "true" si tu veux un fonctionnement comme les catégories
        'labels'       => $labels3,
        'show_ui'      => true,
        'show_in_rest' => true, // important si tu veux les gérer via Gutenberg/REST API
        'rewrite'      => array( 'slug' => 'savant' ),
    ) );
}
add_action( 'init', 'dourousi_register_taxonomies' );



function dourousi_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'dourousi_deactivate' );