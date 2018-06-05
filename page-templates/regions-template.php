<?php
/**
 * Template name: Regiony
 * The template for displaying regions page.
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
$entry 		= get_field('_regions_entry');
$regions 	= regions_list();
$ppp 		= 4;
?>

<main data-ng-controller="RegionsCtrl">
	<div class="container-fluid page-container regions-page">

		<section>
			<div class="page-entry">
				<div class="container">
					<?php echo page_header( get_the_ID(), get_the_title()); ?>

					<div class="row">
						<div class="page-entry-text col-sm-8 text-entry">
							<?php if(!empty_content($entry)) : ?><?php echo wp_kses_post( $entry ); ?><?php endif; ?>
						</div>
						<div class="col-sm-4 regions-list">
							<div class="form-group no-margin spf-select-field inline-block">
							<?php if($regions) : ?>
								<select data-ng-change="goToRegion('<?php echo esc_url( get_permalink( template_id( 'search' ) ) ); ?>')" data-ng-model="region" name="region" id="region" class="selectpicker" data-size="5" data-live-search="true">
									<option value="none">- Wybierz z listy -</option>
									<?php foreach($regions as $region) : ?>
									<option value="<?php echo esc_url( get_permalink($region) ); ?>"><?php echo esc_html( get_the_title($region) ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<div class="places-bg-image">

			<div class="container regions-container">
				<?php if($regions) : ?>
					<?php $count = count($regions); ?>
					<?php $i=1; foreach($regions as $region) : ?>
						<?php  
						$thumbnail = get_field('_region_thumbnail', $region);
						$images    = get_field('_region_images', $region);
						$image 	   = !$thumbnail && $images[0]['width'] > 1109 && $images[0]['height'] > 299 ? $images[0] : $thumbnail;
						?>
						<?php if($image) : ?>
						<div class="row region-item relative transition">
							<a href="<?php echo esc_url( get_permalink($region) ); ?>" class="nuh">
							<div class="region-item-mask absolute-cover transition"></div>
							<img src="<?php echo esc_url($image['sizes']['region-image']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
							<div class="region-item-info absolute-center-both">
								<h2 class="text-uppercase color3"><?php echo esc_html( get_the_title($region) ); ?></h2>
								<span class="btn btn-primary text-uppercase btn-md">Zobacz więcej</span>
							</div>
							</a>
						</div>
						<?php endif; ?>
					
					<?php if($i == $ppp) : ?><?php break; ?><?php endif; ?>

					<?php $i++; endforeach; ?>

					<?php if($count > $ppp) : ?>
					<div id="regionsAnchor">
						<div data-ng-repeat="region in regions" data-ng-show="region.visible">
							<region-item data-single-item="region" data-item-type="region"></region-item>
						</div>
						<div class="posts-space" data-ng-if="lastpage"></div>
						<div class="show-more-places text-center" data-ng-if="!lastpage && !loading">
							<span class="btn btn-success text-uppercase" data-ng-click="loadRegions()">Zobacz kolejne</span>
						</div>
						<div class="preloader-container overflow" data-ng-if="loading">
							<div class="uil-rolling-css preloader">
								<div><div></div><div></div></div>
							</div>
						</div>
					</div>
					<?php endif; ?>

				<?php endif; ?>
			</div>

		</div>
		
	</div>
</main>

<?php get_footer(); ?>