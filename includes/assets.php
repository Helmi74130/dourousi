<?php
if (!defined('ABSPATH')) exit;

/**
 * Enqueue les scripts et styles front-end pour le plugin Dourousi.
 * Inclut les styles principaux, les variables CSS dynamiques, Plyr et Font Awesome.
 */
function dourousi_enqueue_front_assets() {
    // Conditions pour charger les assets front-end
    $should_enqueue = is_singular('cours') || is_post_type_archive('cours') || is_tax(array('savant', 'difficulte', 'categorie_cours'));

    if ($should_enqueue) {
        // Enqueue les styles principaux du plugin
        wp_enqueue_style(
            'dourousi-front',
            DOUROUSI_PLUGIN_URL . 'css/dourousi-front.css',
            array(),
            DOUROUSI_VERSION
        );

        // Récupérer les options du plugin ou utiliser les valeurs par défaut
        $options = get_option('dourousi_options');

        $color_text       = $options['color_text']       ?? '#000000';
        $color_main       = $options['color_main']       ?? '#2b8a3e';
        $color_secondary  = $options['color_secondary']  ?? '#ff9800';
        $color_background = $options['color_background'] ?? '#f5f5f5';
        $color_text_audio = $options['color_text_audio'] ?? '#2c2c2cff';
        // $color_text_hover n'est pas utilisé dans :root, mais gardé en commentaire si besoin futur
        // $color_text_hover = $options['color_text_hover'] ?? '#555555';

        // Générer le CSS inline avec les variables personnalisées
        $custom_css = "
            :root {
                --dourousi-color-text: {$color_text};
                --dourousi-color-main: {$color_main};
                --dourousi-color-secondary: {$color_secondary};
                --dourousi-color-background: {$color_background};
                --dourousi-color-text-audio: {$color_text_audio};
            }";

        wp_add_inline_style('dourousi-front', $custom_css);

        // Enqueue la bibliothèque Plyr pour les lecteurs audio/vidéo
        wp_enqueue_style(
            'plyr-css',
            'https://cdn.plyr.io/3.8.3/plyr.css',
            array(),
            '3.8.3'
        );
        wp_enqueue_script(
            'plyr-js',
            'https://cdn.plyr.io/3.8.3/plyr.polyfilled.js',
            array(),
            '3.8.3',
            true 
        );

        // Enqueue notre JS front-end, dépendant de Plyr et jQuery
        wp_enqueue_script(
            'dourousi-front-js',
            DOUROUSI_PLUGIN_URL . 'js/dourousi-front.js',
            array('plyr-js', 'jquery'),
            DOUROUSI_VERSION,
            true 
        );
    }

    // Enqueue Font Awesome s'il n'est pas déjà présent
    if (!wp_style_is('font-awesome', 'enqueued') && !wp_style_is('font-awesome', 'registered')) {
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
            array(),
            '6.5.2'
        );
    }
}
add_action('wp_enqueue_scripts', 'dourousi_enqueue_front_assets');

/**
 * Enqueue les styles pour la page d'administration des réglages du plugin.
 */
function dourousi_enqueue_admin_settings_css($hook) {
    $correct_hook = 'cours_page_dourousi-settings';

    if ($hook === $correct_hook) {
        wp_enqueue_style(
            'dourousi-option-admin-css',
            DOUROUSI_PLUGIN_URL . 'css/option-admin.css',
            array(),
            DOUROUSI_VERSION
        );
    }
}
add_action('admin_enqueue_scripts', 'dourousi_enqueue_admin_settings_css');