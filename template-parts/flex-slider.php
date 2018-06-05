<?php 
$slider = get_field('_slider'); 
$slide_speed = get_field('_slide_speed'); 
$slide_speed = $slide_speed ? $slide_speed : 5000;
?>
<?php if($slider) : ?>
<div class="slider container-fluid overflow" id="flexslider">
    <div class="flexslider" data-slide-speed="<?php echo esc_html($slide_speed); ?>">
        <ul class="slides">
        <?php foreach($slider as $slide) : ?>
			<?php  
			$image = $slide['_slide_image'];
			$text1 = $slide['_slide_text_1'];
			$text2 = $slide['_slide_text_2'];
			$link  = $slide['_slide_link'];
            ?>
            <li>
                <img src="<?php echo esc_url($image['sizes']['max-image']); ?>" alt="<?php echo esc_attr($image['alt']); ?>"/>
                <div class="flex-caption container">
                	<?php if($text1) : ?><h3 class="color5"><?php echo p2br($text1); ?><span></span></h3><?php endif; ?>
                    <?php if($text2) : ?><h2 class="h1 color3 no-margin"><strong><?php echo p2br($text2); ?></strong></h2><?php endif; ?>
                	<?php if($link) : ?><a class="btn btn-primary" href="<?php echo esc_url(get_permalink($link)); ?>"><?php pll_trans('Czytaj więcej'); ?> <i class="fa fa-angle-double-right"></i></a><?php endif; ?>
                </div><!--end flex caption-->
            </li>
        <?php endforeach; ?>
        </ul><!--end slides-->
    </div><!--end flexslider-->
    <div class="custom-navigation">
        <a href="#" class="flex-arr flex-prev transition"><i class="fa fa-angle-left fa-16 color2 absolute-center-both"></i></a>
        <a href="#" class="flex-arr flex-next transition"><i class="fa fa-angle-right fa-16 color2 absolute-center-both"></i></a>
    </div>
    <div class="custom-controls-container"></div>
</div><!--end slider-->
<?php endif; ?>
