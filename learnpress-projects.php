<?php
/**
 * @link              https://joshuamckendall.github.io/learnpress-projects
 * @since             1.0.0
 * @package           LearnPress-Projects
 *
 * @wordpress-plugin
 * Plugin Name:       LearnPress - Projects
 * Plugin URI:        https://joshuamckendall.github.io/learnpress-projects
 * Description:       LearnPress Projects extends LearnPress to add the project course type.
 * Version:           1.0.0
 * Author:            Joshua McKendall
 * Author URI:        https://joshuamckendall.github.io/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lp-projects
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit();

define( 'LP_ADDON_PROJECTS_FILE', __FILE__ );
define( 'LP_ADDON_PROJECTS_VER', '1.0.0' );
define( 'LP_ADDON_PROJECTS_REQUIRE_VER', '1.0.0' );

function activate_lp_projects() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-lp-projects-activator.php';
	$lp_projects = new LP_Projects_Activator;
	$lp_projects::activate( $lp_projects );
}

function deactivate_lp_projects() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-lp-projects-deactivator.php';
	LP_Projects_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_lp_projects' );
register_deactivation_hook( __FILE__, 'deactivate_lp_projects' );


/**
 * Class LP_Addon_Projects_Preload
 */
class LP_Addon_Projects_Preload {

	/**
	 * LP_Addon_Projects_Preload constructor.
	 */
	public function __construct() {

		add_action( 'init', 							array( $this, 'register_rewrite_rules' ) );
		add_action( 'init',								array( $this, 'register_omics_taxonomy' ) );
		add_action( 'init',								array( $this, 'register_sidebars' ) );
		add_action( 'set_current_user',					array( $this, 'hide_admin_bar_for_non_admins' ) );
		add_action( 'learn_press_add_rewrite_rules', 	array( $this, 'project_rewrite_rules' ) );
		add_action( 'learn-press/ready', 				array( $this, 'load' ) );
		// add_action( 'learn-press/before-courses-loop',	array( $this, 'render_projects_tabs' ) );
		add_action( 'admin_notices', 					array( $this, 'admin_notices' ) );
		add_action( 'template_include', 				array( $this, 'template_controller' ), 10 );
		add_action( 'widgets_init', 					array( $this, 'register_widget' ) );

		add_filter( 'query_vars', 						array( $this, 'register_query_vars' ) );
		add_filter( 'post_type_link', 					array( $this, 'project_permalink' ), 10, 2 );
		
	}

	/**
	 * Load addon
	 */
	public function load() {
		LP_Addon::load( 'LP_Addon_Projects', 'inc/load.php', __FILE__ );
		remove_action( 'admin_notices', array( $this, 'admin_notices' ) );

		do_action( 'lp_projects_loaded', $this );
	}


	public function hide_admin_bar_for_non_admins() {

	    if( ! current_user_can('edit_posts') ) {

	        show_admin_bar(false);

	    }

	}

	public function register_rewrite_rules() {

		$project_page_id = learn_press_get_page_id( 'projects' );
		$slug = get_post_field( 'post_name', $project_page_id );
		$project_structure = $slug . '/%project%';

		add_rewrite_tag( '%projects_page%', '([^&]+)' );
		add_rewrite_rule( '^'. $slug .'/page/([0-9]{1,})/?$', 'index.php?course_category='.$slug.'&projects_page=$matches[1]', 'top' );

		if( $project_structure ) {

			add_rewrite_tag( '%project%', '([^/]+)', 'post_type=lp_course&name=' );
			add_permastruct( 'projects', $project_structure, array( 'with_front' => false ) );

		}

	}

	public function project_rewrite_rules() {

		$post_types   = get_post_types( '', 'objects' );
		$course_type  = LP_COURSE_CPT;
		$project_page_id = learn_press_get_page_id( 'projects' );
		$slug = get_post_field( 'post_name', $project_page_id );
		$has_category = false;
		if ( preg_match( '!(%?course_category%?)!', $slug ) ) {
			$slug         = preg_replace( '!(%?course_category%?)!', '(.+?)/([^/]+)', $slug );
			$has_category = true;
		}
		$current_url        = learn_press_get_current_url();
		$query_string       = str_replace( trailingslashit( get_home_url() /* SITE_URL */ ), '', $current_url );
		$custom_slug_lesson = sanitize_title_with_dashes( LP()->settings->get( 'lesson_slug' ) );
		$custom_slug_quiz   = sanitize_title_with_dashes( LP()->settings->get( 'quiz_slug' ) );

		if ( $has_category ) {
			add_rewrite_rule(
				'^' . $slug . '(?:/' . $post_types['lp_lesson']->rewrite['slug'] . '/([^/]+))/?$',
				'index.php?' . $course_type . '=$matches[2]&course_category=$matches[1]&course-item=$matches[3]&item-type=lp_lesson',
				'top'
			);
			add_rewrite_rule(
				'^' . $slug . '(?:/' . $post_types['lp_quiz']->rewrite['slug'] . '/([^/]+)/?([^/]+)?)/?$',
				'index.php?' . $course_type . '=$matches[2]&course_category=$matches[1]&course-item=$matches[3]&question=$matches[4]&item-type=lp_quiz',
				'top'
			);

		} else {

			add_rewrite_rule(
				'^' . $slug . '/([^/]+)(?:/' . $post_types['lp_lesson']->rewrite['slug'] . '/([^/]+))/?$',
				'index.php?' . $course_type . '=$matches[1]&course-item=$matches[2]&item-type=lp_lesson',
				'top'
			);
			add_rewrite_rule(
				'^' . $slug . '/([^/]+)(?:/' . $post_types['lp_quiz']->rewrite['slug'] . '/([^/]+)/?([^/]+)?)/?$',
				'index.php?' . $course_type . '=$matches[1]&course-item=$matches[2]&question=$matches[3]&item-type=lp_quiz',
				'top'
			);

		}

	}

	public function register_sidebars() {

		register_sidebar( array(
						'name'          => esc_html__( 'Sidebar Projects', 'lp-projects' ),
						'id'            => 'sidebar_projects',
						'description'   => esc_html__( 'Sidebar Projects', 'lp-projects' ),
						'before_widget' => '<aside id="%1$s" class="widget %2$s">',
						'after_widget'  => '</aside>',
						'before_title'  => '<h4 class="widget-title">',
						'after_title'   => '</h4>',
					) );

	}

	public function register_omics_taxonomy() {

		// Add new taxonomy, make it hierarchical (like categories)
			$labels = array(
				'name'              => _x( '-Omics Fields', 'taxonomy general name', 'lp-projects' ),
				'singular_name'     => _x( '-Omics Field', 'taxonomy singular name', 'lp-projects' ),
				'search_items'      => __( 'Search -Omics Fields', 'lp-projects' ),
				'all_items'         => __( 'All -Omics Fields', 'lp-projects' ),
				'edit_item'         => __( 'Edit -Omics Field', 'lp-projects' ),
				'update_item'       => __( 'Update -Omics Field', 'lp-projects' ),
				'add_new_item'      => __( 'Add New -Omics Field', 'lp-projects' ),
				'new_item_name'     => __( 'New -Omics Field Name', 'lp-projects' ),
				'menu_name'         => __( '-Omics Fields', 'lp-projects' ),
			);

			$args = array(
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'omics' ),
			);

			register_taxonomy( 'omics', array( 'lp_course' ), $args );		

	}

	public function project_permalink( $permalink, $post ) {

		if( is_admin() ) {
			return $permalink;
		}

		if( get_post_type( $post ) == 'lp_course' && has_term( 'project', 'course_category', $post ) ) {

			$project_page_id = learn_press_get_page_id( 'projects' );
			$slug = get_post_field( 'post_name', $project_page_id );			

			return trailingslashit( home_url( $slug . '/' . $post->post_name ) );

	    }

	    return $permalink;		

	}

	public function register_query_vars( $vars ) {

		$vars[] .= 'projects_page';
		$vars[] .= 'projects_tab';
		$vars[] .= 'omics_tab';
 
		return $vars;

	}


	public function template_controller( $template ) {

		if( is_main_query() && is_page() && ( get_queried_object_id() == ( $page_id = learn_press_get_page_id( 'projects' ) ) && $page_id ) ) {

			$template = LP_ADDON_PROJECTS_FILE . '/templates/project-archive.php';

			return $template;

		}

		return $template;	

	}

	public function get_projects_tabs() {

		$tabs = array();

		$tabs['my-projects'] = array(

			'title'		=> __( 'My Projects', 'lp-projects' ),
			'slug'		=> 'my-projects',
			'class'		=> 'my-projects-btn button',
			'enabled'	=> is_user_logged_in(),
			'default'	=> false,
			// 'callback'	=> array( $this, 'render_purchased_projects' )

		); 

		$tabs['all'] = array(

			'title'		=> __( 'All', 'lp-projects' ),
			'slug'		=> 'all',
			'class'		=> 'all-projects-btn tab-button',
			'enabled'	=> true,
			'default'	=> true,
			// 'callback'	=> null

		); 

		$omics = get_terms( array( 'taxonomy' => 'omics', 'hide_empty' => true ) );

		foreach ( $omics as $key => $omic ) {
			
			$tabs[ $omic->slug ] = array(

				'title'		=> $omic->name,
				'slug'		=> $omic->slug,
				'class'		=> $omic->slug . '-btn tab-button',
				'enabled'	=> true,
				'default'	=> false

			);

		}

		return apply_filters( 'learn_press_projects_tabs', $tabs );

	}

	public function render_projects_tabs() {

		if( get_queried_object_id() != learn_press_get_page_id( 'projects' ) ) {
			return;
		}

		global $wp;
		$current_url = trailingslashit( home_url( add_query_arg( array(), $wp->request ) ) );
		$link = '';

		foreach ( $this->get_projects_tabs() as $key => $tabs ) {

			$active = '';

			if( ! $tabs['enabled'] ) {
				continue;
			}
			
			if( $tabs['default'] === true ) {

				$active = 'active';

			} else {

				$active = '';

			}

			$link = add_query_arg( 'projects_tab', $tabs['slug'], $current_url );

			learn_press_projects_template( 'loop/projects-tabs.php', array( 

				'title' 	=> $tabs['title'],
				'link'		=> $link,
				'class'		=> $tabs['class'],
				'enabled'	=> $tabs['enabled'],
				'active'	=> $active

			) );

		}

	}

	public function register_widget() {

		include_once dirname( LP_ADDON_PROJECTS_FILE ) . '/inc/class-lp-latest-projects-widget.php';
		include_once dirname( LP_ADDON_PROJECTS_FILE ) . '/inc/class-lp-projects-tab-widget.php';

		register_widget( 'LP_Lastest_Projects_Widget' );
		register_widget( 'LP_Projects_Tab_Widget' );

	}

	/**
	 * Admin notice
	 */
	public function admin_notices() {
		?>
        <div class="error">
            <p><?php echo wp_kses(
					sprintf(
						__( '<strong>%s</strong> addon version %s requires %s version %s or higher is <strong>installed</strong> and <strong>activated</strong>.', 'lp-projects' ),
						__( 'LearnPress Wishlist', 'lp-projects' ),
						LP_ADDON_PROJECTS_VER,
						sprintf( '<a href="%s" target="_blank"><strong>%s</strong></a>', admin_url( 'plugin-install.php?tab=search&type=term&s=learnpress' ), __( 'LearnPress', 'lp-projects' ) ),
						LP_ADDON_PROJECTS_REQUIRE_VER
					),
					array(
						'a'      => array(
							'href'  => array(),
							'blank' => array()
						),
						'strong' => array()
					)
				); ?>
            </p>
        </div>
		<?php
	}
}

new LP_Addon_Projects_Preload();