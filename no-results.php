<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @author Rafał Puczel
 */
?>

<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php _e( 'Nic nie znaleziono', 'rfswp' ); ?></h1>
	</header><!--end page-header-->

	<div class="page-content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'rfswp' ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

		<?php elseif ( is_search() ) : ?>

			<p><?php _e( 'Przykro nam, ale wyszukiwane słowa nie zostały znalezione. Spróbuj ponownie używając innych.', 'rfswp' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'rfswp' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
	</div><!--end page-content-->
</section><!--end no-results-->
