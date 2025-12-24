<?php
/**
 * Comparison sections renderer.
 *
 * Renders ACF repeater comparison_sections with nested feature blocks.
 *
 * @package aichatbotfree
 */

if ( ! function_exists( 'have_rows' ) || ! have_rows( 'comparison_sections' ) ) {
	return;
}
?>

<section class="comparison-sections">
	<?php
	while ( have_rows( 'comparison_sections' ) ) :
		the_row();

		$section_title = get_sub_field( 'comparison_title' );
		$section_intro = get_sub_field( 'comparison_intro' );
		?>
		<article class="comparison-section">
			<?php if ( $section_title ) : ?>
				<h2 class="comparison-section__title"><?php echo esc_html( $section_title ); ?></h2>
			<?php endif; ?>

			<?php if ( $section_intro ) : ?>
				<div class="comparison-section__intro">
					<?php echo wp_kses_post( $section_intro ); ?>
				</div>
			<?php endif; ?>

			<?php if ( have_rows( 'feature_blocks' ) ) : ?>
				<div class="comparison-section__features">
					<?php
					while ( have_rows( 'feature_blocks' ) ) :
						the_row();

						$feature_title = get_sub_field( 'feature_title' );
						$left_title    = get_sub_field( 'left_product_title' );
						$right_title   = get_sub_field( 'right_product_title' );
						$winner_label  = get_sub_field( 'winner_label' );
						$winner_link   = get_sub_field( 'winner_link' );
						?>
						<div class="comparison-feature">
							<?php if ( $feature_title ) : ?>
								<h3 class="comparison-feature__title"><?php echo esc_html( $feature_title ); ?></h3>
							<?php endif; ?>

							<div class="comparison-feature__columns">
								<div class="comparison-feature__column comparison-feature__column--left">
									<?php if ( $left_title ) : ?>
										<h4 class="comparison-feature__column-title"><?php echo esc_html( $left_title ); ?></h4>
									<?php endif; ?>

									<?php if ( have_rows( 'left_product_rows' ) ) : ?>
										<ul class="comparison-feature__list">
											<?php
											while ( have_rows( 'left_product_rows' ) ) :
												the_row();
												$left_row_text = get_sub_field( 'left_row_text' );

												if ( ! $left_row_text ) {
													continue;
												}
												?>
												<li class="comparison-feature__list-item"><?php echo esc_html( $left_row_text ); ?></li>
												<?php
											endwhile;
											?>
										</ul>
									<?php endif; ?>
								</div>

								<div class="comparison-feature__column comparison-feature__column--right">
									<?php if ( $right_title ) : ?>
										<h4 class="comparison-feature__column-title"><?php echo esc_html( $right_title ); ?></h4>
									<?php endif; ?>

									<?php if ( have_rows( 'right_product_rows' ) ) : ?>
										<ul class="comparison-feature__list">
											<?php
											while ( have_rows( 'right_product_rows' ) ) :
												the_row();
												$right_row_text = get_sub_field( 'right_row_text' );

												if ( ! $right_row_text ) {
													continue;
												}
												?>
												<li class="comparison-feature__list-item"><?php echo esc_html( $right_row_text ); ?></li>
												<?php
											endwhile;
											?>
										</ul>
									<?php endif; ?>
								</div>
							</div>

							<?php if ( $winner_label && $winner_link ) : ?>
								<div class="comparison-feature__winner">
									<a class="comparison-feature__winner-link cta-text-link" href="<?php echo esc_url( $winner_link ); ?>">
										<?php echo esc_html( $winner_label ); ?>
									</a>
								</div>
							<?php endif; ?>
						</div>
						<?php
					endwhile;
					?>
				</div>
			<?php endif; ?>
		</article>
		<?php
	endwhile;
	?>
</section>
