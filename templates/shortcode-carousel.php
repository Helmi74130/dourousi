<?php
if (!defined('ABSPATH')) exit; // Sécurité


?>

<div class="dourousi-courses-carousel">
    <?php while($query->have_posts()) : $query->the_post(); ?>
        <div class="dourousi-course-card">
            <?php if ($show_thumbnail && has_post_thumbnail()) : ?>
                <div class="card-thumbnail">
                    <?php the_post_thumbnail('medium'); ?>
                </div>
            <?php else: ?>
                <div class="card-thumbnail placeholder">
                    <span class="placeholder-icon">🎧</span>
                </div>
            <?php endif; ?>
            <div class="card-content">
                <h3 class="card-title"><?php the_title(); ?></h3>
                <div class="card-meta">
                    <?php 
                    $savants = get_the_term_list(get_the_ID(), 'savant', '', ', ');
                    $difficulte = get_the_term_list(get_the_ID(), 'difficulte', '', ', ');
                    
                    if ($savants) {
                        echo '<p class="card-savant">👤 ' . strip_tags($savants) . '</p>';
                    }
                    if ($difficulte) {
                        echo '<p class="card-difficulte">🎯 ' . strip_tags($difficulte) . '</p>';
                    }
                    ?>
                </div>
                <div class="card-actions">
                    <a href="<?php the_permalink(); ?>" class="card-button">Voir le cours</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>


