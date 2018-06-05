<?php  

function loadPlaces() {
	$page 		= intval($_POST['page']	);
	$ppp 		= intval($_POST['ppp']);
	$filterType = isset($_POST['filterType']) ? sanitize_text_field( $_POST['filterType'] ) : '';
	$filterBy 	= isset($_POST['filter']) ? sanitize_text_field( $_POST['filter'] ) : '';
	$exclude 	= isset($_POST['exclude']) ? $_POST['exclude'] : array();

	$sortBy 	= $_POST['sortBy'];
	$isSortOn 	= is_array($sortBy) ?  true : false;

	$isFilter 	= $filterType != '' && $filterBy != 'none' ? true : false;

	$metaQuery 	= array();

	if($isFilter) {
		$metaQuery = array(
			array(
				'key'		=> $filterType == 'county' ? '_place_county' : '_place_region',
				'value' 	=> $filterBy,
				'type'		=> $filterType == 'county' ? 'CHAR' : 'NUMERIC',
				'compare' 	=> '='	
			)
		);
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
	//wp_send_json( array($exclude) );

	$posts = new WP_Query(array(
		'post_type'			=> 'post',
		'post_status'		=> 'publish',
		'posts_per_page' 	=> $ppp,
		'paged' 			=> $page,
		//'post__not_in' 		=> $exclude,
		'meta_query' 		=> $metaQuery,
	));
	$allPosts 		= wp_count_posts('post')->publish;
	$foundPosts 	= $posts->found_posts;
	$foundOnPage 	= $posts->post_count;
	
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

			$postsData[] = array(
				'imageSrc' 	=> $images ? esc_url($images[0]['sizes']['place-image']) : '',
				'imageAlt' 	=> $images && $images[0]['alt'] ? esc_attr($images[0]['alt']) : esc_html(get_the_title()),
				'name'		=> esc_html(get_the_title()),
				'excerpt'	=> empty_content($excerpt) ? '' : wp_kses($excerpt, array('br'=>array())),
				'county'	=> $county ? esc_html($county) : '',
				'contact'	=> $contact ? esc_html($contact) : '',
				'link'		=> esc_url(get_permalink()),
				'extras'	=> $place_extras
			);
		}
	} wp_reset_postdata();

	$notFoundByFilter 	= $foundOnPage == 0 ? true : false;
	$allPages 			= ceil($allPosts/$ppp);

	// if no more filtered but still more posts
	if(($isFilter || $isSortOn) && $foundOnPage < $ppp) {
		
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

			if($isFilter) {
				$filterQuery = array(
					'key'		=> $filterType == 'county' ? '_place_county' : '_place_region',
					'value' 	=> $filterBy,
					'type'		=> $filterType == 'county' ? 'CHAR' : 'NUMERIC',
					'compare' 	=> '='	
				);
			}
		}
		$mQuery = array(
			$filterQuery,
			$sortByMetaQuery
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

		$sortOtherFound = $posts->found_posts;
		//wp_send_json( $sortOtherFound );
		//wp_send_json( array($ppp-$foundOnPage, $sortOtherFound, $allPages, $mQuery) );
		
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

		if($isSortOn && $sortOtherFound < $ppp-$foundOnPage) {
			$fQuery = array();
			if($isFilter) {
				//wp_send_json( 'isfilter' );
				$fQuery = array(array(
					'key'		=> $filterType == 'county' ? '_place_county' : '_place_region',
					'value' 	=> $filterBy,
					'type'		=> $filterType == 'county' ? 'CHAR' : 'NUMERIC',
					'compare' 	=> '='	
				));
			}
			
			$page = 1;
			$more_posts = new WP_Query(array(
				'post_type'			=> 'post',
				'post_status'		=> 'publish',
				'posts_per_page' 	=> $ppp-$foundOnPage,
				'paged' 			=> $page,
				'post__not_in' 		=> $exclude,
				'meta_query' 		=> $fQuery,
			));

			//wp_send_json( array($ppp-$foundOnPage, $more_posts->found_posts, $fQuery) );
			
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

	$exclude 	= $isFilter || $isSortOn ? $exclude : array();

	if(empty($postsData)) {
		wp_send_json( array('nodata') );
	};

	$isLastPage = $page == $allPages ? true : false;

	wp_send_json( array('ok', $postsData, $isLastPage, $notFoundByFilter, $exclude, $foundPosts, $page, $allPages) );
}
add_action('wp_ajax_loadPlaces', 'loadPlaces');
add_action('wp_ajax_nopriv_loadPlaces', 'loadPlaces');
?>