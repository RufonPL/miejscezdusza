<?php
/**
 * This function will set default sessions
 * @param n/a
 * @return n/a
*/
function defaultSessions() {
	if( !isset($_SESSION['places_visited']) ) {
		$_SESSION['places_visited'] = array();
	}
}
add_action('template_redirect', 'defaultSessions');

/**
 * This function will set cookie for voted on place
 * @param $place_id, $type
 * @return n/a
*/
function setVotingCookie($place_id, $type) {
	if( !isset($_COOKIE['md_place_'.$type.'_'.$place_id]) ) {
		setcookie('md_place_'.$type.'_'.$place_id, true, time() + (86400), '/');
	}
}

/**
 * This function will check if user has voted on a place in the last 24 hours
 * @param $place_id
 * @return boolean
*/
function hasVotedToday($place_id, $type) {
	return isset($_COOKIE['md_place_'.$type.'_'.$place_id]);
}

/**
 * This function will return month number
 * @param n/a
 * @return int
*/
function placeMonth($current=false) {
	$month = $current ? absint( date('n') ) : absint( date('n') ) - 1;
	$month = $month < 1 ? 12 : $month;

	return $month;
}

/**
 * This function will return year number
 * @param n/a
 * @return int
*/
function placeYear($current=false) {
	$year = $current ? absint( date('Y') ) : absint( date('Y') ) - 1;
	$year = $year < 1 ? 12 : $year;

	return $year;
}

/**
 * This function will return month slug by given month number
 * @param $monthNo
 * @return string
*/
function monthSlug($monthNo) {
	$months = array('styczen', 'luty', 'marzec', 'kwiecien', 'maj', 'czerwiec', 'lipiec', 'sierpien', 'wrzesien', 'pazdziernik', 'listopad', 'grudzien');

	return $months[$monthNo-1];
}

/**
 * This function will return month name by given month number
 * @param $monthNo
 * @return string
*/
function monthName($monthNo) {
	$months = array('Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień');

	return $months[$monthNo-1];
}

/**
 * This function will render html header for a month contest
 * @param n/a
 * @return html
*/
function monthPlaceHeader($monthOnly=false, $monthNo = false) {
	$imgsPath 		= get_bloginfo( 'template_url' ).'/images/';
	$base 			= 'miejsce_miesiaca.png';
	$month 			= $monthNo ? absint( $monthNo ) : placeMonth(true);

	if( $monthOnly ) {
		return '<img class="month-name" src="'.esc_url( $imgsPath.'dates/'.monthSlug($month) ).'.png" alt="'.monthSlug($month).'">';
	}else {
		return '<img class="inline-block month-base" src="'.esc_url( $imgsPath.$base ).'" alt="Miejsce miesiąca"><img class="inline-block month-name" src="'.esc_url( $imgsPath.'dates/'.monthSlug($month) ).'.png" alt="'.monthSlug($month).'">';
	}
}

/**
 * This function will render html header for a year contest
 * @param n/a
 * @return html
*/
function yearPlaceHeader($yearOnly=false) {
	$imgsPath 		= get_bloginfo( 'template_url' ).'/images/';
	$base 			= 'miejsce-roku.png';
	$year 			= placeYear(true);

	if( $yearOnly ) {
		return '<img class="year-name" src="'.esc_url( $imgsPath.'dates/'.$year ).'.png" alt="'.$year.'">';
	}else {
		return '<img class="inline-block month-base" src="'.esc_url( $imgsPath.$base ).'" alt="Miejsce roku"><img class="inline-block month-name" src="'.esc_url( $imgsPath.'dates/'.$year ).'.png" alt="'.$year.'">';
	}
}

/**
 * This function will restrict voting to 14th of each month
 * @param n/a
 * @return int
*/
function monthVotingActive() {
	return absint( date('d') < 15 );
}

/**
 * This function will restrict voting to 14th of each month
 * @param n/a
 * @return int
*/
function yearVotingActive() {
	return absint( date('m') < 3 );
}

/**
 * This function will collect post visits - will collect only posts with publish date('Y-m') the same as current date('Y-m')
 * @param $place_id
 * @return n/a
*/
function collectPlaceVisit($place_id) {
	global $wpdb;
	$visited 			= $_SESSION['places_visited'];
	$placePublishYear 	= get_the_date('Y', $place_id);
	$placePublishMonth 	= get_the_date('n', $place_id);
	$month 				= placeMonth(true); //current month

	if( !in_array($place_id, $visited) && $place_id > 0 && $placePublishYear == date('Y') && $placePublishMonth == $month ) {

		$_SESSION['places_visited'][] = $place_id;

		$wpdb->insert(
			$wpdb->prefix.'places_visits',
			array(
				'post_id' 	=> $place_id,
				'date'		=> date('Y-m-d H:i')
			),
			array('%d', '%s')
		);

	}
}

/**
 * This function will create wp query of qualified posts for place of the month contest
 * @param $ppp, $page
 * @return object
*/
function getQualifiedMonthPlaces($year, $month, $day, $ppp, $page, $meta_query = array()) {
	global $wpdb;

	$post_in 	= array();

	$qualified = $wpdb->get_results("SELECT post_id, COUNT(*) as count FROM {$wpdb->prefix}places_visits WHERE MONTH(date) = {$month} AND YEAR(date) = {$year} GROUP BY post_id ORDER BY count DESC, date DESC LIMIT 10");

	if( $qualified ) {
		if( count($qualified) > 2 ) { 
			foreach($qualified as $place) {
				$post_in[] = $place->post_id;
			}
		}else {
			$post_in = array(0);
		}

		if( $day > 14 ) {
			//$post_in = array(0);
		}
		
		$places = new WP_Query( array(
			'post_type'			=> 'post',
			'posts_per_page'	=> 4,
			'post_status'		=> 'publish',
			'posts_per_page' 	=> $ppp,
			'paged' 			=> $page,
			'post__in' 			=> $post_in,
			'orderby'			=> 'post__in',
			'meta_query' => $meta_query
		) );

		return $places;
	}

	// must return wp query
	return $places = new WP_Query( array(
		'post_type'			=> 'post',
		'posts_per_page'	=> 1,
		'post_status'		=> 'publish',
		'post__in' 			=> array(0),
	) );
}

/**
 * This function will create wp query of posts for place of the year contest
 * @param $year, $ppp, $page
 * @return object
*/
function getYearContestPlaces($year, $ppp, $page, $meta_query = array()) {
	global $wpdb;

	$post_in 	= array();
	$type 		= 'month';
	$places 	= $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}places_winners WHERE year = '%d' AND type = '%s'", array($year, $type) ) );

	if( !$places ) {
		$post_in = array(0);
	}else {
		foreach($places as $place) {
			$post_in[] = $place->winner;
		}
	}

	$posts = new WP_Query( array(
		'post_type'				=> '',
		'posts_per_page'	=> $ppp,
		'paged' 					=> $page,
		'post_status'			=> 'publish',
		'post__in' 				=> $post_in,
		'orderby'					=> 'post__in',
		'meta_query' 			=> $meta_query
	) );

	if($posts->have_posts()) {
		return $posts;
	}; wp_reset_postdata();

	return $places = new WP_Query( array(
		'post_type'			=> 'post',
		'posts_per_page'	=> 1,
		'post_status'		=> 'publish',
		'post__in' 			=> array(0),
	) );
}

/**
 * This function will return places qualified for contest as well as the winner for given month and year
 * @param 
 * @return 
*/
function getContestPlaces($year, $month, $type = 'month') {
	$places = false;

	switch($type) {
		case 'month':
			$places = getQualifiedMonthPlaces($year, $month, 1, -1, 1);
			break;
		case 'year': 
			$places = getYearContestPlaces($year, -1, 1);
			break;
	}

	$allVotes 	= array();

	if( !$places ) return false;

	if($places->have_posts()) {
		while($places->have_posts()) { $places->the_post();
			$votes = get_post_meta( get_the_ID(), 'votes-'.$type, true ); 
			$votes = $votes ? $votes : 0;
			$allVotes[] = array(
				'id'=> get_the_ID(),
				'votes' => $votes
			);
		}

		function sortByVotes($a, $b) {
			return $a['votes'] < $b['votes'];
		}
		usort($allVotes, 'sortByVotes');
	
		$max = max(array_column($allVotes, 'votes'));
		$maxs = array();

		foreach($allVotes as $item) {
			if($item['votes'] == $max) {
				$maxs[] = $item['id'];
			}
		}
		
		$dates = array();
		
		foreach($maxs as $post_id) {
			$dates[] = array(
				'id' => $post_id,
				'date' => get_the_date('Y-m-d H:i:s', $post_id)
			);
		}
		
		function sortByDate($a, $b) {
			return strtotime($b['date']) - strtotime($a['date']);
		}

		usort($dates, 'sortByDate');
		$winner = $dates[0]['id'];

		$data = new StdClass();
		$data->all = $allVotes;
		$data->winner = $winner;

		switch($type) {
			case 'month':
				setPlaceWinner($type, $winner, $year, $month+1);
				break;
			case 'year': 
				setPlaceWinner($type, $winner, $year, 0);
				break;
		}

		return $data;
	}
	return false;
}

/**
 * This function will set current month winner
 * @param $type, $winner, $year, $month = 
 * @return n/a
*/
function setPlaceWinner($type, $winner, $year, $month = 0) {
	global $wpdb;

	$get_winner = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}places_winners WHERE year = '%d' AND month = '%d'", array($year, $month)) );

	if( $get_winner !== null ) {

		if( $get_winner->year == $year && $get_winner->month == $month ) return;

		$wpdb->update(
			$wpdb->prefix.'places_winners',
			array(
				'type'		=> $type,
				'year' 		=> $year,
				'month' 	=> $month,
				'winner'	=> $winner
			),
			array($get_winner->id),
			array('%s', '%d', '%d', '%d'),
			array('%d')
		);

	}else {

		$wpdb->insert(
			$wpdb->prefix.'places_winners',
			array(
				'type'		=> $type,
				'year' 		=> $year,
				'month' 	=> $month,
				'winner'	=> $winner
			),
			array('%s', '%d', '%d', '%d')
		);

	}
}

/**
 * This function will schedule cron event for updating current month winner
 * @param n/a
 * @return n/a
*/
if( !wp_next_scheduled('update_contest_winner_event') ) {
	wp_schedule_event( time(), 'hourly', 'update_contest_winner_event' );
}
add_action('update_contest_winner_event', 'update_contest_winner');

function update_contest_winner() {
	getContestPlaces( absint(date('Y')), placeMonth() );
	getContestPlaces( absint(date('Y')), 0, 'year' );
}


/**
 * This function will return list of winners ordered by date from latest descending
 * @param $type, $ppp, $page
 * @return mixed
*/
function winners_list($type, $ppp, $page) {
	global $wpdb;

	$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}places_winners WHERE type = '%s'", $type) );

	if( $count < 1 ) return;

	$ppp 		= $ppp < 0 ? 9999 : $ppp;
	$offset 	= ($page - 1) * $ppp;

	$pages 		= ceil( $count / $ppp );
	$lastPage 	= $page == $pages;

	$currenYear 	= date('Y');
	$currentMonth 	= placeMonth(true);

	$allYears = $wpdb->get_results( "SELECT DISTINCT year FROM {$wpdb->prefix}places_winners" );

	switch($type) {
		case 'month':

			$winners = $wpdb->get_results( $wpdb->prepare(" SELECT * FROM {$wpdb->prefix}places_winners WHERE type = '%s' AND NOT (year = '%d' AND month = '%d') ORDER BY year DESC, month DESC LIMIT {$ppp} OFFSET {$offset}", array($type, $currenYear, $currentMonth) ) );

			break;
		case 'year':
			$winners = $wpdb->get_results( $wpdb->prepare(" SELECT * FROM {$wpdb->prefix}places_winners WHERE type = '%s' ORDER BY year DESC LIMIT {$ppp} OFFSET {$offset}", $type) );
			
			break;
	}

	$data 							= array();
	$data['winners'] 		= $winners;
	$data['lastPage'] 	= $lastPage;

	if( $allYears ) {
		foreach($allYears as $year) {
			$data['years'][] = $year->year;
		}
	}

	return $data;
}


function has_ever_won($place_id, $type = 'month') {
	global $wpdb;

	$data = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}places_winners WHERE winner = '%d' AND type = '%s'", array($place_id, $type) ) );

	if( $data ) {
		return $data;
	}
	
	return false;
}

function monthLaurel($place_id) {
	$has_won 	= has_ever_won( $place_id );
	$laurel 	= '';
	
	if( $has_won ) {
		$month 	= monthPlaceHeader(true, $has_won->month);

		$laurel .= '<div class="place-laurel">'.$month.'</div>';
	}

	return $laurel;
}
?>