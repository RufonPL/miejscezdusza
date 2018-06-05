<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @author Rafał Puczel
 */
?>

</div>

<?php 
$logo = get_field('_logo', 'option'); 
$args = array(
	'theme_location' 	=> 'footer', 
	'container_class' 	=> 'navbar-footer', 
	'menu_class' 		=> 'footer-nav',
	'fallback_cb'		=> '',
	'menu_id' 			=> 'footer-menu',
	'walker' 			=> new Rfswp_Walker_Nav_Menu()
); 
?>

<footer>

	<?php if( rfs_role('subscriber') && is_profile_page() ) : //user ?> 
	<?php else : ?>
		<?php require_template_part('awarded', 'footer'); ?>
	<?php endif; ?>

	<div class="container-fluid footer">
		<div class="container">
			<div class="row">
				<div class="col-md-4">
					<?php if($logo) : ?>
					<a class="nuh" href="<?php bloginfo( 'url' ); ?>">
						<img class="inline-block footer-logo" src="<?php echo esc_url($logo['sizes']['medium']); ?>" alt="<?php echo esc_attr($logo['alt']); ?>">
					</a>
					<?php endif; ?>
					<h6 class="inline-block copyright font1 normal text-uppercase no-margin">Wszelkie prawa zastrzeżone<br>Copyright &copy; <?php echo date('Y'); ?></h6>
				</div>
				<div class="col-md-8 text-right">
					<?php wp_nav_menu($args); ?>
				</div>
			</div>
		</div>
	</div>
	
</footer>
</div>
<span class="rwd-size" id="s1366"></span><span class="rwd-size" id="s1280"></span><span class="rwd-size" id="s1152"></span><span class="rwd-size" id="s1024"></span><span class="rwd-size" id="s992"></span><span class="rwd-size" id="s860"></span><span class="rwd-size" id="s768"></span><span class="rwd-size" id="s640"></span><span class="rwd-size" id="s540"></span><span class="rwd-size" id="s480"></span>

<?php wp_footer(); ?> 
<?php enqueue_footer_css(); ?>

</body>
</html>