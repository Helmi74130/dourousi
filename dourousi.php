<?php
/**
 * Plugin Name: Dourousi
 * Description: Permet de créer un CPT "cours" avec meta (auteur, nom du livre, PDF, image à la une, lien externe) et des chapitres audio répétables.
 * Version: 1.0.0
 * Author: Helmi
 * Text Domain: dourousi
 */

if (!defined('ABSPATH')) {
    exit;
}

// --- CONSTANTES ---
define('DOUROUSI_VERSION', '0.1.0');
define('DOUROUSI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DOUROUSI_PLUGIN_URL', plugin_dir_url(__FILE__));

// --- INCLUSIONS DES FICHIERS ---
$includes = [
    'assets.php',
    'cpt.php',
    'meta-fields.php',
    'settings.php',
    'time-listen.php',
    'templates-loader.php', // Contient la logique single_template filtrée
    'shortcodes.php',
];

foreach ($includes as $file) {
    require_once DOUROUSI_PLUGIN_DIR . 'includes/' . $file;
}

// --- LOGIQUE DE TEMPLATING (Archive & Single de secours) ---

/**
 * Charge les templates d'archive et de single par défaut du plugin
 * si le thème n'en fournit pas.
 *
 * NOTE: Le template 'single-cours' devrait idéalement être géré par includes/templates-loader.php
 * qui a la logique pour charger les templates alternatifs. Ceci est un fallback.
 *
 * @param string $template Chemin du template actuel.
 * @return string
 */
add_filter('template_include', 'dourousi_load_templates');
function dourousi_load_templates(string $template): string {
    // 1. Template d'archive 'cours'
    if (is_post_type_archive('cours')) {
        $plugin_template = DOUROUSI_PLUGIN_DIR . 'templates/archive-cours.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    
    // 2. Template single 'cours' (Fallback)
    if (is_singular('cours')) {
        $plugin_template = DOUROUSI_PLUGIN_DIR . 'templates/single-cours.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    
    return $template;
}

// --- ASSETS ET BLOCS ---

/**
 * Enqueue les styles spécifiques à l'archive du CPT 'cours'.
 */
function dourousi_enqueue_archive_styles(): void {
    if (is_post_type_archive('cours')) {
        wp_enqueue_style(
            'dourousi-archive', 
            DOUROUSI_PLUGIN_URL . 'css/archive-cours.css',
            [],
            DOUROUSI_VERSION // Utilisation de la constante de version
        );
    }
}
add_action('wp_enqueue_scripts', 'dourousi_enqueue_archive_styles');

/**
 * Enregistre les blocs Gutenberg personnalisés.
 */
function dourousi_register_blocks(): void {
    $block_script_path = 'blocks/dourousi-courses-block.js';
    
    wp_register_script(
        'dourousi-courses-block',
        DOUROUSI_PLUGIN_URL . $block_script_path,
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
        filemtime(DOUROUSI_PLUGIN_DIR . $block_script_path) // Utilisation de filemtime pour la mise en cache
    );

    register_block_type('dourousi/courses', [
        'editor_script' => 'dourousi-courses-block',
    ]);
}
add_action('init', 'dourousi_register_blocks');