<?php
if (!defined('ABSPATH')) exit;

/**
 * Récupère la clé du template configuré pour les 'cours'.
 * @return string La clé du template ('default' par défaut).
 */
function dourousi_get_single_template_key(): string {
    $options = get_option('dourousi_options', []);
    return $options['single_template'] ?? 'default';
}

/**
 * Filtre pour charger le template 'single-cours' selon l'option choisie.
 *
 * @param string $single Chemin actuel du template.
 * @return string Le chemin du template à utiliser.
 */
add_filter('single_template', 'dourousi_load_single_template');
function dourousi_load_single_template(string $single): string {
    if (!is_singular('cours')) {
        return $single;
    }

    $key = dourousi_get_single_template_key();
    $template_file = "single-cours-{$key}.php";
    $template_path = plugin_dir_path(dirname(__FILE__)) . 'templates/';

    // 1. Recherche dans le thème pour un override (priorité élevée)
    $located = locate_template(
        [
            "dourousi/{$template_file}",
            $template_file
        ]
    );

    if ($located) {
        return $located;
    }

    // 2. Recherche dans les fichiers de template du plugin
    $plugin_template = $template_path . $template_file;
    if (file_exists($plugin_template)) {
        return $plugin_template;
    }

    // Retourne le template original si rien n'est trouvé
    return $single;
}

// ----------------------------------------------------------------------

/**
 * Enqueue le CSS spécifique au template 'single-cours' choisi.
 */
add_action('wp_enqueue_scripts', 'dourousi_enqueue_single_template_assets', 20);
function dourousi_enqueue_single_template_assets(): void {
    if (!is_singular('cours')) {
        return;
    }

    $key = dourousi_get_single_template_key();
    $relative_css_path = "templates/css/single-cours-{$key}.css";
    
    $plugin_dir_url = plugin_dir_url(dirname(__FILE__));
    $plugin_dir_path = plugin_dir_path(dirname(__FILE__));

    $css_url = $plugin_dir_url . $relative_css_path;
    $css_full_path = $plugin_dir_path . $relative_css_path;
    
    // Vérifie si le fichier CSS existe avant de l'ajouter
    if (file_exists($css_full_path)) {
        // Utilisation de la constante de version si elle est définie
        $version = defined('DOUROUSI_VERSION') ? DOUROUSI_VERSION : null;
        
        wp_enqueue_style(
            "dourousi-single-{$key}",
            $css_url,
            [], // Pas de dépendances spécifiques
            $version
        );
    }
}