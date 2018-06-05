<?php  
// get subscription term end based on months length
function subscription_term($months_length, $base_date=false, $format=false) {
	$base 	= $base_date && strtotime($base_date) == false ? $base_date : strtotime($base_date);
	$start 	= $base_date ? date('Ymd', $base) : date('Ymd');
	$months = strtotime($start . '+'.$months_length.' months');

	return $format ? date('Ymd', $months) : $months;
}

function promo_term($days_length, $base_date=false, $format=false) {
	$base 	= $base_date && strtotime($base_date) == false ? $base_date : strtotime($base_date);
	$start 	= $base_date ? date('Ymd', $base) : date('Ymd');
	$days 	= strtotime($start . '+'.$days_length.' days');

	return $format ? date('Ymd', $days) : $days;
}

/**
 * This function will return user's place id
 * @param $user_id (int)
 * @return mixed
*/
function get_owner_place($user_id) {
	$place = get_posts(
		array(
			'post_type' 		=> 'post',
			'meta_key' 			=> '_place_owner',
			'meta_value' 		=> $user_id,
			'posts_per_page' 	=> 1,
		)
	);
	if($place) {
		return $place[0]->ID;
	}
	return false;
}
function get_term_length_shortcode() {
	$place_id 	= get_owner_place( get_current_user_id() );
	$expiryDate = get_field('_place_expiry_date', $place_id);
	return '<strong class="color5">'.$expiryDate.'</strong>';
}
add_shortcode( 'subscription_term', 'get_term_length_shortcode' );

function validate_owner_name($errors, $update, $user) {

	if( $_POST['role'] == 'contributor' ) {
		if( empty($_POST['first_name']) ) {
			$errors->add( 'first_name_required', 'Imię jest wymagane' );
		}

		if( empty($_POST['last_name']) ) {
			$errors->add( 'last_name_required', 'Nazwisko jest wymagane' );
		}
	}

	if( empty( $errors->errors ) ){	}
}
add_filter('user_profile_update_errors', 'validate_owner_name', 10, 3);

/**
 * This function will return an array with product data: name, base price, tax and total price of a product
 * @param $type (string)
 * @param $id (int)
 * @return array
*/
function place_product($type, $id, $data=array()) {
	$tax 	= get_field('_vat_tax', 'option');
	$price 	= 0;
	$name 	= '';
	$termUnit = '';

	switch($type) {
		case 'subscription':
			$name  		= 'Abonament '.get_field('_subscription_'.$id.'_name', 'option');
			$price 		= get_field('_subscription_'.$id.'_price', 'option');
			$term 		= get_field('_subscription_'.$id.'_term', 'option');
			$termUnit 	= 'months';
			break;
		case 'promo':
			$names 		= array('Slider', 'Box', 'Obiekt polecany');
			$promoTypes = array('slider', 'box', 'recommended');
			$name 		= 'Reklama - '.$names[$id-1];
			$price 		= get_field('_advert_'.$id.'_price', 'option');
			$term 		= get_field('_advert_'.$id.'_term', 'option');
			$termUnit 	= 'days';
			$promoType 	= $promoTypes[$id-1];
			break;
	}

	$tax = $tax / 100;

	$product = array(
		'id'	=> absint( $id ), 
		'name'	=> esc_html( $name ),
		'price' => $price,
		'tax'	=> $tax,
		'total'	=> $price + ($price*$tax),
		'term'	=> absint( $term ),
		'unit'	=> $termUnit,
		'type'	=> esc_html( $type ),
		'promo_type' => $type == 'promo' ? $promoType : ''
	);

	if( $data ) {
		return array_merge($product, $data);
	}

	return $product;
}

function format_price($price) {
	return number_format($price, 2);
}

/**
 * This function will render order number
 * @param n/a
 * @return string
*/
function md_order_number() {
	global $wpdb;

	$month 	= absint( date('m') );
	$year 	= absint( date('Y') );

	$order_nos = $wpdb->get_row($wpdb->prepare("SELECT MAX(number) as last_number FROM {$wpdb->prefix}place_order_numbers WHERE month = '%d' AND year = '%d'", array($month, $year)));
	
	$new_number = $order_nos === NULL ? 1 : $order_nos->last_number+1;

	$order_no = $new_number.'/'.$month.'/'.$year;	
	
	$insert_no = $wpdb->insert(
		$wpdb->prefix.'place_order_numbers',
		array(
			'number'=> $order_no,
			'month'	=> $month,
			'year'	=> $year
		),
		array('%d', '%d', '%d')
	);
	
	return $order_no;	
}


function format_place_date($date, $full = false) {
	$format 		= $full ? 'YmdHi' : 'Ymd';
	$createDate 	= DateTime::createFromFormat('d/m/Y', $date);
	$formattedDate 	= $createDate ? $createDate->format($format) : false;

	return $formattedDate;
}

/**
 * This function will check if a place promo is active (not expired)
 * @param $place_id (int)
 * @param $promo (string)
 * @return boolean
*/
function is_place_promo_active($place_id, $promo) {
	$status 	 	= get_field('_place_promo_'.$promo, $place_id);
	$expiry_date 	= get_field('_place_promo_'.$promo.'_expiry_date', $place_id);
	$date_of_expiry = format_place_date($expiry_date);
	
	if( $status != 1 ) return false;

	return $date_of_expiry >= current_time( 'Ymd' );
}

/**
 * This function will check if a place is active (not expired)
 * @param $place_id (int)
 * @return boolean
*/
function is_place_active($place_id) {
	$expiry_date 	= get_field('_place_expiry_date', $place_id);
	//$expiry 		= DateTime::createFromFormat('d/m/Y', $expiry_date);
	$date_of_expiry = format_place_date($expiry_date);
	
	return $date_of_expiry >= current_time( 'Ymd' );
}

/**
 * This function will return product avtivation date depending on whether a place has any future expiry date or promo date
 * @param 
 * @return 
*/
function product_activation_date($place_id, $type, $promo = '') {
	$activation_date = current_time( 'YmdHi' );

	switch($type) {
		case 'subscription':

			if( is_place_active($place_id) ) {
				$place_expiry_date 	= get_field('_place_expiry_date', $place_id);
				$activation_date 	= format_place_date($place_expiry_date);
			}

			break;
		case 'promo':

			if( is_place_promo_active($place_id, $promo) ) {
				$promo_expiry_date 	= get_field('_place_promo_'.$promo.'_expiry_date', $place_id);
				$activation_date 	= format_place_date($promo_expiry_date);
			}

			break;
	}

	return $activation_date;
}

/**
 * This function will return an array of promoted places ids and a number of found posts for a given promo type
 * @param $promo (string)
 * @param $orderby (string)
 * @param $originalQuery (boolean) - returns wp qeury
 * @return mixed
*/
function get_promoted_places_only($promo, $orderby = 'rand', $originalQuery = false, $ppp = false, $page = 1, $includeOther = false) {
	$promoted 	= array(
		'found'		=> 0,
		'places'	=> array()
	);
	//$limit 		= get_field('_promo_'.$promo.'_limit', 'option');
	$limit = 4;
	$other 		= array();
	$all_ids 	= array();

	$places = new WP_Query( array(
		'post_type'			=> 'post',
		'posts_per_page'	=> $limit,
		'post_status'		=> 'publish',
		'orderby'			=> $orderby,
		'paged'				=> $page,
		'meta_query'		=> array(
			array(
				'key'		=> '_place_expiry_date',
				'value'		=> current_time( 'Ymd' ),
				'type'		=> 'CHAR',
				'compare'	=> '>='
			),
			array(
				'key'		=> '_place_promo_'.$promo.'',
				'value'		=> 1,
				'type'		=> 'NUMERIC',
				'compare'	=> '='
			),
			array(
				'key'		=> '_place_promo_'.$promo.'_expiry_date',
				'value'		=> current_time( 'Ymd' ),
				'type'		=> 'CHAR',
				'compare'	=> '>='
			)
		)
	) );

	$found = $places->found_posts;

	if( $places->have_posts()) {
		while( $places->have_posts()) { $places->the_post();
			$promoted['places'][] = get_the_ID();
		}
		$promoted['found'] = $found;
	}; wp_reset_postdata();

	if( $includeOther && $found < $limit && $promo == 'recommended' ) { // only in user profile
		$otherPlaces = new WP_Query( array(
			'post_type'				=> 'post',
			'posts_per_page'	=> $limit - $found,
			'post_status'			=> 'publish',
			'orderby'					=> 'rand',
			'post__not_in'		=> $promoted['places'],
			'meta_query'			=> array(
				array(
					'key'			=> '_place_expiry_date',
					'value'		=> current_time( 'Ymd' ),
					'type'		=> 'CHAR',
					'compare'	=> '>='
				)
			)
		) );

		if( $otherPlaces->have_posts() ) {
			while($otherPlaces->have_posts()) { $otherPlaces->the_post();
				$other[] = get_the_ID();
			}
		}; wp_reset_postdata();

		$all_ids = array_merge($promoted['places'], $other);

		if( !empty($all_ids) ) {
			$allPlaces = new WP_Query( array(
				 'post_type'      => 'post',
				 'posts_per_page' => $ppp,
				 'post_status'    => 'publish',
				 'post__in'				=> $all_ids,
				 'supress_filters' => true,
				 'orderby'				=> 'post__in'
			) );

			$places = $allPlaces;
		}
		
	}

	return $originalQuery ? $places : $promoted;
}

/**
 * This function will return an array of promoted places to loop in view template for a given promo type
 * @param $promo (string)
 * @return n/a
*/
function get_promoted_places($promo) {
	$promoted 	= get_promoted_places_only($promo);
	$other 		= array();
	
	$limit = get_field('_promo_'.$promo.'_limit', 'option');

	$foundPromoted = $promoted['found'];

	if( $foundPromoted < $limit ) {

		$otherPlaces = new WP_Query( array(
			'post_type'			=> 'post',
			'posts_per_page'	=> $limit - $foundPromoted,
			'post_status'		=> 'publish',
			'orderby'			=> 'rand',
			'post__not_in'		=> $promoted['places'],
			'meta_query'		=> array(
				array(
					'key'		=> '_place_expiry_date',
					'value'		=> current_time( 'Ymd' ),
					'type'		=> 'CHAR',
					'compare'	=> '>='
				)
			)
		) );

		if( $otherPlaces->have_posts()) {
			while($otherPlaces->have_posts()) { $otherPlaces->the_post();
				$other[] = get_the_ID();
			}
		}; wp_reset_postdata();

	}
	
	if( $foundPromoted > $limit ) {
		$promoted 	= get_promoted_places_only($promo, 'rand');
	}

	$all = array_merge($promoted['places'], $other);

	// move one item to end of array so it is displayed on the left side of slider
	if( $promo == 'slider' && $foundPromoted < $limit && $foundPromoted > 2 ) {
		$moveItem = $all[2];
		unset($all[2]);
		$all[] = $moveItem;

		$all = array_values($all);
	}

	// slice promo box array to 3 and move promoted to middle box if there is only one promoted item
	if( $promo == 'box') {
		$all = array_slice($all, 0, 3);

		if( $foundPromoted == 1 ) {
			$moveItem1 = $all[0];
			unset($all[0]);
			$all[0] = $moveItem1;
			
			if( count($all) > 1 ) {
				$moveItem2 = $all[1];
				unset($all[1]);
				$all[1] = $moveItem2;
			}

			$all = array_values($all);
		}
	}

	return $all;
}

/**
 * This function will return an array of promoted places to loop in view template for a given promo type
 * @param $promo (string)
 * @return n/a
*/
function get_promoted_places2($promo) {  // to do if need to be other 
	$promotedPlaces 	= get_promoted_places_only($promo);
	$promoted 			= array();
	$other 				= array();
	
	if( $promotedPlaces->have_posts()) {
		while( $promotedPlaces->have_posts()) { $promotedPlaces->the_post();
			$promoted[] = get_the_ID();
		}
	}; wp_reset_postdata();

	$limit = get_field('_promo_'.$promo.'_limit', 'option');

	//$foundPromoted = $promoted['found'];
	$foundPromoted = $promotedPlaces->found_posts;

	if( $foundPromoted < $limit ) {

		$otherPlaces = new WP_Query( array(
			'post_type'			=> 'post',
			'posts_per_page'	=> $limit - $foundPromoted,
			'post_status'		=> 'publish',
			'orderby'			=> 'rand',
			'post__not_in'		=> $promoted
		) );

		if( $otherPlaces->have_posts()) {
			while($otherPlaces->have_posts()) { $otherPlaces->the_post();
				$other[] = get_the_ID();
			}
		}; wp_reset_postdata();

	}
	
	if( $foundPromoted > $limit ) {
		$promoted 	= get_promoted_places_only($promo, 'rand');
	}

	$all = new WP_Query();
	$all->posts = array_merge( $promotedPlaces->posts, $otherPlaces->posts );

	echo '<pre style="margin-left:200px">';
	print_r($all);
	echo '</pre>';
	// if( $promo == 'slider' && $foundPromoted < $limit && $foundPromoted > 2 ) {
	// 	$moveItem = $all[2];
	// 	unset($all[2]);
	// 	$all[] = $moveItem;

	// 	$all = array_values($all);
	// }

	// if( $promo == 'box') {
	// 	$all = array_slice($all, 0, 3);

	// 	if( $foundPromoted == 1 ) {
	// 		$moveItem1 = $all[0];
	// 		unset($all[0]);
	// 		$all[0] = $moveItem1;
			
	// 		if( count($all) > 1 ) {
	// 			$moveItem2 = $all[1];
	// 			unset($all[1]);
	// 			$all[1] = $moveItem2;
	// 		}

	// 		$all = array_values($all);
	// 	}
	// }

	return $all;
}

/**
 * This function will return list of user invoices list
 * @param n/a
 * @return array
*/
function invoices_list() {
	global $wpdb;

	if( !is_user_logged_in() ) return array();

	$data 					= array();
	$payment_status = 'completed';
	$user_id 				= get_current_user_id();

	$orders = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}place_orders WHERE user_id = '%d' AND payment_status = '%s' GROUP BY order_id ORDER BY order_date DESC", array($user_id, $payment_status) ) );

	if( $orders ) {
		foreach($orders as $order) {
			$data[] = array(
				'id'				=> $order->id,
				'date'			=> $order->order_date,
				'order_id'	=> $order->order_id
			);
		}
	}

	return $data;
}

/**
 * This function will generate pdf invoice and force download it
 * @param n/a
 * @return n/a
*/
function generateInvoice($order_id) {
	global $wpdb;

	$payment_status = 'completed';

	$orders = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}place_orders WHERE order_id = '%s' AND payment_status = '%s' ORDER BY order_date DESC", array($order_id, $payment_status) ) );

	if( $orders ) {
		$user_id 								= $orders[0]->user_id;
		$payment_date 					= $orders[0]->payment_date;
		$place_id 							= get_owner_place($user_id);
		$invoice_company 				= get_field('_invoice_company', 'option');
		$invoice_address 				= get_field('_invoice_address', 'option');
		$invoice_nip 						= get_field('_invoice_nip', 'option');
		$place_invoice_company 	= get_field('_invoice_place_company', $place_id);
		$place_invoice_address 	= get_field('_invoice_place_address', $place_id);
		$place_invoice_nip 			= get_field('_invoice_place_nip', $place_id);
		$products 							= array();
		$prices     						= array();

		require_once get_template_directory().'/vendor/autoload.php';
	
		$options = new Dompdf\Options();
		$options->setIsRemoteEnabled(true);
		
		$dompdf = new Dompdf\Dompdf($options);

		foreach($orders as $order) {
			$products[] = place_product($order->product_type, $order->product_id);
		}
		
		$html = rfs_get_email_template('invoice', array(
			'payment_date'					=> date('Y-m-d', strtotime($payment_date)),
			'order_id'							=> $order_id,
			'company_name' 					=> wp_kses($invoice_company, array('br'=>array())),
			'company_address' 			=> wp_kses($invoice_address, array('br'=>array())),
			'company_nip'						=> esc_html( $invoice_nip ),
			'place_company_name' 		=> wp_kses($place_invoice_company, array('br'=>array())),
			'place_company_address' => wp_kses($place_invoice_address, array('br'=>array())),
			'place_company_nip'			=> esc_html( $place_invoice_nip ),
			'products'							=> $products
		));

		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');

		// Render the HTML as PDF
		$dompdf->render();

		$dompdf->stream('test');
	}
}

/**
 * This function will call generate invoice function
 * @param n/a
 * @return n/a
*/
function download_invoice() {
	if( isset($_GET['invoice']) ) {
		generateInvoice( sanitize_text_field( $_GET['invoice'] ) );
	}
}
add_action('template_redirect', 'download_invoice');
?>