<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Ajouter le sous-menu "Options"
add_action('admin_menu', function() {
    add_submenu_page(
        'edit.php?post_type=cours',
        __('Options Dourousi', 'dourousi'),
        __('Options', 'dourousi'),
        'manage_options',
        'dourousi-settings',
        'dourousi_render_settings_page'
    );
});

// Enregistrer les options
add_action('admin_init', function() {
    register_setting('dourousi_options_group', 'dourousi_options');

    add_settings_section(
        'dourousi_main_section',
        __('Personnalisation du lecteur', 'dourousi'),
        null,
        'dourousi-settings'
    );

    // Text color
    add_settings_field(
        'color_text',
        __('Couleur du texte', 'dourousi'),
        'dourousi_color_text_field',
        'dourousi-settings',
        'dourousi_main_section'
    );
    // Couleur au survol
    add_settings_field(
        'color_text_hover',
        __('Couleur du texte au survol', 'dourousi'),
        'dourousi_color_text_hover',
        'dourousi-settings',
        'dourousi_main_section'
    );

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

    // Couleur de fond audio
    add_settings_field(
        'color_background',
        __('Couleur de fond du lecteur audio', 'dourousi'),
        'dourousi_color_background_field',
        'dourousi-settings',
        'dourousi_main_section'
    );
    // Couleur du texte audio
    add_settings_field(
        'color_text_audio',
        __('Couleur du texte du lecteur audio', 'dourousi'),
        'dourousi_color_text_field_audio',
        'dourousi-settings',
        'dourousi_main_section'
    );
});

// Champs input
function dourousi_color_main_field() {
    $options = get_option('dourousi_options');
    $value = isset($options['color_main']) ? $options['color_main'] : '#333333';
    echo '<input type="color" name="dourousi_options[color_main]" value="' . esc_attr($value) . '">';
}
function dourousi_color_text_hover() {
    $options = get_option('dourousi_options');
    $value = isset($options['color_text_hover']) ? $options['color_text_hover'] : '#555555';
    echo '<input type="color" name="dourousi_options[color_text_hover]" value="' . esc_attr($value) . '">';
}
function dourousi_color_text_field_audio() {
    $options = get_option('dourousi_options');
    $value = isset($options['color_text_audio']) ? $options['color_text_audio'] : '#2c2c2cff';
    echo '<input type="color" name="dourousi_options[color_text_audio]" value="' . esc_attr($value) . '">';
}



function dourousi_color_text_field() {
    $options = get_option('dourousi_options');
    $value = isset($options['color_text']) ? $options['color_text'] : '#2b8a3e';
    echo '<input type="color" name="dourousi_options[color_text]" value="' . esc_attr($value) . '">';
}

function dourousi_color_secondary_field() {
    $options = get_option('dourousi_options');
    $value = isset($options['color_secondary']) ? $options['color_secondary'] : '#ff9800';
    echo '<input type="color" name="dourousi_options[color_secondary]" value="' . esc_attr($value) . '">';
}

function dourousi_color_background_field() {
    $options = get_option('dourousi_options');
    $value = isset($options['color_background']) ? $options['color_background'] : '#f5f5f5';
    echo '<input type="color" name="dourousi_options[color_background]" value="' . esc_attr($value) . '">';
}

// Render page
function dourousi_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Options Dourousi', 'dourousi'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('dourousi_options_group');
            do_settings_sections('dourousi-settings');
            submit_button();
            ?>
        </form>
    </div>




    <div class="wrap">
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

<script>  

    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('generate-shortcode');
        const output = document.getElementById('shortcode-result');

        if (!btn) return;

        btn.addEventListener('click', function () {
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




