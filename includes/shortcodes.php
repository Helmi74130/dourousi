<?php
if (!defined('ABSPATH')) exit; // Sécurité

function dourousi_courses_shortcode($atts) {


    // 1. Récupère les variables CSS de l'option du plugin
    $options = get_option('dourousi_options');

    $color_text       = isset($options['color_text']) ? $options['color_text'] : '#000000';
    $color_main       = isset($options['color_main']) ? $options['color_main'] : '#2b8a3e';
    $color_secondary  = isset($options['color_secondary']) ? $options['color_secondary'] : '#ff9800';
    $color_background = isset($options['color_background']) ? $options['color_background'] : '#f5f5f5';
    $color_text_audio = isset($options['color_text_audio']) ? $options['color_text_audio'] : '#2c2c2cff';
    $color_text_hover = isset($options['color_text_hover']) ? $options['color_text_hover'] : '#555555';
    
    // 2. Génère le CSS inline avec les variables
    $custom_css = ":root {
        --dourousi-color-text: {$color_text};
        --dourousi-color-main: {$color_main};
        --dourousi-color-secondary: {$color_secondary};
        --dourousi-color-background: {$color_background};
        --dourousi-color-text-audio: {$color_text_audio};
        --dourousi-color-text-hover: {$color_text_hover};
    }";

    // 3. Charge la feuille de style commune
    wp_enqueue_style(
        'dourousi-front',
        DOUROUSI_PLUGIN_URL . 'css/dourousi-front.css',
        array(),
        DOUROUSI_VERSION
    );

    // 4. Ajoute le CSS inline après la feuille de style
    wp_add_inline_style('dourousi-front', $custom_css);





    // 1. Fusionne les attributs des deux versions
    $atts = shortcode_atts(array(
        'number' => 6,
        'orderby' => 'date',
        'order' => 'DESC',
        'savant' => '',
        'categorie' => '',
        'difficulte' => '',
        'show_thumbnail' => 'true',
        'show_excerpt' => 'false',
        'layout' => 'cards', // 'cards' est le layout par défaut
    ), $atts, 'dourousi_courses');

    // Assainit les attributs
    $number = intval($atts['number']);
    $orderby = sanitize_text_field($atts['orderby']);
    $order = sanitize_text_field($atts['order']);
    $layout = sanitize_text_field($atts['layout']);
    $show_thumbnail = filter_var($atts['show_thumbnail'], FILTER_VALIDATE_BOOLEAN);
    $show_excerpt = filter_var($atts['show_excerpt'], FILTER_VALIDATE_BOOLEAN);
    
    // 2. Construit la tax_query pour les filtres multiples
    $tax_query = array('relation' => 'AND');

    if(!empty($atts['savant'])) {
        $tax_query[] = array(
            'taxonomy' => 'savant',
            'field' => 'slug',
            'terms' => explode(',', $atts['savant']),
        );
    }

    if(!empty($atts['categorie'])) {
        $tax_query[] = array(
            'taxonomy' => 'categorie_cours',
            'field' => 'slug',
            'terms' => explode(',', $atts['categorie']),
        );
    }

    if(!empty($atts['difficulte'])) {
        $tax_query[] = array(
            'taxonomy' => 'difficulte',
            'field' => 'slug',
            'terms' => explode(',', $atts['difficulte']),
        );
    }

    // 3. Exécute la requête WP_Query
    $query_args = array(
        'post_type' => 'cours',
        'posts_per_page' => $number,
        'orderby' => $orderby,
        'order' => $order,
        // N'ajoute la tax_query que si des filtres sont présents (plus d'un élément dans le tableau, le premier étant 'relation')
        'tax_query' => (count($tax_query) > 1) ? $tax_query : array(),
    );
    
    $query = new WP_Query($query_args);

    if (!$query->have_posts()) {
        return '<p>Aucun cours trouvé.</p>';
    }

    // 4. Génère le rendu HTML
    ob_start();
    
    // Si tu veux une logique de template comme dans ta 2e version
    if ($layout === 'list') {
        include DOUROUSI_PLUGIN_DIR . 'templates/shortcode-list.php';
        wp_enqueue_style( 'dourousi-list-css', DOUROUSI_PLUGIN_URL . 'css/shortcode-list.css', array(), '1.0' );
        // Note: Tu dois t'assurer que le chemin 'DOUROUSI_PLUGIN_DIR' est bien défini
    } elseif ($layout === 'carousel') {
        wp_enqueue_style( 'slick-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', array(), '1.8.1' );
        wp_enqueue_style( 'dourousi-carousel-css', DOUROUSI_PLUGIN_URL . 'css/shortcode-carousel.css', array(), '1.0' );
        wp_enqueue_style( 'slick-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css', array(), '1.8.1' );
        wp_enqueue_script( 'slick-js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array('jquery'), '1.8.1', true );
        wp_enqueue_script( 'dourousi-carousel-js', DOUROUSI_PLUGIN_URL . 'js/shortcode-carousel.js', array('jquery', 'slick-js'), '1.0', true );

        include DOUROUSI_PLUGIN_DIR . 'templates/shortcode-carousel.php';
    } else {
        // Utilise la version 'grid' par défaut si aucun template n'est inclus
        wp_enqueue_style( 'dourousi-grid-css', DOUROUSI_PLUGIN_URL . 'css/shortcode-grid.css', array(), '1.0' );
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