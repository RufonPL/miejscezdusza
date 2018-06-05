<?php  
use PayPal\Api\Amount; 
use PayPal\Api\Order; 
use PayPal\Api\Details; 
use PayPal\Api\Item; 
use PayPal\Api\ItemList; 
use PayPal\Api\Payer; 
use PayPal\Api\PayerInfo;
//use PayPal\Api\BaseAddress;
use PayPal\Api\Payment; 
use PayPal\Api\RedirectUrls; 
use PayPal\Api\Transaction;

// for executing payment
use PayPal\Api\ExecutePayment; 
use PayPal\Api\PaymentExecution; 

/**
 * This function will create paypal order and return payment url
 * @param $products (array)
 * @param $payerData (array)
 * @param $currency (string)
 * @param $paymentDescription (string)
 * @param $orderID (int)
 * @return array
*/
function payWithPayPal($products = array(), $payerData = array(), $currency, $paymentDescription, $orderID) {
	
	require_once get_template_directory().'/payment/paypal-config.php';

	$payer = new Payer(); 
	
	// $address = new BaseAddress();
	// $address->setLine1($payerData['address1'])->setCity($payerData['city'])->setPostalCode($payerData['postcode'])->setCountryCode($payerData['country_code']);

	$payerInfo = new PayerInfo();
	$payerInfo->setEmail($payerData['email'])->setFirstName($payerData['first_name'])->setLastName($payerData['last_name'])->setBillingAddress($address);


	$payer->setPaymentMethod("paypal")->setPayerInfo($payerInfo);

	if($products) {
		$itemList 	= new ItemList(); 
		$total 		= 0;
		$items 		= array();

		foreach($products as $product) {
			$item = new Item();
			
			$items[] = $item->setName($product['name'])->setCurrency($currency)->setQuantity(1)->setPrice($product['total']);

			$total += $product['total']; 
		}
		$itemList->setItems($items);

		$amount = new Amount(); 
		$amount->setCurrency($currency)->setTotal($total);

		$order = new Order();

		$transaction = new Transaction(); 
		$transaction->setAmount($amount)->setItemList($itemList)->setDescription($paymentDescription)->setCustom($orderID)->setNotifyUrl($paypalUrls['notify']);


		$baseUrl = get_bloginfo( 'url' );
		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl($paypalUrls['return'])->setCancelUrl($paypalUrls['cancel']);

		$payment = new Payment(); 
		$payment->setIntent("sale")->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array($transaction));

		try {
			$payment->create($apiContext);
		} catch (PayPal\Exception\PayPalConnectionException $ex) {
			echo $ex->getCode(); // Prints the Error Code
			echo $ex->getData(); // Prints the detailed error message 
			die($ex);
		} catch (Exception $ex) {
			die($ex);
		}
		
		$approvalUrl = $payment->getApprovalLink();

		return array(
			'url' => $approvalUrl,
			'id'  => $payment->getId() 
		);
	}
	return false;
}

/**
 * This function will register payment into database
 * @param 
 * @return 
*/
function register_order($user_id, $place_id, $order_id, $products, $paymentMethod, $payOrderId) {
	global $wpdb;

	$wpdb->query('START TRANSACTION');

	foreach($products as $product) {
		$insert = $wpdb->insert(
			$wpdb->prefix.'place_orders',
			array(
				'user_id'			=> $user_id,
				'place_id'			=> $place_id,
				'order_id'			=> $order_id,
				'order_date'		=> current_time( 'Y-m-d H:i' ),
				'product_id'		=> $product['id'],
				'product_name'		=> $product['name'],
				'product_type'		=> $product['type'],
				'promo_type'		=> $product['promo_type'],
				'price'				=> $product['total'],
				'term'				=> $product['term'],
				'term_unit'			=> $product['unit'],
				'payment_method'	=> $paymentMethod,
				'payment_status'	=> 'NEW',
				'pay_order_id'		=> $payOrderId
			),
			array('%d','%d','%s','%s','%d','%s','%s','%s','%s','%d','%s','%s','%s','%s')
		);

		if( !$insert ) {
			$wpdb->query('ROLLBACK');
			return false;
		}

	}

	$wpdb->query('COMMIT');

	return true;
}

/**
 * This function will render payment notification depending on the payment finish status
 * @param $type (string)
 * @return html
*/
function payment_completion_text($type) {
	switch($type) {
		case 'ok':
			$text = get_field('_payment_confirmation_text', 'option');
			break;
		case 'cancelled':
			$text = get_field('_payment_cancelled_text', 'option');
			break;
		case 'error':
			$text = get_field('_payment_error_text', 'option');
			break;
	}

	if( !empty_content( $text ) ) {
		return $text;
	}

	return false;
}

/**
 * This function will redirect payu return url - error is a wp reserved term so we will catch it earlier to proccess it
 * @param n/a
 * @return n/a
*/
function reserved_term_intercept(){
	if( isset($_GET['payment']) && $_GET['payment'] == 'payu' ) {
		if( isset($_GET['error']) ) {
			if( $_GET['error'] == 501 ) {
				wp_safe_redirect( get_permalink().'?payu=cancelled' ); exit;
			}else {
				wp_safe_redirect( get_permalink().'?payu=error' ); exit;
			}
		}else {
			wp_safe_redirect( get_permalink().'?payu=ok' ); exit;
		}
	}
}
add_action( 'init' , 'reserved_term_intercept', 9 );

function send_payment_completed_email($order_id) {
	global $wpdb;

	$orders = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}place_orders WHERE order_id = '%s'", $order_id ) );

	if( $orders ) {
		$user_id 	= $orders[0]->user_id;
		$userdata = get_userdata( $user_id );

		if( is_wp_error( $userdata ) ) return;

		$firstName 	= $userdata->first_name;
		$lastName 	= $userdata->last_name;
		$email 			= $userdata->user_email;

		$method 		= $orders[0]->payment_method;
		$trId 			= $orders[0]->transaction_id;
		$products 	= array();

		foreach($orders as $order) {
			if( strtoupper( $order->payment_status ) == 'COMPLETED' ) {
				$products[] = place_product($order->product_type, $order->product_id, array('activation_date' => $order->activation_date));
			}
		}

		$message = rfs_get_email_template('order-completed', array(
			'name'			=> sanitize_text_field( $firstName.' '.$lastName ),
			'order_id'	=> sanitize_text_field( $order_id ),
			'tr_id'			=> sanitize_text_field( $trId ),
			'products'	=> $products,
			'method'		=> sanitize_text_field( $method )
		));
		$subject 	= esc_html(get_bloginfo('name')).' - Zamówienie zostało zrealizowane';
		$send 		= wp_mail($email, $subject, $message);
	}

	return;
}
?>