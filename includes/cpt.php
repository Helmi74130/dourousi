<?php

if (!defined('ABSPATH')) exit; // Sécurité: empêche l'accès direct au fichier

/**
 * Récupère le slug personnalisé pour le CPT "cours" depuis les options du plugin.
 * Utilise 'cours' comme valeur par défaut.
 *
 * @return string Le slug nettoyé.
 */
function dourousi_get_cpt_slug(): string
{
    $options = get_option('dourousi_options');
    // Utilisation de l'opérateur de coalescence nul et sanitize_title pour la sécurité
    return sanitize_title($options['custom_slug'] ?? 'cours');
}

/**
 * Enregistre le Custom Post Type "cours".
 */
function dourousi_register_cpt()
{
    $slug = dourousi_get_cpt_slug();

    $labels = array(
        'name'                  => 'Cours',
        'singular_name'         => 'Cours',
        'menu_name'             => 'Dourousi',
        'name_admin_bar'        => 'Cours',
        'add_new'               => 'Ajouter',
        'add_new_item'          => 'Ajouter un cours',
        'edit_item'             => 'Éditer le cours',
        'new_item'              => 'Nouveau cours',
        'view_item'             => 'Voir le cours',
        'search_items'          => 'Rechercher un cours',
        'not_found'             => 'Aucun cours trouvé',
        'not_found_in_trash'    => 'Aucun cours dans la corbeille',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-welcome-learn-more',
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions'),
        'has_archive'           => true,
        'rewrite'               => array('slug' => $slug),
        'show_in_rest'          => true, // Important pour l'éditeur de blocs (Gutenberg)
    );

    register_post_type('cours', $args);
}
add_action('init', 'dourousi_register_cpt');

/**
 * Enregistre les taxonomies personnalisées : difficulté, catégorie_cours, savant.
 */
function dourousi_register_taxonomies()
{
    $cpt_slug = dourousi_get_cpt_slug(); // Slug du CPT pour la cohérence des URLs de taxonomies

    // Taxonomie : Difficulté
    register_taxonomy('difficulte', ['cours'], [
        'hierarchical'      => true,
        'labels'            => [
            'name'              => 'Difficultés',
            'singular_name'     => 'Difficulté',
            'search_items'      => 'Rechercher des difficultés',
            'all_items'         => 'Toutes les difficultés',
            'edit_item'         => 'Éditer la difficulté',
            'update_item'       => 'Mettre à jour',
            'add_new_item'      => 'Ajouter une difficulté',
            'new_item_name'     => 'Nouvelle difficulté', // Ajout pour clarté dans l'UI
            'menu_name'         => 'Difficultés', // Ajout pour le menu
        ],
        'show_ui'           => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => $cpt_slug . '-difficulte'],
    ]);

    // Taxonomie : Catégorie de cours
    register_taxonomy('categorie_cours', ['cours'], [
        'hierarchical'      => true,
        'labels'            => [
            'name'              => 'Catégories de cours',
            'singular_name'     => 'Catégorie',
            'search_items'      => 'Rechercher des catégories',
            'all_items'         => 'Toutes les catégories',
            'edit_item'         => 'Éditer la catégorie',
            'update_item'       => 'Mettre à jour',
            'add_new_item'      => 'Ajouter une catégorie',
            'new_item_name'     => 'Nouvelle catégorie', // Ajout
            'menu_name'         => 'Catégories', // Ajout
        ],
        'show_ui'           => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => $cpt_slug . '-categorie'],
    ]);

    // Taxonomie : Savant
    register_taxonomy('savant', ['cours'], [
        'hierarchical'      => true, // Peut être défini sur false si les savants ne sont pas hiérarchiques
        'labels'            => [
            'name'              => 'Savants',
            'singular_name'     => 'Savant',
            'search_items'      => 'Rechercher un savant',
            'all_items'         => 'Tous les savants',
            'edit_item'         => 'Éditer le savant',
            'update_item'       => 'Mettre à jour',
            'add_new_item'      => 'Ajouter un savant',
            'new_item_name'     => 'Nouveau savant', // Ajout
            'menu_name'         => 'Savants',
        ],
        'show_ui'           => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => $cpt_slug . '-savant'],
    ]);
}
add_action('init', 'dourousi_register_taxonomies');

/**
 * Active le plugin : Enregistre CPT/Taxonomies et met à jour les règles de réécriture.
 */
function dourousi_activate()
{
    dourousi_register_cpt();
    dourousi_register_taxonomies();
    flush_rewrite_rules(); // Nécessaire après l'enregistrement des CPT/taxonomies
}
register_activation_hook(__FILE__, 'dourousi_activate');

/**
 * Désactive le plugin : Met à jour les règles de réécriture.
 */
function dourousi_deactivate()
{
    flush_rewrite_rules(); // Nettoie les règles de réécriture à la désactivation
}
register_deactivation_hook(__FILE__, 'dourousi_deactivate');

/**
 * Met à jour les règles de réécriture lorsque le slug du CPT est modifié dans les options.
 */
add_action('update_option_dourousi_options', function ($old_value, $value) {
    // Récupérer les anciens et nouveaux slugs en toute sécurité
    $old_slug = $old_value['custom_slug'] ?? '';
    $new_slug = $value['custom_slug']     ?? '';

    if ($old_slug !== $new_slug) {
        // Il est essentiel d'enregistrer à nouveau les CPT/taxonomies AVANT de flusher
        // pour que les nouvelles règles soient prises en compte.
        dourousi_register_cpt();
        dourousi_register_taxonomies();
        flush_rewrite_rules();
    }
}, 10, 2);