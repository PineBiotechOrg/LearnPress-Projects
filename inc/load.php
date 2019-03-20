<?php

/**
 * @link       https://github.com/JoshuaMcKendall/LearnPress-Projects-Plugin
 * @since      1.0.0
 *
 * @package    lp-projects
 * @subpackage LearnPress-Projects/inc
 */

/**
 * The core LearnPress projects class.
 *
 * @since      1.0.0
 * @package    lp-projects
 * @subpackage LearnPress-Projects/inc
 * @author     Joshua McKendall <mail@joshuamckendall.com>
 */

class LP_Addon_Projects extends LP_Addon {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The slug of the associated tab
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $_tab_slug    The slug for the tab.
	 */
	protected $_tab_slug = '';

	/**
	 * @var bool
	 */
	public static $in_loop = false;


	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $version    The current version of the plugin.
	 */
	public $version = LP_ADDON_PROJECTS_VER;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $version    The current version of the plugin.
	 */
	public $require_version = LP_ADDON_PROJECTS_REQUIRE_VER;


	public function __construct() {

		parent::__construct();

		$this->plugin_name = __('LearnPress Projects', 'lp-projects');

		add_filter( 'learn-press/profile-tabs', array( $this, 'projects_tab' ), 100, 1 );
		$this->_tab_slug = sanitize_title( __( 'projects', 'lp-projects' ) );
		
	}

	/**
	 *
	 * Define constants
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function _define_constants() {
		define( 'LP_ADDON_PROJECTS_PATH', dirname( LP_ADDON_PROJECTS_FILE ) );
		define( 'LP_ADDON_PROJECTS_INC', LP_ADDON_PROJECTS_PATH . '/inc/' );
		define( 'LP_ADDON_PROJECTS_TEMPLATE', LP_ADDON_PROJECTS_PATH . '/templates/' );
		define( 'LP_ADDON_PROJECTS_ASSETS', LP_ADDON_PROJECTS_PATH . '/assets/' );
	}

	/**
	 *
	 * Include the neccessary file(s) that LearnPress Projects relies on. 
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function _includes() {

		include_once LP_ADDON_PROJECTS_INC . 'functions.php';
		include_once LP_ADDON_PROJECTS_INC . 'template-functions.php';

	}

	/**
	 *
	 * Initalize the admin and public facing hooks
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function _init_hooks() {

		remove_action( 'thim_courses_loop_item_thumb', 'thim_courses_loop_item_thumbnail' );
		remove_action( 'learn-press/checkout-order-review', 'learn_press_order_comment', 5 );
		remove_action( 'thim_wrapper_loop_end', 'thim_wrapper_loop_end' );

		add_action( 'wp_loaded', 									array( $this, 'bp_add_new_item' ) );
		add_action( 'pre_get_posts',								array( $this, 'filter_out_projects_from_courses' ), 10 );
		//add_action( 'learn-press/before-purchase-button', 			array( $this, 'add_inline_checkout_fields' ) );
		//add_action( 'learn_press_projects_checkout_form',			array( $this, 'render_inline_checkout_form' ) );
		add_action( 'thim_wrapper_loop_end',						array( $this, 'render_sidebars' ) );
		add_action( 'template_include', 							array( $this, 'show_projects' ), 10 );

		//add_filter( 'get_categories_taxonomy',						array( $this, 'exclude_projects_category' ), 10, 2 );
		//add_filter( 'learn-press/frontend-default-scripts',			array( $this, 'register_lp_projects_checkout' ) );
		add_filter( 'learn_press_profile_tab_endpoints', 			array( $this, 'profile_tab_endpoints' ) );
		add_filter( 'learn-press/course-settings-fields/archive',	array( $this, 'projects_page_id_settings' ) );
		add_filter( 'single_term_title',							array( $this, 'filter_project_page_title' ) );
		add_filter( 'single_course_category_title',					array( $this, 'filter_project_page_title' ) );
		add_filter( 'thim_courses_loop_item_thumb',					array( $this, 'project_loop_item_thumbnail' ) );
		add_filter( 'learn_press_get_breadcrumb',					array( $this, 'change_project_breadcrumb' ) );
		add_filter( 'learn-press/purchase-course-button-text',		array( $this, 'purchase_project_button_text' ) );
		add_filter( 'learn-press/course/result-heading',			array( $this, 'project_progress_text' ) );
		add_filter( 'learn-press/enroll-course-button-text',		array( $this, 'free_project_button_text' ) );
		add_filter( 'learn_press_locate_template',					array( $this, 'project_features' ), 10, 3 );
		// add_filter( 'learn_press_locate_template',					array( $this, 'order_review' ), 10, 3 );
		//add_filter( 'learn_press_locate_template',					array( $this, 'single_project' ), 10, 3 );
		add_filter( 'thim_core_list_sidebar',						array( $this, 'register_projects_sidebar' ) );
		add_filter( 'learn_press_get_return_url',					array( $this, 'lp_project_order_received' ), 10, 2 );
		add_filter( 'thim_default_login_redirect', 					array( $this, 'redirect_back_to_project' ), 10 );
		add_filter( 'learn-press/query/user-purchased-courses', 	array( $this, 'filter_out_projects_from_bp_profile_courses' ), 10, 3 );
		// add_filter( 'learn_press_get_template_part',						array( $this, 'project_features' ), 10, 3 );

		// LP_Request_Handler::register_ajax( 'inline_checkout', array( $this, 'inline_checkout' ) );

		$this->rewrite_endpoint();

	}


	/**
	 *
	 * Initalize the projects scripts
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function _enqueue_assets() {

		// wp_register_script( 'lp_projects', untrailingslashit( plugins_url( '/', LP_ADDON_PROJECTS_FILE ) ) . '/assets/js/lp-projects.js', array( 'jquery' ), $this->version, true );

		// wp_localize_script( 'lp_projects', 'lp_projects_params', array(

		//  	'current_user'	=> wp_get_current_user()

 	// 	) );

 	// 	wp_enqueue_script( 'lp_projects' );

		if( ! is_admin() ) {
			
			wp_enqueue_style( 'lp-projects-style', untrailingslashit( plugins_url( '/', LP_ADDON_PROJECTS_FILE ) ) . '/assets/css/lp-projects.css' );

		}

	}

	public function register_lp_projects_checkout( $scripts ) {

		if( learn_press_is_course() && ! learn_press_is_learning_course() ) {

			$scripts[ 'checkout' ][ 'url' ] = untrailingslashit( plugins_url( '/', LP_ADDON_PROJECTS_FILE ) ) . '/assets/js/lp-projects-checkout.js';

		}

		return $scripts;

	}

	public function exclude_projects_category( $taxonomy, $args ) {

		if( $taxonomy == 'course_category' ) {
			$args['exclude'] = array('project');
		}	

	}

	/**
	 * Add is_learnpress condition.
	 *
	 * @param $is
	 *
	 * @return bool
	 */
	public function is_learnpress( $is ) {
		return $is || is_post_type_archive( 'lp_course' ) || is_singular( array( 'lp_course' ) );
	}

	/**
	 * Add new item.
	 */
	public function bp_add_new_item() {

		if( ! function_exists('bp_is_active') ) {
			return;
		}

		global $bp;

		// array(
		// 			'name'                    => __( 'Orders', 'learnpress-buddypress' ),
		// 			'slug'                    => $this->get_tab_orders_slug(),
		// 			'show_for_displayed_user' => false,
		// 			'screen_function'         => array( $this, 'bp_tab_content' ),
		// 			'default_subnav_slug'     => 'all',
		// 			'position'                => 100
		// 		),
		$tabs = apply_filters( 'learn-press/buddypress/project-tab', array(
				array(
					'name'                    => __( 'Projects', 'learnpress-buddypress' ),
					'slug'                    => $this->get_tab_projects_slug(),
					'show_for_displayed_user' => true,
					'screen_function'         => array( $this, 'bp_tab_content' ),
					'default_subnav_slug'     => 'all',
					'position'                => 21
				)
			)
		);
		// create new nav item
		foreach ( $tabs as $tab ) {
			bp_core_new_nav_item( $tab );
		}
	}

	 /*
	 * Get tab content.
	 */
	public function bp_tab_content() {
		global $bp;
		$current_component = $bp->current_component;
		$type = 'projects';
		$slugs = LP()->settings->get( 'profile_endpoints' );
		$tab_slugs = array_keys( $slugs, $current_component );
		$tab_slug = array_shift( $tab_slugs );
		


		if ( $current_component == $type ) {
			add_action( 'bp_template_content', array( $this, "bp_tab_projects_content" ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		do_action( 'learn-press/buddypress/bp-tab-content', $current_component );
	}

	/**
	 * Tab courses content.
	 */
	public function bp_tab_projects_content() {
		// $viewing_user = learn_press_get_current_user();

		echo learn_press_projects_template_content( 'user-projects.php', array(
				'query' => $this->get_users_purchased_projects( bp_displayed_user_id() )
			) );
		
		// learn_press_projects_template(
		// 	'user-projects.php',
		// 	array(
		// 		'query' => $this->get_users_purchased_projects( $viewing_user->get_id() )
		// 	)
		// );
	}

	/**
	 * Get profile tab courses slug.
	 *
	 * @return mixed
	 */
	public function get_tab_projects_slug() {
		$slugs = LP()->settings->get( 'profile_endpoints' );
		$slug  = '';
		if ( isset( $slugs['profile-projects'] ) ) {
			$slug = $slugs['profile-projects'];
		}
		if ( ! $slug ) {
			$slug = 'projects';
		}

		return apply_filters( 'learn_press_bp_tab_projects_slug', $slug );
	}

	/**
	 * Filter out courses from the main courses archive that are in the Project category.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object    $query    The WP_Query object.
	 */
	public function filter_out_projects_from_courses( $query ) {

		if( $query->is_main_query() && is_post_type_archive( 'lp_course' ) && ! is_admin() ) {

			//Get original tax query
			//$tax_query = $query->get('tax_query');

			//Add our taxonomy query to filter out courses in the projects category
			$tax_query = array(

				array(

						'taxonomy' 	=> 'course_category',
						'field'		=> 'slug',
						'terms'		=> 'project',
						'operator'	=> 'NOT IN'

					)

			);

			$query->set('tax_query', $tax_query);

		}

	}

	/**
	 * Adds two hidden inputs to the Buy this course button form.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_inline_checkout_fields() {

		echo  '<input type="hidden" name="nonce"
               value="'. esc_attr( wp_create_nonce( 'inline-checkout' ) ) . '"/>
               <input type="hidden" name="lp-ajax"
               value="inline_checkout"/>';

	}

	/**
	 * Processes the ajax request sent by the Buy this course button form for a single course/project.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function inline_checkout() {

		sleep( 1 );
		$nonce = ! empty( $_POST['nonce'] ) ? $_POST['nonce'] : null;

		if ( ! wp_verify_nonce( $nonce, 'inline-checkout' ) ) {
			die ( __( 'You do not have permission to do this action', 'lp-projects' ) );
		}

		$course_id = ! empty( $_POST['course_id'] ) ? absint( $_POST['course_id'] ) : 0;
		$user_id   = get_current_user_id();


		if ( ( get_post_type( $course_id ) != 'lp_course' ) || ! $user_id ) {
			return;
		}

		if( ! LP()->cart->is_empty() ) {

			LP()->cart->empty_cart();

		}

		remove_action( 'learn-press/checkout-order-review', 'learn_press_order_comment' );

		LP()->cart->add_to_cart( $course_id );

		learn_press_projects_template( 'user-project-inline-checkout-order-review.php' );

		die;

	}


	public function get_tab_slug() {
		return apply_filters( 'learn_press_course_projects_tab_slug', $this->_tab_slug, $this );
	}


	/**
	 * Rewrite endpoint.
	 */
	public function rewrite_endpoint() {

		if( ! learn_press_is_profile() ) {
			return false;
		}
		$endpoint                     = preg_replace( '!_!', '-', $this->get_tab_slug() );
		LP()->query_vars[ $endpoint ] = $endpoint;
		add_rewrite_endpoint( $endpoint, EP_ROOT | EP_PAGES );
	}

	public function profile_tab_endpoints( $endpoints ) {
		$endpoints[] = $this->get_tab_slug();

		return $endpoints;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   mixed
	 */
	public function change_project_breadcrumb( $breadcrumb ) {

		return $breadcrumb;

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   array
	 */
	public function projects_tab( $tabs ) {

		$tabs[ $this->get_tab_slug() ] = array(
			'title'    => __( 'Projects', 'lp-projects' ),
			'slug'     => $this->get_tab_slug(),
			'callback' => array( $this, 'projects_tab_content' ),
			'priority' => 12
		);

		return $tabs;

	}

	public function projects_tab_content( $tab, $tabs, $profile ) {

		$viewing_user = $profile->get_user();
		learn_press_projects_template(
			'user-projects.php',
			array(
				'query' => $this->get_users_purchased_projects( $viewing_user->get_id() )
			)
		);

	}


	public function build_tax_query( $relation, $args = array() ) {

		$defaults = apply_filters( 'lp_projects_build_tax_query_default_args', array(

			'relation'			=> null,
			'project_category'	=> array( 

					'taxonomy'		=> 'course_category',
					'field'			=> 'slug',
					'terms'			=> 'project'
			)

		) );

		$args = wp_parse_args( $args, $defaults );
		$tax_query = array(

			$args['project_category']

		);

		// foreach ( $args as $key => $value ) {
			


		// }

		return $tax_query;

	}

	public function filter_out_projects_from_bp_profile_courses( $query_parts, $user_id, $args ) {

		global $wpdb;

		$project_id = null;

		$query_parts['where'] .= $wpdb->prepare( 

			"
				AND NOT EXISTS (SELECT * FROM {$wpdb->term_relationships} tr 
									JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
									JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
								WHERE c.ID = tr.object_id
									AND tt.taxonomy = %s
									AND t.name = %s)

			", array( 'course_category', 'project' ) );

		return $query_parts;

	}

	public function render_inline_checkout_form() {

		learn_press_projects_template( 'user-project-inline-checkout.php' );

	}


	public function parse_projects_archive_tab_action( $action ) {

		$projects_archive_tab_action = get_query_var( 'projects_tab' );

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_users_purchased_projects( $user_id ) {
		// $pid = (array) get_user_meta( $user_id, '_lpr_wish_list', true );
		return learn_press_get_users_purchased_projects( $user_id );
	}

	public function projects_page_id_settings( $settings ) {

		$settings[] = array(

			'title'   => __( 'Projects Page', 'lp-projects' ),
			'id'      => 'projects_page_id',
			'default' => '',
			'type'    => 'pages-dropdown'

		);

		return $settings;

	}

	public function filter_project_page_title( $title ) {

		if( is_tax( 'course_category', 'project' ) ) {

			if( learn_press_get_page_id( 'projects' ) ) {

				$page_id = learn_press_get_page_id( 'projects' );

				$title = get_the_title( $page_id );

				return $title;

			}

		}

		return $title;

	}

	public function project_loop_item_thumbnail( $course = null ) {

		$course = LP_Global::course();
		if( has_term( 'project', 'course_category' ) ) {

			$button = esc_html__( 'View Project', 'lp-projects' );

		} else {

			$button = esc_html__( 'Read More', 'eduma' );

		}
		echo '<div class="course-thumbnail">';
		echo '<a class="thumb" href="' . esc_url( get_the_permalink( $course->get_id() ) ) . '" >';
		echo thim_get_feature_image( get_post_thumbnail_id( $course->get_id() ), 'full', apply_filters( 'thim_course_thumbnail_width', 400 ), apply_filters( 'thim_course_thumbnail_height', 320 ), $course->get_title() );
		echo '</a>';
		do_action( 'thim_inner_thumbnail_course' );
		echo '<a class="course-readmore" href="' . esc_url( get_the_permalink( $course->get_id() ) ) . '">' . $button . '</a>';
		echo '</div>';		

	}

	public function purchase_project_button_text( $text ) {

		global $post;

		if( has_term( 'project', 'course_category', $post->ID ) && get_post_type( $post->ID ) == 'lp_course' ) {

			$text = __( 'Buy this project', 'lp-projects' );

		}

		return $text;

	}

	public function free_project_button_text( $text ) {

		global $post;

		if( has_term( 'project', 'course_category', $post->ID ) && get_post_type( $post->ID ) == 'lp_course' ) {

			$text = __( 'Start Project', 'lp-projects' );

		}

		

		return $text;

	}


	public function project_progress_text( $text ) {

		global $post;

		if( has_term( 'project', 'course_category', $post->ID ) && get_post_type( $post->ID ) == 'lp_course' ) {

			$text = __( 'Project progress', 'lp-projects' );

		}

		return $text;

	}


	public function project_features( $template, $template_name, $template_path ) {

		global $post;

		if( $template_name != 'single-course/tabs/overview.php' ) {
			return $template;
		}

		if( has_term( 'project', 'course_category', $post->ID ) && get_post_type( $post->ID ) == 'lp_course' ) {

			$template = LP_ADDON_PROJECTS_TEMPLATE . $template_name;

		}

		return $template;

	}

	// public function order_review( $template, $template_name , $template_path ) {


	// 	if( $template_name != 'checkout/review-order.php' ) {
	// 		return $template;
	// 	}

	// 	$template = LP_ADDON_PROJECTS_TEMPLATE . $template_name;

	// 	return $template;

	// }

	public function single_project( $template, $template_name , $template_path ) {

		$project = get_the_ID();

		if( $template_name != 'content-single-course.php' ) {
			return $template;
		}

		if( get_post_type( $project ) != 'lp_course' ) {
			return $template;
		}

		if( ! has_term( 'project', 'course_category', $project ) ) {
			return $template;
		}

		$template = LP_ADDON_PROJECTS_TEMPLATE . $template_name;

		return $template;

	}

	public function lp_project_order_received( $return_url, $order ) {

		$order_items = $order->get_items();
		$count = count( $order_items );

		if( $order_items && $count == 1 ) {

			foreach ( $order_items as $key => $item ) {

				$return_url = add_query_arg( 'order', 'success', get_the_permalink( $item['course_id'] ) );

			}

			
		}

		return $return_url;

	}

	public function render_sidebars() {

		global $post;

		$class_col = thim_wrapper_layout();
		$get_post_type = get_post_type();
		$project_page_id = learn_press_get_page_id( 'projects' );
		$current_id = $post->ID;
		if ( is_404() ) {
			$class_col = 'col-sm-12 full-width';
		}
		echo '</main>';

		if ( $class_col != 'col-sm-12 full-width' ) {
			if( $current_id == $project_page_id || has_term( 'project', 'course_category', $current_id ) ) {
				// get_sidebar( 'projects' );
				learn_press_projects_template( 'sidebars/sidebar-projects.php' );
			} else if ( $get_post_type == "lpr_course" || $get_post_type == "lpr_quiz" || $get_post_type == "lp_course" || $get_post_type == "lp_quiz" || thim_check_is_course() || thim_check_is_course_taxonomy() ) {
				get_sidebar( 'courses' );
			} else if ( $get_post_type == "tp_event" ) {
				get_sidebar( 'events' );
			} else if ( $get_post_type == "product" ) {
				get_sidebar( 'shop' );
			} else {
				get_sidebar();
			}
		}
		echo '</div>';

        do_action( 'thim_after_site_content' );

        echo '</div>';

	}

	/**
	 * @param $template
	 *
	 * @return string
	 */
	public function show_projects( $template ) {
		// $file = '';
		// if ( is_singular( array( LP_COLLECTION_CPT ) ) ) {
		// 	global $post;
		// 	if ( ! preg_match( '/\[learn_press_collection\s?(.*)\]/', $post->post_content ) ) {
		// 		$post->post_content .= '[learn_press_collection id="' . get_the_ID() . '" limit="2"]';
		// 	}

		// 	$file   = 'single-collection.php';
		// 	$find[] = learn_press_template_path() . "/addons/collections/{$file}";
		// } elseif ( is_post_type_archive( LP_COLLECTION_CPT ) ) {
		// 	$file   = 'archive-collection.php';
		// 	$find[] = learn_press_template_path() . "/addons/collections/{$file}";
		// }
		// if ( $file ) {
		// 	$template = locate_template( array_unique( $find ) );
		// 	if ( ! $template ) {
		// 		$template = LP_COLLECTIONS_TEMPLATES . $file;
		// 	}
		// }

		//MINE

		if( is_main_query() && is_page() && ( get_queried_object_id() == ( $page_id = learn_press_get_page_id( 'projects' ) ) && $page_id ) ) {

			$template = learn_press_projects_locate_template( 'project-archive.php' );

			return $template;

		}

		return $template;
	}

	public function redirect_back_to_project( $url ) {

		$project = get_the_ID();

		if( get_post_type( $project ) != 'lp_course' ) {
			return $url;
		}

		if( ! has_term( 'project', 'course_category', $project ) ) {
			return $url;
		}


		$url = get_the_permalink( $project );

		return $url;

	}

	/**
	 * @return bool
	 */
	protected function _is_archive() {
		return learn_press_is_courses() || learn_press_is_course_tag() || learn_press_is_course_category() || learn_press_is_search() || learn_press_is_course_tax();
	}


	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

add_action( 'plugins_loaded', array( 'LP_Addon_Projects', 'instance' ) );