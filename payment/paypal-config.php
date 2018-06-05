<?php
require get_template_directory().'/vendor/autoload.php';

$clientId 			= get_field('_paypal_client_id', 'option');
$clientSecret 	= get_field('_paypal_client_secret', 'option');
$testMode 			= get_field('_paypal_test_mode', 'option');
$mode 					= $testMode == 1 ? 'sandbox' : 'live';

$baseUrl 	= get_bloginfo( 'url' );
$paypalUrls = array(
	'return'	=> esc_url( $baseUrl.'/paypal-execute-payment?status=ok'),
	'cancel'	=> esc_url( $baseUrl.'/paypal-execute-payment?status=cancelled'),
	'notify'	=> esc_url( $baseUrl.'/paypal-notifications'),
);

function getApiContext($clientId, $clientSecret) {

	$apiContext = new \PayPal\Rest\ApiContext(
		new \PayPal\Auth\OAuthTokenCredential(
			$clientId,
			$clientSecret
		)
	);

	$apiContext->setConfig(
		array(
			'mode' 					=> $mode,
			'cache.enabled' => true,
			// 'http.CURLOPT_CONNECTTIMEOUT' => 30
			// 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
			//'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
		)
	);

	return $apiContext;
}
$apiContext = getApiContext($clientId, $clientSecret);
?>