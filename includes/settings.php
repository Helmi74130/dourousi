<?php
if (! defined('ABSPATH')) exit; // Sécurité

/**
 * 1. Création de la page d'options (Sous-menu du CPT 'cours')
 */
add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=cours',
        __('Options Dourousi', 'dourousi'),
        __('Options', 'dourousi'),
        'manage_options',
        'dourousi-settings',
        'dourousi_render_settings_page'
    );
});

/**
 * Variables globales pour les valeurs par défaut
 */
$dourousi_default_colors = [
    'color_main'       => '#2b8a3e',
    'color_secondary'  => '#ff9800',
    'color_text'       => '#333333',
    'color_text_hover' => '#555555',
    'color_background' => '#f5f5f5',
    'color_text_audio' => '#2c2c2c',
];


/**
 * 2. Enregistrement des options et des champs
 */
add_action('admin_init', function () {
    
    // Enregistre le groupe d'options 'dourousi_options'
    register_setting('dourousi_options_group', 'dourousi_options', array(
        'sanitize_callback' => 'dourousi_sanitize_options'
    ));


    // ==========================================================
    // 2.1. DÉFINITION DES SECTIONS
    // ==========================================================

    // Section 1 : Slug, Template et Affichage (Structure)
    add_settings_section(
        'dourousi_slug_section',
        __('Options de structure et de contenu', 'dourousi'),
        null, // Pas de callback de description pour cette section
        'dourousi-settings'
    );

    // Section 2 : Couleurs et Personnalisation du Lecteur
    add_settings_section(
        'dourousi_main_section',
        __('Personnalisation des couleurs du front-end', 'dourousi'),
        'dourousi_main_section_callback', // Callback de description
        'dourousi-settings'
    );


    // ==========================================================
    // 2.2. DÉFINITION DES CHAMPS PAR SECTION
    // ==========================================================

    // --- Champs de la Section 1 (dourousi_slug_section) ---

    // 1.1 Slug personnalisé
    add_settings_field(
        'custom_slug',
        __('Slug personnalisé pour les cours', 'dourousi'),
        'dourousi_custom_slug_field',
        'dourousi-settings',
        'dourousi_slug_section' 
    );

    // 1.2 Choix du template (Ajouté)
    add_settings_field(
        'single_template',
        __('Template de la page de cours', 'dourousi'),
        'dourousi_single_template_field_cb',
        'dourousi-settings',
        'dourousi_slug_section'
    );
    
    // 1.3 Afficher la section Catégories
    add_settings_field(
        'show_categories_section',
        __('Afficher la section "Explorer d\'autres thèmes" ?', 'dourousi'),
        'dourousi_show_categories_field',
        'dourousi-settings',
        'dourousi_slug_section' // CORRIGÉ : Rattaché à la section structure
    );

    // --- Champs de la Section 2 (dourousi_main_section - Couleurs) ---

    add_settings_field(
        'color_main',
        __('Couleur principale', 'dourousi'),
        'dourousi_color_main_field',
        'dourousi-settings',
        'dourousi_main_section'
    );

    add_settings_field(
        'color_secondary',
        __('Couleur secondaire', 'dourousi'),
        'dourousi_color_secondary_field',
        'dourousi-settings',
        'dourousi_main_section'
    );

    add_settings_field(
        'color_text',
        __('Couleur du texte général', 'dourousi'),
        'dourousi_color_text_field',
        'dourousi-settings',
        'dourousi_main_section'
    );

    add_settings_field(
        'color_text_hover',
        __('Couleur du texte au survol (liens/menus)', 'dourousi'),
        'dourousi_color_text_hover',
        'dourousi-settings',
        'dourousi_main_section'
    );

    add_settings_field(
        'color_background',
        __('Couleur de fond des cartes / lecteur', 'dourousi'),
        'dourousi_color_background_field',
        'dourousi-settings',
        'dourousi_main_section'
    );

    add_settings_field(
        'color_text_audio',
        __('Couleur du texte du lecteur audio', 'dourousi'),
        'dourousi_color_text_field_audio',
        'dourousi-settings',
        'dourousi_main_section'
    );
});


// ==========================================================
// 3. FONCTIONS DE CALLBACK DES CHAMPS ET SECTIONS
// ==========================================================

// Callback de description pour la section principale (couleurs)
function dourousi_main_section_callback()
{
    echo '<p>' . __('Configurez les couleurs pour harmoniser le lecteur audio et la page de cours avec votre thème.', 'dourousi') . '</p>';
}

// Champ : Slug personnalisé
function dourousi_custom_slug_field()
{
    $options = get_option('dourousi_options');
    $value = isset($options['custom_slug']) ? $options['custom_slug'] : 'cours';
    echo '<input type="text" name="dourousi_options[custom_slug]" value="' . esc_attr($value) . '" />';
    echo '<p class="description">' . __('Exemple : cours, formations, audio... Vous devez réenregistrer vos Permaliens après modification.', 'dourousi') . '</p>';
}

// Champ : Afficher les catégories (Checkbox)
function dourousi_show_categories_field()
{
    $options = get_option('dourousi_options');
    $checked = isset($options['show_categories_section']) ? (bool) $options['show_categories_section'] : false;
    echo '<label><input type="checkbox" name="dourousi_options[show_categories_section]" value="1" ' . checked(1, $checked, false) . '> ' . __('Oui, afficher cette section sur les pages de cours', 'dourousi') . '</label>';
}


// --- Fonctions utilitaires pour les couleurs ---

function dourousi_get_option_value($key)
{
    global $dourousi_default_colors;
    $options = get_option('dourousi_options');
    return isset($options[$key]) ? esc_attr($options[$key]) : $dourousi_default_colors[$key];
}

function dourousi_render_color_field($key)
{
    $value = dourousi_get_option_value($key);
    echo '<input type="color" name="dourousi_options[' . $key . ']" value="' . $value . '">';
}

// Callbacks spécifiques pour les couleurs
function dourousi_color_main_field() { dourousi_render_color_field('color_main'); }
function dourousi_color_secondary_field() { dourousi_render_color_field('color_secondary'); }
function dourousi_color_text_field() { dourousi_render_color_field('color_text'); }
function dourousi_color_text_hover() { dourousi_render_color_field('color_text_hover'); }
function dourousi_color_background_field() { dourousi_render_color_field('color_background'); }
function dourousi_color_text_field_audio() { dourousi_render_color_field('color_text_audio'); }


// --- Fonctions de gestion des templates ---

/**
 * Liste les templates disponibles (single-cours-*.php) dans /templates/
 */
function dourousi_get_available_single_templates() {
    $templates = array();
    $templates['default'] = __('Thème par défaut', 'dourousi'); // Le template de base

    // chemins possibles (si le fichier admin-options.php est dans includes/ ou à la racine)
    $try_dirs = array(
        plugin_dir_path( __FILE__ ) . 'templates/',
        plugin_dir_path( dirname(__FILE__) ) . 'templates/',
    );

    foreach ( $try_dirs as $dir ) {
        if ( is_dir( $dir ) ) {
            $files = glob( $dir . 'single-cours-*.php' );
            if ( $files ) {
                foreach ( $files as $f ) {
                    if ( preg_match('/single-cours-([a-z0-9\-_]+)\.php$/i', basename($f), $m) ) {
                        $key = $m[1];
                        // Rendre le nom plus lisible pour l'utilisateur
                        $templates[ $key ] = ucwords( str_replace( array('-', '_'), ' ', $key ) ); 
                    }
                }
            }
            if ( count( $templates ) > 1 ) break; // Arrêter si des templates personnalisés sont trouvés
        }
    }

    return $templates;
}

/**
 * Callback affichage du select du template
 */
function dourousi_single_template_field_cb() {
    $options   = get_option('dourousi_options', array());
    $current   = isset($options['single_template']) ? $options['single_template'] : 'default';
    $templates = dourousi_get_available_single_templates();

    echo '<select name="dourousi_options[single_template]">';
    foreach ( $templates as $key => $label ) {
        printf(
            '<option value="%s" %s>%s</option>',
            esc_attr($key),
            selected( $current, $key, false ),
            esc_html($label)
        );
    }
    echo '</select>';
    echo '<p class="description">'.__('Choisissez le template de la page de cours. Les fichiers doivent être nommés `single-cours-[nom].php` dans votre dossier `templates`.', 'dourousi').'</p>';
}

/**
 * Sanitize options
 */
function dourousi_sanitize_options($input) {
    $clean = array();

    // templates available
    $available = array_keys( dourousi_get_available_single_templates() );

    // slug
    if ( isset($input['custom_slug']) ) {
        $clean['custom_slug'] = sanitize_title( $input['custom_slug'] );
    }

    // colors (utilise sanitize_hex_color)
    $color_keys = array('color_main','color_secondary','color_text','color_text_hover','color_background','color_text_audio');
    foreach ($color_keys as $k) {
        if ( isset($input[$k]) ) {
            $clean[$k] = sanitize_hex_color( $input[$k] );
        }
    }

    // show categories (checkbox)
    $clean['show_categories_section'] = ! empty($input['show_categories_section']) ? 1 : 0;

    // template
    if ( isset($input['single_template']) && in_array($input['single_template'], $available, true) ) {
        $clean['single_template'] = $input['single_template'];
    } else {
        // Garantir qu'une valeur par défaut valide est sélectionnée
        $clean['single_template'] = $available[0] ?? 'default';
    }

    return $clean;
}


/**
 * 4. Fonction de rendu de la page d'options (HTML)
 * Inclut le formulaire de réglages et le générateur de shortcode stylisé.
 */
function dourousi_render_settings_page()
{
?>
<div class="wrap">
  <h1><?php _e('Options Dourousi', 'dourousi'); ?></h1>

  <div style="display: flex; gap: 30px; margin-top: 20px;">

    <div class="form-wrap-dourousi" style="flex: 2;">
      <h2><?php _e('Paramètres Généraux et de Design', 'dourousi'); ?></h2>
      <form method="post" action="options.php">
        <?php
                settings_fields('dourousi_options_group'); 
                do_settings_sections('dourousi-settings'); 
                submit_button(); 
                ?>
      </form>
    </div>

    <div class="shortcode-generator-section postbox" style="flex: 1; padding: 15px; background: #fff;">
      <h2>Générateur de shortcode Dourousi</h2>
      <p>Configurez votre shortcode pour afficher une liste de cours.</p>

      <table class="form-table">
        <tr>
          <th scope="row"><label for="shortcode-number">Nombre de cours</label></th>
          <td><input type="number" id="shortcode-number" value="6" min="1" style="width: 100%;"></td>
        </tr>

        <tr>
          <th scope="row"><label for="shortcode-layout">Layout</label></th>
          <td>
            <select id="shortcode-layout" style="width: 100%;">
              <option value="grid">Grille</option>
              <option value="carousel">Carousel</option>
              <option value="list">Liste</option>
            </select>
          </td>
        </tr>

        <tr>
          <th scope="row"><label for="shortcode-savant">Savant (slug)</label></th>
          <td><input type="text" id="shortcode-savant" placeholder="ex: ibn-baz" style="width: 100%;"></td>
        </tr>

        <tr>
          <th scope="row"></th>
          <td>
            <button type="button" class="button button-primary" id="generate-shortcode">Générer le Shortcode</button>
          </td>
        </tr>
      </table>

      <h3 style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">Votre shortcode :</h3>
      <textarea id="shortcode-result" rows="3" style="width:100%; font-family: monospace;"></textarea>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const btn = document.getElementById('generate-shortcode');
  const output = document.getElementById('shortcode-result');

  if (!btn) return;

  btn.addEventListener('click', function() {
    const number = document.getElementById('shortcode-number').value || 6;
    const layout = document.getElementById('shortcode-layout').value;
    const savant = document.getElementById('shortcode-savant').value;

    let shortcode = `[dourousi_courses number="${number}" layout="${layout}"`;
    if (savant) shortcode += ` savant="${savant}"`;
    shortcode += `]`;

    output.value = shortcode;
  });

  // Copier au clic
  output.addEventListener('focus', function() {
    this.select();
    document.execCommand('copy');
  });
});
</script>

<?php
}