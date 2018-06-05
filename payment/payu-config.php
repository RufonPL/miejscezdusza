<?php
$testMode 	= get_field('_payu_test_mode', 'option');
$posId 			= get_field('_payu_pos_id', 'option');
$aouthKey 	= get_field('_payu_oauth_client_secret', 'option');
$signKey 		= get_field('_payu_signature_key', 'option');

$environment = $testMode ? 'sandbox' : 'secure';
//set Sandbox Environment
OpenPayU_Configuration::setEnvironment($environment);

//set POS ID and Second MD5 Key (from merchant admin panel)
OpenPayU_Configuration::setMerchantPosId($posId);
OpenPayU_Configuration::setSignatureKey($signKey);

//set Oauth Client Id and Oauth Client Secret (from merchant admin panel)
OpenPayU_Configuration::setOauthClientId($posId);
OpenPayU_Configuration::setOauthClientSecret($aouthKey);