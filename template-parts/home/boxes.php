<?php $promoted = get_promoted_places('box'); ?>
<?php if($promoted) : ?>
	<?php $count = count($promoted); ?>
	<section>
		<div class="container-fluid boxes">
			<div class="container">
				<div class="row">
				<?php foreach($promoted as $place_id) : ?>
					<?php  
					$thumbnail 	= get_field('_place_thumbnail', $place_id);
					$images    	= get_field('_place_images', $place_id);
					$image 	   	= !$thumbnail && $images[0]['width'] > 1159 && $images[0]['height'] > 489 ? $images[0] : $thumbnail;
					$location 	= get_field('_place_city', $place_id);
					switch($count) {
						case 1:
							$cols = 'col-md-12';
							break;
						case 2:
							$cols = 'col-md-6';
							break;
						case 3:
							$cols = 'col-md-4';
							break;
						default:
							break;				
					}
					?>
					<?php if($image) : ?>
					<div class="box <?php echo $cols; ?>">
						<div class="box-inner relative overflow">
							<a href="<?php echo esc_url( get_permalink($place_id) ); ?>">
								<div class="box-grad"></div>
								<div class="box-thin-border absolute-cover transition"></div>
								<img src="<?php echo esc_url($image['sizes']['box-image']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
								<div class="box-content absolute-center-both text-center zindex3">
									<?php if($location) : ?><h2 class="h1 color3 tshadow no-margin"><?php echo esc_html( $location ); ?></h2><?php endif; ?>
									<h4 class="color3 tshadow font1 normal no-margin"><?php echo esc_html( get_the_title($place_id) ); ?></h4>
									<span class="btn btn-primary text-uppercase">Zobacz</span>
								</div>
							</a>
						</div>
					</div>
					<?php endif; ?>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>