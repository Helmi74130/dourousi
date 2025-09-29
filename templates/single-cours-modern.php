<?php
if (!defined('ABSPATH')) exit;


get_header();

if (have_posts()) :
  while (have_posts()) : the_post();
    $post_id = get_the_ID();
    $nom_livre = get_post_meta($post_id, '_dourousi_nom_livre', true);
    $pdf_id = intval(get_post_meta($post_id, '_dourousi_pdf_id', true));
    $external = get_post_meta($post_id, '_dourousi_external', true);
    $chapters = get_post_meta($post_id, '_dourousi_chapters', true);
    $commentateur = get_post_meta($post_id, '_dourousi_commentateur', true);

    $durations = function_exists('dourousi_get_course_duration') ? dourousi_get_course_duration(get_the_ID()) : ['total' => 0];

    $auteur = wp_get_post_terms($post_id, 'savant', array('fields' => 'names'));
    $auteur_list = !empty($auteur) ? implode(', ', $auteur) : '';

    $difficulty = wp_get_post_terms($post_id, 'difficulte', array('fields' => 'names'));
    $categories_meta = wp_get_post_terms($post_id, 'categorie_cours', array('fields' => 'names'));

?>





<article id="post-<?php the_ID(); ?>" <?php post_class('dourousi-course'); ?>>

  <header class="wii-course-hero">

    <div class="wii-course-details">

      <div class="course-meta-tags">
        <?php if ($difficulty) : ?>
        <div class="meta-line meta-difficulty">
          <strong>Difficulté :</strong>
          <?php foreach ($difficulty as $term) : ?>
          <span class="meta-tag difficulty-tag"><?php echo esc_html($term); ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php
            $is_complete = get_post_meta(get_the_ID(), '_dourousi_is_complete', true);
            $badge_class = $is_complete === '1' ? 'wii-badge-complet' : 'wii-badge-incomplet';
            $badge_icon = $is_complete === '1' ? 'fa-solid fa-check' : 'fa-regular fa-hourglass';
            $badge_text = $is_complete === '1' ? esc_html__('Cours complet', 'dourousi') : esc_html__('En cours', 'dourousi');
            ?>
        <span class="wii-dourousi-badge <?php echo $badge_class; ?>">
          <i class="<?php echo $badge_icon; ?>"></i> <?php echo $badge_text; ?>
        </span>
      </div>

      <h1><?php the_title(); ?></h1>
      <div class="wii-dourousi-excerpt">
        <?php echo '<p>' . esc_html(get_the_excerpt()) . '</p>'; ?>
      </div>

      <div class="wii-course-authorship">
        <?php if ($commentateur) : ?>
        <p class="wii-commentator"><strong><?php esc_html_e('Commentateur :', 'dourousi'); ?></strong>
          <?php echo esc_html($commentateur); ?></p>
        <?php endif; ?>
        <?php if ($auteur_list) : ?>
        <p class="wii-author"><strong><?php esc_html_e('Auteur original :', 'dourousi'); ?></strong>
          <?php echo esc_html($auteur_list); ?></p>
        <?php endif; ?>
        <?php if ($nom_livre) : ?>
        <p class="wii-book"><strong><?php esc_html_e('Basé sur le livre :', 'dourousi'); ?></strong>
          <?php echo esc_html($nom_livre); ?></p>
        <?php endif; ?>
      </div>

      <div class="wii-course-actions">
        <?php if ($pdf_id) : $pdf_url = wp_get_attachment_url($pdf_id); ?>
        <a href="<?php echo esc_url($pdf_url); ?>" download class="wii-btn wii-btn-primary">
          <span class="wii-icon"><i class="fa-solid fa-download"></i></span>
          <?php esc_html_e('Télécharger le PDF', 'dourousi'); ?>
        </a>
        <?php endif; ?>

        <?php if ($external) : ?>
        <a href="<?php echo esc_url($external); ?>" target="_blank" rel="noopener noreferrer"
          class="wii-btn wii-btn-secondary">
          <span class="wii-icon"><i class="fa-solid fa-link"></i></span>
          <?php esc_html_e('Ressources externes', 'dourousi'); ?>
        </a>
        <?php endif; ?>
      </div>

    </div>

    <?php if (has_post_thumbnail()) : ?>
    <div class="wii-course-thumbnail-container">
      <?php the_post_thumbnail('large'); ?>
    </div>
    <?php endif; ?>

  </header>

  <div class="wii-content-wrapper">

    <?php
        $chapters = get_post_meta(get_the_ID(), '_dourousi_chapters', true);
        if (is_array($chapters) && !empty($chapters)) :
          $first_chapter = $chapters[0];
          $first_audio_id = isset($first_chapter['audio_id']) ? intval($first_chapter['audio_id']) : 0;
          $first_audio_url = $first_audio_id ? wp_get_attachment_url($first_audio_id) : ''; ?>

    <section class="wii-course-chapters-section">

      <div class="wii-player-area">
        <div class="dourousi-progress">
          <p><strong><?php esc_html_e('Progression :', 'dourousi'); ?></strong>
            <span class="progress-text">0 / <?php echo count($chapters); ?>
              <?php esc_html_e('cours terminés', 'dourousi'); ?></span>
          </p>
          <div class="progress-bar">
            <div class="progress-fill" style="width:0%"></div>
          </div>
        </div>

        <div class="custom-audio-player-box">
          <p class="current-chapter-title">
            <?php esc_html_e('Lecture actuelle :'); ?> <span
              id="current-title-display"><?php echo esc_html($first_chapter['title']); ?></span>
          </p>
          <audio id="main-audio-player" class="dourousi-audio-player" controls preload="none"
            src="<?php echo esc_url($first_audio_url); ?>" data-post-id="<?php echo get_the_ID(); ?>">
            <?php esc_html_e('Votre navigateur ne supporte pas le lecteur audio.', 'dourousi'); ?>
          </audio>
        </div>

        <p class="dourousi-course-duration">
          <strong><?php esc_html_e('Durée totale estimée :', 'dourousi'); ?></strong>
          <?php echo $durations ? dourousi_format_duration($durations['total']) : 'N/A'; ?>
        </p>

      </div>

      <ul class="dourousi-chapters-list">
        <?php foreach ($chapters as $index => $chapter) :
                $title = isset($chapter['title']) ? $chapter['title'] : 'Chapitre ' . ($index + 1);
                $audio_id = isset($chapter['audio_id']) ? intval($chapter['audio_id']) : 0;
                $audio_url = $audio_id ? wp_get_attachment_url($audio_id) : '';

                if ($audio_url) : ?>
        <li class="dourousi-chapter" data-audio="<?php echo esc_url($audio_url); ?>" data-id="<?php echo $index; ?>">

          <span class="chapter-info">
            <i class="fa-solid fa-play chapter-icon"></i>
            <span class="chapter-title"><?php echo esc_html($title); ?></span>
          </span>

          <label class="chapter-complete-label">
            <input type="checkbox" class="chapter-done" data-id="<?php echo $index; ?>">
            <span></span>
          </label>
        </li>
        <?php endif;
              endforeach; ?>
      </ul>

    </section>

    <?php endif; ?>



    <?php
        $content = trim(get_the_content());
        if (! empty($content)) : ?>
    <section class="wii-main-content">
      <h2><?php esc_html_e('Description détaillée', 'dourousi'); ?></h2>
      <div class="dourousi-content">
        <?php the_content(); ?>
      </div>
    </section>
    <?php endif; ?>


    <section class="wii-course-navigation-section">
      <div class="dourousi-navigation">
      </div>
    </section>

    <?php
        $options = get_option('dourousi_options');
        // 1. Vérifie si l'utilisateur a activé l'affichage de cette section dans les options du plugin
        $show_categories = isset($options['show_categories_section']) && $options['show_categories_section'];

        if ($show_categories) :

          // 2. Récupère 3 catégories de cours ayant des posts, de manière aléatoire
          $categories_query = get_terms(array(
            'taxonomy'   => 'categorie_cours',
            'hide_empty' => true,
            'orderby'    => 'rand',
            'number'     => 3,
            // S'assure de ne sélectionner que les catégories qui ont des cours
            'object_ids' => get_posts(array(
              'post_type'      => 'cours',
              'posts_per_page' => -1,
              'fields'         => 'ids',
            )),
          ));

          // 3. Affiche la section si la requête a réussi et qu'il y a des termes
          if (!empty($categories_query) && !is_wp_error($categories_query)) : ?>

    <section class="dourousi-category-section">
      <h2><?php esc_html_e('Explorer d\'autres thèmes', 'dourousi'); ?></h2>

      <div class="dourousi-category-grid">

        <?php
                // 4. Boucle à travers chaque catégorie sélectionnée
                foreach ($categories_query as $term) :

                  // Compte le nombre de cours dans cette catégorie (nécessite une nouvelle requête WP_Query)
                  $count = new WP_Query(array(
                    'post_type'      => 'cours',
                    'posts_per_page' => -1,
                    'tax_query'      => array(
                      array(
                        'taxonomy' => 'categorie_cours',
                        'field'    => 'term_id',
                        'terms'    => $term->term_id,
                      ),
                    ),
                  ));
                  $cours_count = $count->found_posts;
                  wp_reset_postdata(); // Important : réinitialiser après une nouvelle requête
                ?>

        <div class="dourousi-card-content">
          <h3><?php echo esc_html($term->name); ?></h3>
          <p><?php echo intval($cours_count); ?> <?php esc_html_e('cours', 'dourousi'); ?></p>
          <a href="<?php echo esc_url(get_term_link($term)); ?>" class="dourousi-btn">
            <?php esc_html_e('Voir la catégorie', 'dourousi'); ?>
          </a>
        </div>
        <?php endforeach; ?>

      </div>
    </section>

    <?php
          endif; // Fin de if (!empty($categories_query)...
        endif; // Fin de if ($show_categories)...
        ?>

  </div>
</article>

<?php
  endwhile;
endif;

// 2. Fin du fichier : Appel du footer
get_footer();