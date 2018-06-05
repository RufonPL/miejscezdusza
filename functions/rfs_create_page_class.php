<?php  

class RFS_Create_Page {

	private $page_id;
	private $page_slug;
	private $page_title;
	private $page_content;

	public function __construct($args) {
		if(sanitize_text_field( $args['slug'] == '' )) {
			return;
		}
		$this->page_id 		= absint($args['id']) > 900 ? absint($args['id']) : 999 ;
		$this->page_slug 	= $args['slug'];
		$this->page_title 	= $args['title'];
		$this->page_content = $args['content'];
		add_filter( 'the_posts', array($this, 'rfs_page'), -10 );
	}

	public function rfs_page($posts) {
		global $wp;
		global $wp_query;
		
		global $rfs_page_detect; // used to stop double loading
			$rfs_page_url = $this->page_slug; // URL of the fake page
		
		if ( !$rfs_page_detect && (strtolower($wp->request) == $rfs_page_url /*|| $wp->query_vars['page_id'] == $rfs_page_url*/) ) {
			// stop interferring with other $posts arrays on this page (only works if the sidebar is rendered *after* the main page)
			$rfs_page_detect = true;
			
			// create a fake virtual page
			$post = new stdClass;
			$post->post_author = 1;
			$post->post_name = $rfs_page_url;
			$post->guid = get_bloginfo('wpurl') . '/' . $rfs_page_url;
			$post->post_title = $this->page_title;
			$post->post_content = wp_kses_post($this->page_content);
			$post->ID = -$this->page_id;
			$post->post_type = 'page';
			$post->post_status = 'static';
			$post->comment_status = 'closed';
			$post->ping_status = 'open';
			$post->comment_count = 0;
			$post->post_date = current_time('mysql');
			$post->post_date_gmt = current_time('mysql', 1);
			$posts=NULL;
			$posts[]=$post;
			
			// make wpQuery believe this is a real page too
			$wp_query->is_page = true;
			$wp_query->is_singular = true;
			$wp_query->is_home = false;
			$wp_query->is_archive = false;
			$wp_query->is_category = false;
			unset($wp_query->query["error"]);
			$wp_query->query_vars["error"]="";
			$wp_query->is_404=false;
		}
		
		return $posts;
	}

}

?>