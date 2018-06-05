<?php
/**
 * Template name: Wyszukiwarka
 * The template for displaying serach places page.
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
$counties 	= counties_list();
$regions 	= regions_list();
?>
	
<main data-ng-controller="SearchPlacesCtrl">
    <div class="container-fluid page-container search-places-page">
		<div class="search-places-top">
			<div class="container">
				<div class="search-places-entry text-entry">
					<?php if(!empty_content(get_the_content())) : ?><?php echo wp_kses_post( get_the_content() ); ?><?php endif; ?>
				</div>
				<div class="search-places-form">
					<div class="inline-block">
						<p class="no-margin f36 font2 color3 text-uppercase"><strong>Wyszukiwarka</strong></p>
					</div>
					<div class="inline-block">
						<div class="inline-block spf-filters">
							<p class="no-margin lh18 color3">Wybierz sposób filtrowania</p>
							<label 
								class="normal no-margin color3 filter-type" 
								data-ng-class="{'filter-type-active' : !ftIsRegion, '' : ftIsRegion}"
								data-ng-click="ftIsRegion=false"
								>
									<span class="relative inline-block">
										<i class="fa-16 absolute-center-both fa fa-circle-o"></i>
										<i class="fa-16 absolute-center-both fa fa-dot-circle-o"></i>
									</span>Województwo
							</label>

							<label 
								class="normal no-margin color3 filter-type"
								data-ng-class="{'filter-type-active' : ftIsRegion, '' : !ftIsRegion}"
								data-ng-click="ftIsRegion=true"
								>
									<span class="relative inline-block">
										<i class="fa-16 absolute-center-both fa fa-circle-o"></i>
										<i class="fa-16 absolute-center-both fa fa-dot-circle-o"></i>
									</span>Region
							</label>
						</div>
						<div class="inline-block spf-select">
							<div class="form-group no-margin spf-select-field inline-block" data-ng-show="!ftIsRegion" data-ng-class="{'spf-invalid' : spfInvalid}">
							<?php if($counties) : ?>
								<select data-ng-change="spfCheckValue()" data-ng-model="filterby" name="spf-counties" id="spf-counties" class="selectpicker" data-size="5" data-live-search="true">
									<option value="none">- Wybierz z listy -</option>
									<?php foreach($counties as $county) : ?>
									<option data-county="<?php echo sanitize_title( $county ) ?>" value="<?php echo esc_html( $county ); ?>"><?php echo esc_html( $county ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
							</div>
							<div class="form-group no-margin spf-select-field inline-block" data-ng-show="ftIsRegion" data-ng-class="{'spf-invalid' : spfInvalid}">
							<?php if($regions) : ?>
								<select data-ng-change="spfCheckValue()" data-ng-model="filterby" name="spf-regions" id="spf-regions" class="selectpicker" data-size="5" data-live-search="true">
									<option value="none">- Wybierz z listy -</option>
									<?php foreach($regions as $region) : ?>
									<option data-region="<?php echo sanitize_title( get_the_title($region) ); ?>" value="<?php echo $region; ?>"><?php echo esc_html( get_the_title($region) ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
							</div>
							<div class="form-group no-margin spf-filter-submit inline-block">
								<span data-ng-click="filterBy()" class="btn btn-primary btn-form text-uppercase"><i class="fa fa-search"></i>Szukaj</span>
							</div>
						</div>
					</div>
				</div>
				<div class="sort-places-form">
					<p class="f24 font2 color2 text-center margin-md text-uppercase"><strong>Dodatkowe filtrowanie</strong></p>
					<div class="sort-places-options row">
						<?php if(get_extras_list()) : ?>
							<?php $extras = count(get_extras_list()) ?>
							<?php $i=1; foreach(get_extras_list() as $extra) : ?>
							<div class=" col-xs-6 col-sm-3 sort-places-option">
								<sort-by-option class="spo-option" data-ng-model="spo.sortby" spo-value="<?php echo esc_html( $extra['value'] ) ?>" spo-label="<?php echo esc_html( $extra['label'] ) ?>">
								</sort-by-option>
							</div>

							<?php if($i%4==0 && $i!=$extras) : ?>
							</div>
							<div class="sort-places-options row">
							<?php endif; ?>

							<?php $i++; endforeach; ?>
						<?php endif; ?>
					</div>
					<div class="form-group no-margin spo-filter-submit text-center">
						<span data-ng-click="!sorting && sortBy()" class="btn btn-primary btn-form text-uppercase">Zastosuj</span>
					</div>
				</div>
			</div>
		</div>

		<section id="placesAnchor" data-ng-controller="LoadPlacesCtrl" data-ng-init="initLoad({})">
			<div class="container-fluid places-list-container relative places-bg-image">
				<!--<div class="places-list-cover absolute-cover" data-ng-if="sorting">
					<div class="uil-rolling-css preloader">
						<div><div></div><div></div></div>
					</div>
				</div>-->
				<div></div>
				<div class="container" id="home-places">
					<?php echo page_header( get_the_ID(), 'Wyniki wyszukiwania', false); ?>
					<div class="alert alert-warning text-center" data-ng-if="notFoundByFilter && !loading">
						Nie znaleziono miejsc spełniających wybrane kryteria. Pozostałe MIEJSCA Z DUSZĄ
					</div>
					<div class="alert alert-success text-center" data-ng-if="(isFilter || isSortOn) && FoundByFilter > 0 && !loading">
						Znalezione MIEJSCA Z DUSZĄ <!--<strong>{{ FoundByFilter }}</strong>-->
					</div>
					<div data-ng-if="hasData">
						<div data-ng-repeat="place in places" data-ng-show="place.visible">
							<div class="alert alert-success text-center" data-ng-if="(isFilter || isSortOn) && FoundByFilter > 0 && !loading && $index == FoundByFilter">
								Pozostałe MIEJSCA Z DUSZĄ:
							</div>
							<place-item data-place="place"></place-item>
						</div>
						<div class="posts-space" data-ng-if="lastpage"></div>
						<div class="show-more-places text-center" data-ng-if="!lastpage && !loading">
							<span class="btn btn-success text-uppercase" data-ng-click="loadPlaces({filter:filterby, filterType:filterType, sortBy:sortOptions})">Zobacz kolejne</span>
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

    </div>
</main>

<?php get_footer(); ?>
