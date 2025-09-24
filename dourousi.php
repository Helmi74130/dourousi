<?php
/**
 * Plugin Name: DOUROUSI
 * Description: Permet de créer un CPT "cours" avec meta (auteur, nom du livre, PDF, image à la une, lien externe) et des chapitres audio répétables.
 * Version: 0.1.0
 * Author: Helmi
 * Text Domain: dourousi
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'DOUROUSI_VERSION', '0.1.0' );
define( 'DOUROUSI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DOUROUSI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once DOUROUSI_PLUGIN_DIR . 'includes/assets.php';
require_once DOUROUSI_PLUGIN_DIR . 'includes/cpt.php';
require_once DOUROUSI_PLUGIN_DIR . 'includes/meta-fields.php';
require_once DOUROUSI_PLUGIN_DIR . 'includes/settings.php';

add_filter('template_include', function($template) {
    if (is_singular('cours')) {
        $plugin_template = DOUROUSI_PLUGIN_DIR . 'templates/single-cours.php';
        if (file_exists($plugin_template)) return $plugin_template;
    }
    return $template;
});


// Inclure les shortcodes
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';


function dourousi_enqueue_admin_assets($hook) {
    // Vérifie qu’on est bien sur ta page d’options
    if ($hook !== 'settings_page_dourousi-options') return;

    wp_enqueue_script(
        'dourousi-shortcode-generator',
        DOUROUSI_PLUGIN_URL . 'admin/js/shortcode-generator.js',
        array(),
        DOUROUSI_VERSION,
        true
    );
}
add_action('admin_enqueue_scripts', 'dourousi_enqueue_admin_assets');
