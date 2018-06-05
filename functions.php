<?php
/**
 *
 * @author Rafał Puczel
 */

if (!isset($content_width)) $content_width = 770;


/**
 * Adds support for a custom header image.
 */
//require get_template_directory() . '/inc/custom-header.php';
/**
 * rsfwp_setup function.
 * 
 * @access public
 * @return void
 */
function rfswp_setup() {

	require 'inc/class-Rfswp_Walker_Nav_Menu.php';

	load_theme_textdomain('rfswp', get_template_directory().'/languages');

	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails on posts and pages
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	register_nav_menus( array(
		'primary' => __('Primary Menu', 'rfswp'),
		'footer' => __('Footer Menu', 'rfswp'),
	) );

	/**
	 * Enable support for Post Formats
	 */
	//add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	
}

add_action( 'after_setup_theme', 'rfswp_setup' );


// Hide WP version
function remove_version() {
  return '';
}
add_filter('the_generator', 'remove_version');

//Hide Login Error messages:
function wrong_login() {
  return 'Wrong username or password.';
}
add_filter('login_errors', 'wrong_login');


function remove_head_scripts() { //places all scripts in footer (even plugins')
	remove_action('wp_head', 'wp_print_scripts');
	remove_action('wp_head', 'wp_print_head_scripts', 9);
	remove_action('wp_head', 'wp_enqueue_scripts', 1);

	add_action('wp_footer', 'wp_print_scripts', 5);
	add_action('wp_footer', 'wp_enqueue_scripts', 5);
	add_action('wp_footer', 'wp_print_head_scripts', 5);
}
add_action( 'wp_enqueue_scripts', 'remove_head_scripts' );

function rfswp_scripts() {
	wp_enqueue_style('libs', get_template_directory_uri().'/css/libs.min.css', array(), '1.0');
	wp_enqueue_style('styles', get_template_directory_uri().'/css/styles.min.css', array(), '1.0');
	wp_enqueue_style('custom', get_template_directory_uri().'/css/custom-styles.css', array(), '1.0');

	
	wp_enqueue_style('cssmap', get_template_directory_uri().'/cssmap-poland/cssmap-poland.css', array(), '1.0');
	wp_enqueue_script('cssmap', get_template_directory_uri().'/cssmap-poland/jquery.cssmap.min.js',array(),'1.0', true);

	wp_enqueue_script('libs', get_template_directory_uri().'/js/libs.min.js',array(),'1.0', true);
	wp_enqueue_script('scripts', get_template_directory_uri().'/js/scripts.min.js',array(),'1.0', true);
	
}
add_action( 'wp_enqueue_scripts', 'rfswp_scripts' );

function enqueue_footer_css() {
	$css = '<link property="stylesheet" href="'.get_template_directory_uri().'/font-awesome/css/font-awesome.min.css" rel="stylesheet">';
	/* $css .= '
<link href="https://fonts.googleapis.com/css?family=Exo:300,400,500,600,700,800,900|Titillium+Web:300,400,600,700,900&subset=latin-ext" rel="stylesheet">
';	 */
	/* $css .= '<script src="https://cdn.jsdelivr.net/ga-lite/latest/ga-lite.min.js" async></script> <script> var galite = galite || {}; galite.UA = "UA-52143241-1";</script>'; */
	echo $css;
}


function script_tag_defer($tag, $handle) {
    if (is_admin()){
        return $tag;
    }
    if (strpos($tag, '/wp-includes/js/jquery/jquery')) {
        //return $tag;
    }
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.') !==false) {
		return $tag;
    }
    else {
        return str_replace(' src',' defer src', $tag);
    }
}
//add_filter('script_loader_tag', 'script_tag_defer',10,2);

function site_time_zone() {
	date_default_timezone_set("Europe/Warsaw");
}
add_action('init', 'site_time_zone');

function rfs_remove_script_version($src){
    return remove_query_arg('ver', $src);
}
add_filter( 'script_loader_src', 'rfs_remove_script_version' );
add_filter( 'style_loader_src', 'rfs_remove_script_version' );


// REMOVE WP EMOJI
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );	
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}


/**
 * Custom template tags for this theme.
 */
require get_template_directory().'/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory().'/inc/extras.php';


// hide admin bar links
function mytheme_admin_bar_render() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('comments');
	$wp_admin_bar->remove_menu('customize');
	$wp_admin_bar->remove_menu('new-content');
}
add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );

// hide admin menus
function sb_remove_admin_menus(){
	global $menu;
	global $submenu;
	//remove_menu_page('edit.php?post_type=page'); 
	//remove_menu_page('edit-comments.php');
	unset($submenu['themes.php'][6]);
	//unset($submenu['options-general.php'][15]); // writing
	//unset($submenu['options-general.php'][25]); // discussion
}
add_action('admin_menu', 'sb_remove_admin_menus');

function rfs_unregister_taxonomy(){
    register_taxonomy('post_tag', array());
    register_taxonomy('category', array());
}
add_action('init', 'rfs_unregister_taxonomy');

function my_remove_post_type_support() {
    remove_post_type_support('post', 'post-formats');
	remove_post_type_support('post', 'excerpt');
	//remove_post_type_support('post', 'comments');
	remove_post_type_support('post', 'revisions');
	remove_post_type_support('post', 'trackbacks');
	remove_post_type_support('post', 'author');
	remove_post_type_support('post', 'custom-fields');
	remove_post_type_support('post', 'thumbnail');
}
add_action( 'init', 'my_remove_post_type_support', 10 );

function rfs_admin_menu_icons() {
  global $menu;
  foreach( $menu as $key => $val ) {
    if( 'Miejsca' == $val[0] ) {
      $menu[$key][6] = 'dashicons-location-alt';
    }
  }
}
add_action( 'admin_menu', 'rfs_admin_menu_icons' );


function rfs_change_post_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'Miejsca';
    $submenu['edit.php'][5][0] = 'Miejsca';
    $submenu['edit.php'][10][0] = 'Dodaj Miejsce';
}
function rfs_change_post_object() {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'Miejsca';
    $labels->singular_name = 'Miejsce';
    $labels->add_new = 'Dodaj miejsce';
    $labels->add_new_item = 'Dodaj miejsce';
    $labels->edit_item = 'Edytuj miejsce';
    $labels->new_item = 'Miejsce';
    $labels->view_item = 'Zobacz miejsce';
    $labels->search_items = 'Szukaj miejsc';
    $labels->not_found = 'Brak miejsc';
    $labels->not_found_in_trash = 'Brak miejsc w koszu';
    $labels->all_items = 'Wszystkie miejsca';
    $labels->menu_name = 'Miejsca';
    $labels->name_admin_bar = 'Miejsca';
}
add_action( 'admin_menu', 'rfs_change_post_label' );
add_action( 'init', 'rfs_change_post_object' );


add_image_size( 'max-image', 1920, 3000 );
add_image_size( 'slider-image', 1160, 490, true );
add_image_size( 'box-image', 350, 260, true );
add_image_size( 'place-image', 410, 350, true );
add_image_size( 'region-image', 1110, 300, true );

 
function rfs_image_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'box-image' => __( 'Box Image' )
    ) );
}
add_filter( 'image_size_names_choose', 'rfs_image_sizes' );

add_filter( 'auto_core_update_send_email', '__return_false' );

require get_template_directory().'/functions/rfs_create_page_class.php';
require get_template_directory().'/functions/wpdb-sessions.php';
require get_template_directory().'/functions/cpt.php';
require get_template_directory().'/functions/login.php';
//require get_template_directory().'/functions/polylang.php';
require get_template_directory().'/functions/email.php';
require get_template_directory().'/functions/payment.php';
require get_template_directory().'/functions/acf.php';
require get_template_directory().'/functions/custom.php';
require get_template_directory().'/functions/premium.php';
require get_template_directory().'/functions/user.php';
require get_template_directory().'/functions/voting.php';
require get_template_directory().'/functions/ajax.php';
require get_template_directory().'/functions/admin/contests.php';
?>