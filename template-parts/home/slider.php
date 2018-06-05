<?php $promoted = get_promoted_places('slider'); ?>
<?php if($promoted) : ?>
	<?php $count = count($promoted); ?>
	<div class="container-fluid home-slider overflow" id="home-slider">
		<div class="row">
			<div class="owl-carousel home-slider-carousel" id="home-slider-carousel" data-images="<?php echo $count; ?>">
			<?php foreach($promoted as $place_id) : ?>
				<?php  
				$thumbnail = get_field('_place_thumbnail', $place_id);
				$images    = get_field('_place_images',$place_id);
				$image 	   = !$thumbnail && $images[0]['width'] > 1159 && $images[0]['height'] > 489 ? $images[0] : $thumbnail;
				?>
				<?php if($image) : ?>
				<div class="home-slider-item relative" data-item="<?php echo $i; ?>">
					<div class="home-slider-item-mask absolute-cover"></div>
					<div class="home-slider-item-inner relative">
						<img src="<?php echo esc_url($image['sizes']['slider-image']); ?>" alt="<?php echo esc_attr($image['alt']); ?>"/>
						<div class="absolute-center-both home-slider-item-text">
							<h2 class="color3"><?php echo esc_html( get_the_title($place_id) ); ?></h2>
							<a href="<?php echo esc_url( get_permalink($place_id) ); ?>" class="btn btn-primary text-uppercase">Zobacz</a>
						</div>
					</div>
				</div>
				<?php endif; ?>
			<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php endif; ?>