<?php  
/* Custom POST TYPES  - Produkt */

function produkt_taxonomies() {

	$categories_labels = array(
		'name'				=> __( 'Kategorie - produkty'),
		'singular_name'		=> __( 'Kategoria'),
		'menu_name'			=> __( 'Kategorie'),
		'all_items'			=> __( 'Wszystkie kategorie'),
		'edit_item'			=> __( 'Edytuj kategorię'),
		'view_item'			=> __( 'Zobacz kategorie'),
		'update_item'		=> __( 'Aktualizauj kategorię'),
		'add_new_item'		=> __( 'Dodaj nową kategorię'),
		'new_item_name'		=> __( 'Nazwa nowej kategorii'),
		'search_items'		=> __( 'Szukaj kategorii'),
		'popular_items'		=> __( 'Popularne kategorie') 
	);

	$categories_args = array(
		'labels'			=> apply_filters('kategoria-produktu_labels', $categories_labels ),
		'public'			=> true,
		'show_in_nav_menus'	=> true,
		'show_tag_cloud'	=> false,
		'show_admin_column'	=> true,
		'hierarchical'		=> true,
		'rewrite'			=> array('slug' => 'katalog-produktow', 'with_front' => false)
	);
	
	register_taxonomy('kategoria-produktu', array('produkt'), $categories_args );
	
}
//add_action( 'init', 'produkt_taxonomies' );

/* Custom POST TYPES  - Regiony */

function register_regiony_post_type() {
	register_post_type( 'regiony', array(
		'labels'	=> array(
			'name'               => __('Regiony', 'rfswp'),
			'singular_name'      => __('Region', 'rfswp'),
			'add_new'            => __('Dodaj region', 'rfswp'),
			'add_new_item'       => __('Dodaj region', 'rfswp'),
			'edit_item'          => __('Edytuj region', 'rfswp'),
			'new_item'           => __('Nowy region', 'rfswp'),
			'view_item'          => __('Zobacz region', 'rfswp'),
			'search_items'       => __('Szukaj regionów', 'rfswp'),
			'not_found'          => __('Nie znaleziono regionów', 'rfswp'),
			'not_found_in_trash' => __('Kosz jest pusty', 'rfswp'),
			'parent_item_colon'  => __('Regiony:', 'rfswp'),
			'menu_name'          => __('Regiony', 'rfswp'),
		),
		'description'         	=> '',
		'public'              	=> true,
		'exclude_from_search' 	=> false,
		'publicly_queryable'  	=> true,
		'show_ui'             	=> true, // show in admin
		'show_in_nav_menus'   	=> true,
		'show_in_menu'        	=> true, //show in admin menu - show_ui must be true
		'menu_position'       	=> 5,
		'menu_icon'           	=> 'dashicons-location',
		'capability_type'     	=> 'post', 
		'hierarchical'        	=> false,
		'supports'            	=> array(
			'title', 
			'editor', 
			'page-attributes', 
			'revisions'
		),
		'has_archive'         	=> false,
		'query_var'           	=> true,
		'can_export'          	=> true,
	) );
}
add_action( 'init', 'register_regiony_post_type' ); 


function rfs_custom_ppp($query) {
	//if($_SERVER['REMOTE_ADDR'] == '83.6.229.134') {
		// echo '<pre style="padding-left:200px;">';
		// print_r($query);
		// echo '</pre>'; 
	//} 
	if($query->is_main_query() && $query->is_tax() && $query->queried_object->taxonomy == 'menu-category'  && !is_admin()) {
		$query->set('posts_per_page', 10);
	}
}
//add_filter('pre_get_posts', 'rfs_custom_ppp');

/**************************************
		CUSTOM TABLE COLUMS
***************************************/
function places_add_column_admin_table($columns) {
	$new_order = array();
	foreach($columns as $key => $title) {
		if ($key=='comments') {
			$new_order['county'] 		= 'Województwo';
			$new_order['region'] 		= 'Region';
			$new_order['expiry_date'] 	= 'Data ważności';
			$new_order['promos'] 		= 'Promocja obiektu';
		}
		$new_order[$key] = $title;
	}
	return $new_order;
}
add_filter('manage_edit-post_columns', 'places_add_column_admin_table');

function places_show_data_column_admin_table($columns, $post_id) {
	switch($columns) {
		case 'county':
			echo esc_html(get_field('_place_county', $post_id));
			break;
		case 'region':
			$region_id = get_field('_place_region', $post_id);
			if($region_id) {
				echo esc_html(get_the_title($region_id));
			}
			break;
		case 'expiry_date':
			$expiry_date 	= get_field('_place_expiry_date', $post_id);
			$expiry 		= DateTime::createFromFormat('d/m/Y', $expiry_date);
			$date_of_expiry = $expiry ? $expiry->format('Ymd') : '';
			
			if( $date_of_expiry < current_time( 'Ymd' ) ) {
				echo '<strong style="color:#dc3232">Nieaktywne</strong>';
			}else {
				echo $expiry_date;
			}
			break;
		case 'promos':
			$promos = array(
				'slider' 		=> 'Slider', 
				'box' 			=> 'Box', 
				'recommended' 	=> 'Polecany'
			);
			foreach( $promos as $promo => $label ) {
				$status = get_field('_place_promo_'.$promo, $post_id);

				if( $status == 1 ) {
					$promo_expity_date 	= get_field('_place_promo_'.$promo.'_expiry_date', $post_id);
					$promo_expiry 		= DateTime::createFromFormat('d/m/Y', $promo_expity_date);
					$promo_date_of_expiry = $promo_expiry ? $promo_expiry->format('Ymd') : '';

					if( $promo_date_of_expiry >= current_time( 'Ymd' ) ) {
						echo '<p>'.$label.' - <strong>'.$promo_expity_date.'</strong></p>';
					}
				}
			}
			break;
	}
}
add_action('manage_post_posts_custom_column', 'places_show_data_column_admin_table', 10, 2 );

function places_sortable_columns( $columns ) {
	$columns['county'] 		= 'county';
	$columns['region'] 		= 'region';
	$columns['expiry_date'] = 'expiry_date';

	return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'places_sortable_columns' );

function places_sortable_columns_order_by( $query ) {
	if( ! is_admin() )
		return;

	$orderby = $query->get( 'orderby');

	if( 'county' == $orderby ) {
		$query->set('meta_key','_place_county');
		$query->set('orderby','meta_value');
	}
	if( 'region' == $orderby ) {
		$query->set('meta_key','_place_region');
		$query->set('orderby','meta_value_num');
	}
	if( 'expiry_date' == $orderby ) {
		$query->set('meta_key','_place_expiry_date');
		$query->set('orderby','meta_value_num');
	}
}
add_action( 'pre_get_posts', 'places_sortable_columns_order_by' );
?>