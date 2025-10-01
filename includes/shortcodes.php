<?php
if (!defined('ABSPATH')) exit;

/**
 * Gestionnaire du shortcode [dourousi_courses].
 * Affiche une liste ou une grille des cours avec options de filtrage et de style.
 *
 * @param array $atts Attributs du shortcode.
 * @return string Le contenu HTML à afficher.
 */
function dourousi_courses_shortcode($atts) {
    // --- PARTIE 1: GESTION DES STYLES ET OPTIONS CSS ---

    // Récupère les options du plugin avec des valeurs par défaut.
    $options = get_option('dourousi_options');

    $defaults_colors = [
        'color_text'        => '#000000',
        'color_main'        => '#2b8a3e',
        'color_secondary'   => '#ff9800',
        'color_background'  => '#f5f5f5',
        'color_text_audio'  => '#2c2c2cff',
        'color_text_hover'  => '#555555',
    ];

    $colors = shortcode_atts($defaults_colors, $options, 'dourousi_colors');

    // Génère le CSS inline pour les variables CSS.
    $custom_css = ":root {
        --dourousi-color-text: {$colors['color_text']};
        --dourousi-color-main: {$colors['color_main']};
        --dourousi-color-secondary: {$colors['color_secondary']};
        --dourousi-color-background: {$colors['color_background']};
        --dourousi-color-text-audio: {$colors['color_text_audio']};
        --dourousi-color-text-hover: {$colors['color_text_hover']};
    }";

    // Charge la feuille de style principale et ajoute le CSS inline.
    wp_enqueue_style(
        'dourousi-front',
        DOUROUSI_PLUGIN_URL . 'css/dourousi-front.css',
        [],
        DOUROUSI_VERSION
    );

    wp_add_inline_style('dourousi-front', $custom_css);

    // --- PARTIE 2: GESTION DES ATTRIBUTS DU SHORTCODE ET REQUÊTE ---

    $atts = shortcode_atts([
        'number'         => 6,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'savant'         => '',
        'categorie'      => '',
        'difficulte'     => '',
        'show_thumbnail' => 'true',
        'show_excerpt'   => 'false',
        'layout'         => 'cards', // 'cards' est le layout par défaut
    ], $atts, 'dourousi_courses');

    // Assainissement des attributs
    $number         = intval($atts['number']);
    $orderby        = sanitize_text_field($atts['orderby']);
    $order          = sanitize_text_field($atts['order']);
    $layout         = sanitize_text_field($atts['layout']);
    $show_thumbnail = filter_var($atts['show_thumbnail'], FILTER_VALIDATE_BOOLEAN);
    $show_excerpt   = filter_var($atts['show_excerpt'], FILTER_VALIDATE_BOOLEAN);

    // Construction de la tax_query pour les filtres.
    $tax_query = ['relation' => 'AND'];
    $taxonomy_map = [
        'savant'     => 'savant',
        'categorie'  => 'categorie_cours',
        'difficulte' => 'difficulte',
    ];

    foreach ($taxonomy_map as $att_key => $taxonomy_name) {
        if (!empty($atts[$att_key])) {
            $tax_query[] = [
                'taxonomy' => $taxonomy_name,
                'field'    => 'slug',
                'terms'    => array_map('sanitize_title', explode(',', $atts[$att_key])),
            ];
        }
    }

    // Exécution de la requête WP_Query
    $query_args = [
        'post_type'      => 'cours',
        'posts_per_page' => $number,
        'orderby'        => $orderby,
        'order'          => $order,
        // N'ajoute la tax_query que si des filtres ont été ajoutés (taille > 1 car 'relation' est le premier élément).
        'tax_query'      => (count($tax_query) > 1) ? $tax_query : [],
    ];

    $query = new WP_Query($query_args);

    if (!$query->have_posts()) {
        return '<p>Aucun cours trouvé.</p>';
    }

    // --- PARTIE 3: RENDU HTML ---

    ob_start();

    // Logique de template selon le 'layout'
    if ($layout === 'list') {
        wp_enqueue_style( 'dourousi-list-css', DOUROUSI_PLUGIN_URL . 'css/shortcode-list.css', [], '1.0' );
        include DOUROUSI_PLUGIN_DIR . 'templates/shortcode-list.php';
    } elseif ($layout === 'carousel') {
        wp_enqueue_style( 'slick-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', [], '1.8.1' );
        wp_enqueue_style( 'slick-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css', [], '1.8.1' );
        wp_enqueue_style( 'dourousi-carousel-css', DOUROUSI_PLUGIN_URL . 'css/shortcode-carousel.css', [], '1.0' );

        wp_enqueue_script( 'slick-js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', ['jquery'], '1.8.1', true );
        wp_enqueue_script( 'dourousi-carousel-js', DOUROUSI_PLUGIN_URL . 'js/shortcode-carousel.js', ['jquery', 'slick-js'], '1.0', true );

        include DOUROUSI_PLUGIN_DIR . 'templates/shortcode-carousel.php';
    } else {
        // Rendu 'cards' par défaut
        wp_enqueue_style( 'dourousi-grid-css', DOUROUSI_PLUGIN_URL . 'css/shortcode-grid.css', [], '1.0' );
        ?>
<div class="dourousi-courses-grid">
  <?php while($query->have_posts()) : $query->the_post(); ?>
  <a href="<?php the_permalink(); ?>" class="dourousi-card-link">
    <div class="dourousi-course-card">
      <?php if ($show_thumbnail && has_post_thumbnail()) : ?>
      <div class="card-thumbnail">
        <?php the_post_thumbnail('medium'); ?>
      </div>
      <?php endif; ?>
      <div class="card-content">
        <h3 class="card-title"><?php the_title(); ?></h3>
        <div class="card-meta">
          <?php
                                $savants = get_the_term_list(get_the_ID(), 'savant', '', ', ');
                                $difficulte = get_the_term_list(get_the_ID(), 'difficulte', '', ', ');

                                if ($savants) {
                                    echo '<p class="card-savant">Par : ' . strip_tags($savants) . '</p>';
                                }
                                if ($difficulte) {
                                    echo '<p class="card-difficulte">Niveau : ' . strip_tags($difficulte) . '</p>';
                                }
                                ?>
        </div>
        <?php if ($show_excerpt) : ?>
        <div class="card-excerpt">
          <?php the_excerpt(); ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </a>
  <?php endwhile; ?>
</div>
<?php
    }

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('dourousi_courses', 'dourousi_courses_shortcode');