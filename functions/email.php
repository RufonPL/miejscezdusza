<?php  
// Set email default options
function rfs_mail_from($old) {
	return get_option('admin_email');
}
add_filter('wp_mail_from', 'rfs_mail_from');

function rfs_mail_from_name($old) {
 	return get_option('blogname');
}
add_filter('wp_mail_from_name', 'rfs_mail_from_name');

function rfs_mail_contenttype($content_type){
	return 'text/html';
}
add_filter('wp_mail_content_type','rfs_mail_contenttype');


function rfs_get_email_template($file, $params=array()) {
	$file = TEMPLATEPATH.'/email-templates/'.$file.'.php';
	if(file_exists($file) && is_file($file)) {
		ob_start();
		include $file;
		$params = $params ? $params : '';
		return ob_get_clean();
	}else {
		return false;
	}
}

function rfs_email_get_header() {
	$logo = get_field('_logo','option');
	$logo = $logo ? '<img src="'.esc_url($logo['sizes']['medium']).'" alt="'.esc_attr($logo['alt']).'"/>' : '<h2>'.get_option('blogname').'</h2>';
	return '
		<div class="em-header">
			<a href="'.esc_url(get_bloginfo('url')).'">'.$logo.'</a>
		</div>';	
}

function rfs_email_get_footer() {
	return '
		<div class="em-footer">
			<p>Pozdrawiamy</p>
			<p>'.get_bloginfo('name').'</p>	
		</div>';	
}
?>