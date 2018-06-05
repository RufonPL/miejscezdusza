<?php $region_id = $params['region_id']; ?>
<div class="container-fluid places-bg-image recommended-from-region">
	<section id="placesAnchor" data-ng-controller="LoadPlacesCtrl" data-ng-init="initLoad({region: <?php echo absint( $region_id ); ?>})">
		<div class="container-fluid places-list-container places-bg-image" data-ng-if="hasData">
			<div class="container" id="places-list">
				<?php echo page_header( 'option', get_field('_page_title_text_recommended_in_region', 'option'), false, '_recommended_in_region' ); ?>
				<div class="alert alert-warning text-center" data-ng-if="notFoundByFilter && !loading">
					Nie znaleziono miejsc z tego regionu. Polecane MIEJSCA Z DUSZĄ w innych regionach
				</div>
				<div class="alert alert-success text-center" data-ng-if="FoundByFilter > 0 && !loading">
						Znalezione MIEJSCA Z DUSZĄ: <strong>{{ FoundByFilter }}</strong>
					</div>
				<div data-ng-repeat="place in places" data-ng-show="place.visible">
					<div class="alert alert-success text-center" data-ng-if="FoundByFilter > 0 && !loading && $index == FoundByFilter">
						Miejca z pozostałych regionów:
					</div>
					<place-item data-place="place"></place-item>
				</div>
				<div class="posts-space" data-ng-if="lastpage"></div>
				<div class="show-more-places text-center" data-ng-if="!lastpage && !loading">
					<span class="btn btn-success text-uppercase" data-ng-click="loadPlaces({region: <?php echo absint( $region_id ); ?>})">Zobacz kolejne</span>
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