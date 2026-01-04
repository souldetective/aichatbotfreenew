<?php
/**
 * Archive template for AI Chatbot Free theme.
 */
get_header();
?>
<main class="section">
  <div class="container">
    <div class="section-title">
      <div>
        <p class="badge" style="display:inline-block; background:rgba(255,255,255,0.06); color:var(--muted); margin:0;">
          Archive
        </p>
        <h1 style="margin-top:10px;"><?php the_archive_title(); ?></h1>
        <?php if ( get_the_archive_description() ) : ?>
          <p><?php echo wp_kses_post( get_the_archive_description() ); ?></p>
        <?php endif; ?>
      </div>
    </div>
    <div class="blog-layout">
      <div class="blog-main">
        <?php if ( have_posts() ) : ?>
          <div class="blog-grid">
            <?php while ( have_posts() ) : the_post(); ?>
              <article class="blog-card">
                <?php if ( has_post_thumbnail() ) : ?>
                  <a class="blog-card__image" href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail( 'large' ); ?>
                  </a>
                <?php endif; ?>
                <div class="blog-card__content">
                  <h3 class="blog-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                  <p class="blog-card__excerpt"><?php echo esc_html( aichatbotfree_get_excerpt_chars( 180 ) ); ?></p>
                  <a class="blog-card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'aichatbotfree' ); ?></a>
                </div>
              </article>
            <?php endwhile; ?>
          </div>
          <div class="blog-pagination">
            <?php the_posts_pagination(
              [
                'mid_size'  => 2,
                'prev_text' => __( '« Prev', 'aichatbotfree' ),
                'next_text' => __( 'Next »', 'aichatbotfree' ),
              ]
            ); ?>
          </div>
        <?php else : ?>
          <div class="card">
            <h3>No content found</h3>
            <p>Try browsing other categories or search for a chatbot guide.</p>
            <?php get_search_form(); ?>
          </div>
        <?php endif; ?>
      </div>
      <aside class="blog-sidebar">
        <?php if ( is_active_sidebar( 'blog-sidebar' ) ) : ?>
          <?php dynamic_sidebar( 'blog-sidebar' ); ?>
        <?php endif; ?>
      </aside>
    </div>
  </div>
</main>
<?php get_footer(); ?>
