<?php
if (!defined('ABSPATH')) exit;

get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        
        // ==========================================================
        // 1. PRÉPARATION DES DONNÉES (LOGIQUE PURE - INCHANGÉE)
        // ==========================================================
        
        $post_id      = get_the_ID();
        $nom_livre    = get_post_meta($post_id, '_dourousi_nom_livre', true);
        $pdf_id       = intval(get_post_meta($post_id, '_dourousi_pdf_id', true));
        $external     = get_post_meta($post_id, '_dourousi_external', true);
        $commentateur = get_post_meta($post_id, '_dourousi_commentateur', true);
        $chapters     = get_post_meta($post_id, '_dourousi_chapters', true);
        $is_complete  = get_post_meta($post_id, '_dourousi_is_complete', true) === '1';

        $durations = function_exists('dourousi_get_course_duration') ? dourousi_get_course_duration($post_id) : ['total' => 0];
        $auteur_terms = wp_get_post_terms($post_id, 'savant', array('fields' => 'names'));
        $auteur_list = !empty($auteur_terms) ? implode(', ', $auteur_terms) : '';
        $difficulty = wp_get_post_terms($post_id, 'difficulte', array('fields' => 'names'));
        $excerpt = get_the_excerpt();

        $status_class = $is_complete ? 'wii-badge-complet' : 'wii-badge-incomplet';
        $status_icon  = $is_complete ? 'fa-solid fa-check' : 'fa-regular fa-hourglass';
        $status_text  = $is_complete ? esc_html__('Cours complet', 'dourousi') : esc_html__('En cours', 'dourousi');
        $pdf_url = $pdf_id ? wp_get_attachment_url($pdf_id) : false;

        // Récupérer l'URL de l'image mise en avant
        $thumbnail_id = get_post_thumbnail_id($post_id);
        $thumbnail_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('dourousi-course'); ?>>

  <header class="wii-hero-section **is-fullscreen**" <?php if ($thumbnail_url) : ?>
    style="background-image: url('<?php echo esc_url($thumbnail_url); ?>');" <?php endif; ?>>

    <div class="wii-hero-content-wrapper">
      <div class="wii-hero-left">
        <div class="wii-course-details">

          <div class="course-meta-tags">
            <?php if ($difficulty) : ?>
            <p class="meta-line meta-difficulty">
              <strong><?php esc_html_e('Difficulté :', 'dourousi'); ?></strong>
              <?php foreach ($difficulty as $term) : ?>
              <span class="meta-tag difficulty-tag"><?php echo esc_html($term); ?></span>
              <?php endforeach; ?>
            </p>
            <?php endif; ?>

            <span class="wii-dourousi-badge <?php echo esc_attr($status_class); ?>">
              <i class="<?php echo esc_attr($status_icon); ?>"></i>
              <?php echo $status_text; ?>
            </span>
          </div>

          <h1><?php the_title(); ?></h1>

          <div class="wii-hero-meta wii-hero-meta-top">
            <?php if ($commentateur) : ?>
            <p class="wii-author"><strong><?php esc_html_e('Commentaire :', 'dourousi'); ?></strong>
              <?php echo esc_html($commentateur); ?></p>
            <?php endif; ?>

            <?php if ($auteur_list) : ?>
            <p class="wii-author"><strong><?php esc_html_e('Auteur :', 'dourousi'); ?></strong>
              <?php echo esc_html($auteur_list); ?></p>
            <?php endif; ?>

            <?php if ($nom_livre) : ?>
            <p class="wii-book"><strong><?php esc_html_e('Livre :', 'dourousi'); ?></strong>
              <?php echo esc_html($nom_livre); ?></p>
            <?php endif; ?>
          </div>

          <?php if ($excerpt) : ?>
          <div class="wii-dourousi-excerpt">
            <p><?php echo esc_html($excerpt); ?></p>
          </div>
          <?php endif; ?>

          <div class="wii-hero-meta wii-link">
            <?php if ($pdf_url) : ?>
            <p class="wii-attachment">
              <a href="<?php echo esc_url($pdf_url); ?>" download>
                <span class="wii-icon"><i class="fa-solid fa-download"></i></span>
                <?php esc_html_e('Télécharger le PDF', 'dourousi'); ?>
              </a>
            </p>
            <?php endif; ?>

            <?php if ($external) : ?>
            <p class="wii-external">
              <a href="<?php echo esc_url($external); ?>" target="_blank" rel="noopener noreferrer">
                <span class="wii-icon"><i class="fa-solid fa-link"></i></span>
                <?php esc_html_e('Ressources externes', 'dourousi'); ?>
              </a>
            </p>
            <?php endif; ?>
          </div>

        </div>
      </div>
    </div>
  </header>

  <?php
    if (is_array($chapters) && !empty($chapters)) :
        // Préparer les données pour le lecteur par défaut
        $first_chapter = $chapters[0];
        $first_audio_id = isset($first_chapter['audio_id']) ? intval($first_chapter['audio_id']) : 0;
        $first_audio_url = $first_audio_id ? wp_get_attachment_url($first_audio_id) : ''; 
    ?>
  <section class="wii-course-player-section">

    <div class="wii-dourousi-chapters">
      <div class="dourousi-course-duration">
        <p><strong><?php esc_html_e('Durée totale du cours :', 'dourousi'); ?></strong>
          <?php echo $durations ? dourousi_format_duration($durations['total']) : 'N/A'; ?>
        </p>
      </div>
      <div class="dourousi-progress">
        <div class="progress-bar">
          <div class="progress-fill" style="width:0%"></div>
        </div>
        <div class="progress-text">0 / <?php echo count($chapters); ?>
          <?php esc_html_e('cours terminés', 'dourousi'); ?>
        </div>
      </div>
      <div class="custom-audio-container">
        <div class="current-chapter-title">
          <p id="current-title-display"><?php echo esc_html($first_chapter['title']); ?></p>
        </div>

        <div class="custom-audio">
          <audio id="main-audio-player" class="dourousi-audio-player" controls preload="none"
            src="<?php echo esc_url($first_audio_url); ?>" data-post-id="<?php echo get_the_ID(); ?>">
            <?php esc_html_e('Votre navigateur ne supporte pas le lecteur audio.', 'dourousi'); ?>
          </audio>
        </div>
      </div>
    </div>

    <ul class="dourousi-chapters-list">
      <?php foreach ($chapters as $index => $chapter) :
                $title   = isset($chapter['title']) ? $chapter['title'] : 'Chapitre ' . ($index + 1);
                $audio_id   = isset($chapter['audio_id']) ? intval($chapter['audio_id']) : 0;
                $audio_url = $audio_id ? wp_get_attachment_url($audio_id) : '';

                if ($audio_url) : ?>
      <li class="dourousi-chapter" data-audio="<?php echo esc_url($audio_url); ?>" data-id="<?php echo $index; ?>">

        <span class="chapter-content-wrapper">
          <span class="chapter-title"><?php echo esc_html($title); ?></span>
        </span>

        <label class="chapter-complete-label">
          <input type="checkbox" class="chapter-done" data-id="<?php echo $index; ?>">
          <span></span> <?php esc_html_e("J'ai terminé ce cours", "dourousi"); ?>
        </label>
      </li>
      <?php endif;
            endforeach; ?>
    </ul>

  </section>
  <?php endif; ?>

  <section class="dourousi-content-section">
    <div class="dourousi-content">
      <?php the_content(); ?>
    </div>
  </section>

  <?php
    $options = get_option('dourousi_options');
    $show_categories = isset($options['show_categories_section']) && $options['show_categories_section'];

    if ($show_categories) :
        $categories_query = get_terms(array(
            'taxonomy'   => 'categorie_cours',
            'hide_empty' => true,
            'orderby'    => 'rand',
            'number'     => 3,
            'object_ids' => get_posts(array(
                'post_type'      => 'cours',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            )),
        ));

        if (!empty($categories_query) && !is_wp_error($categories_query)) : ?>
  <section class="dourousi-category-section">

    <div class="dourousi-category-grid">
      <?php foreach ($categories_query as $term) :
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
                    wp_reset_postdata(); 
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
  <?php endif; 
    endif;
    ?>

  <nav class="dourousi-navigation">
    <?php
        $prev_post = get_previous_post(false); 
        if ($prev_post) :
            $prev_thumb = get_the_post_thumbnail($prev_post->ID, 'thumbnail', array('class' => 'dourousi-nav-thumb'));
        ?>
    <a class="dourousi-prev" href="<?php echo get_permalink($prev_post->ID); ?>">
      <?php echo $prev_thumb; ?>
      <span>← <?php echo esc_html(get_the_title($prev_post->ID)); ?></span>
    </a>
    <?php endif; ?>

    <?php
        $next_post = get_next_post(false);
        if ($next_post) :
            $next_thumb = get_the_post_thumbnail($next_post->ID, 'thumbnail', array('class' => 'dourousi-nav-thumb'));
        ?>
    <a class="dourousi-next" href="<?php echo get_permalink($next_post->ID); ?>">
      <span><?php echo esc_html(get_the_title($next_post->ID)); ?> →</span>
      <?php echo $next_thumb; ?>
    </a>
    <?php endif; ?>
  </nav>


</article>

<?php
    endwhile;
endif;

get_footer();