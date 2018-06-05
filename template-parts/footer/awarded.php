<?php  
$bg 			= get_field('_awarded_bg', 'option');
$monthly_image 	= get_field('_awarded_month_image', 'option');
$monthly_link 	= get_field('_awarded_month_link', 'option');
$yearly_image 	= get_field('_awarded_year_image', 'option');
$yearly_link 	= get_field('_awarded_year_link', 'option');
$type 			= isset($params['type']) ? $params['type'] : '';
?>
<div class="container-fluid awarded-container relative overflow <?php if( $type == 'user' ) : ?>awarded-container-user<?php endif; ?>">
	<?php if( $bg && $type != 'user' ) : ?>
	<img class="absolute-center-both grayscale awarded-bg" src="<?php echo esc_url($bg['sizes']['max-image']); ?>" alt="<?php echo esc_attr($bg['alt']); ?>">
	<?php endif; ?>
	<div class="awarded-mask absolute-cover"></div>
	<div class="container relative zindex3">
		<div class="row text-center">
			<div class="col-sm-6 awarded-col">
				<?php if($monthly_image) : ?>
				<a class="awarded-link transition relative nuh" href="<?php echo esc_url( $monthly_link ); ?>">
					<?php echo monthPlaceHeader(true); ?>
					<img src="<?php echo esc_url($monthly_image['sizes']['large']); ?>" alt="<?php echo esc_attr($monthly_image['alt']); ?>">
				</a>
				<?php endif; ?>
				<?php if( $type == 'user' ) : ?>
				<a href="<?php echo esc_url( $monthly_link ); ?>" class="btn btn-vote text-uppercase">Zagłosuj</a>
				<?php endif; ?>
			</div>
			<div class="col-sm-6 awarded-col">
				<?php if($yearly_image) : ?>
				<a class="awarded-link transition relative nuh" href="<?php echo esc_url( $yearly_link ); ?>">
					<?php echo yearPlaceHeader(true); ?>
					<img src="<?php echo esc_url($yearly_image['sizes']['large']); ?>" alt="<?php echo esc_attr($yearly_image['alt']); ?>">
				</a>
				<?php endif; ?>
				<?php if( $type == 'user' ) : ?>
				<a href="<?php echo esc_url( $yearly_link ); ?>" class="btn btn-vote text-uppercase">Zagłosuj</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>