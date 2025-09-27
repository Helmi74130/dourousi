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
 * 2. Enregistrement des options et des champs
 */
add_action('admin_init', function () {
    // Enregistre le groupe d'options 'dourousi_options'
    register_setting('dourousi_options_group', 'dourousi_options');

    // Section Slug
    add_settings_section(
        'dourousi_slug_section',
        __('Paramètres du slug', 'dourousi'),
        null,
        'dourousi-settings'
    );

    // Section principale pour la personnalisation du lecteur
    add_settings_section(
        'dourousi_main_section',
        __('Personnalisation des couleurs du lecteur audio (Front-end)', 'dourousi'),
        'dourousi_main_section_callback', // Callback pour une description facultative de la section
        'dourousi-settings'
    );


    add_settings_field(
        'custom_slug',
        __('Slug personnalisé pour les cours', 'dourousi'),
        'dourousi_custom_slug_field',
        'dourousi-settings',
        'dourousi_slug_section'
    );


    // Description facultative de la section
    function dourousi_main_section_callback()
    {
        echo '<p>' . __('Configurez les couleurs utilisées par le lecteur audio et la page.', 'dourousi') . '</p>';
    }

    // --- Champs de couleur ---

    // Couleur principale
    add_settings_field(
        'color_main',
        __('Couleur principale', 'dourousi'),
        'dourousi_color_main_field',
        'dourousi-settings',
        'dourousi_main_section'
    );

    // Couleur secondaire
    add_settings_field(
        'color_secondary',
        __('Couleur secondaire', 'dourousi'),
        'dourousi_color_secondary_field',
        'dourousi-settings',
        'dourousi_main_section'
    );

    // Couleur du texte standard
    add_settings_field(
        'color_text',
        __('Couleur du texte général', 'dourousi'),
        'dourousi_color_text_field',
        'dourousi-settings',
        'dourousi_main_section'
    );

    // Couleur du texte au survol
    add_settings_field(
        'color_text_hover',
        __('Couleur du texte au survol', 'dourousi'),
        'dourousi_color_text_hover',
        'dourousi-settings',
        'dourousi_main_section'
    );

    // Couleur de fond du lecteur
    add_settings_field(
        'color_background',
        __('Couleur de fond du lecteur audio', 'dourousi'),
        'dourousi_color_background_field',
        'dourousi-settings',
        'dourousi_main_section'
    );

    // Couleur du texte du lecteur audio
    add_settings_field(
        'color_text_audio',
        __('Couleur du texte du lecteur audio', 'dourousi'),
        'dourousi_color_text_field_audio',
        'dourousi-settings',
        'dourousi_main_section'
    );
});

// Champ Slug
function dourousi_custom_slug_field()
{
    $options = get_option('dourousi_options');
    $value = isset($options['custom_slug']) ? $options['custom_slug'] : 'cours';
    echo '<input type="text" name="dourousi_options[custom_slug]" value="' . esc_attr($value) . '" />';
    echo '<p class="description">' . __('Exemple : cours, formations, audio...', 'dourousi') . '</p>';
}

/**
 * 3. Callbacks des champs de couleur (Input HTML)
 */
$dourousi_default_colors = [
    'color_main'       => '#2b8a3e',
    'color_secondary'  => '#ff9800',
    'color_text'       => '#333333',
    'color_text_hover' => '#555555',
    'color_background' => '#f5f5f5',
    'color_text_audio' => '#2c2c2cff',
];

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

function dourousi_color_main_field()
{
    dourousi_render_color_field('color_main');
}
function dourousi_color_secondary_field()
{
    dourousi_render_color_field('color_secondary');
}
function dourousi_color_text_field()
{
    dourousi_render_color_field('color_text');
}
function dourousi_color_text_hover()
{
    dourousi_render_color_field('color_text_hover');
}
function dourousi_color_background_field()
{
    dourousi_render_color_field('color_background');
}
function dourousi_color_text_field_audio()
{
    dourousi_render_color_field('color_text_audio');
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

  <div class="form-wrap-dourousi">
    <h2><?php _e('Personnalisation de Dourousi', 'dourousi'); ?></h2>
    <form method="post" action="options.php">
      <?php
                settings_fields('dourousi_options_group'); // Champ caché de sécurité et groupe d'options
                do_settings_sections('dourousi-settings'); // Affichage des sections et des champs
                submit_button(); // Bouton "Enregistrer les modifications"
                ?>
    </form>
  </div>

  <div class="shortcode-generator-section">
    <h2>Générateur de shortcode Dourousi</h2>
    <p>Configurez votre shortcode et copiez-le dans vos pages ou articles.</p>

    <table class="form-table">
      <tr>
        <th scope="row"><label for="shortcode-number">Nombre de cours</label></th>
        <td><input type="number" id="shortcode-number" value="6" min="1"></td>
      </tr>

      <tr>
        <th scope="row"><label for="shortcode-layout">Layout</label></th>
        <td>
          <select id="shortcode-layout">
            <option value="grid">Grille</option>
            <option value="carousel">Carousel</option>
            <option value="list">Liste</option>
          </select>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="shortcode-savant">Savant (slug)</label></th>
        <td><input type="text" id="shortcode-savant" placeholder="ex: ibn-baz"></td>
      </tr>

      <tr>
        <th scope="row"></th>
        <td>
          <button type="button" class="button button-primary" id="generate-shortcode">Générer</button>
        </td>
      </tr>
    </table>

    <h3>Votre shortcode :</h3>
    <textarea id="shortcode-result" rows="3" style="width:100%;"></textarea>
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

});
</script>




<?php
}