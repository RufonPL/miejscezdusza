<?php  
/**
 * This function will add user place as seen or to visit
 * @param $type (string): seen or to-visit
 * @param $place_id (int)
 * @return int/boolean
*/
function add_user_place($type, $place_id) {
	if( !is_user_logged_in() ) return;

	return add_user_meta( get_current_user_id(), 'place-'.$type, $place_id);
}

/**
 * This function will reove user place as seen or to visit
 * @param $type (string): seen or to-visit
 * @param $place_id (int)
 * @return boolean
*/
function remove_user_place($type, $place_id) {
	if( !is_user_logged_in() ) return;

	return delete_user_meta( get_current_user_id(), 'place-'.$type, $place_id);
}

/**
 * This function will return an array of places seen or to visit for a user
 * @param $type (string): seen or to-visit
 * @return array
*/
function get_user_places($type) {
	$places = array();
	$seen 	= get_user_meta( get_current_user_id(), 'place-'.$type );

	if( $seen ) {
		foreach($seen as $place_id) {
			//if( is_place_active($place_id) ) {
				$places[] = array(
					'id'		=> $place_id,
					'type'	=> $type
				);
			//}
		}
	}

	return $places;
}
?>