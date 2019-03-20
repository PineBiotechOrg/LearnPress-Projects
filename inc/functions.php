<?php


function learn_press_projects_locate_template( $template_name ) {
	return learn_press_locate_template( $template_name, learn_press_template_path() . '/addons/projects/', LP_ADDON_PROJECTS_TEMPLATE );
}

if ( ! function_exists( 'learn_press_projects_template' ) ) {
	/**
	 * Get projects template.
	 *
	 * @param $name
	 * @param null $args
	 */
	function learn_press_projects_template( $template_name, $args = null ) {
		//LP_Addon_Projects::instance()->get_template($name, $args);
		learn_press_get_template( $template_name, $args, learn_press_template_path() . '/addons/projects/', LP_ADDON_PROJECTS_TEMPLATE );
	}
}

if( ! function_exists('learn_press_projects_template_content') ) {

	function learn_press_projects_template_content( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

		ob_start();
		learn_press_projects_template( $template_name, $args );

		return ob_get_clean();		

	}

}


if( ! function_exists('learn_press_projects_template_part') ) {

	function learn_press_projects_template_part( $slug, $name = '' ) {

		$template = '';

		// Look in yourtheme/slug-name.php and yourtheme/learnpress/slug-name.php
		if ( $name ) {
			$template = locate_template( array(
				"{$slug}-{$name}.php",
				learn_press_template_path() . "/addons/projects/{$slug}-{$name}.php"
			) );
		}

		// Get default slug-name.php
		if ( ! $template && $name && file_exists( LP_ADDON_PROJECTS_PATH . "/templates/{$slug}-{$name}.php" ) ) {
			$template = LP_ADDON_PROJECTS_PATH . "/templates/{$slug}-{$name}.php";
		}

		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/learnpress/slug.php
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", learn_press_template_path() . "/{$slug}.php" ) );
		}

		// Allow 3rd party plugin filter template file from their plugin
		if ( $template ) {
			$template = apply_filters( 'learn_press_get_template_part', $template, $slug, $name );
		}
		if ( $template && file_exists( $template ) ) {
			load_template( $template, false );
		}

		return $template;


	}

}

add_filter( 'learn_press_profile_tabs', 'learn_press_projects_tab', 10, 2 );
if ( ! function_exists( 'learn_press_projects_tab' ) ) {
	/**
	 *  Add Projects tab into profile page.
	 *
	 * @param $tabs
	 * @param $user
	 *
	 * @return mixed
	 */
	function learn_press_projects_tab( $tabs, $user ) {
		$content = '';

		$tabs[32] = array(
			'tab_id'      => 'user_projects',
			'tab_name'    => __( 'Projects', 'lp-projects' ),
			'tab_content' => apply_filters( 'learn_press_user_projects_tab_content', $content, $user )
		);
		// Private customize
		if ( $user->ID != get_current_user_id() ) {
			unset ( $tabs[32] );
		}

		return $tabs;
	}
}

add_filter( 'learn_press_user_projects_tab_content', 'learn_press_user_projects_tab_content', 10, 2 );
if ( ! function_exists( 'learn_press_user_projects_tab_content' ) ) {
	/**
	 * Setup projects tab content.
	 *
	 * @param $content
	 * @param $user
	 *
	 * @return string
	 */
	function learn_press_user_projects_tab_content( $content, $user ) {
		ob_start();
		learn_press_projects_template( 'user-projects.php', array( 'user' => $user ) );
		$content .= ob_get_clean();

		return $content;
	}
}


add_action( 'learn_press_projects_loop_item_title', 'learn_press_projects_loop_item_title', 5 );

if ( ! function_exists( 'learn_press_projects_loop_item_title' ) ) {
	/**
	 * Loop item title.
	 */
	function learn_press_projects_loop_item_title() {
		learn_press_projects_template( 'loop/title.php' );
	}
}


function learn_press_get_projects( $per_page = 15 ) {


		$args     = array(
			'post_type'           => 'lp_course',
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page,
			'tax_query'			  => array(

				array(

						'taxonomy' 	=> 'course_category',
						'field'		=> 'slug',
						'terms'		=> 'project'

					)

			),
		);
		$query    = new WP_Query( $args );
		// $user = learn_press_get_user( $user_id );
		$projects = array();
		global $post;
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) : $query->the_post();

				$projects[ $post->ID ] = $post;

				
			endwhile;
			wp_reset_postdata();
		endif;

		return $projects;

}


function learn_press_get_users_purchased_projects( $user_id ) {


		$args     = array(
			'post_type'           => 'lp_course',
			'post_status'         => 'publish',
			'posts_per_page'      => 15,
			'tax_query'			  => array(

				array(

						'taxonomy' 	=> 'course_category',
						'field'		=> 'slug',
						'terms'		=> 'project'

					)

			),
		);
		$projects    = new WP_Query( $args );

		return $projects;

}


function learn_press_get_projects_page_link() {

	$project_page_id = learn_press_get_page_id( 'projects' );

	return get_page_link( $project_page_id );

}

function learn_press_user_owns_project( $course_id, $projects_tab ) {

	$user = learn_press_get_current_user();

	if( $projects_tab == 'my-projects' ) {

		if( $user->has_purchased_course( $course_id ) ) {

			return true;

		}

	}

	return false;

}

function learn_press_projects_page_link( $key, $value ) {

	$projects_page_link = learn_press_get_projects_page_link();

	if( isset( $key ) && isset( $value ) ) {

		$projects_page_link = add_query_arg( $key, $value, $projects_page_link );

	}

	return $projects_page_link;

}


function get_omics_tabs( $atts = array() ) {

	$omics = get_terms( array( 'taxonomy' => 'omics', 'hide_empty' => true ) );
	$tax_query = array();

	if( term_exists( $atts['tab'], 'omics' ) ) {

		$tax_query['relation'] = 'AND';

		$tax_query[] = array(

				'taxonomy'	=> 'omics',
				'field'		=> 'slug',
				'terms'		=> $atts[ 'tab' ]

		);

	}

	$tax_query[] = array(

			'taxonomy' 	=> 'course_category',
			'field'		=> 'slug',
			'terms'		=> 'project'

	);

	$limit = $atts[ 'limit' ];

	$query_args = array(

		'post_type'           => 'lp_course',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => $limit,
		'offset'              => ( max( get_query_var( 'projects_page' ) - 1, 0 ) ) * $limit,
		'tax_query'			  => $tax_query,
	);

	return $query_args;
}
