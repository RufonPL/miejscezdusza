<?php
global $wpdb;
require_once get_template_directory().'/vendor/autoload.php';
require_once get_template_directory().'/payment/payu-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$body = file_get_contents('php://input');
	$data = trim($body);

	$response 	= OpenPayU_Order::consumeNotification($data);
	$order 		= $response->getResponse()->order;

	if( $order->orderId ) {
		$orderStatus = OpenPayU_Order::retrieve($order->orderId);

		if( $orderStatus->getStatus() == 'SUCCESS' ) {
			$transactionId 	= $response->getResponse()->properties[0]->value;
			$paymentStatus 	= $order->status; //NEW PENDING CANCELED REJECTED COMPLETED WAITING_FOR_CONFIRMATION
			$payuOrderId 		= $order->orderId;
			$orderId 				= $order->extOrderId;

			$orderData = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}place_orders WHERE pay_order_id = '%s'", $payuOrderId) );

			if( $orderData ) {
				if( $orderData[0]->payment_status != 'COMPLETED' ) {
					
					$wpdb->update(
						$wpdb->prefix.'place_orders',
						array(
							'payment_status' 	=> strtolower($paymentStatus),
							'transaction_id'	=> $transactionId,
						),
						array(
							'pay_order_id'	=> $payuOrderId
						),
						array('%s','%s'),
						array('%s')
					);

					if( $paymentStatus == 'COMPLETED' ) {
						foreach($orderData as $item) {
							//$place_id 	= get_owner_place($item->user_id);
							$place_id 	= $item->place_id;
							$type 		= $item->product_type;
							$id 		= $item->product_id;
							$term 		= $item->term;
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
									'id'						=> $item->id,
									'pay_order_id'	=> $payuOrderId
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

						send_payment_completed_email($orderId);

					}

				}
			}
		}
	}

	header("HTTP/1.1 200 OK");
}
exit;
?>