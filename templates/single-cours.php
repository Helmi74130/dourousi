<?php
if (!defined('ABSPATH')) exit;

get_header(); // ou partie de ton thème

if (have_posts()) :
    while (have_posts()) : the_post();
        $post_id = get_the_ID();
        $nom_livre = get_post_meta($post_id, '_dourousi_nom_livre', true);
        $pdf_id = intval(get_post_meta($post_id, '_dourousi_pdf_id', true));
        $external = get_post_meta($post_id, '_dourousi_external', true);
        $chapters = get_post_meta($post_id, '_dourousi_chapters', true);

        $auteur = wp_get_post_terms($post_id, 'savant', array('fields' => 'names'));
        $auteur_list = !empty($auteur) ? implode(', ', $auteur) : '';

        $difficulty = wp_get_post_terms($post_id, 'difficulte', array('fields' => 'names'));

        $categories = wp_get_post_terms($post_id, 'categorie_cours', array('fields' => 'names'));
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('dourousi-course'); ?>>
  <div class="wii-hero-section">
    <div class="wii-hero-right">
      <?php if (has_post_thumbnail()) : ?>
      <div class="wii-dourousi-thumbnail">
        <?php the_post_thumbnail('large'); ?>
      </div>
      <?php endif; ?>
    </div>
    <div class="wii-hero-left">
      <div>
        <div>
          <?php
                            $is_complete = get_post_meta(get_the_ID(), '_dourousi_is_complete', true);
                            if ($is_complete === '1') {
                                echo '<span class="wii-dourousi-badge wii-badge-complet">✅ Cours complet</span>';
                            } else {
                                echo '<span class="wii-dourousi-badge wii-badge-incomplet">⏳ En cours</span>';
                            }
                            ?>
        </div>
        <h1><?php the_title(); ?></h1>
        <div class="wii-hero-meta">
          <?php if ($auteur_list) : ?>
          <p class="wii-author"><strong>Auteur :</strong> <?php echo esc_html($auteur_list); ?></p>
          <?php endif; ?>

          <?php if ($nom_livre) : ?>
          <p class="wii-book"><strong>Livre :</strong> <?php echo esc_html($nom_livre); ?></p>
          <?php endif; ?>
        </div>
        <div class="wii-dourousi-excerpt">
          <?php
                            $excerpt = get_the_excerpt(); // retourne l’extrait
                            echo '<p>' . esc_html($excerpt) . '</p>';
                            ?>
        </div>
        <div class="wii-hero-meta wii-link">
          <?php if ($pdf_id) :
                                $pdf_url = wp_get_attachment_url($pdf_id); ?>
          <p class="wii-attachment">
            <a href="<?php echo esc_url($pdf_url); ?>" download>
              <span class="wii-icon">&#128196;</span> Télécharger le PDF
            </a>
          </p>
          <?php endif; ?>

          <?php if ($external) : ?>
          <p class="wii-external">
            <a href="<?php echo esc_url($external); ?>" target="_blank" rel="noopener noreferrer">
              <span class="wii-icon">&#128279;</span> Ressources externes
            </a>
          </p>
          <?php endif; ?>
        </div>

      </div>
    </div>

  </div>



  <?php
            $chapters = get_post_meta(get_the_ID(), '_dourousi_chapters', true);

            if (is_array($chapters) && !empty($chapters)) :
                // Prendre la première piste comme "par défaut"
                $first_chapter = $chapters[0];
                $first_audio_id = isset($first_chapter['audio_id']) ? intval($first_chapter['audio_id']) : 0;
                $first_audio_url = $first_audio_id ? wp_get_attachment_url($first_audio_id) : '';
            ?>
  <div class="wii-dourousi-chapters">
    <div class="dourousi-progress">
      <div class="progress-bar">
        <div class="progress-fill" style="width:0%"></div>
      </div>
      <div class="progress-text">0 / <?php echo count($chapters); ?> cours terminés</div>
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

    <!-- Liste des chapitres -->
    <ul class="dourousi-chapters-list">
      <?php foreach ($chapters as $index => $chapter) :
                            $title     = isset($chapter['title']) ? $chapter['title'] : 'Chapitre ' . ($index + 1);
                            $audio_id  = isset($chapter['audio_id']) ? intval($chapter['audio_id']) : 0;
                            $audio_url = $audio_id ? wp_get_attachment_url($audio_id) : '';

                            if ($audio_url) : ?>
      <li class="dourousi-chapter" data-id="<?php echo $index; ?>">
        <a href="#" class="chapter-link" data-audio="<?php echo esc_url($audio_url); ?>"
          data-id="<?php echo $index; ?>">
          <?php echo esc_html($title); ?>
        </a>
        <label class="chapter-complete-label">
          <input type="checkbox" class="chapter-done" data-id="<?php echo $index; ?>">
          <span></span> <?php esc_html_e("J'ai terminé ce cours", "dourousi"); ?>
        </label>

      </li>
      <?php endif; ?>
      <?php endforeach; ?>
    </ul>

  </div>
  <?php endif; ?>




  <?php if ($difficulty) : ?>
  <p><strong>Difficulté :</strong> <?php echo esc_html(implode(', ', $difficulty)); ?></p>
  <?php endif; ?>

  <?php if ($categories) : ?>
  <p><strong>Catégories :</strong> <?php echo esc_html(implode(', ', $categories)); ?></p>
  <?php endif; ?>









  <div class="dourousi-content">
    <?php the_content(); ?>
  </div>


<?php
$categories = get_terms(array(
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

if (!empty($categories) && !is_wp_error($categories)) : ?>
    <div class="dourousi-category-section">
        <h2><?php esc_html_e('Explorer aussi ces catégories', 'dourousi'); ?></h2>

        <div class="dourousi-category-grid">
            <?php foreach ($categories as $term) :
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
            ?>
                <div class="dourousi-card-content">
                    <h3><?php echo esc_html($term->name); ?></h3>
                    <p><?php echo intval($cours_count); ?> cours</p>
                    <a href="<?php echo esc_url(get_term_link($term)); ?>" class="dourousi-btn">
                        Voir la catégorie
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>




  <div class="dourousi-navigation">
    <?php
                // Précédent
                $prev_post = get_previous_post(false); // false = pas limité à la même catégorie
                if ($prev_post) :
                    $prev_thumb = get_the_post_thumbnail($prev_post->ID, 'thumbnail', array('class' => 'dourousi-nav-thumb'));
                ?>
    <a class="dourousi-prev" href="<?php echo get_permalink($prev_post->ID); ?>">
      <?php echo $prev_thumb; ?>
      <span>← <?php echo esc_html(get_the_title($prev_post->ID)); ?></span>
    </a>
    <?php endif; ?>

    <?php
                // Suivant
                $next_post = get_next_post(false);
                if ($next_post) :
                    $next_thumb = get_the_post_thumbnail($next_post->ID, 'thumbnail', array('class' => 'dourousi-nav-thumb'));
                ?>
    <a class="dourousi-next" href="<?php echo get_permalink($next_post->ID); ?>">
      <span><?php echo esc_html(get_the_title($next_post->ID)); ?> →</span>
      <?php echo $next_thumb; ?>
    </a>
    <?php endif; ?>
  </div>



</article>

<?php
    endwhile;
endif;

get_footer();