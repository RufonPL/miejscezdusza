<article>
	<div id="post-<?php the_ID(); ?>" <?php post_class('row'); ?>>
		<div class="row">
			<div class="payment-confirmation-text">
			<?php if( isset($_GET['paypal']) ) : ?>

				<?php echo payment_completion_text( sanitize_text_field( $_GET['paypal'] ) ); ?>

			<?php endif; ?>

			<?php if( isset($_GET['payu']) ) : ?>

				<?php echo payment_completion_text( sanitize_text_field( $_GET['payu'] ) ); ?>

			<?php endif; ?>
			</div>
		</div>
	</div>
</article>