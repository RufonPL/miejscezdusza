<?php
add_filter('acf/settings/show_admin', '__return_false');

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Różne',
		'menu_title'	=> 'Różne',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> true,
		'icon_url'		=> 'dashicons-screenoptions',
		'position'		=> 22
	));
	
	$acf_subpages = array(
		'Nagłówek',
		'Social Media',
		'Wyróżnione miejsca',
		'Profil Użytkownika',
		'Profil Premium',
		'Nagłówki graficzne',
		'Płatności - Ustawienia',
		'Faktury'
	);
	
	foreach($acf_subpages as $subpage) {
		acf_add_options_sub_page(array(
			'page_title' 	=> $subpage,
			'menu_title'	=> $subpage,
			'parent_slug'	=> 'theme-general-settings',
		));
	}
	
}
function my_toolbars($toolbars) {
	/* echo '< pre >';
		print_r($toolbars);
	echo '< /pre >';
	die;  */
	$toolbars['Section_header'] = array();
	$toolbars['Section_header'][1] = array('bold');
	
	$toolbars['Simple'] = array();
	$toolbars['Simple'][1] = array('bold', 'italic', 'link', 'unlink', 'alignleft', 'aligncenter', 'alignright');
	
	$toolbars['Text_field'] = array();
	$toolbars['Text_field'][1] = array('bold'); // bold hidden
	
	$toolbars['Text_field_bold'] = array();
	$toolbars['Text_field_bold'][1] = array('bold');
	
	if(($key = array_search('code', $toolbars['Full' ][2])) !== false) {
	    unset($toolbars['Full'][2][$key]);
	}
	return $toolbars;
}
add_filter('acf/fields/wysiwyg/toolbars', 'my_toolbars');

function acf_admin_styles() {
    echo '<style type="text/css">
			[data-toolbar="section_header"] iframe  {
				height:100px !important;
				min-height:100px !important;
			}
			[data-toolbar="section_header"]   {
				height:160px !important;
				min-height:160px !important;
			}
			.wyswig-header {
				height:240px;
			}
			.wyswig-text_field {
				height:180px;
			}
			[data-toolbar="text_field"] iframe  {
				height:100px !important;
				min-height:100px !important;
			}
			[data-toolbar="text_field"] .mce-toolbar-grp {
				display:none;
			}
			.wyswig-text_field_bold {
				height:180px;
			}
			[data-toolbar="text_field_bold"] iframe  {
				height:100px !important;
				min-height:100px !important;
			}
			.wyswig-medium {
				height:350px;
			}
			.wyswig-medium-visual {
				height:310px;
			}
			.wyswig-medium [data-toolbar] iframe,
			.wyswig-medium-visual [data-toolbar] iframe {
				height:170px !important;
				min-height:170px !important;				
			}
         </style>';
}
add_action('admin_head', 'acf_admin_styles');

function acf_map_api() {
	acf_update_setting('google_api_key', 'AIzaSyB4XNTTMcp8RATV_NwU_UMaXVDEUXbyCd0');
}
add_action('acf/init', 'acf_map_api');

function get_owners_with_places() {
	$places = get_posts(
		array(
			'post_type' 		=> 'post',
			'meta_key' 			=> '_place_owner',
			'posts_per_page' 	=> -1,
		)
	);

	$owners = array();

	if($places) {
		foreach($places as $place) {
			$owner = get_post_meta( $place->ID, '_place_owner', true );
			if($owner) {
				$owners[] = $owner;
			}
		}
		wp_reset_postdata(); 
	}

	return $owners;
}

function filter_place_owner_field($args, $field, $post) {
	$args['exclude'] = get_owners_with_places();
	
	return $args;
}
add_filter('acf/fields/user/query', 'filter_place_owner_field', 10, 3);
?>