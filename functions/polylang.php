<?php  
function show_flags($class=false) {
	if(function_exists('pll_the_languages')) {
		echo '<div class="flags-box inline-block text-center '.$class.'"><ul class="flags">';
			pll_the_languages(array(
				'dropdown'			     => 0,
				'show_names'			 => 1,
				'show_flags'			 => 0,
				'display_names_as' 		 => 'name', //or slug
				'hide_if_empty'			 => 1,
				'force_home'			 => 0,
				'echo'					 => 1,
				'hide_if_no_translation' => 0,
				'hide_current'			 => 0,
				'post_id'				 => NULL,
				'raw'					 => 0
			));
		echo '</ul><!--end flags--></div><!--end flags box-->';
	}	
	return false;
}
if(function_exists('pll_the_languages')) {
	$group = get_bloginfo('name');
	$phrases = array(
	);
	foreach($phrases as $name => $phrase) {
		pll_register_string($name, $phrase, $group);
	}
}

function pll_trans($string, $return=false) {
	if(function_exists('pll_the_languages')) {
		if($return==true) {
			return pll__($string);	
		}else {
			return pll_e($string);	
		}
	}else {
		if($return==true) {
			return $string;	
		}else {
			echo $string;	
		}	
	}
}


?>