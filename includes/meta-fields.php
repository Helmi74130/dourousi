<?php

if (!defined('ABSPATH')) exit;

/**
 * Ajoute la meta box pour les détails du cours.
 */
function dourousi_add_meta_boxes() {
    add_meta_box(
        'dourousi_course_details',
        'Détails du cours Dourousi', // Titre de la meta box
        'dourousi_course_details_callback',
        'cours', // CPT concerné
        'normal', // Contexte
        'high'    // Priorité
    );
}
add_action('add_meta_boxes', 'dourousi_add_meta_boxes');

/**
 * Contenu HTML de la meta box des détails du cours.
 *
 * @param WP_Post $post L'objet post actuel.
 */
function dourousi_course_details_callback($post) {
    wp_nonce_field('dourousi_save_meta', 'dourousi_meta_nonce');

    // Récupérer les métas existants, avec des valeurs par défaut sécurisées
    $commentateur = get_post_meta($post->ID, '_dourousi_commentateur', true);
    $nom_livre    = get_post_meta($post->ID, '_dourousi_nom_livre', true);
    $is_complete  = get_post_meta($post->ID, '_dourousi_is_complete', true);
    $pdf_id       = (int) get_post_meta($post->ID, '_dourousi_pdf_id', true);
    $external     = get_post_meta($post->ID, '_dourousi_external', true);
    $chapters     = get_post_meta($post->ID, '_dourousi_chapters', true) ?: []; // Assure que c'est un tableau

    ?>
<div class="dourousi-course-details">
  <h4>Informations principales</h4>
  <p>
    <label for="dourousi_commentateur">Commentateur du livre</label>
    <input type="text" id="dourousi_commentateur" name="dourousi_commentateur"
      value="<?php echo esc_attr($commentateur); ?>" class="widefat" />
  </p>

  <p>
    <label for="dourousi_nom_livre">Nom du livre</label>
    <input type="text" id="dourousi_nom_livre" name="dourousi_nom_livre" value="<?php echo esc_attr($nom_livre); ?>"
      class="widefat" />
  </p>

  <p>
    <label>
      <input type="checkbox" id="dourousi_is_complete" name="dourousi_is_complete" value="1"
        <?php checked($is_complete, '1'); ?> />
      Cours complet ?
    </label>
  </p>

  <p>
    <label>PDF à télécharger</label>
    <input type="hidden" id="dourousi_pdf_id" name="dourousi_pdf_id" value="<?php echo esc_attr($pdf_id); ?>" />
    <button class="button dourousi-select-pdf" type="button">Choisir / remplacer le PDF</button>
    <span class="dourousi-pdf-display">
      <?php echo $pdf_id ? esc_html(get_the_title($pdf_id)) : 'Aucun PDF sélectionné'; ?>
    </span>
    <small class="description">Le fichier sera ajouté via la bibliothèque média (recommandé).</small>
  </p>

  <p>
    <label for="dourousi_external">Lien ressources externes</label>
    <input type="url" id="dourousi_external" name="dourousi_external" value="<?php echo esc_attr($external); ?>"
      placeholder="https://..." class="widefat" />
  </p>

  <hr />
  <h4>Chapitres audio</h4>

  <div id="dourousi_chapters_container">
    <?php
            foreach ($chapters as $index => $ch) :
                $title    = $ch['title']    ?? '';
                $audio_id = (int) ($ch['audio_id'] ?? 0);
                ?>
    <div class="dourousi-chapter-row">
      <h5>Chapitre #<?php echo esc_html($index + 1); ?></h5>
      <p>
        <label for="dourousi_chapters_<?php echo $index; ?>_title">Titre du chapitre</label>
        <input type="text" id="dourousi_chapters_<?php echo $index; ?>_title"
          name="dourousi_chapters[<?php echo $index; ?>][title]" value="<?php echo esc_attr($title); ?>"
          class="widefat" />
      </p>
      <p>
        <input type="hidden" name="dourousi_chapters[<?php echo $index; ?>][audio_id]" class="dourousi_audio_id"
          value="<?php echo esc_attr($audio_id); ?>" />
        <button class="button dourousi-select-audio" type="button">Choisir audio</button>
        <span class="dourousi-audio-display">
          <?php echo $audio_id ? esc_html(get_the_title($audio_id)) : 'Aucun audio'; ?>
        </span>
        <button class="button dourousi-remove-chapter" type="button">Supprimer</button>
      </p>
    </div>
    <?php endforeach; ?>
  </div>

  <div id="dourousi_chapter_template" style="display:none;">
    <div class="dourousi-chapter-row">
      <h5>Chapitre #__index__</h5>
      <p>
        <label for="dourousi_chapters___index___title">Titre du chapitre</label>
        <input type="text" id="dourousi_chapters___index___title" name="dourousi_chapters[__index__][title]" value=""
          class="widefat" />
      </p>
      <p>
        <input type="hidden" name="dourousi_chapters[__index__][audio_id]" class="dourousi_audio_id" value="" />
        <button class="button dourousi-select-audio" type="button">Choisir audio</button>
        <span class="dourousi-audio-display">Aucun audio</span>
        <button class="button dourousi-remove-chapter" type="button">Supprimer</button>
      </p>
    </div>
  </div>
  <p><button class="button button-primary" id="dourousi_add_chapter" type="button">Ajouter un chapitre</button></p>
</div>

<style>
/* Styles admin pour améliorer la lisibilité */
.dourousi-course-details label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
}

.dourousi-course-details input[type="text"],
.dourousi-course-details input[type="url"] {
  margin-bottom: 10px;
}

.dourousi-chapter-row {
  border: 1px solid #ccc;
  padding: 10px;
  margin-bottom: 15px;
  background: #f9f9f9;
  border-radius: 5px;
}

.dourousi-chapter-row h5 {
  margin-top: 0;
  margin-bottom: 15px;
  border-bottom: 1px solid #eee;
  padding-bottom: 5px;
}

.dourousi-chapter-row .button {
  margin-right: 5px;
}
</style>
<?php
}

/**
 * Enqueue les scripts et styles d'administration.
 * Charge les scripts de la médiathèque de WordPress et le JS/CSS personnalisé du plugin.
 *
 * @param string $hook Le hook de la page d'administration actuelle.
 */
function dourousi_admin_enqueue($hook) {
    // Ne charger les assets que sur les écrans d'édition/ajout du CPT 'cours'
    if (in_array($hook, ['post-new.php', 'post.php'])) {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === 'cours') {
            wp_enqueue_media(); // Charge les scripts pour l'uploader de médias
            wp_enqueue_script(
                'dourousi-admin-js',
                DOUROUSI_PLUGIN_URL . 'js/dourousi-admin.js',
                ['jquery'], // Dépendance à jQuery
                DOUROUSI_VERSION,
                true // Charge dans le footer
            );

            // Passe des données PHP au script JS
            wp_localize_script(
                'dourousi-admin-js',
                'dourousi_admin',
                [
                    'pluginUrl' => DOUROUSI_PLUGIN_URL,
                    'nonce'     => wp_create_nonce('dourousi_admin_nonce'), // Nonce pour les requêtes AJAX potentielles
                    'i18n'      => [ // Internationalisation pour le JS
                        'pdfTitle'   => 'Sélectionner un PDF',
                        'pdfButton'  => 'Utiliser ce PDF',
                        'audioTitle' => 'Sélectionner un fichier audio',
                        'audioButton'=> 'Utiliser cet audio',
                        'noPdf'      => 'Aucun PDF sélectionné',
                        'noAudio'    => 'Aucun audio',
                    ]
                ]
            );
            wp_enqueue_style(
                'dourousi-admin-css',
                DOUROUSI_PLUGIN_URL . 'css/dourousi-admin.css',
                [],
                DOUROUSI_VERSION
            );
        }
    }
}
add_action('admin_enqueue_scripts', 'dourousi_admin_enqueue');

/**
 * Sauvegarde les données de la meta box lorsque le post est enregistré.
 *
 * @param int $post_id L'ID du post.
 * @return void
 */
function dourousi_save_meta($post_id) {
    // Vérifications de sécurité essentielles
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['dourousi_meta_nonce']) || !wp_verify_nonce($_POST['dourousi_meta_nonce'], 'dourousi_save_meta')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['post_type']) && $_POST['post_type'] !== 'cours') return;

    // Définir une fonction utilitaire pour la gestion des métas
    $manage_meta = function($key, $sanitize_callback, $default = null) use ($post_id) {
        $meta_key = '_dourousi_' . $key;
        if (isset($_POST['dourousi_' . $key])) {
            $value = call_user_func($sanitize_callback, wp_unslash($_POST['dourousi_' . $key]));
            if ($value !== $default) { // Si la valeur est différente de la valeur par défaut ou absente, on update
                update_post_meta($post_id, $meta_key, $value);
            } else { // Sinon, on supprime si c'est la valeur par défaut / vide
                delete_post_meta($post_id, $meta_key);
            }
        } else {
            delete_post_meta($post_id, $meta_key);
        }
    };

    // Sanitize et sauvegarde des champs simples
    $manage_meta('commentateur', 'sanitize_text_field');
    $manage_meta('nom_livre', 'sanitize_text_field');
    $manage_meta('external', 'esc_url_raw');
    $manage_meta('pdf_id', 'absint', 0); // pdf_id est un entier, 0 est la valeur par défaut

    // Champ "Cours complet" (checkbox)
    $is_complete = isset($_POST['dourousi_is_complete']) && $_POST['dourousi_is_complete'] === '1' ? '1' : '0';
    update_post_meta($post_id, '_dourousi_is_complete', $is_complete);


    // Gestion des chapitres (répéteur)
    if (isset($_POST['dourousi_chapters']) && is_array($_POST['dourousi_chapters'])) {
        $chapters_raw = $_POST['dourousi_chapters'];
        $chapters     = [];

        foreach ($chapters_raw as $ch_data) {
            $title    = sanitize_text_field(wp_unslash($ch_data['title'] ?? ''));
            $audio_id = absint($ch_data['audio_id'] ?? 0); // absint pour s'assurer que c'est un entier positif

            // N'enregistre que les chapitres avec un titre ou un audio
            if ($title !== '' || $audio_id > 0) {
                $chapters[] = [
                    'title'    => $title,
                    'audio_id' => $audio_id,
                ];
            }
        }
        if (!empty($chapters)) {
            update_post_meta($post_id, '_dourousi_chapters', $chapters);
        } else {
            delete_post_meta($post_id, '_dourousi_chapters');
        }
    } else {
        delete_post_meta($post_id, '_dourousi_chapters');
    }
}
add_action('save_post', 'dourousi_save_meta');