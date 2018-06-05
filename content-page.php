<?php
/**
 * The template used for displaying page content in page.php
 *
 * @author Rafał Puczel
 */
?>

<article>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header>
			<?php echo page_header( get_the_ID(), get_the_title()); ?>
		</header>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</div>
</article>
