<?php 
if (!defined('ABSPATH')) exit;

get_header(); ?>

<div class="dourousi-archive-cours">
  <h1><?php post_type_archive_title(); ?></h1>

  <div class="dourousi-cours-grid">
    <?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
    <article class="dourousi-cours-card">
      <?php if ( has_post_thumbnail() ) : ?>
      <div class="dourousi-thumbnail">
        <a href="<?php the_permalink(); ?>">
          <?php the_post_thumbnail('medium'); ?>
        </a>
      </div>
      <?php endif; ?>

      <h2 class="dourousi-title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
      </h2>

      <div class="dourousi-meta">
        <?php 
                        $savants = get_the_term_list( get_the_ID(), 'savant', 'Par : ', ', ' );
                        $difficulte = get_the_term_list( get_the_ID(), 'difficulte', 'Niveau : ', ', ' );
                        if ($savants) echo '<p>'.$savants.'</p>';
                        if ($difficulte) echo '<p>'.$difficulte.'</p>';
                        ?>
      </div>

      <a href="<?php the_permalink(); ?>" class="dourousi-btn">Voir le cours</a>
    </article>
    <?php endwhile; ?>

    <div class="dourousi-pagination">
      <?php the_posts_pagination(); ?>
    </div>
    <?php else : ?>
    <p>Aucun cours disponible.</p>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>