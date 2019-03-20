<?php

/**
 * Adds LP_Collections_Widget widget.
 */
class LP_Projects_Tab_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'projects_tab_widget',
			__( 'Project Tabs', 'lp-projects' ),
			array( 'description' => __( 'Display tabs of project classifications', 'lp-projects' ) )
		);
	}

	/**
	 * Front-end display
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( !empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		

		$tabs = array();

		$tabs['my-projects'] = array(

			'title'		=> __( 'My Projects', 'lp-projects' ),
			'slug'		=> 'my-projects',
			'class'		=> 'my-projects-btn tab-button',
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

		$tabs = apply_filters( 'learn_press_projects_tabs', $tabs );

		echo '<ul class="projects-tabs-widget">';

		foreach ( $tabs as $handle => $tab ) {

			if( ! $tab[ 'enabled' ] ) {
				continue;
			}

			$title = $tab[ 'title' ];
			$link = learn_press_projects_page_link( 'projects_tab', $tab['slug'] );
			$class = $tab[ 'class' ];
			$default_tab = ( $tab['default'] ) ? $tab['slug'] : '';
			$current_tab = get_query_var( 'projects_tab' );
			$active = ( $current_tab == $tab['slug'] || ( empty( $current_tab ) && $default_tab == $tab['slug'] ) ) ? 'active' : '';

			echo '<li class="'.esc_attr( $class ).'-list-item"><span class="project-tab '. esc_attr( $class ) .'">';

			echo '<a href="'. esc_attr( $link ) .'" class="'. esc_attr( $active ) .'  '.esc_attr( $class ).'-link" >'. esc_html( $title ) .'</a>';

			echo '</span></li>';
			
		}

		echo '</ul>';


		echo $args['after_widget'];
	}

	/**
	 * Back-end form
	 *
	 * @param array $instance
	 *
	 * @return mixed
	 */
	public function form( $instance ) {
		$title  = !empty( $instance['title'] ) ? $instance['title'] : __( 'Project Tabs', 'lp-projects' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'lp-projects' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance           = array();
		$instance['title']  = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}

}
