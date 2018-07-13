<?php  
function rfs_add_frontend_ajax() {
	wp_enqueue_script('rfs_ajax',  get_stylesheet_directory_uri() . '/js/ajax.js', array('jquery'),'', true );
	wp_localize_script(
		'rfs_ajax', 
		'frontendajax', 
		array(
			'ajaxurl' 				=> admin_url( 'admin-ajax.php' ),
			'load_regions_nonce' 	=> wp_create_nonce( 'regions_nonce' ),
			'login_nonce' 			=> wp_create_nonce( 'get_login_nonce' ),
			'vote_nonce' 			=> wp_create_nonce( 'get_vote_nonce' ),
			'load_winners_nonce'	=> wp_create_nonce( 'get_winners_nonce' ),
			'payment_nonce'			=> wp_create_nonce( 'get_payment_nonce' ),
			'user_action_nonce'		 => wp_create_nonce( 'get_user_action_nonce' ),
		)
	);
}
add_action('wp_enqueue_scripts', 'rfs_add_frontend_ajax', 9);

function loadPlaces_posts_data($posts, $contest = '', $exclude) {
	$data 			= array(
		'postsData' => array(),
		'exclude' 	=> $exclude
	);
	$last_page 	= false;

	if($posts->have_posts()) {

		while($posts->have_posts()) { $posts->the_post();
			$images 	= get_field('_place_images');
			$excerpt 	= get_field('_place_excerpt');
			$county 	= get_field('_place_county');
			$contact 	= get_field('_place_contact');
			$extras 	= get_field('_place_extras');
			
			$data['exclude'][] = get_the_ID();
			
			$place_extras = array();

			if($extras) {
				foreach($extras as $extra) {
					$place_extras[$extra['value']] = esc_html($extra['label']);
				}
			}

			$hasVotedToday = $contest != '' ? hasVotedToday(get_the_ID(), $contest) : '';

			$data['postsData'][] = array(
				'id'		=> get_the_ID(),
				'imageSrc' 	=> $images ? esc_url($images[0]['sizes']['place-image']) : '',
				'imageAlt' 	=> $images && $images[0]['alt'] ? esc_attr($images[0]['alt']) : esc_html(get_the_title()),
				'name'		=> esc_html(get_the_title()),
				'excerpt'	=> empty_content($excerpt) ? '' : wp_kses($excerpt, array('br'=>array())),
				'county'	=> $county ? esc_html($county) : '',
				'contact'	=> $contact ? esc_html($contact) : '',
				'link'		=> esc_url(get_permalink()),
				'extras'	=> $place_extras,
				'votedOn'	=> $hasVotedToday,
                'hasLaurMonth' => monthPlaceImageUrl( get_the_ID() )
			);
		}
	} wp_reset_postdata();

	return $data;
}

function loadPlaces_contests_posts($ppp, $page, $contest, $meta_query) {
	switch($contest) {
		case 'month':
			$day 	= date('d');
			$month 	= placeMonth();
			$year 	= date('Y');
			
			$posts 	= getQualifiedMonthPlaces($year, $month, $day, $ppp, $page, array( $meta_query) );
			break;
		case 'year':
			$year = placeYear(true);
			$posts = getYearContestPlaces($year, $ppp, $page, array( $meta_query));
			break;
	}

	return $posts;
}

function loadPlaces_promo_posts($ppp, $page, $promo) {
	switch($promo) {
		case 'recommended':
			$posts = get_promoted_places_only($promo, 'date', true, $ppp, $page, true);
			break;
	}

	return $posts;
}

function loadPlaces_all_active_count($meta_query, $ppp) {
	$posts = new WP_Query(array(
		'post_type'				=> 'post',
		'post_status'			=> 'publish',
		'posts_per_page' 	=> 1,
		'meta_query' 			=> array( $meta_query ),
	));

	return array(
		'post_count' 	=> $posts->found_posts,
		'pages'				=> ceil($posts->found_posts / $ppp)
	);
}

function loadPlaces_filtered_posts($ppp, $exclude, $meta_query) {
	$posts = new WP_Query(array(
		'post_type'				=> 'post',
		'post_status'			=> 'publish',
		'posts_per_page' 	=> $ppp,
		'paged' 					=> 1,
		'post__not_in'		=> $exclude,
		'meta_query' 			=> array( $meta_query ),
	));

	return $posts;
}

function loadPlaces_merged_queries_posts($query1, $query2) {
	$post_ids = array();
	$merged_posts = array_merge($query1->posts, $query2->posts);

	if( $merged_posts ) {
		foreach($merged_posts as $p) {
			$post_ids[] = $p->ID;
		}
	}

	$posts = new WP_Query(array(
		'post_type'				=> 'post',
		'post_status'			=> 'publish',
		'posts_per_page' 	=> -1,
		'post__in'				=> $post_ids,
		'orderby' 				=> 'post__in'
	));

	return $posts;
}

function loadPlaces_updated_exclude($data, $exclude) {
	if( $data['postsData'] ) {
		foreach($data['postsData'] as $post) {
			$exclude[] = $post['id'];
		}
	}

	return $exclude;
}

function loadPlaces_sorted($posts, $activePlacesOnly, $ppp, $page, $sort_query, $exclude) {
	$last_page = false;
	$filterNothingFound = false;

	$sort_query_rest = array(
		$activePlacesOnly,
		$sort_query
	);

	$sort_query['relation'] = 'OR';
	$new_sort_query = array(
		$activePlacesOnly,
		$sort_query
	);

	// if( $page == 1 ) {
	// 	$exclude = array();
	// }

	$found_posts 			= $posts->found_posts;
	$found_on_page 		= $posts->post_count;
	$all_active_count = loadPlaces_all_active_count($activePlacesOnly, $ppp);
	$all_pages 				= $all_active_count['pages'];
	$last_page 				= $all_pages == $page;
	$case = array(0,0,0,0);

	if( $found_on_page == 0 ) {
		if( $page == 1 ) {
			$case[0] = 1;
			$posts = loadPlaces_filtered_posts($ppp, $exclude, $activePlacesOnly);
			$filterNothingFound = true;
		}else {
			$case[0] = 2;
			$posts = loadPlaces_filtered_posts($ppp, $exclude, $new_sort_query);

			if( $posts->post_count == 0) {
				$case[0] = 3;
				$posts = loadPlaces_filtered_posts($ppp, $exclude, $activePlacesOnly);
			}
			if( $posts->post_count > 0 && $posts->post_count < $ppp ) {
				$case[0] = 4;
				$posts_rest 	= loadPlaces_filtered_posts($posts->post_count, $exclude, $new_sort_query);
				$data 				= loadPlaces_posts_data($posts_rest, '', $exclude);
				$exclude  		= loadPlaces_updated_exclude($data, $exclude);

				$posts_more_or = loadPlaces_filtered_posts($ppp-$posts->post_count, $exclude, $activePlacesOnly);
				$posts = loadPlaces_merged_queries_posts($posts_rest, $posts_more_or);
			}
		}

		$data = loadPlaces_posts_data($posts, '', $exclude);
		$last_page = $all_active_count['pages'] == $page;
	}else if( $found_on_page > 0 && $found_on_page < $ppp ) {
		$case[1] = 1;

		$posts_rest = loadPlaces_filtered_posts($found_on_page, $exclude, $sort_query_rest);
		$data 			= loadPlaces_posts_data($posts_rest, '', $exclude);
		if( $found_posts <= $ppp ) {
			
			$posts_more_or = loadPlaces_filtered_posts($ppp-$found_on_page, $exclude, $new_sort_query);
			
			$posts_merged = loadPlaces_merged_queries_posts($posts_rest, $posts_more_or);
	
			if( $posts_merged->post_count > 0 && $posts_merged->post_count < $ppp ) {
				$case[1] = 2;
				$posts_rest 	= $posts_merged;
				$data 				= loadPlaces_posts_data($posts_rest, '', $exclude);
				$exclude  		= loadPlaces_updated_exclude($data, $exclude);
				
				$posts_more_or = loadPlaces_filtered_posts($ppp-$posts_merged->post_count, $exclude, $activePlacesOnly);
				$posts_merged = loadPlaces_merged_queries_posts($posts_rest, $posts_more_or);
	
			}
			$data = loadPlaces_posts_data($posts_merged, '', $exclude);
			
			$filter_found = $posts_merged->found_posts;
			$filter_found_on_page = $posts_merged->post_count;
	
			$last_page = $all_active_count['pages'] == $page;
		}else {
			$last_page = true;
		}
	}else {$case[2] = 1;
		$data = loadPlaces_posts_data($posts, '', $exclude);
	}

	wp_send_json( array('ok', $data['postsData'], $last_page, $filterNothingFound, $data['exclude'], $found_posts, 'end', $page, json_encode($case), $posts->post_count, $all_active_count['post_count']) );
}

function loadPlaces_filtered($posts, $activePlacesOnly, $ppp, $page, $filter_query, $exclude) {
	$last_page = false;
	$filterNothingFound = false;

	$filter_query_rest = array(
		$activePlacesOnly,
		$filter_query
	);

	$found_posts 			= $posts->found_posts;
	$found_on_page 		= $posts->post_count;
	$all_active_count = loadPlaces_all_active_count($activePlacesOnly, $ppp);
	$all_pages 				= $all_active_count['pages'];
	$last_page 				= $all_pages == $page;

	if( $found_on_page == 0 ) {
		$posts = loadPlaces_filtered_posts($ppp, $exclude, $activePlacesOnly);
		$filterNothingFound = true;
		
		$data = loadPlaces_posts_data($posts, '', $exclude);
		$filter_found = $posts->found_posts;
		$filter_found_on_page = $posts->post_count;

		$last_page = $filter_found_on_page == $filter_found;
	}else if( $found_on_page > 0 && $found_on_page < $ppp ) {
		$posts_rest = loadPlaces_filtered_posts($found_on_page, $exclude, $filter_query_rest);
		$data 			= loadPlaces_posts_data($posts_rest, '', $exclude);
		
		if( $found_posts <= $ppp ) {
			$exclude  	= loadPlaces_updated_exclude($data, $exclude);
			$posts_more = loadPlaces_filtered_posts($ppp-$found_on_page, $exclude, $activePlacesOnly);
			
			$posts_merged = loadPlaces_merged_queries_posts($posts_rest, $posts_more);
			$data 				= loadPlaces_posts_data($posts_merged, '', $exclude);
			
			$filter_found = $posts_merged->found_posts;
			$filter_found_on_page = $posts_merged->post_count;

			$last_page = $all_active_count['pages'] == $page;
		}else {
			$last_page = true;
		}
	}else if( $found_posts == $ppp * $page) {
		if( $page == 1 ) {
			$exclude = array();
		}
		$data = loadPlaces_posts_data($posts, '', $exclude);
		$filter_found = $posts->found_posts;
		$filter_found_on_page = $posts->post_count;

		$last_page = $all_active_count['pages'] == $page;
	}else {
		$data = loadPlaces_posts_data($posts, '', $exclude);
	}

	wp_send_json( array('ok', $data['postsData'], $last_page, $filterNothingFound, $data['exclude'], $found_posts, 'end', $page) );
}

function loadPlaces($skip_region = false) {
	$page 			= absint( $_POST['page'] );
	$ppp 				= absint( $_POST['ppp'] );
	$exclude 		= isset($_POST['exclude']) ? $_POST['exclude'] : array();
	$metaQuery 	= array();

	// other types
	$region 			= isset( $_POST['region'] ) && !$skip_region ? absint( $_POST['region'] ) : 0;
	$contest 			= isset( $_POST['contest'] ) ? sanitize_text_field( $_POST['contest'] ) : '';

	$promo 							= isset( $_POST['promo'] ) ? sanitize_text_field( $_POST['promo'] ) : '';
	$sortBy 						= isset( $_POST['sortBy'] ) ? $_POST['sortBy'] : false;
	$filterType 				= isset( $_POST['filterType'] ) ? sanitize_text_field( $_POST['filterType'] ) : '';
	$filter 						= isset( $_POST['filter'] ) ? sanitize_text_field( $_POST['filter'] ) : 'none';
	$filterActive 			= $filter != 'none' && $filterType != '';
	$filterNothingFound = false;


	$activePlacesOnly = array(
		'key'			=> '_place_expiry_date',
		'value'		=> current_time( 'Ymd' ),
		'type'		=> 'CHAR',
		'compare'	=> '>='
	);

	$metaQuery[] = $activePlacesOnly;

	// region single page
	if( $region > 0 ) {
		$metaQuery[] = array(
			'key'		=> '_place_region',
			'value' 	=> $region,
			'type'		=> 'NUMERIC',
			'compare' 	=> '='
		);
	}

	if( $filterActive ) {
		$filter_query = array(
			'key'			=> $filterType == 'county' ? '_place_county' : '_place_region',
			'value' 	=> $filter,
			'type'		=> $filterType == 'county' ? 'CHAR' : 'NUMERIC',
			'compare' => '='	
		);
		$metaQuery[] = $filter_query;
	}

	if( $sortBy ) {
		$sort_query = array();
		foreach($sortBy as $option) {
			$sort_query[] = array(
				'key'		=> '_place_extras',
				'value' 	=> '"'.$option.'"',
				'type'		=> 'CHAR',
				'compare' 	=> 'LIKE'
			);
		}
		$metaQuery[] = $sort_query;
	}
	
	$posts = new WP_Query(array(
		'post_type'				=> 'post',
		'post_status'			=> 'publish',
		'posts_per_page' 	=> $ppp,
		'paged' 					=> $page,
		'meta_query' 			=> $metaQuery,
	));

	// month or year page
	if( $contest != '' ) {
		$posts = loadPlaces_contests_posts($ppp, $page, $contest, $activePlacesOnly);
	}

	// user profile page - recommended posts
	if( $promo != '' ) {
		$posts = loadPlaces_promo_posts($ppp, $page, $promo);
	}
	
	$all_active_count = loadPlaces_all_active_count($activePlacesOnly, $ppp);
	$found_on_page 		= $posts->post_count;
	$found_posts 			= $posts->found_posts;
	$all_pages 				= ceil( $found_posts / $ppp );
	$last_page 				= $all_pages == $page;

	// region single page
	if( $region > 0 && $all_pages == 0) {
		loadPlaces(true); // skip region and load all other places
	}
	if( $skip_region ) {
		$filterNothingFound = true;
		$found_posts = 0;
	}

	if( $sortBy ) {
		loadPlaces_sorted($posts, $activePlacesOnly, $ppp, $page, $sort_query, $exclude);
	}else if( $filterActive && $all_active_count['pages'] >= $page ) {
		loadPlaces_filtered($posts, $activePlacesOnly, $ppp, $page, $filter_query, $exclude);
	}
	
	$data = loadPlaces_posts_data($posts, $contest, $exclude);

	if( empty( $data['postsData'] ) ) {
		if( $filterActive || $sortBy ) {
			$filterNothingFound = true;
			$last_page = true;
		}else {
			wp_send_json( array('nodata') );
		}
	}

	//wp_send_json(array($all_active_count['pages'] , $page) );
	wp_send_json( array('ok', $data['postsData'],  $last_page, $filterNothingFound, $data['exclude'], $found_posts, $page, $all_pages, $ppp, $found_on_page) );
}

function loadPlacess() {
	$page 		= intval($_POST['page']	);
	$ppp 		= intval($_POST['ppp']);
	$filterType = isset($_POST['filterType']) ? sanitize_text_field( $_POST['filterType'] ) : '';
	$filterBy 	= isset($_POST['filter']) ? sanitize_text_field( $_POST['filter'] ) : '';
	$exclude 	= isset($_POST['exclude']) ? $_POST['exclude'] : array();
	$sortBy 	= $_POST['sortBy'];
	$contest 	= isset($_POST['contest']) ? sanitize_text_field( $_POST['contest'] ) : '';
	$promo 		= isset($_POST['promo']) ? sanitize_text_field( $_POST['promo'] ) : '';

	$activePlacesQuery = array(
		'key'		=> '_place_expiry_date',
		'value'		=> current_time( 'Ymd' ),
		'type'		=> 'CHAR',
		'compare'	=> '>='
	);	
	
	$region 	= isset($_POST['region']) ? $_POST['region'] : 0;
	$regionQuery = array(
		'key'		=> '_place_region',
		'value' 	=> $region,
		'type'		=> 'NUMERIC',
		'compare' 	=> '='
	);

	$isSortOn 	= is_array($sortBy) ?  true : false;
	$isFilter 	= $filterType != '' && $filterBy != 'none' ? true : false;

	// used when filtering posts
	$queryByFiter = array(
		'key'		=> $filterType == 'county' ? '_place_county' : '_place_region',
		'value' 	=> $filterBy,
		'type'		=> $filterType == 'county' ? 'CHAR' : 'NUMERIC',
		'compare' 	=> '='	
	);
	
	// First query to match exact criteria
	$metaQuery 	= array();

	if($isFilter) {
		$metaQuery[] = $queryByFiter;
	}

	if($isSortOn) {
		foreach($sortBy as $option) {
			$metaQuery[] = array(
				'key'		=> '_place_extras',
				'value' 	=> '"'.$option.'"',
				'type'		=> 'CHAR',
				'compare' 	=> 'LIKE'
			);
		}
	}

	if( $region > 0 ) {
		$metaQuery[] = $regionQuery;
	}

	$metaQuery[] = $activePlacesQuery;

	$active_posts = new WP_Query( array(
		 'post_type'         => 'post',
		 'posts_per_page'    => 1,
		 'post_status'       => 'publish',
		 'meta_query' 		=> array($activePlacesQuery)
	) );
	$activePlacesCount = $active_posts->found_posts;

	$posts = new WP_Query(array(
		'post_type'			=> 'post',
		'post_status'		=> 'publish',
		'posts_per_page' 	=> $ppp,
		'paged' 			=> $page,
		'meta_query' 		=> $metaQuery,
	));

	//$allPosts 		= wp_count_posts('post')->publish;
	$allPosts 		= $posts->found_posts;
	$foundPosts 	= $posts->found_posts;
	$foundOnPage 	= $posts->post_count;

	if( $contest != '' ) {
		switch($contest) {
			case 'month':
				$day 	= date('d');
				$month 	= placeMonth();
				$year 	= date('Y');
				
				$posts 	= getQualifiedMonthPlaces($year, $month, $day, $ppp, $page);
				break;
			case 'year':
				$year = placeYear(true);
				$posts = getYearContestPlaces($year, $ppp, $page);
				break;
		}
		
		$allPosts = $posts->found_posts;
		$cc = $posts->found_posts;
	}

	if( $promo != '' ) {
		switch($promo) {
			case 'recommended':
				$posts = get_promoted_places_only($promo, 'date', true, $ppp, $page, true);
				break;
		}
		//wp_send_json( $posts );
		$allPosts = $posts->found_posts;
		$cc = $posts->found_posts;
		//wp_send_json( $posts->posts );
	}
	
	// Data to be sent back
	$postsData = array();

	if($posts->have_posts()) {
		while($posts->have_posts()) { $posts->the_post();
			$images 	= get_field('_place_images');
			$excerpt 	= get_field('_place_excerpt');
			$county 	= get_field('_place_county');
			$contact 	= get_field('_place_contact');
			$extras 	= get_field('_place_extras');
			
			$exclude[] 	= get_the_ID();
			$place_extras = array();

			if($extras) {
				foreach($extras as $extra) {
					$place_extras[$extra['value']] = esc_html($extra['label']);
				}
			}

			$hasVotedToday = hasVotedToday(get_the_ID(), $contest);

			$postsData[] = array(
				'id'		=> get_the_ID(),
				'imageSrc' 	=> $images ? esc_url($images[0]['sizes']['place-image']) : '',
				'imageAlt' 	=> $images && $images[0]['alt'] ? esc_attr($images[0]['alt']) : esc_html(get_the_title()),
				'name'		=> esc_html(get_the_title()),
				'excerpt'	=> empty_content($excerpt) ? '' : wp_kses($excerpt, array('br'=>array())),
				'county'	=> $county ? esc_html($county) : '',
				'contact'	=> $contact ? esc_html($contact) : '',
				'link'		=> esc_url(get_permalink()),
				'extras'	=> $place_extras,
				'votedOn'	=> $hasVotedToday
			);
		}
	} wp_reset_postdata();

	$notFoundByFilter 	= $foundOnPage == 0 ? true : false;
	$allPages 			= ceil($allPosts/$ppp);

	
	if( $isFilter && $foundPosts == $ppp ) {
		//wp_send_json( 'check for more' );
	}


	// if no more posts found by filter or exact sorting or from region but there are still more posts found
	if( ($isFilter || $isSortOn || ($region > 0 && $foundOnPage == 0)) && $foundOnPage < $ppp ) {
		//wp_send_json( array('sfddfd') );
		$sortByMetaQuery 	= array();
		$sortQuery 			= array();
		$filterQuery		= array();

		if($isSortOn) {
			$i=1; foreach($sortBy as $option) {
				$sortQuery[] = array(
					'key'		=> '_place_extras',
					'value' 	=> '"'.$option.'"',
					'type'		=> 'CHAR',
					'compare' 	=> 'LIKE'
				);
			$i++; }
			$sortByMetaQuery = $sortQuery;
			$sortByMetaQuery['relation'] = 'OR';

			// when sorting with filter on
			if($isFilter) {
				$filterQuery = $queryByFiter;
			}
		}

		$mQuery = array(
			$filterQuery,
			$sortByMetaQuery,
			$activePlacesQuery
		);
		
		$page = 1;
		$posts = new WP_Query(array(
			'post_type'			=> 'post',
			'post_status'		=> 'publish',
			'posts_per_page' 	=> $ppp-$foundOnPage,
			'paged' 			=> $page,
			'post__not_in' 		=> $exclude,
			'meta_query' 		=> $mQuery
		));

		$allPages 	= $posts->max_num_pages;

		$sortOtherFound = absint( $posts->found_posts );
		
		if($posts->have_posts()) {
			$i=1; while($posts->have_posts()) { $posts->the_post();
				$images 	= get_field('_place_images');
				$excerpt 	= get_field('_place_excerpt');
				$county 	= get_field('_place_county');
				$contact 	= get_field('_place_contact');
				$extras 	= get_field('_place_extras');
				
				$exclude[] 	= get_the_ID();
				$place_extras = array();

				if($extras) {
					foreach($extras as $extra) {
						$place_extras[$extra['value']] = esc_html($extra['label']);
					}
				}

				$postsData[] = array(
					'id'		=> get_the_ID(),
					'imageSrc' 	=> $images ? esc_url($images[0]['sizes']['place-image']) : '',
					'imageAlt' 	=> $images && $images[0]['alt'] ? esc_attr($images[0]['alt']) : esc_html(get_the_title()),
					'name'		=> esc_html(get_the_title()),
					'excerpt'	=> empty_content($excerpt) ? '' : wp_kses($excerpt, array('br'=>array())),
					'county'	=> $county ? esc_html($county) : '',
					'contact'	=> $contact ? esc_html($contact) : '',
					'link'		=> esc_url(get_permalink()),
					'extras'	=> $place_extras
				);
			$i++; }
		} wp_reset_postdata();

		//when sorting and no more posts found with sorting options but still some other posts found
		if($isSortOn && $sortOtherFound < $ppp-$foundOnPage ) {
			wp_send_json( array($sortOtherFound, $ppp-$foundOnPage, $ppp-$sortOtherFound) );
			$fQuery = array();
			if($isFilter) {
				$fQuery[] = $queryByFiter;
			}
			$fQuery[] = $activePlacesQuery;
			
			$page = 1;
			$more_posts = new WP_Query(array(
				'post_type'			=> 'post',
				'post_status'		=> 'publish',
				'posts_per_page' 	=> $ppp-$foundOnPage,
				'paged' 			=> $page,
				'post__not_in' 		=> $exclude,
				'meta_query' 		=> $fQuery,
			));
			
			$allPages 	= $more_posts->max_num_pages;

			if($more_posts->have_posts()) {
				$i=1; while($more_posts->have_posts()) { $more_posts->the_post();
					$images 	= get_field('_place_images');
					$excerpt 	= get_field('_place_excerpt');
					$county 	= get_field('_place_county');
					$contact 	= get_field('_place_contact');
					$extras 	= get_field('_place_extras');
					
					$exclude[] 	= get_the_ID();
					$place_extras = array();

					if($extras) {
						foreach($extras as $extra) {
							$place_extras[$extra['value']] = esc_html($extra['label']);
						}
					}

					$postsData[] = array(
						'id'		=> get_the_ID(),
						'imageSrc' 	=> $images ? esc_url($images[0]['sizes']['place-image']) : '',
						'imageAlt' 	=> $images && $images[0]['alt'] ? esc_attr($images[0]['alt']) : esc_html(get_the_title()),
						'name'		=> esc_html(get_the_title()),
						'excerpt'	=> empty_content($excerpt) ? '' : wp_kses($excerpt, array('br'=>array())),
						'county'	=> $county ? esc_html($county) : '',
						'contact'	=> $contact ? esc_html($contact) : '',
						'link'		=> esc_url(get_permalink()),
						'extras'	=> $place_extras
					);
				$i++; }
			} wp_reset_postdata();
		}
	}

	$exclude 	= $isFilter || $isSortOn || $region>0 ? $exclude : array();

	if(empty($postsData)) {
		wp_send_json( array('nodata') );
	};

	$isLastPage = $page == $allPages ? true : false;

	if( ($isFilter || $isSortOn) && $allPages * $ppp == absint($foundPosts) ) {
		$isLastPage = false;
	}

	if( $promo != '' ) {
		$isLastPage = true;
	}

	wp_send_json( array('ok', $postsData, $isLastPage, $notFoundByFilter, $exclude, $foundPosts, $page, $allPages, $ppp, 'sss', $sortOtherFound, $foundOnPage, $more_posts->found_posts) );
}
add_action('wp_ajax_loadPlaces', 'loadPlaces');
add_action('wp_ajax_nopriv_loadPlaces', 'loadPlaces');

function loadWinners() {
	$nonce 	= $_POST['nonce'];
	$page 	= absint( $_POST['page'] );
	$ppp 	= absint( $_POST['ppp'] );
	$type 	= sanitize_text_field( $_POST['type'] );
	$postsData = array();

	if( !wp_verify_nonce( $nonce, 'get_winners_nonce' ) ) {
		wp_send_json( array('nonce') );
	}

	$winnersData = winners_list($type, $ppp, $page);

	if( !$winnersData ) {
		wp_send_json( array('noData') );
	}
	
	if( !$winnersData['winners']) {
		wp_send_json( array('noData') );
	}

	foreach($winnersData['winners'] as $winner) {
		$year 	= absint( $winner->year );
		$month 	= absint( $winner->month );
		$postID = absint( $winner->winner );
		$county = get_field('_place_county', $postID);
		$image 	= get_field('_place_thumbnail', $postID);

		$postsData[] = array(
			'id' 		=> $postID,
			'link'		=> esc_url( get_permalink( $postID ) ),
			'name'		=> esc_html( get_the_title( $postID ) ),
			'imageSrc' 	=> $image ? esc_url($image['sizes']['region-image']) : '',
			'imageAlt' 	=> $image && $image['alt'] ? esc_attr($image['alt']) : esc_html( get_the_title( $postID ) ),
			'county'	=> esc_html( $county ),
			'year'		=> $year,
			'month'		=> monthName($month)
		);
	}

	$isLastPage = $winnersData['lastPage'];

	wp_send_json( array('ok', $postsData, $isLastPage) );
}
add_action('wp_ajax_loadWinners', 'loadWinners');
add_action('wp_ajax_nopriv_loadWinners', 'loadWinners');

function load_regions() {
	$nonce 	= $_POST['nonce'];
	$page 	= absint( $_POST['page'] );
	$ppp  	= 4;
	$data 	= array();

	if(!wp_verify_nonce( $nonce, 'regions_nonce' )) {
		wp_send_json( array('nonce') );
	}

	$regions = new WP_Query( array(
		'post_type'			=> 'regiony',
		'posts_per_page' 	=>  $ppp,
		'post_status'		=> 'publish',
		'paged'				=> $page
	) );

	if($regions->have_posts()) {
		while($regions->have_posts()) {
			$regions->the_post();

			$thumbnail = get_field('_region_thumbnail');
			$images    = get_field('_region_images');
			$image 	   = !$thumbnail && $images[0]['width'] > 1109 && $images[0]['height'] > 299 ? $images[0] : $thumbnail;
			if($image) {
				$data[] = array(
					'imageSrc' 	=> esc_url($image['sizes']['region-image']),
					'imageAlt' 	=> $image['alt'] ? esc_attr($image['alt']) : esc_html(get_the_title()),
					'name'		=> esc_html(get_the_title()),
					'link'		=> esc_url(get_permalink())
				);
			}
		}
	} wp_reset_postdata();
	
	$allPages 	= $regions->max_num_pages;
	$isLastPage = $allPages <= $page ? true : false;
	$nextpage 	= $page + 1;

	wp_send_json( array('ok', $data, $nextpage, $isLastPage, $allPages) );
}
add_action('wp_ajax_load_regions', 'load_regions');
add_action('wp_ajax_nopriv_load_regions', 'load_regions');

function voteOnPlace() {
	global $wpdb;
	$nonce 		= $_POST['nonce'];
	$placeID 	= absint( $_POST['id'] );
	$type 		= sanitize_text_field( $_POST['type'] );

	if(!wp_verify_nonce( $nonce, 'get_vote_nonce' )) {
		wp_send_json( array('error', 'nonce') );
	}

	$types = array('month', 'year');

	if( !in_array($type, $types) ) {
		wp_send_json( array('error', 'error') );
	}

	setVotingCookie($placeID, $type);

	$votes = get_post_meta( $placeID, 'votes-'.$type, true );
	$votes = $votes ? absint($votes) : 0;

	update_post_meta( $placeID, 'votes-'.$type, $votes+1 );

	wp_send_json( 'ok' );
}
add_action('wp_ajax_voteOnPlace', 'voteOnPlace');
add_action('wp_ajax_nopriv_voteOnPlace', 'voteOnPlace');

function process_login() {
	global $wpdb;
	$nonce 	= $_POST['nonce'];
	$type 	= sanitize_text_field( $_POST['type'] );

	if(!wp_verify_nonce( $nonce, 'get_login_nonce' )) {
		wp_send_json( array('error', 'nonce') );
	}

	switch($type) {
		case 'login':

			$login 		= sanitize_text_field( $_POST['formData']['loginName'] );
			$pass 		= $_POST['formData']['loginPass'];
			$referrer 	= esc_url( $_POST['formData']['loginReferrer'] );

			if(!username_exists($login)) {
				wp_send_json( array('error', 'wrong') );
			}

			$user = get_user_by('login', $login);

			if( !wp_check_password($pass, $user->data->user_pass, $user->ID )) {
				wp_send_json( array('error', 'wrong') );
			}
			
			$credentials 					= array();
			$credentials['user_login'] 		= $login;
			$credentials['user_password'] 	= $pass;
			$signon 						= wp_signon($credentials, false);

			if(is_wp_error($signon)) {
				wp_send_json( array('error', 'error') );
			}

			$slug 		= basename($referrer);
			$placeID 	= get_page_by_path( $slug, OBJECT, 'post' );

			$refLink 	= $placeID > 0 ? esc_url( get_permalink( $placeID ) ) : rfs_redirect_to('profile');
				
			$link = rfs_role('subscriber', $login) ? $refLink : rfs_redirect_to('admin');
			wp_send_json( array( 'ok', $refLink ) );

			break;
		case 'register':

			//wp_send_json( array('error', 'notactive') );

			$login 	= sanitize_text_field( $_POST['formData']['registerName'] );
			$email 	= sanitize_text_field( $_POST['formData']['registerEmail'] );

			if( !is_email( $email ) ) {
				wp_send_json( array('error', 'email') );
			}

			if( username_exists( $login ) ) {
				wp_send_json( array('error', 'userexists') );
			}

			if( email_exists( $email ) ) {
				wp_send_json( array('error', 'emailexists') );
			}

			$wpdb->query('START TRANSACTION');

			$password = wp_generate_password(16);
			$new_user = wp_insert_user(array(
				'user_login' 	=> $login,
				'user_email' 	=> $email,
				'user_pass'	 	=> $password,
				'role'			=> 'subscriber'
			));

			if(is_wp_error($new_user)) {
				$wpdb->query('ROLLBACK');
				wp_send_json( array('error', 'error') );
			}

			$wpdb->query('COMMIT');

			$message = rfs_get_email_template('registration', array(
				'login' 	=> $login,
				'password' 	=> $password
			));
			$subject 	= esc_html(get_bloginfo('name')).' - Konto użytkownika';
			$send 		= wp_mail($email, $subject, $message);

			wp_send_json( array('ok') );

			break;
		case 'remind':

			//wp_send_json( array('error', 'notactive') );

			$email 	= sanitize_text_field( $_POST['formData']['remindEmail'] );

			if( !is_email( $email ) ) {
				wp_send_json( array('error', 'email') );
			}

			$user_id = email_exists( $email );

			if( !$user_id ) {
				wp_send_json( array('error', 'notexists') );
			}

			$password 		= wp_generate_password(16);
			$reset_password = wp_update_user( array( 
				'ID' => $user_id, 
				'user_pass' => $password 
			) );

			if ( is_wp_error( $reset_password ) ) {
				wp_send_json( array('error', 'error') );
			} 

			$userdata = get_userdata( $user_id );

			$message = rfs_get_email_template('password', array(
				'login' 	=> $userdata->user_login,
				'password' 	=> $password
			));
			$subject 	= esc_html(get_bloginfo('name')).' - Przypomnienie hasła';
			$send 		= wp_mail($email, $subject, $message);

			wp_send_json( array('ok') );
			break;
		default:
			wp_send_json( array('error', 'error') );
			break;
	}
}
add_action('wp_ajax_process_login', 'process_login');
add_action('wp_ajax_nopriv_process_login', 'process_login');

function make_payment() {
	global $wpdb;
	$nonce 			= $_POST['nonce'];
	$method 		= sanitize_text_field( $_POST['method'] );
	$subscription 	= absint( $_POST['subscription'] );
	$promos 		= $_POST['promos'];

	if(!wp_verify_nonce( $nonce, 'get_payment_nonce' )) {
		wp_send_json( array('error', 'nonce') );
	}

	if( !is_user_logged_in() ) {
		wp_send_json( array( 'error' ) );
	}

	$user_id 	= get_current_user_id();
	$userdata 	= get_userdata( $user_id );

	$email 		= $userdata->user_email;
	$firstName 	= $userdata->first_name;
	$lastName 	= $userdata->last_name;

	if( empty($firstName) || empty($lastName) || !is_email( $email ) ) {
		wp_send_json( array( 'error' ) );
	}

	if( $subscription < 1 && empty($promos) ) {
		wp_send_json( array( 'error', 'empty' ) );
	}

	$products 	= array();
	$prices 	= array();
	$place_id 	= get_owner_place( $user_id );
	$order_id 	= md_order_number();
	$payuOrder 	= array();

	require_once get_template_directory().'/vendor/autoload.php';

	if( $subscription > 0 ) {
		$product 	= place_product('subscription', $subscription);
		$products[] = $product;

		if( $method == 'payu' ) {
			$prices[]	= $product['total'];
			$payuOrder['products'][0]['name'] 		= $product['name'];
			$payuOrder['products'][0]['unitPrice'] 	= $product['total'] * 100;
			$payuOrder['products'][0]['quantity'] 	= 1;
			$payuOrder['products'][0]['virtual'] 	= true;
		}
	}

	if( !empty($promos) ) {
		$i = $subscription > 0 ? 1 : 0; foreach($promos as $promo) {
			$product 	= place_product('promo', $promo);
			$products[] = $product;

			if( $method == 'payu' ) {
				$prices[]	= $product['total'];
				$payuOrder['products'][$i]['name'] 		= $product['name'];
				$payuOrder['products'][$i]['unitPrice'] = $product['total'] * 100;
				$payuOrder['products'][$i]['quantity'] 	= 1;
				$payuOrder['products'][$i]['virtual'] 	= true;
			}
		$i++; }
	}

	switch( $method ) {
		case 'payu':
			require_once get_template_directory().'/payment/payu-config.php';

			$payuOrder['notifyUrl'] 	= esc_url( get_bloginfo('url').'/payu-notifications' );
			$payuOrder['continueUrl'] 	= esc_url( get_bloginfo('url').'/payment-confirmation?payment=payu' );
			$payuOrder['customerIp'] 	= '127.0.0.1';
			$payuOrder['merchantPosId'] = OpenPayU_Configuration::getMerchantPosId();
			$payuOrder['currencyCode'] 	= 'PLN';
			$payuOrder['description'] 	= esc_html( get_the_title( $place_id ) );

			$payuOrder['totalAmount'] 			= array_sum($prices) * 100;
			$payuOrder['extOrderId'] 			= $order_id;
			$payuOrder['buyer']['email'] 		= $email;
			$payuOrder['buyer']['firstName'] 	= $firstName;
			$payuOrder['buyer']['lastName'] 	= $lastName;

			$createOrder 	= OpenPayU_Order::create($payuOrder);
			$response 		= $createOrder->getResponse();

			if( $response->status->statusCode == 'SUCCESS' ) {
				$paymentId = $response->orderId;

				$registerOrder = register_order($user_id, $place_id, $order_id, $products, $method, $paymentId);

				if( !$registerOrder ) {
					wp_send_json( array( 'error' ) );
				}

				$paymentUrl = $response->redirectUri;
				
			}

			break;
		case 'paypal':

			$payerData = array(
				'email'			=> $email,
				'first_name'	=> $firstName,
				'last_name'		=> $lastName,
			);

			$pay =  payWithPayPal($products, $payerData, 'PLN', esc_html( get_the_title( $place_id ) ), $order_id);

			if( $pay ) {
				$paymentId = $pay['id'];

				$registerOrder = register_order($user_id, $place_id, $order_id, $products, $method, $paymentId);

				if( !$registerOrder ) {
					wp_send_json( array( 'error' ) );
				}
				
				$paymentUrl = $pay['url'];

			}

			break;
		default:
			wp_send_json( array( 'error' ) );
			break;
	}

	$message = rfs_get_email_template('order-placed', array(
		'name'				=> sanitize_text_field( $firstName.' '.$lastName ),
		'order_id'		=> sanitize_text_field( $order_id ),
		'payment_id'	=> sanitize_text_field( $paymentId ),
		'products'		=> $products,
		'pay_url'			=> esc_url( $paymentUrl ),
		'method'			=> sanitize_text_field( $method )
	));
	$subject 	= esc_html(get_bloginfo('name')).' - Zamówienie zostało przyjęte';
	$send 		= wp_mail($email, $subject, $message);

	wp_send_json( array( 'ok', $paymentUrl ) );
}
add_action('wp_ajax_make_payment', 'make_payment');
add_action('wp_ajax_nopriv_make_payment', 'make_payment');

function user_actions() {
	$nonce = $_POST['nonce'];

	if(!wp_verify_nonce( $nonce, 'get_user_action_nonce' )) {
		wp_send_json( array('error', 'nonce') );
	}

	if( !is_user_logged_in() ) {
		wp_send_json( array( 'error' ) );
	}

	$type 		= sanitize_text_field( $_POST['type'] );
	$place_id = absint( $_POST['place_id'] );
	$do 			= sanitize_text_field( $_POST['do'] );

	$type 		= $type == 'visit' ? 'to-visit' : $type;

	if( $do == 'add' ) {
		$action = add_user_place($type, $place_id);
		if( $type == 'seen' ) {
			remove_user_place('to-visit', $place_id);
		}
	}else {
		$action = remove_user_place($type, $place_id);
	}

	if( !$action ) {
		wp_send_json( array( 'error' ) );
	}

	wp_send_json( array('ok') );
}
add_action('wp_ajax_user_actions', 'user_actions');
add_action('wp_ajax_nopriv_user_actions', 'user_actions');
?>