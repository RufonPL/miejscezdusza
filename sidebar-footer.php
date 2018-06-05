<?php
/**
 * The sidebar containing the footer widget areas.
 *
 * If no active widgets in either sidebar, they will be hidden completely.
 *
 */

// If none of the sidebars have widgets, then let's bail early.
if ( ! is_active_sidebar( 'footer-1' ) && ! is_active_sidebar( 'footer-2' ) && ! is_active_sidebar( 'footer-3' ))
	return;

// If we get this far, we have widgets. Let's do this.
?>

<div id="tertiary" class="widget-area" role="complementary">
	<div class="sidebar-inner">
		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
		<div class="col-md-3 col-sm-3 col-xs-6 footer-col">
			<?php dynamic_sidebar( 'footer-1' ); ?>
		</div><!--end footer col-->
		<?php endif; ?>

		<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
		<div class="col-md-4 col-sm-4 col-xs-6 footer-col">
			<?php dynamic_sidebar( 'footer-2' ); ?>
		</div><!--end footer col-->
		<?php endif; ?>
		
		<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
		<div class="col-md-2 col-sm-2 col-xs-6 footer-col">
			<?php dynamic_sidebar( 'footer-3' ); ?>
		</div><!--end footer col-->
		<?php endif; ?>
	</div><!--end sidebar-inner-->
</div><!--end tertiary-->
