<?php
/**
 * Template Name: Blog Layout
 *
 * Blog page with sidebar and grid layout.
 */

get_header();

$page_id = get_the_ID();
$selected_categories = get_post_meta( $page_id, '_aichatbotfree_blog_categories', true );
$selected_categories = is_array( $selected_categories ) ? array_filter( array_map( 'absint', $selected_categories ) ) : [];

$paged = max( 1, (int) get_query_var( 'paged' ) );
$query_args = [
    'post_type'      => 'post',
    'posts_per_page' => (int) get_option( 'posts_per_page' ),
    'paged'          => $paged,
];

if ( ! empty( $selected_categories ) ) {
    $query_args['category__in'] = $selected_categories;
}

$blog_query = new WP_Query( $query_args );
$pagination_base = get_pagenum_link( 1 );
$pagination_format = get_option( 'permalink_structure' ) ? 'page/%#%/' : '&paged=%#%';
?>
<main class="section">
  <div class="container">
    <div class="section-title">
      <div>
        <h1 style="margin-top:10px;"><?php the_title(); ?></h1>
        <?php if ( has_excerpt( $page_id ) ) : ?>
          <p><?php echo wp_kses_post( get_the_excerpt( $page_id ) ); ?></p>
        <?php endif; ?>
      </div>
    </div>
    <div class="blog-layout">
      <div class="blog-main">
        <?php if ( $blog_query->have_posts() ) : ?>
          <div class="blog-grid">
            <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>
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
            <?php
            echo wp_kses_post(
              paginate_links(
                [
                  'total'     => $blog_query->max_num_pages,
                  'current'   => $paged,
                  'base'      => trailingslashit( $pagination_base ) . '%_%',
                  'format'    => $pagination_format,
                  'mid_size'  => 2,
                  'prev_text' => __( '« Prev', 'aichatbotfree' ),
                  'next_text' => __( 'Next »', 'aichatbotfree' ),
                ]
              )
            );
            ?>
          </div>
        <?php else : ?>
          <div class="card">
            <h3><?php esc_html_e( 'No posts found', 'aichatbotfree' ); ?></h3>
            <p><?php esc_html_e( 'Try browsing other categories or search for a chatbot guide.', 'aichatbotfree' ); ?></p>
            <?php get_search_form(); ?>
          </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
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
