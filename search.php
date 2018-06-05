<?php
/**
 * The template for displaying Search Results pages.
 *
 * @author Rafał Puczel
 */

get_header(); ?>

<div class="container">
    <div class="row">
    	<div class="col-md-12 col-sm-12 col-xs-12 wrap">
        	<div class="col-md-8 col-sm-8 col-xs-12 content">
			
					<?php if ( have_posts() ) : ?>
			
						<header class="page-header">
							<h1 class="page-title"><?php printf( __( 'Wyniki wyszukiwania dla: %s', 'rfswp' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
						</header><!--end page-header-->
			
						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>
			
							<?php get_template_part( 'content', 'search' ); ?>
			
						<?php endwhile; ?>
			
						<?php rfswp_content_nav( 'nav-below' ); ?>
			
					<?php else : ?>
			
						<?php get_template_part( 'no-results', 'search' ); ?>
			
					<?php endif; ?>
			
			</div><!--end content-->
        	<div class="col-md-4 col-sm-4 col-xs-12 sidebar">
            	<?php get_sidebar(); ?>
            </div><!--end sidebar-->
        </div><!--end wrap-->
    </div><!--end row-->
</div><!--end container-->  

<?php get_footer(); ?>