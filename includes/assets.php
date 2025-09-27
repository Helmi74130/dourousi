<?php
/**
 * Charger le CSS front-end du plugin
 */
function dourousi_enqueue_front_css() {
    if (is_singular('cours')) {
        wp_enqueue_style(
            'dourousi-front',
            DOUROUSI_PLUGIN_URL . 'css/dourousi-front.css',
            array(),
            DOUROUSI_VERSION
        );
         // Récupérer l’option (ou valeur par défaut)
            $options = get_option('dourousi_options');

            $color_text       = isset($options['color_text']) ? $options['color_text'] : '#000000';
            $color_main       = isset($options['color_main']) ? $options['color_main'] : '#2b8a3e';
            $color_secondary  = isset($options['color_secondary']) ? $options['color_secondary'] : '#ff9800';
            $color_background = isset($options['color_background']) ? $options['color_background'] : '#f5f5f5';
            $color_text_audio = isset($options['color_text_audio']) ? $options['color_text_audio'] : '#2c2c2cff';
            $color_text_hover = isset($options['color_text_hover']) ? $options['color_text_hover'] : '#555555';

            $custom_css = ":root {
                --dourousi-color-text: {$color_text};
                --dourousi-color-main: {$color_main};
                --dourousi-color-secondary: {$color_secondary};
                --dourousi-color-background: {$color_background};
                --dourousi-color-text-audio: {$color_text_audio};
                    
            }";

            wp_add_inline_style('dourousi-front', $custom_css);
    }
}
add_action('wp_enqueue_scripts', 'dourousi_enqueue_front_css');



/**
 * Charger la  bibliothèque Plyr pour les lecteurs audio
 */

function dourousi_enqueue_plyr() {
    if (is_singular('cours')) {
        // CSS de Plyr
        wp_enqueue_style(
            'plyr-css',
            'https://cdn.plyr.io/3.8.3/plyr.css',
            array(),
            '3.8.3'
        );

        // JS de Plyr
        wp_enqueue_script(
            'plyr-js',
            'https://cdn.plyr.io/3.8.3/plyr.polyfilled.js',
            array(),
            '3.8.3',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'dourousi_enqueue_plyr');

// Notre JS front (si besoin)

function dourousi_enqueue_front_js() {
    if (is_singular('cours')) {
        wp_enqueue_script(
            'dourousi-front-js',
            DOUROUSI_PLUGIN_URL . 'js/dourousi-front.js',
            array('plyr-js', 'jquery'),
            DOUROUSI_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'dourousi_enqueue_front_js');


function dourousi_enqueu_front_settings_css($hook) {
    // Le hook correct est 'cpt_page_slug-de-la-sous-page'
    // Ici, le CPT est 'cours' et le slug est 'dourousi-settings'
    $correct_hook = 'cours_page_dourousi-settings';

    if ( $hook === $correct_hook ) {
        wp_enqueue_style(
            'dourousi-option-admin-css', // handle
            DOUROUSI_PLUGIN_URL . 'css/option-admin.css', // chemin vers ton CSS
            array(),
            DOUROUSI_VERSION
        );
    }
}

add_action('admin_enqueue_scripts', 'dourousi_enqueu_front_settings_css');