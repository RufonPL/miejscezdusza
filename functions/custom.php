<?php
function template_id($template, $permalink=false) {
	global $post;
	$template_page = get_posts(array(
		'post_type'=>'page',
		'meta_key' => '_wp_page_template',
		'meta_value' => 'page-templates/'.$template.'-template.php'
	));
	
	$ids = array();
	if($template_page) {
		foreach($template_page as $t_page) {
			$ids[] = $t_page->ID; 
		}
		if(count($ids)>1) {
			if($permalink) {
				return $ids[0];
			}else {	
				return $ids;
			}
		}else {
			return $ids[0];
		}
	}
	return false;
}
function require_template_part($template_part_name, $subfolder=false, $params = array()) {
	$sub = $subfolder ? $subfolder.'/' : '';
	$file = get_template_directory().'/template-parts/'.$sub.$template_part_name.'.php';
	if(file_exists($file)) {
		require $file;
	}
	return;
}
function p2br($content) {
	$newcontent = preg_replace("/<p[^>]*?>/", "", $content);
	$newcontent = str_replace("</p>", "<br />", $newcontent);
	return preg_replace('#(( ){0,}<br( {0,})(/{0,1})>){1,}$#i', '', $newcontent);
}
function excerpt($string, $limit=16, $more=NULL, $exclude=false) {
	$words = explode(' ',$string);
	$excerpt = implode(' ',array_splice($words,0,$limit)).$more; 	
	$excerpt = trim($excerpt,',;');
	return strip_tags($excerpt, $exclude);
}

function rfs_pagination($pages = '', $range = 2) {  
     $showitems = ($range * 2)+1;  

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '') {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages) {
             $pages = 1;
         }
     }   
	 echo '<div class="clearfix"></div>';
	 if(1 != $pages) {
		echo '<div class="pagination">'; 
		if($paged==1) {
			echo '<span class="prev-page text-uppercase"><i class="fa fa-angle-left"></i></span>';	
		}else {
			echo '<a class="prev-page text-uppercase" href="'.get_pagenum_link($paged - 1).'"><i class="fa fa-angle-left"></i></a>';
		}
		echo '<div class="pagination-pages inline-block">';
		for ($i=1; $i <= $pages; $i++) {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
                 echo ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".get_pagenum_link($i)."'>".$i."</a>";
             }
        }
		echo '</div>';
		if($paged==$pages) {
			echo '<span class="next-page text-uppercase"><i class="fa fa-angle-right"></i> </span>';	
		}else {
			echo '<a class="next-page text-uppercase" href="'.get_pagenum_link($paged + 1).'"><i class="fa fa-angle-right"></i></a>';
		}
		echo '</div>';
	 }
}

function admin_add_jquery() {
	echo "
	<script>
	jQuery(document).ready(function($) {
		if($('#page_for_posts').length) {
			$('#page_for_posts').parent().hide();
			$('#front-static-pages').find('p').first().hide();
			$('#front-static-pages').find('#page_on_front option').first().hide()
			$('#posts_per_rss').closest('tr').hide();
			$('input[name=rss_use_excerpt]').closest('tr').hide();
			$('#posts_per_page').closest('tr').hide();
		}
	});
	</script>";
}
//add_action('admin_head', 'admin_add_jquery');

function empty_content($str) {
    return trim(str_replace('&nbsp;','',strip_tags($str))) == '';
}

function show_breadcrumbs() {
	if(function_exists('yoast_breadcrumb')) {
		return yoast_breadcrumb('<div class="container-fluid" id="breadcrumbs"><div class="container">','</div></div>', false);
	}
}

function social_icons($container_class = '') {
	$social = get_field('_social_icons', 'option');	
	$html 	= '';
	
	if($social) {
		$html .= '<div class="social-media '.$container_class.'">';
		foreach($social as $icons) {
			$icon  = $icons['_icon'];
			$link  = $icons['_link'];
			if($icon == 'envelope-o' && is_email( $link )) {
				$link = 'mailto:'.$link;
			}
			if($icon == 'phone') {
				$link = 'tel:'.str_replace(' ', '', $link);
			}
			$target = $icon != 'envelope-o' && $icon != 'phone' ? 'target="_blank"' : '';
			$html .= '<a '.$target.' class="relative inline-block social-icon transition nuh social-'.esc_html($icon).'" href="'.esc_url($link).'"><i class="fa fa-'.esc_html($icon).' fa-15 absolute-center-both color3"></i></a>';
		}
		$html .= '</div>';
	}
	return $html;
}

function rfs_buttons_lines_tiny_mce(){
	add_filter( 'mce_buttons', 'rfs_tiny_mce_buttons_restore', 5 );	
}	 
add_action( 'admin_head', 'rfs_buttons_lines_tiny_mce', 5 );

function rfs_tiny_mce_buttons_restore( $buttons_array ){
	$mce_buttons = array( 
			'formatselect',		// Dropdown list with block formats to apply to selection.
			'bold',				// Applies the bold format to the current selection.
			'italic',			// Applies the italic format to the current selection.
			'underline',		// Applies the underline format to the current selection.
			'bullist',			// Formats the current selection as a bullet list.
			'numlist',			// Formats the current selection as a numbered list.
			'blockquote',		// Applies block quote format to the current block level element.
			'alignleft',		// Left aligns the current block or image.
			'aligncenter',		// Left aligns the current block or image.
			'alignright',		// Right aligns the current block or image.
			'alignjustify',		// Full aligns the current block or image.
			'link',				// Creates/Edits links within the editor.
			'unlink',			// Removes links from the current selection.
			'wp_more',			// Inserts the <!-- more --> tag.
			'spellchecker',		// ???
			'wp_adv',			// Toggles the second toolbar on/off.
			'dfw' 				// Distraction-free mode on/off.
		); 
	return $mce_buttons;
}

function rfs_mce_buttons( $buttons ) {
	array_unshift( $buttons, 'fontsizeselect' ); 
	return $buttons;
}
add_filter( 'mce_buttons_2', 'rfs_mce_buttons' );

function rfs_mce_text_sizes( $initArray ){
	$initArray['fontsize_formats'] = "12px 13px 14px 16px 18px 20px 24px 28px 32px 36px";
	return $initArray;
}
add_filter( 'tiny_mce_before_init', 'rfs_mce_text_sizes' );

function rfs_get_footer_menu($menu_location) {
	$menu_exists = wp_nav_menu(array(
		'theme_location' 	=> $menu_location, 
		'echo' 				=> false,
		'fallback_cb' 		=> '__return_false'
	));	
	if(!empty($menu_exists)) {
		$locations 	= get_nav_menu_locations();
		$menu_id 	= $locations[$menu_location] ;
		$menu_name 	= wp_get_nav_menu_object($menu_id);
		
		$html = '<h5 class="footer-nav-header color1 text-uppercase">&sect; '.esc_html($menu_name->name).'<span></span></h5>';
		$args = array(
			'theme_location' 	=> $menu_location, 
			'container_class' 	=> 'navbar-footer', 
			'menu_class' 		=> 'footer-nav list-unstyled',
			'fallback_cb'		=> '',
			'menu_id' 			=> 'footer'.$menu_id.'-menu',
			'echo'				=> false,
			'walker' 			=> new Rfswp_Walker_Nav_Menu()); 
		$html .= wp_nav_menu($args);
		return $html;
	}
	return false;
}

function place_extras($extras = array()) {
	if(is_array($extras)) {
		$count = count($extras);
		$html = '<div class="place-single-extras"><div class="place-single-extras-wrap">';
		$i=1; foreach($extras as $extra) {
			$html .= '<div class="place-extra inline-block">';
			$html .= '<span data-toggle="tooltip" data-placement="top" title="'.esc_html( $extra['label'] ).'" class="pi-icon pi-'.esc_html( $extra['value'] ).'"></span>';
			$html .= '</div>';
			if($i%4 == 0 && $i!=$count) {
				$html .= '</div><div class="place-single-extras-wrap">';
			}
		$i++; }
		$html .= '</div></div>';
		return $html;
	}	
	return;
}

function counties_list() {
	return array(
		'Dolnośląskie',
		'Kujawsko-pomorskie',
		'Lubelskie',
		'Lubuskie',
		'Łódzkie',
		'Małopolskie',
		'Mazowieckie',
		'Opolskie',
		'Podkarpackie',
		'Podlaskie',
		'Pomorskie',
		'Śląskie',
		'Świętokrzyskie',
		'Warmińsko-mazurskie',
		'Wielkopolskie',
		'Zachodniopomorskie'
	);
}

function regions_list() {
	$regions = new WP_Query( array(
		'post_type'	        => 'regiony',
		'posts_per_page'    => -1,
		'post_status'       => 'publish'
	) );
	
	$list = array();
	
	if( $regions->have_posts() ) {
		while( $regions->have_posts() ) {
			$regions->the_post();
			$list[] = get_the_ID();
		}
	} wp_reset_postdata();

	return $list;
}

function get_extras_list() {
	$field 		= get_field_object('field_589f007328849');
	$choices 	= $field['choices'];
	$extras 	= array();
	if(is_array($choices)) {
		foreach($choices as $value => $label) {
			$extras[] = array(
				'value' => $value,
				'label'	=> $label
			);
		}
	}
	return $extras;
}

function set_map_pin_coords($type, $value) {
	switch($type) {
		case 'lng': // left-right
			$offset = 0.8;
			$start 	= 14.127705;
			$end 	= 24.144155;
			break;
		case 'lat': // top-bottom
			$offset = 2.6;
			$start 	= 54.854484;
			$end 	= 49.003295;
			break;
	}

	$one 		= 100 / ($end - $start);
	$percent 	= (($value - $start) * $one) - $offset; 

	return $percent;
}

function map_counties() {
	$counties = array();
	$counties['Dolnośląskie'] 			= 's38';
	$counties['Kujawsko-pomorskie'] 	= 's36';
	$counties['Lubelskie'] 				= 's33';
	$counties['Lubuskie'] 				= 's25';
	$counties['Łódzkie'] 				= 's26';
	$counties['Małopolskie'] 			= 's24';
	$counties['Mazowieckie'] 			= 's50';
	$counties['Opolskie'] 				= 's21';
	$counties['Podkarpackie'] 			= 's15';
	$counties['Podlaskie'] 				= 's22';
	$counties['Pomorskie'] 				= 's11';
	$counties['Śląskie'] 				= 's21';
	$counties['Świętokrzyskie'] 		= 's23';
	$counties['Warmińsko-mazurskie'] 	= 's18';
	$counties['Wielkopolskie'] 			= 's27';
	$counties['Zachodniopomorskie'] 	= 's18';
	return $counties;
}

function show_preloader() {
	echo '
		<div class="preloader-sm inline-block preloader-container overflow">
			<div class="uil-rolling-css preloader">
				<div><div></div><div></div></div>
			</div>
		</div>
	';
}

function page_header($id, $text, $center=true, $other=false, $h=true, $extraClass=false) {
	$image 			= get_field('_page_title_image'.$other, $id);
	$centerClass 	= $center ? 'text-center' : '';
	$extraClass 	= $extraClass ? 'no-margin' : '';

	$html = $h ? '<div class="page-header-title row '.$centerClass.' '.$extraClass.'">' : '';
	if($image) {
		$html .= '<img src="'.esc_url( $image['sizes']['large'] ).'" alt="'.esc_attr( $image['alt'] ).'" class="inline-block">';
	}else {
		if($text) {
			$html .= $h ? '<h1 class="text-uppercase">'.esc_html( $text ).'</h1>' : esc_html( $text );
		}
	}
	$html .= $h ? '</div>' : '';

	return $html;
}

/**
 * This function will set default 30 days expiry date on post cration if the date is not set
 * @param n/a
 * @return n/a
*/
function set_place_expiry_date_on_post_save($new_status, $old_status, $post) {
	global $post_type;
	if($post_type != 'post') return;
	$expiry_date = sanitize_text_field($_POST['acf']['field_5911eab96cdd2']);

	if( empty($expiry_date) ) {
		$_POST['acf']['field_5911eab96cdd2'] = subscription_term(1, false, true);
	}
}
add_action( 'transition_post_status', 'set_place_expiry_date_on_post_save', 10, 3 );

function user_place_visit_status($place_id) {
	
}

function places_count_text($count) {
	$text = ' obiektów';
				
	if( $count == 1 ) {
		$text = ' obiekt';
	}
	if( ($count > 1 && $count < 5) || ($count > 21 && strpos((string)$count, 5) !== false) || ($count > 21 && strpos((string)$count, 1) !== false) ) {
		$text = ' obiekty';
	}

	return $count.$text;
}
?>
