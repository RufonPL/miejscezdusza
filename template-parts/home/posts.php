<section id="placesAnchor" data-ng-controller="LoadPlacesCtrl" data-ng-init="initLoad({})">
	<div class="container-fluid places-list-container places-bg-image" data-ng-if="hasData">
		<div class="container" id="places-list">
			<?php echo page_header( 'option', get_field('_page_title_text_places_list', 'option'), false, '_places_list' ); ?>
			<div data-ng-repeat="place in places" data-ng-show="place.visible">
				<place-item data-place="place"></place-item>
			</div>
			<div class="posts-space" data-ng-if="lastpage"></div>
			<div class="show-more-places text-center" data-ng-if="!lastpage && !loading">
				<span class="btn btn-success text-uppercase" data-ng-click="loadPlaces(dynamicData)">Zobacz kolejne</span>
			</div>
			<div class="preloader-container overflow" data-ng-if="loading">
				<div class="uil-rolling-css preloader">
					<div><div></div><div></div></div>
				</div>
			</div>
		</div>
	</div>
</section>