<?php  
use PayPal\Api\Amount; 
use PayPal\Api\Details; 
use PayPal\Api\ExecutePayment; 
use PayPal\Api\Payment; 
use PayPal\Api\PaymentExecution; 
use PayPal\Api\Transaction;

require_once get_template_directory().'/payment/paypal-config.php';

global $wpdb;
$url = esc_url( get_bloginfo('url').'/payment-confirmation' );

if( isset($_GET['status']) && $_GET['status'] == 'ok' && isset($_GET['paymentId']) ) {
	$paymentId = $_GET['paymentId']; 
	$payment = Payment::get($paymentId, $apiContext);

	$execution = new PaymentExecution(); 
	$execution->setPayerId($_GET['PayerID']);

	 try {
		 $result = $payment->execute($execution, $apiContext);
	 } catch (PayPal\Exception\PayPalConnectionException $ex) {
		echo $ex->getCode(); // Prints the Error Code
		echo $ex->getData(); // Prints the detailed error message 
		die($ex);
	} catch (Exception $ex) {
		die($ex);
	}

	$updateStatus = $wpdb->update(
		$wpdb->prefix.'place_orders',
		array('payment_status' => 'Pending'),
		array('pay_order_id' => $paymentId),
		array('%s'),
		array('%s')
	);

	if( $updateStatus === false ) {
		wp_safe_redirect( $url.'?paypal=error' ); exit;
	}
	wp_safe_redirect( $url.'?paypal=ok' ); exit;
	//return $payment;
}elseif( isset($_GET['status']) && $_GET['status'] == 'cancelled' ) {

	wp_safe_redirect( $url.'?paypal=cancelled' ); exit;
	
}
?>