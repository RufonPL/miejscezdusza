<?php
global $wpdb;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	require_once get_template_directory().'/payment/paypal-ipn.php';

	$testMode 		= get_field('_paypal_test_mode', 'option');
	$paypalEmail 	= get_field('_paypal_email', 'option');

	$ipn = new PaypalIPN();

	if( $testMode == 1 ) {
		$ipn->useSandbox();
	}

	$verified = $ipn->verifyIPN();

	if( $_POST['receiver_email'] != $paypalEmail ) {
		return false;
	}

	if( $verified ) {

		$orderId 				= sanitize_text_field( $_POST['custom'] );
		$paymentStatus 	= sanitize_text_field( $_POST['payment_status'] );
		$transactionId 	= sanitize_text_field( $_POST['txn_id'] );

		$orderData = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}place_orders WHERE order_id = '%s'", $orderId) );

		if( $orderData ) {
				
			if( $paymentStatus != 'Completed' ) {
				$wpdb->update(
					$wpdb->prefix.'place_orders',
					array(
						'payment_status' 	=> $paymentStatus,
						'transaction_id'	=> $transactionId,
					),
					array(
						'order_id'	=> $orderId
					),
					array('%s','%s'),
					array('%s')
				);
			}

			if( $paymentStatus == 'Completed' ) {
				
				foreach($orderData as $item) {

					if( $item->transaction_id == '' ) {

						$place_id 	= $item->place_id;
						$type 			= $item->product_type;
						$id 				= $item->product_id;
						$term 			= $item->term;
						$promo_type = $item->promo_type;

						$activationDate = product_activation_date($place_id, $type, $promo_type);

						$wpdb->update(
							$wpdb->prefix.'place_orders',
							array(
								'payment_date'		=> current_time( 'Y-m-d H:i' ),
								'activation_date'	=> date('Y-m-d H:i', strtotime($activationDate)),
								'payment_status' 	=> strtolower($paymentStatus),
								'transaction_id'	=> $transactionId,
							),
							array(
								'id'			 => $item->id,
								'order_id' => $orderId
							),
							array('%s','%s','%s','%s'),
							array('%d','%s')
						);

						switch($type) {
							case 'subscription':
								$expiryDate = subscription_term($term, $activationDate, true);
								update_field('_place_expiry_date', $expiryDate, $place_id);
								break;
							case 'promo':
								$promos = array('slider', 'box', 'recommended');
								$expiryDate = promo_term($term, $activationDate, true);
								update_field('_place_promo_'.$promos[$id-1], 1, $place_id);
								update_field('_place_promo_'.$promos[$id-1].'_expiry_date', $expiryDate, $place_id);
								break;

						}

					}
				}

				send_payment_completed_email($orderId);

			}

		}
	}

	header("HTTP/1.1 200 OK");
}
exit;
?>