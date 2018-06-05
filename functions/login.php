<?php  
function rfs_redirect_to($type) {
	$base_url = esc_url( get_bloginfo('url') );
	switch($type) {
		case 'login':
			$link = $base_url.'/logowanie';
			break;
		case 'profile':
			$link = $base_url.'/profil';
			break;
		case 'admin':
			$link = $base_url.'/wp-admin';
			break;
		default:
			$link = $base_url;
	}
	return $link;
}

function set_roles_names() {
	global $wp_roles;
	if( !isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	$wp_roles->roles['subscriber']['name'] 	= 'Użytkownik';
	$wp_roles->role_names['subscriber'] 	= 'Użytkownik';
	$wp_roles->roles['contributor']['name'] = 'Właściciel obiektu';
	$wp_roles->role_names['contributor'] 	= 'Właściciel obiektu';
}
add_action( 'init', 'set_roles_names' );

function restrict_admin() {
	if(!defined('DOING_AJAX') && !current_user_can('publish_posts')){
		wp_redirect(home_url()); exit();
	}
}
add_action( 'admin_init', 'restrict_admin' );

function hide_admin_bar() {
	if (!current_user_can('publish_posts') && !is_admin()) {
		add_filter('show_admin_bar', '__return_false');
	}
}
add_action( 'after_setup_theme', 'hide_admin_bar' );

function remove_unnecessary_roles() {
	if( get_role('author') ){
		remove_role( 'author' );
	}
}
add_action( 'admin_init', 'remove_unnecessary_roles' );

function rfs_role($role, $login=false) {
	if( !is_user_logged_in() ) return false;

	$user_id = get_current_user_id();
	$user 	 = $login ? get_user_by('login', $login) : get_userdata($user_id);

	if($user) {
		$roles = $user->roles;
		if(in_array($role, $roles)) {
			return true;
		}
	}
	return false;
}

function is_login_page() {
	return is_page('logowanie');
}

function is_profile_page() {
	return is_page('profil');
}

function rfs_create_pages() {

	new RFS_Create_Page(array(
		'id'		=> -999,
		'slug' 		=> 'logowanie',
		'title' 	=> 'Logowanie',
		'content' 	=> ''
	));

	new RFS_Create_Page(array(
		'id'		=> -998,
		'slug' 		=> 'profil',
		'title' 	=> 'Profil',
		'content' 	=> ''
	));

	new RFS_Create_Page(array(
		'id'		=> -997,
		'slug' 		=> 'payu-notifications',
		'title' 	=> 'Payu Notifications',
		'content' 	=> ''
	));

	new RFS_Create_Page(array(
		'id'		=> -996,
		'slug' 		=> 'payment-confirmation',
		'title' 	=> 'Potwierdzenie zamówienia',
		'content' 	=> ''
	));

	new RFS_Create_Page(array(
		'id'		=> -995,
		'slug' 		=> 'paypal-notifications',
		'title' 	=> 'PayPal Notifications',
		'content' 	=> ''
	));

	new RFS_Create_Page(array(
		'id'		=> -994,
		'slug' 		=> 'paypal-execute-payment',
		'title' 	=> 'Execute payment',
		'content' 	=> ''
	));

}
add_action('pre_get_posts', 'rfs_create_pages');


function register_user_via_admin($user_id) {
	$email		= $_POST['email'];
	$login		= $_POST['user_login'];
	$password	= $_POST['pass1'];
	$send_email	= $_POST['send_user_notification'];

	//update_user_meta( $user_id, 'subscription_term', subscription_term(1) );

	if($send_email == 1) {
		$message = rfs_get_email_template('registration', array(
			'login' 	=> $login,
			'password' 	=> $password
		));
		$subject 	= esc_html(get_bloginfo('name')).' - Konto użytkownika';
		$send 		= wp_mail($email, $subject, $message);
	}
}
add_action( 'user_register',  'register_user_via_admin', 10, 1);

/**
 * Redirects wp native registration and password reset actions to login page
 */
if(!function_exists('rfs_redirect_action')) {
	function rfs_redirect_action() {
		$action = isset($_GET['action']) ? $_GET['action'] : 'login';
		if($action == 'register' || $action == 'forgot' || $action == 'resetpass') {
			wp_redirect(rfs_redirect_to('login'));
			exit;	
		}
	}
	add_action('init', 'rfs_redirect_action');
}
/**
 * Redirect to the custom login page
 */
if(!function_exists('rfs_login_init')) {
	function rfs_login_init() {
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';
	
		if(isset( $_POST['wp-submit'])) {
			$action = 'post-data';
		}else if(isset($_GET['reauth'])) {
			$action = 'reauth';
		}
	
		if (
			$action == 'post-data'		||			// don't mess with POST requests
			$action == 'reauth'			||			// need to reauthorize
			$action == 'logout'						// user is logging out
		) {
			return;
		}
		
		wp_redirect(rfs_redirect_to('login'));
		exit;
	}
	add_action('login_init', 'rfs_login_init');
}
/**
 * Redirect logged in users to the right page
 */
if(!function_exists('rfs_template_redirect')) {
	function rfs_template_redirect() {
	
		if( ( is_login_page() ) && is_user_logged_in() ) {
			wp_redirect( rfs_redirect_to('profile') );
			exit();
		}

		if(  is_profile_page() && !is_user_logged_in() ) {
			wp_redirect( rfs_redirect_to('login') );
			exit();
		}

		if( is_profile_page() && rfs_role('administrator') ) {
			wp_redirect( rfs_redirect_to('admin') ); exit;
		}
		
	}
	add_action('template_redirect', 'rfs_template_redirect');
}
/**
 * Login page redirect
 */
if(!function_exists('rfs_login_redirect')) {
	function rfs_login_redirect($redirect_to, $url, $user) {
	
		if (!isset($user->errors)) {
			return $redirect_to;
			exit;
		}
		
		wp_redirect(rfs_redirect_to('login'));
		exit;
	
	}
	add_filter('login_redirect', 'rfs_login_redirect', 10, 3);
}
?>