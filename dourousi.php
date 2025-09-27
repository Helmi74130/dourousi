<?php

/**
 * Plugin Name: Dourousi
 * Description: Permet de créer un CPT "cours" avec meta (auteur, nom du livre, PDF, image à la une, lien externe) et des chapitres audio répétables.
 * Version: 0.1.0
 * Author: Helmi
 * Text Domain: dourousi
 */

if (! defined('ABSPATH')) {
    exit;
}

define('DOUROUSI_VERSION', '0.1.0');
define('DOUROUSI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DOUROUSI_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once DOUROUSI_PLUGIN_DIR . 'includes/assets.php';
require_once DOUROUSI_PLUGIN_DIR . 'includes/cpt.php';
require_once DOUROUSI_PLUGIN_DIR . 'includes/meta-fields.php';
require_once DOUROUSI_PLUGIN_DIR . 'includes/settings.php';
require_once DOUROUSI_PLUGIN_DIR . 'includes/time-listen.php';

add_filter('template_include', function ($template) {
    if (is_singular('cours')) {
        $plugin_template = DOUROUSI_PLUGIN_DIR . 'templates/single-cours.php';
        if (file_exists($plugin_template)) return $plugin_template;
    }
    return $template;
});


// Inclure les shortcodes
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';


add_filter('template_include', 'dourousi_load_archive_template');

function dourousi_load_archive_template($template)
{
    if (is_post_type_archive('cours')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/archive-cours.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}

function dourousi_enqueue_styles()
{
    if (is_post_type_archive('cours')) {
        wp_enqueue_style('dourousi-archive', plugin_dir_url(__FILE__) . 'css/archive-cours.css');
    }
}
add_action('wp_enqueue_scripts', 'dourousi_enqueue_styles');

function dourousi_register_blocks()
{
    wp_register_script(
        'dourousi-courses-block',
        plugin_dir_url(__FILE__) . 'blocks/dourousi-courses-block.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        filemtime(plugin_dir_path(__FILE__) . 'blocks/dourousi-courses-block.js')
    );

    register_block_type('dourousi/courses', array(
        'editor_script' => 'dourousi-courses-block',
    ));
}
add_action('init', 'dourousi_register_blocks');