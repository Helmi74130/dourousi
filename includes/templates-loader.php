<?php
if (! defined('ABSPATH')) exit;

/**
 * Filtre pour charger le template single selon l'option
 */
add_filter('single_template', 'dourousi_load_single_template');
function dourousi_load_single_template( $single ) {
    if ( ! is_singular('cours') ) return $single;

    $options = get_option('dourousi_options', array());
    $key = isset($options['single_template']) ? $options['single_template'] : 'default';
    $template_file = 'single-cours-' . $key . '.php';

    // 1) allow theme override: /wp-content/themes/your-theme/dourousi/<template_file>
    $theme_paths = array( 'dourousi/' . $template_file, $template_file );
    $located = locate_template( $theme_paths, false, false );
    if ( $located && file_exists($located) ) {
        return $located;
    }

    // 2) plugin templates
    $possible = array(
        plugin_dir_path( dirname(__FILE__) ) . 'templates/' . $template_file,
        plugin_dir_path( __FILE__ ) . '/../templates/' . $template_file
    );

    foreach ( $possible as $p ) {
        if ( file_exists( $p ) ) {
            return $p;
        }
    }

    return $single;
}

/**
 * Enqueue CSS spécifique au template choisi
 */
add_action('wp_enqueue_scripts', 'dourousi_enqueue_single_template_assets', 20);
function dourousi_enqueue_single_template_assets() {
    if ( ! is_singular('cours') ) return;

    $options = get_option('dourousi_options', array());
    $key = isset($options['single_template']) ? $options['single_template'] : 'default';
    $rel_css = 'templates/css/single-cours-' . $key . '.css';

    // chemin / url robustes (si includes/ est utilisé)
    $plugin_root = plugin_dir_path( dirname(__FILE__) );
    $css_full = $plugin_root . $rel_css;
    $css_url  = plugin_dir_url( dirname(__FILE__) ) . $rel_css;

    if ( file_exists( $css_full ) ) {
        wp_enqueue_style( 'dourousi-single-' . $key, $css_url, array(), defined('DOUROUSI_VERSION') ? DOUROUSI_VERSION : null );
    }
}