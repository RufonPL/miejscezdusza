<?php  
$header = get_field('_profile_premium_header', 'option');
$text 	= get_field('_profile_premium_text', 'option');

$header = $header ? $header : get_the_title();

$subscription_header 	= get_field('_subscription_header', 'option');
$subscription_header2 	= get_field('_subscription_header_expired', 'option');
$subscriptions_active 	= array();

$adverts_header = get_field('_adverts_header', 'option');
$adverts_header = $adverts_header ? $adverts_header : 'Reklama';

$tax = get_field('_vat_tax', 'option');

$place_id 			= get_owner_place( get_current_user_id() );
$is_place_active 	= is_place_active($place_id);

$invoices = invoices_list();
?>
<div data-ng-controller="PremiumCtrl">
	<section>
		<div class="page-entry">
			<div class="container">
				<?php echo page_header('option', $header, true, '_profile_premium'); ?>
				<?php if(!empty_content($text)) : ?>
				<div class="entry-text"><?php echo wp_kses_post( $text ); ?></div>
				<?php endif; ?>

				<p class="margin-md color2 f24 subscription-term-info text-center">
					<?php if( $is_place_active ) : ?>
						<?php if(!empty_content( $subscription_header )) : ?><?php echo p2br($subscription_header); ?><?php endif; ?>
					<?php else : ?>
						<?php if(!empty_content( $subscription_header2 )) : ?><?php echo p2br($subscription_header2); ?><?php endif; ?>
					<?php endif; ?>
				</p>

				<div class="subscriptions row">
				<?php 
				for($i=1; $i<=3; $i++)  {
					$status = get_field('_subscription_'.$i.'_status', 'option');
					if( $status ) {
						$subscriptions_active[] = 1;
					}
				} 	
				switch( count($subscriptions_active) ) {
					case 2:
						$cols = 'col-sm-6';
						break;
					case 3:
						$cols = 'col-sm-4';
						break;
					default:
						$cols = 'col-sm-12';
				}
				?>
				<?php for($i=1; $i<=3; $i++) : ?>
					<?php  
					$status = get_field('_subscription_'.$i.'_status', 'option');
					$name 	= get_field('_subscription_'.$i.'_name', 'option');
					$term 	= get_field('_subscription_'.$i.'_term', 'option');
					$mce 	= get_field('_subscription_'.$i.'_term_text', 'option');
					$price 	= get_field('_subscription_'.$i.'_price', 'option');
					?>
					<?php if($status) : ?>
					<div class="subscription-box <?php echo $cols; ?>" id="subscription-box-<?php echo $i; ?>">
						<div class="subscription-box-inner text-center">
							<?php if($name) : ?><p class="margin-md f30 font2 color2 text-uppercase subscription-name">Abonament <strong><?php echo esc_html( $name ); ?></strong></p><?php endif; ?>
							<?php if($term) : ?><p class="margin-md f30 font2 color2 bold subscription-term"><span><?php echo esc_html( $term ); ?></span> <?php echo esc_html( $mce ); ?></p><?php endif; ?>
							<?php if($price) : ?><p class="margin-md font2 color5 bold f30 subscription-price"><span><?php echo esc_html( $price ); ?></span>zł<small>netto</small></p><?php endif; ?>
							<div class="subscription-select row">
								<div class="subscription-checkbox relative inline-block" data-ng-click="selectSubscription(<?php echo $i; ?>)" data-ng-class="{'checked': selectedSubscription == <?php echo $i; ?>}">
									<i class="fa fa-check fa-36 transition"></i>
								</div>
								<p class="no-margin f18 text-uppercase color2 light inline-block">{{ selectedSubscription == <?php echo $i; ?> ? 'wybrany' : 'wybierz' }}</p>
							</div>
						</div>
					</div>
					<?php endif; ?>
				<?php endfor; ?>	
				</div>

				<p class="margin-md text-center subscription-net-info">*Wszystkie kwoty są cenami netto</p>

			</div>
		</div>
	</section>

	<section>
	<div class="places-bg-image">

		<div class="container premium-promotions">
			<?php echo page_header('option', $adverts_header, true, '_rek', true, 'no-margin'); ?>
			<?php 
				//get_owner_place_term(get_current_user_id()) ?>
			<div class="promotions row">
			<?php for($i=1; $i<=3; $i++) : ?>	
				<?php  
				$status = get_field('_advert_'.$i.'_status', 'option');
				$image 	= get_field('_advert_'.$i.'_image', 'option');
				$text 	= get_field('_advert_'.$i.'_text', 'option');
				$price 	= get_field('_advert_'.$i.'_price', 'option');

				switch($i) {
					case 1:
						$name = 'Slider Główny';
						break;
					case 2:
						$name = 'Box na stronie głównej';
						break;
					case 3:
						$name = 'Obiekt polecany';
						break;
					default:
						break;
				}
				?>
				<?php if($status) : ?>
				<div class="promo-box row" id="promo-box-<?php echo $i; ?>">
					<div class="promo-box-left col-sm-6">
						<div class="promo-image-frame">
							<div class="promo-image-screen relative">
								<?php if($image) : ?>
								<div class="promo-image overflow">
									<img src="<?php echo esc_url($image['sizes']['box-image']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
								<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="promo-box-right col-sm-6">
						<p class="margin-sm color2 font2 f30 bold promo-name"><span class="text-uppercase promo-name-name"><?php echo $name; ?></span><?php if($price) : ?> - <span class="promo-price"><?php echo esc_html( $price ); ?></span>zł<small>netto</small><?php endif; ?></p>
						<div class="promo-text">
							<p><?php if(!empty_content($text)) : ?><?php echo wp_kses($text, array('br'=>array())); ?><?php endif; ?></p>
						</div>
						<div class="promo-select row">
							<div class="subscription-checkbox relative inline-block" data-ng-click="selectPromo(<?php echo $i; ?>, $event)">
								<i class="fa fa-check fa-36 transition"></i>
							</div>
							<p class="no-margin f18 text-uppercase color2 light inline-block">{{ promoSelected(<?php echo $i; ?>) ? 'wybrana reklama' : 'wybieram reklamę' }}</p>
						</div>
					</div>
				</div>
				<?php endif; ?>
			<?php endfor; ?>
			</div>

			<div class="premium-purchase text-right" data-ng-show="!nothingSelected" data-ng-init="tax='<?php echo absint( $tax ); ?>'">
				<div class="premium-purchase-visible inline-block">
					<span class="btn btn-success text-uppercase" data-ng-click="showPaymentMethods()">Kupuję</span>
					<span class="purchase-separator"></span>
				</div>

				<div class="payment-methods slide-toggle text-center" data-ng-show="showPayMethods">
					<p class="margin-sm f24 color2">Podsumowanie</p>
					<table class="table table-striped summary-table">
						<thead>
							<tr>
								<th class="summary-col-1">Produkt</th>
								<th class="summary-col-2">Okres ważności</th>
								<th class="summary-col-3">Cena netto</th>
							</tr>
						</thead>
						<tbody></tbody>
						<tfoot>
							<tr>
								<td></td>
								<td><strong>VAT</strong></td>
								<td class="summary-vat"></td>
							</tr>
							<tr>
								<td></td>
								<td><strong>Do zapłaty</strong></td>
								<td class="summary-total"></td>
							</tr>
						</tfoot>
					</table>
					<p class="margin-md f24 color2">Wybierz sposób płatności</p>
					<div class="alert alert-danger text-center" data-ng-if="paymentError">Wystąpił błąd. Odśwież stronę i spróbuj ponownie.</div>
					<div>
						<p class="margin-md pay-with"><span class="inline-block payment-btn payu-btn relative" data-ng-click="makePayment($event, 'payu')"><i class="loader"></i></span></p>
						<p class="margin-md pay-with"><span class="inline-block payment-btn paypal-btn relative" data-ng-click="makePayment($event, 'paypal')"><i class="loader"></i></span></p>
					</div>
				</div>
			</div>

			<?php if($invoices) : ?>
			<div class="premium-invoices text-right">
				<p class="color2 text-uppercase ls2 relative btn btn-warning" id="show-invoices" data-ng-click="showInvoicesList()">Zobacz faktury</p>
				
				<div class="premium-invoices-inner text-center slide-toggle" data-ng-show="showInvoices">
					<p class="text-center f30 color2 font2"><strong>Faktury</strong></p>

					<table class="table table-striped invoices-table">
						<thead>
							<tr>
								<th>Nr faktury</th>
								<th>Data wystawienia</th>
								<th class="text-center">Pobierz</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($invoices as $invoice) : ?>
								<td><?php echo esc_html( $invoice['order_id'] ); ?></td>
								<td><?php echo esc_html( date('Y-m-d', strtotime($invoice['date'])) ); ?></td>
								<td class="text-center"><a class="invoice-download-icon transition" href="<?php echo esc_url( get_permalink().'?invoice='.urlencode( $invoice['order_id'] ) ); ?>"><img src="<?php bloginfo('template_url'); ?>/images/download.svg" alt=""></a></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<?php endif; ?>

		</div>

	</div>
	</section>

</div>