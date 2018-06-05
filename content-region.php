<?php
/**
 * @author Rafał Puczel
 */
?>

<?php  
$images 	= get_field('_region_images');
$content	= get_field('_region_content');
$county 	= get_field('_region_county');
?>

<article>
	<div id="post-<?php the_ID(); ?>" <?php post_class('row'); ?>>
		<div class="row">
			<div class="col-sm-5 place-single-left">
				<?php if($images) : ?>
				<div class="place-single-gallery row">
					<div class="place-single-image pull-left relative overflow">
						<?php $i=1; foreach($images as $image) : ?>
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
			<div class="place-single-right">
				<section>
					<header>
						<h1 class="h2"><?php the_title(); ?></h1>
					</header>
					<div class="place-single-info region-single-info">
						<?php if($county) : ?>
							<p class="margin-sm">Położenie:</p>
							<h5 class="margin-sm font1 lh1"><?php echo esc_html( $county ); ?></h5>
						<?php endif; ?>
					</div>
					<div class="row place-single-content">
						<?php if(!empty_content($content)) : ?>
						<div class="row place-single-content-text"><?php echo wp_kses_post( $content ); ?></div>
						<?php endif; ?>
					</div>
				</section>
			</div>
		</div>
	</div>
</article>
