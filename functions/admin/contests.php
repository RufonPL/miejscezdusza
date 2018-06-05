<?php  

if( ! class_exists('placesContests') ) {

	class placesContests {

		public $table_object;

		public function __construct() {
			add_action( 'admin_menu', array($this, 'create_menu_pages'), 12 );
			add_action( 'admin_enqueue_scripts',	array($this, 'admin_load_assets'), 0);
		}

		public function create_menu_pages() {
			global $month_page;

			$month_page = add_menu_page('Miejsce miesiąca', 'Miejsce miesiąca', 'edit_posts', 'md_month_contest', array($this, 'md_month_contest'), 'dashicons-star-filled', 8);
			$year_page = add_menu_page('Miejsce roku', 'Miejsce roku', 'edit_posts', 'md_year_contest', array($this, 'md_year_contest'), 'dashicons-star-filled', 8);
		}

		public function admin_load_assets($hook) {
			global $month_page;
			global $year_page;

			if( $month_page == $hook || $hook == $year_page ) {
				//wp_enqueue_script('ekomex_libs_js',  plugin_dir_url( __FILE__ ).'assets/js/ekomex-libs.min.js');
				wp_enqueue_style('md_contest_css', get_template_directory_uri().'/functions/admin/assets/style.css');
			}
		}

		public function md_month_contest() {
			include(__DIR__.'/month.php');
		}

		public function md_year_contest() {
			include(__DIR__.'/year.php');
		}

	}

	new placesContests;
}

?>