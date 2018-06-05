<?php
/**
 * Template name: Mapa
 * The template for displaying map page.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @author Rafał Puczel
 */

get_header(); ?>

<?php  
$entry 				= is_user_logged_in() ? get_field('_map_entry_loggedin') : get_field('_map_entry');
$tooltipP1 		= is_user_logged_in() ? get_field('_map_tooltip_p1_logged_in') : get_field('_map_tooltip_p1');
$tooltipP2 		= is_user_logged_in() ? get_field('_map_tooltip_p2_logged_in') : get_field('_map_tooltip_p2');
$tooltipP3 		= is_user_logged_in() ? get_field('_map_tooltip_p3_logged_in') : get_field('_map_tooltip_p3');
$tooltipText 	= is_user_logged_in() ? get_field('_map_tooltip_text_logged_in') : get_field('_map_tooltip_text');
$tooltipLink 	= is_user_logged_in() ? get_field('_map_tooltip_link_logged_in') : get_field('_map_tooltip_link');

$places = new WP_Query( array(
	'post_type'	        => 'post',
	'posts_per_page'    => -1,
	'post_status'       => 'publish',
	'meta_query'		=> array(
		array(
			'key'		=> '_place_expiry_date',
			'value'		=> current_time( 'Ymd' ),
			'type'		=> 'CHAR',
			'compare'	=> '>='
		)
	)
) );
$counties = array();
$countiesNames = counties_list();
?>

<main>
	<div class="container-fluid page-container map-page">

		<section>
			<div class="page-entry">
				<div class="container">
					<?php echo page_header( get_the_ID(), get_the_title()); ?>
					<?php if(!empty_content($entry)) : ?>
					<div class="entry-text text-entry"><?php echo wp_kses_post( $entry ); ?></div>
					<?php endif; ?>
					<?php if($tooltipText) : ?>
						<div class="tooltip-text text-center text-entry">
							<p><?php echo esc_html( $tooltipP1 ); ?> <strong data-toggle="tooltip" data-placement="top" title="<?php echo esc_html( $tooltipText ); ?>">
							<?php if( $tooltipLink ) : ?><a href="<?php echo esc_url( $tooltipLink ); ?>"><?php endif; ?><?php echo esc_html( $tooltipP2 ); ?><?php if( $tooltipLink ) : ?></a><?php endif; ?>
							</strong> <?php echo esc_html( $tooltipP3 ); ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<div class="places-bg-image">

			<div class="container">

				<!-- Map - Poland -->
				<div id="map-poland-container" class="relative">
					<?php if($places->have_posts()) : ?>
					<div id="map-poland-markers" data-has-info="no">
						<?php while($places->have_posts()) : $places->the_post(); ?>
							<?php  
							$county  = get_field('_place_county');
							$address = get_field('_place_coords');
							$lng 	 = $address['lng'];
							$lat 	 = $address['lat'];
							foreach(map_counties() as $c => $p) {
								if( strcmp( $county, $c ) == 0 ) {
									$counties[sanitize_title( $c )][] = 1;
								}
							}
							?>
							<?php if($address) : ?>
							<span class="map-marker" style="left:<?php echo set_map_pin_coords('lng', $lng); ?>%; top:<?php echo set_map_pin_coords('lat', $lat); ?>%"></span>
							<?php endif; ?>
						<?php endwhile; ?>
					</div>
					<?php endif; wp_reset_postdata(); ?>
					<div id="map-poland" data-link-base="<?php echo esc_url( get_permalink( template_id( 'search' ) ) ); ?>" data-has-info="yes" >
						<ul class="poland">
						<?php $i=1; foreach(map_counties() as $county => $p) : ?>
							<li data-places-found="<?php echo count($counties[sanitize_title( $county )]); ?>" data-info-position="<?php echo $p; ?>" class="pl<?php echo $i; ?>"><a href="#<?php echo sanitize_title( $county ); ?>"><?php echo esc_html( $county ); ?></a></li>
						<?php $i++; endforeach; ?>
						</ul>
					</div>
				</div>
				<!-- END OF Map - Poland -->

				<?php if($counties) : ?>
				<div class="container map-list-container shadowed bgcolor1">
            <div class="row">
							<div class="map-list">
								<ul class="list-unstyled">
									<?php foreach($countiesNames as $name) : ?>
										<?php $key = sanitize_title( $name ); ?>
										<?php if( $counties[$key]) : ?>
										<li class="f16 color4"><span class="text-uppercase inline-block"><?php echo esc_html( $name ); ?></span> <strong class="inline-block"> - <?php echo esc_html( places_count_text( count($counties[$key]) ) ) ?></strong> <a href="<?php echo esc_url( get_permalink( template_id( 'search' ) ).'#'.$key ); ?>" class="btn btn-primary btn-sm text-uppercase">Zobacz obiekty</a></li>
										<?php endif; ?>
									<?php endforeach; ?>
								</ul>
							</div>
            </div>
        </div>
				<?php endif; ?>

			</div>
		</div>

	</div>
</main>

<?php get_footer(); ?>