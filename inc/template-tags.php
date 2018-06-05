<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @author 
 */

if (!function_exists( 'rfswp_content_nav')):
/**
 * Display navigation to next/previous pages when applicable
 */
function rfswp_content_nav($nav_id) {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = ( is_single() ) ? 'post-navigation' : 'paging-navigation';

	?>
	<nav role="navigation" id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo $nav_class; ?>">
		<h4 class="screen-reader-text"><?php _e( '', 'rfswp' ); ?></h4>

	<?php if ( is_single() ) : // navigation links for single posts ?>
		
		<div class="row">
			<div class="col-md-4 col-sm-4 col-xs-4 col-nav-prev">
				<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">&larr; </span> %title' ); ?>
                <?php $prevPost = get_previous_post(true); $prevthumbnail = get_the_post_thumbnail($prevPost->ID, array(80,80) ); 
				 ?>
                <div class="link_img prev_img thumbnail">
				<?php echo $prevthumbnail; ?>
				</div>
				<?php ?>
			</div><!--end col-md-4-->
			<div class="col-md-4 col-nav-next col-sm-4 col-xs-4">
				<?php next_post_link( '<div class="nav-next">%link</div>', ' %title<span class="meta-nav"> &rarr;</span>' ); ?>
                <?php $nextPost = get_next_post(true); $nextthumbnail = get_the_post_thumbnail($nextPost->ID, array(80,80) ); 
				 ?>
                <div class="link_img next_img thumbnail">
				<?php echo $nextthumbnail; ?>
				</div>
				<?php ?>
            </div><!--end col-md-4-->
        </div><!--end row-->

	<?php elseif ($wp_query->max_num_pages > 1 && (is_home() || is_archive() || is_search())) : // navigation links for home, archive, and search pages ?>
		<div class="row">
			<div class="col-md-4">
			
				<?php

		
		global $wp_query;
		$big = 999999999; // need an unlikely integer
		$pages = paginate_links( array(
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format' => '?paged=%#%',
            'current' => max( 1, get_query_var('paged') ),
            'total' => $wp_query->max_num_pages,
            'prev_next' => false,
            'type'  => 'array',
            'prev_next'   => TRUE,
			'prev_text'    => __('«'),
			'next_text'    => __('»'),
        ) );
        if( is_array( $pages ) ) {
            $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
            echo '<ul class="pagination">';
            foreach ( $pages as $page ) {
                    echo "<li>$page</li>";
            }
           echo '</ul>';
        }
			

				?>
			</div><!--end col-md-4-->
		</div><!--end row-->

	<?php endif; ?>

	</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
	<?php
}
endif; // rfswp_content_nav

if (!function_exists( 'rfswp_the_attached_image')) :
/**
 * Prints the attached image with a link to the next attached image.
 */
function rfswp_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'rfswp_attachment_size', array( 1200, 1200 ) );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the
	 * URL of the next adjacent image in a gallery, or the first image (if
	 * we're looking at the last image in a gallery), or, in a gallery of one,
	 * just the link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;



/**
 * Returns true if a blog has more than 1 category
 */
function rfswp_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so rfswp_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so rfswp_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in rfswp_categorized_blog
 */
function rfswp_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'rfswp_category_transient_flusher' );
add_action( 'save_post',     'rfswp_category_transient_flusher' );
