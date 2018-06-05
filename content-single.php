<?php
/**
 * @author Rafał Puczel
 */
?>

<?php
collectPlaceVisit(get_the_ID());
$images 	= get_field('_place_images');
$content	= get_field('_place_content');
$county 	= get_field('_place_county');
$region 	= get_field('_place_region');
$contact 	= get_field('_place_contact');
$extras 	= get_field('_place_extras');

$placesSeen = get_user_places('seen');
$placesToVisit = get_user_places('to-visit');

$laurel = monthLaurel( get_the_ID() );
?>

<article>
	<div id="post-<?php the_ID(); ?>" <?php post_class('row'); ?>>
		<div class="row">
			<div class="col-md-5 place-single-left">
				<?php if($images) : ?>
				<div class="place-single-gallery row">
					<div class="place-single-image pull-left relative overflow">
						<?php $i=1; foreach($images as $image) : ?>
						<?php echo $laurel; ?>
						<a data-thumbnail="<?php echo $i; ?>" class="place-single-image-big absolute-center-both <?php if($i==1) : ?>visible<?php endif; ?>" href="<?php echo esc_url($image['sizes']['max-image']); ?>" data-imagelightbox="lightbox">
							<div class="box-thin-border absolute-cover transition"></div>
							<img src="<?php echo esc_url($image['sizes']['place-image']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" data-description="<?php echo esc_attr($image['description']); ?>">
						</a>
						<?php $i++; endforeach; ?>
					</div>
					<div class="clearfix"></div>
					<div id="place-single-gallery-carousel" class="row owl-carousel" data-images="<?php echo count($images); ?>">
					<?php $i=1; foreach($images as $image) : ?>
						<div class="place-single-gallery-item relative">
							<div class="place-single-gallery-item-mask transition absolute-cover"><i class="fa fa-search fa18 color3 absolute-center-both"></i></div>
							<div class="place-single-gallery-image relative">
								<div class="box-thin-border absolute-cover transition"></div>
								<img data-big-image="<?php echo $i; ?>" src="<?php echo esc_url($image['sizes']['thumbnail']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" data-description="<?php echo esc_attr($image['description']); ?>">
							</div>
						</div>
					<?php $i++; endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<div class="col-md-7 place-single-right">
				<section>
					<header>
						<h1 class="h2"><?php the_title(); ?></h1>
					</header>
					<div class="place-single-info relative row">
						<?php if( rfs_role('subscriber') ) : //user ?>
						<div class="place-user-actions" data-ng-controller="userActionsCtrl">
							<span data-ng-init="place='<?php the_ID(); ?>'"></span>
							<span class="nuh pua-btn text-center inline-block" data-ng-click="userAction('visit')" data-ng-init="visit='<?php echo in_array( get_the_ID(), array_column($placesToVisit, 'id') ); ?>'">
								<span data-toggle="tooltip" data-placement="top" title=""><span class="pua-icon pua-to-visit relative" data-ng-class="{'active': visit, 'inactive': seen}"><span class="pua-loader" data-ng-if="action == 'visit' && loading"></span></span></span>
								<p class="margin-sm text-uppercase f12 color2"><span data-ng-if="!visit">Chcę</span><span data-ng-if="visit">Chcesz</span> zobaczyć</p>
							</span>
							<span class="nuh pua-btn text-center inline-block" data-ng-click="userAction('seen')" data-ng-init="seen='<?php echo in_array( get_the_ID(), array_column($placesSeen, 'id') ); ?>'">
								<span data-toggle="tooltip" data-placement="top" title=""><span class="pua-icon pua-seen relative" data-ng-class="{'active': seen}"><span class="pua-loader" data-ng-if="action == 'seen' && loading"></span></span></span>
								<p class="margin-sm text-uppercase f12 color2">Tu <span data-ng-if="!seen">byłem</span><span data-ng-if="seen">byłeś</span></p>
							</span>
						</div>
						<?php else : ?>
						<div class="place-user-actions">
							<a href="<?php echo esc_url( rfs_redirect_to('login') ); ?>" class="nuh pua-btn text-center inline-block">
								<span data-toggle="tooltip" data-placement="top" title="Zaloguj się, żeby dodać obiekt do Twojej MAPY MIEJSC Z DUSZĄ"><span class="pua-icon pua-to-visit relative"></span></span>
								<p class="margin-sm text-uppercase f12 color2">Chcę zobaczyć</p>
							</a>
							<a href="<?php echo esc_url( rfs_redirect_to('login') ); ?>" class="nuh pua-btn text-center inline-block">
								<span data-toggle="tooltip" data-placement="top" title="Zaloguj się, żeby zaznaczyć obiekt na Twojej MAPIE MIEJSC Z DUSZĄ"><span class="pua-icon pua-seen relative"></span></span>
								<p class="margin-sm text-uppercase f12 color2">Tu byłem</p>
							</a>
						</div>
						<?php endif; ?>
						<div class="col-md-7">
							<?php if($county) : ?>
								<p class="margin-sm">Położenie:</p>
								<h5 class="margin-sm font1 lh1"><?php echo esc_html( $county ); ?></h5>
							<?php endif; ?>
							<?php if($region) : ?>
								<p class="margin-sm">Region:</p>
								<h5 class="margin-sm font1 lh1"><?php echo esc_html( get_the_title($region) ); ?></h5>
							<?php endif; ?>
							<?php if($contact) : ?>
								<p class="margin-sm">Kontakt:</p>
								<h5 class="margin-sm font1 lh1"><?php echo esc_html( $contact ); ?></h5>
							<?php endif; ?>
							<?php echo place_extras($extras); ?>
						</div>
					</div>
				</section>
			</div>
		</div>
		<section>
		<div class="row place-single-content">
			<h2>Poznaj miejsce</h2>
			<?php if(!empty_content($content)) : ?>
			<div class="row place-single-content-text"><?php echo wp_kses_post( $content ); ?></div>
			<?php endif; ?>
		</div>
		</section>
	</div>
</article>
