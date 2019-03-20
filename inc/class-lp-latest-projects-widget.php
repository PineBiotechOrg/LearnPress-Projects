<?php

/**
 * Adds LP_Latest_Projects_Widget widget.
 */
class LP_Lastest_Projects_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'latest_projects_widget',
			__( 'Latest Projects', 'lp-projects' ),
			array( 'description' => __( 'Display latest projects', 'lp-projects' ) )
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
		echo '<div class="thim-widget-courses thim-widget-courses-base">';
		if ( !empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		$tax_query = array( 

						array(

							'taxonomy' 	=> 'course_category',
							'field'		=> 'slug',
							'terms'		=> 'project'

						) );
		// Query collections
		$query_args = array(
			'post_type'      => 'lp_course',
			'post_status'    => 'publish',
			'posts_per_page' => ( !empty ( $instance['number'] ) ? $instance['number'] : - 1 ),
			'tax_query'		 => $tax_query,
		);
		// The Query
		$the_query = new WP_Query( $query_args );

		// The Loop
		if ( $the_query->have_posts() ) {
			global $post;
			echo '<div class="thim-course-list-sidebar">';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$image = get_the_post_thumbnail( $post->ID, 'medium' );
				echo '<div class="lpr_course has-post-thumbnail">';
						if ( has_post_thumbnail() ) {
							$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
							echo '<div class="course-thumbnail">';
		                    echo '<img src="' . esc_url( $src[0] ) . '" alt="' . get_the_title() . '"/>';
		                    echo '</div>';
		                }
		              echo '<div class="thim-course-content">
		              			<h3 class="course-title">
		                    		<a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a>
		                    	</h3>';
		                    	learn_press_course_price();
		              echo '</div>
		              </div>';
			}
			wp_reset_postdata();
			echo '</div>';
		}
		/* Restore original Post Data */
		echo '</div>';
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
		$title  = !empty( $instance['title'] ) ? $instance['title'] : __( 'Projects', 'lp-projects' );
		$number = !empty( $instance['number'] ) ? $instance['number'] : '5';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'lp-projects' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of projects to show:', 'learnpress-collections' ); ?></label>
			<input type="text" size="3" value="<?php echo esc_attr( $number ); ?>" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>">
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
		$instance['number'] = ( !empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : '';
		return $instance;
	}

}
