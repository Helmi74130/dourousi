<?php 

/**
 * Add meta box for course details (author, book, pdf, external link, chapters)
 */
function dourousi_add_meta_boxes() {
    add_meta_box(
        'dourousi_course_details',
        'Détails du cours DOUROUSI',
        'dourousi_course_details_callback',
        'cours',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'dourousi_add_meta_boxes' );

/**
 * Meta box HTML
 */
function dourousi_course_details_callback( $post ) {
    wp_nonce_field( 'dourousi_save_meta', 'dourousi_meta_nonce' );

    // Récupérer les métas existants
    $commentateur = get_post_meta( $post->ID, '_dourousi_commentateur', true );
    $nom_livre = get_post_meta( $post->ID, '_dourousi_nom_livre', true );
    $pdf_id = intval( get_post_meta( $post->ID, '_dourousi_pdf_id', true ) );
    $external = get_post_meta( $post->ID, '_dourousi_external', true );
    $chapters = get_post_meta( $post->ID, '_dourousi_chapters', true ); // array of arrays

    if ( ! is_array( $chapters ) ) {
        $chapters = array();
    }

    // Affichage des champs
    ?>
<div class="dourousi-course-details">
  <h4>Informations principales</h4>
  <p>
    <label for="dourousi_commentateur">Commentateur du livre</label>
    <input type="text" id="dourousi_commentateur" name="dourousi_commentateur"
      value="<?php echo esc_attr( $commentateur ); ?>" />
  </p>

  <p>
    <label for="dourousi_nom_livre">Nom du livre</label>
    <input type="text" id="dourousi_nom_livre" name="dourousi_nom_livre"
      value="<?php echo esc_attr( $nom_livre ); ?>" />
  </p>

  <p>
    <?php $is_complete = get_post_meta( $post->ID, '_dourousi_is_complete', true ); ?>
    <label>
      <input type="checkbox" id="dourousi_is_complete" name="dourousi_is_complete" value="1"
        <?php checked( $is_complete, '1' ); ?> />
      Cours complet ?
    </label>
  </p>


  <p>
    <label>PDF à télécharger</label>
    <input type="hidden" id="dourousi_pdf_id" name="dourousi_pdf_id" value="<?php echo $pdf_id; ?>" />
    <button class="button dourousi-select-pdf" type="button">Choisir / remplacer le PDF</button>
    <span
      class="dourousi-pdf-display"><?php echo $pdf_id ? esc_html(get_the_title($pdf_id)) : 'Aucun PDF sélectionné'; ?></span>
    <small class="description">Le fichier sera ajouté via la bibliothèque média (recommandé).</small>
  </p>

  <p>
    <label for="dourousi_external">Lien ressources externes</label>
    <input type="url" id="dourousi_external" name="dourousi_external" value="<?php echo esc_attr($external); ?>"
      placeholder="https://..." />
  </p>

  <hr />
  <h4>Chapitres audio</h4>


  <div id="dourousi_chapters_container">
    <?php
        foreach ( $chapters as $index => $ch ) :
            $title = isset( $ch['title'] ) ? $ch['title'] : '';
            $audio_id = isset( $ch['audio_id'] ) ? intval( $ch['audio_id'] ) : 0;
            ?>
    <div class="dourousi-chapter-row">
      <h5>Chapitre #<?php echo esc_html($index + 1); ?></h5>
      <p>
        <label>Titre du chapitre</label>
        <input type="text" name="dourousi_chapters[<?php echo $index; ?>][title]"
          value="<?php echo esc_attr( $title ); ?>" />
      </p>
      <p>
        <input type="hidden" name="dourousi_chapters[<?php echo $index; ?>][audio_id]" class="dourousi_audio_id"
          value="<?php echo esc_attr( $audio_id ); ?>" />
        <button class="button dourousi-select-audio" type="button">Choisir audio</button>
        <span
          class="dourousi-audio-display"><?php echo $audio_id ? esc_html(get_the_title($audio_id)) : 'Aucun audio'; ?></span>
        <button class="button dourousi-remove-chapter" type="button">Supprimer</button>
      </p>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Template (hidden) -->
  <div id="dourousi_chapter_template" style="display:none;">
    <div class="dourousi-chapter-row">
      <h5>Chapitre #__index__</h5>
      <p>
        <label>Titre du chapitre</label>
        <input type="text" name="dourousi_chapters[__index__][title]" value="" />
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
/* petit style admin pour rendre lisible */
#dourousi_chapters_container .dourousi-chapter-row label {
  font-weight: 600;
}
</style>

<?php
}

/**
 * Enqueue admin scripts (media uploader + notre JS)
 */
function dourousi_admin_enqueue( $hook ) {
    global $post;
    // n'enqueue que sur le post edit/new of our CPT
    if ( ( $hook === 'post-new.php' || $hook === 'post.php' ) ) {
        $screen = get_current_screen();
        if ( $screen && $screen->post_type === 'cours' ) {
            wp_enqueue_media(); // important pour la media library
            wp_enqueue_script( 'dourousi-admin-js', DOUROUSI_PLUGIN_URL . 'js/dourousi-admin.js', array( 'jquery' ), DOUROUSI_VERSION, true );
            // Pass some data to JS
            wp_localize_script( 'dourousi-admin-js', 'dourousi_admin',
                array(
                    'pluginUrl' => DOUROUSI_PLUGIN_URL,
                    'nonce'     => wp_create_nonce( 'dourousi_admin_nonce' ),
                )
            );
            wp_enqueue_style( 'dourousi-admin-css', DOUROUSI_PLUGIN_URL . 'css/dourousi-admin.css', array(), DOUROUSI_VERSION );
        }
    }
}
add_action( 'admin_enqueue_scripts', 'dourousi_admin_enqueue' );

/**
 * Save meta when saving post
 */
function dourousi_save_meta( $post_id ) {
    // sécurité : autosave, nonce, capabilities, post_type
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['dourousi_meta_nonce'] ) || ! wp_verify_nonce( $_POST['dourousi_meta_nonce'], 'dourousi_save_meta' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    if ( isset( $_POST['post_type'] ) && $_POST['post_type'] !== 'cours' ) return;

    // sanitize & save simple fields
    if ( isset( $_POST['dourousi_commentateur'] ) ) {
        update_post_meta( $post_id, '_dourousi_commentateur', sanitize_text_field( wp_unslash( $_POST['dourousi_commentateur'] ) ) );
    } else {
        delete_post_meta( $post_id, '_dourousi_commentateur' );
    }

    if ( isset( $_POST['dourousi_nom_livre'] ) ) {
        update_post_meta( $post_id, '_dourousi_nom_livre', sanitize_text_field( wp_unslash( $_POST['dourousi_nom_livre'] ) ) );
    } else {
        delete_post_meta( $post_id, '_dourousi_nom_livre' );
    }

    // Cours complet (checkbox)
    if ( isset( $_POST['dourousi_is_complete'] ) && $_POST['dourousi_is_complete'] === '1' ) {
        update_post_meta( $post_id, '_dourousi_is_complete', '1' );
    } else {
        update_post_meta( $post_id, '_dourousi_is_complete', '0' );
    }


    if ( isset( $_POST['dourousi_pdf_id'] ) ) {
        $pdf_id = intval( $_POST['dourousi_pdf_id'] );
        if ( $pdf_id > 0 ) {
            update_post_meta( $post_id, '_dourousi_pdf_id', $pdf_id );
        } else {
            delete_post_meta( $post_id, '_dourousi_pdf_id' );
        }
    }

    if ( isset( $_POST['dourousi_external'] ) ) {
        $url = esc_url_raw( wp_unslash( $_POST['dourousi_external'] ) );
        if ( $url ) update_post_meta( $post_id, '_dourousi_external', $url );
        else delete_post_meta( $post_id, '_dourousi_external' );
    }

    // Chapters (repeater)
    if ( isset( $_POST['dourousi_chapters'] ) && is_array( $_POST['dourousi_chapters'] ) ) {
        $chapters_raw = $_POST['dourousi_chapters'];
        $chapters = array();

        foreach ( $chapters_raw as $ch ) {
            $title = isset( $ch['title'] ) ? sanitize_text_field( wp_unslash( $ch['title'] ) ) : '';
            $audio_id = isset( $ch['audio_id'] ) ? intval( $ch['audio_id'] ) : 0;
            // Only save if title or audio present (avoid empty rows)
            if ( $title !== '' || $audio_id > 0 ) {
                $chapters[] = array(
                    'title' => $title,
                    'audio_id' => $audio_id,
                );
            }
        }
        if ( ! empty( $chapters ) ) {
            update_post_meta( $post_id, '_dourousi_chapters', $chapters );
        } else {
            delete_post_meta( $post_id, '_dourousi_chapters' );
        }
    } else {
        delete_post_meta( $post_id, '_dourousi_chapters' );
    }
}
add_action( 'save_post', 'dourousi_save_meta' );