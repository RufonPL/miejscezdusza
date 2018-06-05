<?php
/**
 * Template name: Miejsce miesiąca
 * The template for displaying monthly contest page.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @author Rafał Puczel
 */

get_header(); ?>

<?php $status = get_field('_awarded_month_status', 'option'); ?>

<main>
	<div class="container-fluid page-container month-place-page">

		<section>
			<div class="page-entry">
				<div class="container text-center">
					<div class="month-place-header">
						<?php echo monthPlaceHeader(); ?> <?php echo yearPlaceHeader(true) ?>
					</div>
					<?php if(!empty_content(get_the_content())) : ?>
					<div class="entry text-entry"><?php the_content(); ?></div>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<?php if( $status && monthVotingActive() ) : ?>
		<section id="placesAnchor" data-ng-controller="LoadPlacesCtrl" data-ng-init="initLoad({contest:'month'})">
			<div class="container-fluid places-list-container places-bg-image">
				<div class="container" id="places-list">
					<?php echo page_header( 'option', get_field('_page_title_text_places_list', 'option'), false, '_places_list' ); ?>

					<div class="alert alert-warning text-center" data-ng-if="!hasData">
						Brak miejsc biorących udział w plebiscycie
					</div>

					<div data-ng-if="hasData">	
						<div data-ng-repeat="place in places" data-ng-show="place.visible">
							<place-item data-place="place" data-contest-place="month" data-voting-active="<?php echo monthVotingActive(); ?>"></place-item>
						</div>
						<div class="posts-space" data-ng-if="lastpage"></div>
						<div class="show-more-places text-center" data-ng-if="!lastpage && !loading">
							<span class="btn btn-success text-uppercase" data-ng-click="loadPlaces({contest:'month'})">Zobacz kolejne</span>
						</div>
						<div class="preloader-container overflow" data-ng-if="loading">
							<div class="uil-rolling-css preloader">
								<div><div></div><div></div></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<section id="placesWinnersAnchor" data-ng-controller="WinnersCtrl" data-ng-init="winnersInit({type:'month'})">
			<div class="container-fluid winners-container places-bg-image" data-ng-if="!noData">
				<div class="container" id="winners-list">
					<?php echo page_header( 'option', get_field('_page_title_text_places_list', 'option'), false, '_places_list' ); ?>
					<div data-ng-repeat="winner in winners" data-ng-show="winner.visible">
						<region-item data-single-item="winner" data-item-type="month"></region-item>
					</div>
					<div class="posts-space" data-ng-if="lastpage"></div>
					<div class="show-more-places text-center" data-ng-if="!lastpage && !loading">
						<span class="btn btn-success text-uppercase" data-ng-click="loadWinners({type:'month'})">Zobacz starsze</span>
					</div>
					<div class="preloader-container overflow" data-ng-if="loading">
						<div class="uil-rolling-css preloader">
							<div><div></div><div></div></div>
						</div>
					</div>
				</div>
			</div>
		</section>
		
	</div>
</main>

<?php get_footer(); ?>