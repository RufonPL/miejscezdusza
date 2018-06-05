<?php  
$header = get_field('_profile_user_header', 'option');
$text 	= get_field('_profile_user_text', 'option');

$header = $header ? $header : get_the_title();

$my_places 			= array();
$placesSeen 		= get_user_places('seen');
$placesToVisit 	= get_user_places('to-visit');
$my_places 		 	= array_merge($placesSeen, $placesToVisit);

$counts = array();
?>
<section>
	<div class="page-entry">
		<div class="container">
			<?php echo page_header('option', $header, true, '_profile'); ?>
			<?php if(!empty_content($text)) : ?>
			<div class="entry-text text-entry"><?php echo wp_kses_post( $text ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>

<div class="places-bg-image">

	<div class="container">

		<!-- Map - Poland -->
		<div id="map-poland-container" class="relative user-map-poland">
			<?php if($my_places) : ?>
			<div id="map-poland-markers" data-has-info="yes">
				<?php foreach($my_places as $place) : ?>
					<?php  
					$county  = get_field('_place_county', $place['id']);
					$address = get_field('_place_coords', $place['id']);
					$lng 	 = $address['lng'];
					$lat 	 = $address['lat'];
					foreach(map_counties() as $c => $p) {
						if( strcmp( $county, $c ) == 0 ) {
							$counts[sanitize_title( $c )][] = 1;
						}
					}
					$type 	= $place['type'];
					?>
					<?php if($address) : ?>
					<span data-county="<?php echo esc_html( $county ); ?>" data-place="<?php echo esc_html( get_the_title( $place['id'] ) ); ?>" data-place-link="<?php echo esc_url( get_permalink( $place['id'] ) ); ?>" class="map-marker my-map-marker <?php echo $type; ?>" style="left:<?php echo set_map_pin_coords('lng', $lng); ?>%; top:<?php echo set_map_pin_coords('lat', $lat); ?>%"></span>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
			<div id="map-poland" data-has-info="no">
				<ul class="poland">
				<?php $i=1; foreach(map_counties() as $county => $p) : ?>
					<li class="pl<?php echo $i; ?>"><a href="#<?php echo sanitize_title( $county ); ?>"><?php echo esc_html( $county ); ?></a></li>
				<?php $i++; endforeach; ?>
				</ul>
			</div>
		</div>
		<!-- END OF Map - Poland -->
		
		<?php if($placesSeen || $placesToVisit) : ?>
			<div class="container map-list-container shadowed bgcolor1">
					<div class="row">
						<div class="map-list map-list-user">
							<?php if($placesSeen) : ?>
							<h3 class="text-center"><span class="map-list-user-seen inline-block"></span>Tu byłem</h3>
							<ul class="list-unstyled">
								<?php foreach($placesSeen as $place) : ?>
									<?php $county  = get_field('_place_county', $place['id']); ?>
									<li class="f16 color4"><span class="text-uppercase inline-block"><?php echo esc_html( get_the_title($place['id']) ); ?></span> <strong class="inline-block"> - <?php echo esc_html( $county ); ?></strong> <a href="<?php echo esc_url( get_permalink( $place['id'] ) ); ?>" class="btn btn-primary btn-sm text-uppercase">Zobacz obiekt</a></li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>
							<?php if($placesToVisit) : ?>
							<h3 class="text-center"><span class="map-list-user-to-visit inline-block"></span>Chcę odwiedzić</h3>
							<ul class="list-unstyled">
								<?php foreach($placesToVisit as $place) : ?>
									<?php $county  = get_field('_place_county', $place['id']); ?>
									<li class="f16 color4"><span class="text-uppercase inline-block"><?php echo esc_html( get_the_title($place['id']) ); ?></span> <strong class="inline-block"> - <?php echo esc_html( $county ); ?></strong> <a href="<?php echo esc_url( get_permalink( $place['id'] ) ); ?>" class="btn btn-primary btn-sm text-uppercase">Zobacz obiekt</a></li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>
						</div>
					</div>
			</div>
		<?php endif; ?>

	</div>

</div>

<?php require_template_part('awarded', 'footer', array('type'=>'user')); ?>

<section id="placesAnchor" data-ng-controller="LoadPlacesCtrl" data-ng-init="initLoad({promo:'recommended'})">
	<div class="container-fluid places-list-container" data-ng-if="hasData">
		<div class="container" id="places-list">
			<?php echo page_header( 'option', get_field('_page_title_text_recommended', 'option'), false, '_recommended' ); ?>
			<div data-ng-repeat="place in places" data-ng-show="place.visible">
				<place-item data-place="place"></place-item>
			</div>
			<div class="posts-space" data-ng-if="lastpage"></div>
			<div class="show-more-places text-center" data-ng-if="!lastpage && !loading">
				<span class="btn btn-success text-uppercase" data-ng-click="loadPlaces({promo:'recommended'})">Zobacz kolejne</span> 
			</div>
			<div class="preloader-container overflow" data-ng-if="loading">
				<div class="uil-rolling-css preloader">
					<div><div></div><div></div></div>
				</div>
			</div>
		</div>
	</div>
</section>
	