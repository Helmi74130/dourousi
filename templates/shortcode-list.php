<?php
if (!defined('ABSPATH')) exit; // Sécurité


?>
<div class="dourousi-courses-list">
    <?php while($query->have_posts()) : $query->the_post(); ?>
        <div class="dourousi-list-item">
            <div class="list-item-content">
                <h3 class="list-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <div class="list-item-meta">
                    <?php 
                    // Affiche les taxonomies "savant" et "difficulte"
                    $savants = get_the_term_list(get_the_ID(), 'savant', 'Par ', ', ', '');
                    $difficulte = get_the_term_list(get_the_ID(), 'difficulte', ' • Niveau ', ', ', '');
                    
                    if ($savants) {
                        echo '<span class="list-meta-savant">' . $savants . '</span>';
                    }
                    if ($difficulte) {
                        echo '<span class="list-meta-difficulte">' . $difficulte . '</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="list-item-link">
                <a href="<?php the_permalink(); ?>" class="dourousi-btn">Voir le cours</a>
            </div>
        </div>
    <?php endwhile; ?>
</div>